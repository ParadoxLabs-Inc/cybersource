<?php declare(strict_types=1);
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout;

use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Framework\Exception\RuntimeException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\BindingValidator;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\PassThroughMapper;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\LineItemsBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\StoredCardRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\StoredCardRequestFactory;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use Throwable;

/**
 * Executes a Unified Checkout auth/sale via REST POST /pts/v2/payments and interprets the response.
 *
 * Consumes the transient-token JWT captured client-side (payment additional_data.transient_token),
 * runs the payment with actionList:[TOKEN_CREATE] to mint the TMS vault ids inline, and translates
 * the JSON reply into the gateway Response object the rest of the module already consumes
 * (mirroring the SOAP-era Gateway::interpretTransaction()). Card-saving to the vault is A2; gateway wiring is A3.
 *
 * Two confirmed spike findings shape the parsing (see UC-API-REFERENCE.md §4):
 *  1. Decision Manager review (AUTHORIZED_PENDING_REVIEW) suppresses tokenInformation entirely — a
 *     successful auth can legitimately carry NO TMS ids. We treat that as success and flag it.
 *  2. TOKEN_CREATE can fail ("Requested service is forbidden" / PROCESSOR_ERROR) while the auth itself
 *     approves, reported as top-level status=DECLINED + reason=PROCESSOR_ERROR. We must not fail an
 *     approved auth for a token-service error -- proceed token-less (carve-out in interpretResponse()).
 *
 * APPROVAL AUTHORITY: the top-level `status` decides approved-vs-declined. processorInformation.responseCode
 * is the raw acquirer code ('100' only on the sandbox simulator); CyberSource's field reference says "Do not
 * use this field to evaluate the result of the authorization."
 *
 * @see UC-API-REFERENCE.md §2, §3, §4
 */
class Response
{
    use PriorTransactionIdTrait;
    use StringNormalizationTrait;

    /**
     * Payment REST endpoint path.
     */
    public const PAYMENTS_PATH = '/pts/v2/payments';

    /**
     * processingInformation.actionList value that mints the TMS vault ids inline.
     */
    public const ACTION_TOKEN_CREATE = 'TOKEN_CREATE';

    /**
     * processingInformation.actionTokenTypes — the TMS ids for the D5 vault mapping.
     *
     * Deliberately EXCLUDES 'customer': cards are stored as standalone TMS payment instruments. The
     * customer (profile) token added nothing here — the module minted one customer PER CARD (TokenBase
     * is the customer-grouping layer), and customer-token creation is a separately provisioned TMS
     * vault permission that is not enabled on all merchant accounts ("Requested service is forbidden"
     * from TOKEN_CREATE, 403 from POST /tms/v2/customers, while paymentInstrument/instrumentIdentifier
     * creation succeeds). Standalone payment instruments are a first-class TMS token type and fully
     * support MIT charge and delete.
     */
    public const ACTION_TOKEN_TYPES = ['paymentInstrument', 'instrumentIdentifier'];

    /**
     * CyberSource payment status values that represent an approved (or accepted-pending) auth.
     *
     * AUTHORIZED_PENDING_REVIEW is a Decision Manager hold — the auth is good but held for review,
     * and (per the spike) carries NO tokenInformation.
     *
     * PENDING_AUTHENTICATION is deliberately ABSENT: it means CyberSource wants a 3DS step-up and no
     * money is authorized yet. Payer Authentication resolves entirely BEFORE place in this design, so
     * treating it as approved would place an unpaid order.
     */
    public const APPROVED_STATUSES = [
        'AUTHORIZED',
        'AUTHORIZED_PENDING_REVIEW',
        'PARTIAL_AUTHORIZED',
        'PENDING',
        'PENDING_REVIEW',
    ];

    /**
     * The processorInformation.responseCode the CyberSource sandbox simulator returns for an approval.
     *
     * Informational only — never an approval decision (see the class docblock, APPROVAL AUTHORITY).
     */
    public const RESPONSE_CODE_APPROVED = '100';

    /**
     * The amount sent for the zero-dollar add-card (tokenize-without-charge) path.
     */
    public const ZERO_DOLLAR_AMOUNT = '0.00';

    /**
     * errorInformation.reason value emitted when a sub-service (e.g. TOKEN_CREATE) is not provisioned.
     */
    public const REASON_PROCESSOR_ERROR = 'PROCESSOR_ERROR';

    /**
     * errorInformation.reason value emitted when Decision Manager (or ATO) REJECTS the order.
     *
     * The auth can still approve at the processor (responseCode=100, status AUTHORIZED_RISK_DECLINED)
     * while DM rejects it — we must treat that as a hard decline, not a clean approval.
     */
    public const REASON_DECISION_PROFILE_REJECT = 'DECISION_PROFILE_REJECT';

    /**
     * Payment statuses that represent a Decision Manager / risk REJECT (auth approved but DM declined).
     */
    public const RISK_DECLINED_STATUSES = [
        'AUTHORIZED_RISK_DECLINED',
        'REJECTED',
    ];

    /**
     * consumerAuthenticationInformation fields surfaced from the reply for 3DS liability-shift /
     * authentication-result record-keeping (the UC completeMandate.consumerAuthentication parity set).
     */
    public const CONSUMER_AUTHENTICATION_FIELDS = [
        'eci',
        'eciRaw',
        'cavv',
        'cavvAlgorithm',
        'xid',
        'paresStatus',
        'authenticationResult',
        'authenticationStatusMsg',
        'veresEnrolled',
        'commerceIndicator',
        'specificationVersion',
        'directoryServerTransactionId',
        'threeDSServerTransactionId',
        'ucafAuthenticationData',
        'ucafCollectionIndicator',
        'indicator',
        'token',
    ];

    /**
     * Response constructor.
     *
     * @param Rest $rest
     * @param Config $config
     * @param Sanitizer $sanitizer
     * @param Data $helper
     * @param CardType $cardType
     * @param ResponseFactory $responseFactory
     * @param PaymentRequestFactory $requestFactory
     * @param StoredCardRequestFactory $storedCardRequestFactory
     * @param TransientTokenReader $transientTokenReader
     * @param BindingValidator $bindingValidator
     * @param PassThroughMapper $passThroughMapper
     * @param Persistor $payerAuthPersistor
     * @param LineItemsBuilder $lineItemsBuilder
     */
    public function __construct(
        protected readonly Rest $rest,
        protected readonly Config $config,
        protected readonly Sanitizer $sanitizer,
        protected readonly Data $helper,
        protected readonly CardType $cardType,
        protected readonly ResponseFactory $responseFactory,
        protected readonly PaymentRequestFactory $requestFactory,
        protected readonly StoredCardRequestFactory $storedCardRequestFactory,
        protected readonly TransientTokenReader $transientTokenReader,
        protected readonly BindingValidator $bindingValidator,
        protected readonly PassThroughMapper $passThroughMapper,
        protected readonly Persistor $payerAuthPersistor,
        protected readonly LineItemsBuilder $lineItemsBuilder,
    ) {
    }

    /**
     * Run a Unified Checkout auth/sale for the given payment and amount, and interpret the reply.
     *
     * $capture is the OPERATION'S intent (Gateway::authorize() -> false, Gateway::captureBundled() ->
     * true): Magento already routed the operation off payment_action, so re-deriving the flag from
     * config here would post an authorization for a bundled capture on an authorize-configured store
     * (money authorized, never captured). Null falls back to the config-derived placement default.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param bool|null $capture Operation intent: true=sale, false=auth-only; null=derive from config.
     * @param array<int|string, mixed> $lineItems Sales items to send as orderInformation.lineItems
     *        (already send_line_items-gated by the TokenBase wiring); empty = none sent.
     * @return GatewayResponse
     * @throws CommandException On a declined transaction (mirrors the SA/SOAP decline path), or on a
     *         failed/stale Payer Authentication record that must not be placed against.
     * @throws RuntimeException On an error/invalid response, or a missing transient token.
     * @throws Throwable
     */
    public function place(
        InfoInterface $payment,
        float $amount,
        ?bool $capture = null,
        array $lineItems = []
    ): GatewayResponse {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order   = $payment->getOrder();
        $storeId = $order->getStoreId() !== null ? (int)$order->getStoreId() : null;

        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request = $this->buildRequest($payment, $amount, $capture, $lineItems);

        // Payer Authentication: consume the pre-place verdict for THIS card and THIS amount, if any.
        // Runs before the money call so a failed/stale result blocks placement rather than dropping
        // the liability shift silently.
        $payerAuth = $this->attachPayerAuth(
            $payment,
            $request,
            $this->getTransientTokenBinding($payment),
            $this->getTransientTokenCardType($payment)
        );

        $response = $this->rest->post(self::PAYMENTS_PATH, $request->toArray());

        $gatewayResponse = $this->interpretResponse($response, $payment, $this->requestsTokenCreate($request));
        $this->seedCardInformationFromTransientToken($payment, $gatewayResponse);
        $this->completePayerAuth($payment, $gatewayResponse, $payerAuth);

        return $gatewayResponse;
    }

    /**
     * Run a Unified Checkout STORED-CARD (vault / MIT) auth/sale from the card's TMS ids, and interpret it.
     *
     * The card is already vaulted, so there is no transient token: we POST /pts/v2/payments with the three
     * TMS ids under paymentInformation and the stored-credential initiator block, then interpret the reply
     * exactly like the new-card path. Config/REST scope is taken from the order (same as place()).
     *
     * $capture carries the operation's intent exactly as on place(): a bundled capture (Gateway::
     * captureBundled(), e.g. a subscription rebill invoiced online) must post capture=true regardless
     * of the configured payment_action, or the paid invoice is backed by an auth that never settles.
     *
     * @param InfoInterface $payment
     * @param CardInterface $card
     * @param float $amount
     * @param bool|null $capture Operation intent: true=sale, false=auth-only; null=derive from config.
     * @param array<int|string, mixed> $lineItems Sales items to send as orderInformation.lineItems
     *        (already send_line_items-gated by the TokenBase wiring); empty = none sent.
     * @return GatewayResponse
     * @throws CommandException On a declined transaction (mirrors the SA/SOAP decline path), or on a
     *         failed/stale Payer Authentication record that must not be placed against.
     * @throws RuntimeException On an error/invalid response, or a card with no vaulted token.
     * @throws Throwable
     */
    public function placeStored(
        InfoInterface $payment,
        CardInterface $card,
        float $amount,
        ?bool $capture = null,
        array $lineItems = []
    ): GatewayResponse {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order   = $payment->getOrder();
        $storeId = $order->getStoreId() !== null ? (int)$order->getStoreId() : null;

        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request = $this->buildStoredCardRequest($payment, $card, $amount, $capture, $lineItems);

        // Payer Authentication on the stored-card CIT (the MIT/admin branches never consult it — see
        // shouldConsumePayerAuth()). The binding is the vault card id the authentication was run against.
        $payerAuth = $this->attachPayerAuth(
            $payment,
            $request,
            $this->payerAuthPersistor->cardBinding((int)$card->getId()),
            $this->stringOrNull($card->getAdditional('cc_type')) ?? ''
        );

        $response = $this->rest->post(self::PAYMENTS_PATH, $request->toArray());

        // StoredCardRequest carries no actionList: the card is already vaulted, so no token is requested
        // and the token-less reply is expected. Never flag uc_token_missing off this path.
        $gatewayResponse = $this->interpretResponse($response, $payment, false);
        $this->completePayerAuth($payment, $gatewayResponse, $payerAuth);

        return $gatewayResponse;
    }

    /**
     * Assemble the stored-card /pts/v2/payments request DTO from the vaulted card's TMS ids and the amount.
     *
     * D5 reverse-mapping (the inverse of CardBuilder's write side): paymentInformation.paymentInstrument.id
     * <- card paymentId (TMS paymentInstrument, the MIT key — REQUIRED, and the ONLY TMS id sent). The
     * card's additional[instrument_identifier] is deliberately NOT sent: the CAS sandbox A/B spike
     * confirmed that sending paymentInformation.instrumentIdentifier.id ALONGSIDE the paymentInstrument id
     * is rejected with 400 INVALID_REQUEST/INVALID_DATA, while the paymentInstrument alone authorizes (it
     * already references its instrument identifier server-side). Charging by instrumentIdentifier alone is
     * NOT a valid fallback either (the API then demands card expiration fields), so PI-only is the
     * contract. No customer id: cards are standalone TMS payment instruments (see ACTION_TOKEN_TYPES).
     * The capture flag is the caller's OPERATION intent when given
     * (never client input — Gateway passes false for authorize(), true for a bundled capture), falling
     * back to the SERVER-SIDE payment_action when null, identical to buildRequest().
     *
     * CIT vs MIT branch: keyed off payment additional_information['is_subscription_generated']. A
     * subscription/scheduled rebill is a merchant-initiated transaction (MIT) — initiator.type='merchant',
     * commerceIndicator='recurring', and a best-effort merchantInitiatedTransaction.previousTransactionId
     * pointing at the prior stored txn id; everything else is a customer-initiated transaction (CIT) —
     * initiator.type='customer' with no commerceIndicator and no MIT sub-object. On the CIT branch only, a
     * security code re-collected at checkout (require_ccv -> payment cc_cid) is forwarded as
     * paymentInformation.card.securityCode; an MIT rebill has no cardholder present to enter one, so it is
     * structurally never sent there. storedCredentialUsed is
     * always true (it's a stored card either way). previousTransactionId is best-effort: it is sourced from
     * the payment's parent/last txn id, stripped of any -capture/-refund suffix (the shared
     * PriorTransactionIdTrait, also used by Gateway's refund fallback), and omitted entirely when unreachable.
     *
     * VERIFY (live-UNVERIFIED — gate production enablement on a boarded + TMS-provisioned MID): the
     * stored-credential fields here are SDK/reference-derived only (sandbox TMS is NOT provisioned, so the
     * stored-card auth has never hit a live MID). Specifically confirm, because each is interchange-/
     * acceptance-affecting on the money path: (1) commerceIndicator='recurring' is the correct indicator for
     * these MIT rebills (vs install / a subsequent-auth qualifier); (2) storedCredentialUsed=true is right
     * for BOTH branches — the stored-credential framework distinguishes the INITIAL credential-on-file
     * transaction (storedCredentialUsed=false, no prior reference) from SUBSEQUENT uses, and A4 sends true
     * unconditionally; (3) whether an MIT with no previousTransactionId is accepted or declined/downgraded.
     *
     * DM-suppression-on-MIT parity (Iter 4): the old SOAP path suppressed Decision Manager whenever
     * amountPaid>0 OR is_subscription_generated; that condition is reproduced here via
     * shouldSuppressDecisionManager() -> enableDecisionManager=false (see below).
     *
     * DEFERRED (OPEN-WALLET-MIT): wallet network-token MIT branching (e.g. Apple/Google Pay network tokens)
     * is NOT handled here and is deferred pending the S6 verdict. Any future wallet/network-token MIT
     * request builder must also call shouldSuppressDecisionManager() like buildRequest()/
     * buildStoredCardRequest() do.
     *
     * @param InfoInterface $payment
     * @param CardInterface $card
     * @param float $amount
     * @param bool|null $capture Operation intent: true=sale, false=auth-only; null=derive from config.
     * @param array<int|string, mixed> $lineItems Sales items to send as orderInformation.lineItems.
     * @return StoredCardRequest
     * @throws RuntimeException When the card carries no vaulted paymentInstrument id.
     */
    public function buildStoredCardRequest(
        InfoInterface $payment,
        CardInterface $card,
        float $amount,
        ?bool $capture = null,
        array $lineItems = []
    ): StoredCardRequest {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        // Self-guard (defense in depth — the Gateway guards this too): never authorize a card the system
        // already knows is un-tokenized. A card can carry a STALE paymentId while still being flagged
        // uc_token_missing (CardBuilder sets the flag without clearing prior ids), so this flag check must
        // come BEFORE — and is independent of — the empty-paymentId check below. Keeps the public builder
        // self-defending regardless of caller.
        if ($card->getAdditional(CardBuilder::CARD_FLAG_TOKEN_MISSING) === '1') {
            throw new RuntimeException(
                __('Stored-card payment requires a vaulted card token. Please re-enter your payment information.')
            );
        }

        $paymentInstrumentId = (string)$card->getPaymentId();
        if ($paymentInstrumentId === '') {
            throw new RuntimeException(
                __('Stored-card payment requires a vaulted card token. Please re-enter your payment information.')
            );
        }

        /** @var StoredCardRequest $request */
        $request = $this->storedCardRequestFactory->create();

        $request->setClientReferenceCode((string)$order->getIncrementId())
            ->setCapture($capture ?? $this->isCapture((int)$order->getStoreId()))
            ->setTotalAmount(number_format((float)$this->sanitizer->amount($amount), 2, '.', ''))
            ->setCurrency($this->sanitizer->alpha((string)$order->getBaseCurrencyCode(), 3))
            ->setBillTo($this->getBillTo($order->getBillingAddress()))
            ->setShipTo($this->getShipTo($order))
            ->setLineItems($this->lineItemsBuilder->build($lineItems))
            ->setPaymentInstrumentId($paymentInstrumentId)
            ->setStoredCredentialUsed(true)
            ->setSolutionId($this->config->getSolutionId())
            ->setApplicationName($this->config->getClientName())
            ->setApplicationVersion($this->config->getClientVersion());

        // Legacy SOAP parity (Gateway::authorize on feature/php81): a subscription-generated rebill (MIT)
        // or any follow-on charge with an amount already paid must NOT re-run Decision Manager. UC analog
        // is processingInformation.enableDecisionManager=false. Done here (not just in the MIT branch) so a
        // CIT follow-on with amountPaid>0 is suppressed too, exactly as the SOAP condition did.
        if ($this->shouldDisableDecisionManager($payment, (int)$order->getStoreId())) {
            $request->setEnableDecisionManager(false);
        }

        // CIT vs MIT: a subscription-generated payment is merchant-initiated (a scheduled rebill).
        $isMit = (bool)$payment->getAdditionalInformation('is_subscription_generated');

        if ($isMit) {
            $request->setInitiatorType('merchant')
                ->setCommerceIndicator('recurring');

            $previousTransactionId = $this->getPriorTransactionId($payment);
            if ($previousTransactionId !== '') {
                $request->setPreviousTransactionId($previousTransactionId);
            } else {
                // A merchant-initiated rebill with no prior-transaction reference is a likely processor
                // decline / rate downgrade. Leave a breadcrumb (no PII) rather than failing here.
                $this->helper->log(
                    Config::CODE,
                    'Unified Checkout: building a merchant-initiated (MIT) stored-card auth with no'
                    . ' previousTransactionId reference; processor may decline or downgrade the rate.'
                );
            }
        } else {
            $request->setInitiatorType('customer');

            // Stored-card CVV re-entry (require_ccv): the checkout re-prompts for the security code on a
            // stored card and posts it as additional_data cc_cid, which TokenBase's assign-data observer
            // stores (digits-only) on the payment. Forward it as paymentInformation.card.securityCode when
            // present. CIT branch only by construction: an MIT rebill has no cardholder present to enter
            // one, so subscription charges stay securityCode-less.
            $securityCode = $this->stringOrNull($payment->getData('cc_cid'))
                ?? $this->stringOrNull($payment->getAdditionalInformation('cc_cid'));
            if ($securityCode !== null) {
                $request->setSecurityCode($securityCode);
            }

            // Decision Manager device-fingerprint parity: a CIT stored-card charge is cardholder-present,
            // so forward the device signal (the legacy SOAP Gateway sent deviceFingerprintID on every auth).
            // Deliberately NOT set on the MIT branch above — a subscription-generated rebill has no
            // cardholder device present to fingerprint. Set only when fingerprinting is enabled + reachable.
            $fingerprintSessionId = $this->getFingerprintSessionId($order);
            if ($fingerprintSessionId !== null) {
                $request->setFingerprintSessionId($fingerprintSessionId);
            }
        }

        return $request;
    }

    /**
     * Whether Decision Manager should be suppressed for this transaction (legacy SOAP parity).
     *
     * Mirrors the feature/php81 Gateway::authorize()/capture() condition exactly: a follow-on charge with
     * an amount already paid (amountPaid > 0) OR a subscription-generated rebill (MIT) must NOT re-run DM —
     * it was already screened on the initiating transaction, and an MIT has no cardholder present to screen.
     *
     * @param InfoInterface $payment
     * @return bool
     */
    protected function shouldSuppressDecisionManager(InfoInterface $payment): bool
    {
        /** @var Payment $payment */
        $amountPaid = (float)$payment->getAmountPaid();

        return $amountPaid > 0
            || (bool)$payment->getAdditionalInformation('is_subscription_generated');
    }

    /**
     * Whether processingInformation.enableDecisionManager=false must be sent for this charge.
     *
     * Forced off when the transaction is exempt (MIT / follow-on, legacy parity) or the merchant
     * disabled the `uc_decision_manager` toggle; otherwise left unset so the account profile governs.
     * The $0 add-card path uses `validate_card_storage` instead — see buildZeroDollarRequest().
     *
     * @param InfoInterface $payment
     * @param int|null $storeId
     * @return bool
     */
    protected function shouldDisableDecisionManager(InfoInterface $payment, ?int $storeId): bool
    {
        return $this->shouldSuppressDecisionManager($payment)
            || $this->config->isDecisionManagerEnabled($storeId) === false;
    }

    /**
     * Whether this charge may consume a persisted Payer Authentication result at all.
     *
     * Payer Auth is a CUSTOMER-INITIATED, browser-originated ceremony, so only those charges consult
     * the validator:
     *  - Payer Authentication disabled for the store short-circuits everything. The validator is a
     *    hard gate now (a refused record STAYS refused), so a record written before a merchant
     *    turned Payer Auth off must be inert rather than a permanent block on that cart.
     *  - A subscription-generated rebill is merchant-initiated (MIT): there is no cardholder to
     *    authenticate, no fresh record can exist, and consulting would let a stale record from the
     *    original checkout throw on an unattended rebill. Same signal as shouldSuppressDecisionManager().
     *  - Admin/MOTO order creation is exempt by design, as is any
     *    other non-frontend origin (cron, console). TokenBase's helper is the module's existing
     *    area-origin signal — frontend + REST webapi + GraphQL are "customer-facing", adminhtml and
     *    crontab are not — so no new dependency and no new definition of "admin" is introduced here.
     *
     * An unresolved area code falls through to consulting — fail-closed: it cannot block an admin
     * order (adminhtml resolves normally) or bypass a FAILED verdict, and with payer_auth_required
     * on it refuses a no-record placement.
     *
     * @param InfoInterface $payment
     * @return bool
     */
    protected function shouldConsumePayerAuth(InfoInterface $payment): bool
    {
        if ($this->config->isPayerAuthEnabled($this->getPayerAuthStoreId($payment)) === false) {
            return false;
        }

        if ((bool)$payment->getAdditionalInformation('is_subscription_generated')) {
            return false;
        }

        try {
            return (bool)$this->helper->getIsFrontend();
        } catch (Throwable $error) {
            return true;
        }
    }

    /**
     * Resolve the store scope the Payer Authentication config should be read at.
     *
     * Both money paths call $this->config->setStoreId() from the order before reaching here, so a
     * null return still lands on the right scope.
     *
     * @param InfoInterface $payment
     * @return int|null
     */
    protected function getPayerAuthStoreId(InfoInterface $payment): ?int
    {
        if (!$payment instanceof Payment) {
            return null;
        }

        $order = $payment->getOrder();

        if (!$order instanceof OrderInterface || $order->getStoreId() === null) {
            return null;
        }

        return (int)$order->getStoreId();
    }

    /**
     * Resolve the persisted Payer Authentication result for this charge and attach its pass-through.
     *
     * The amount and currency are read back off the REQUEST DTO, so the binding check compares the
     * exact strings the money call will carry — there is no second formatting path to drift from.
     *
     * Outcomes: Payer Auth disabled, MIT/admin origin, or no record => nothing attached, placement
     * proceeds unless the store requires Payer Auth (enforcePayerAuthRequired());
     * AUTHENTICATED/ATTEMPTED => per-network pass-through attached; UNAVAILABLE => nothing
     * attached (no liability shift exists to pass) and the outcome is logged; FAILED / obligated /
     * abandoned / stale => BindingValidator throws CommandException and the placement never happens.
     * A thrown block leaves the record in place ON PURPOSE, so re-submitting Place Order is refused
     * identically rather than sailing through unauthenticated.
     *
     * @param InfoInterface $payment
     * @param PaymentRequest|StoredCardRequest $request
     * @param string $binding Binding of the instrument being charged (jti, or Persistor::cardBinding()).
     * @param string $ccType Magento card type code of the instrument being charged.
     * @return array{verdict: \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict,
     *               ca: array<string, mixed>}|null
     * @throws CommandException On a failed, abandoned, or stale-but-valuable authentication record.
     */
    protected function attachPayerAuth(
        InfoInterface $payment,
        PaymentRequest|StoredCardRequest $request,
        string $binding,
        string $ccType
    ): ?array {
        if ($this->shouldConsumePayerAuth($payment) === false) {
            return null;
        }

        $payerAuth = $this->bindingValidator->resolve(
            $payment,
            (string)$request->getTotalAmount(),
            (string)$request->getCurrency(),
            $binding
        );

        if ($payerAuth === null) {
            $this->enforcePayerAuthRequired($payment, $ccType);

            return null;
        }

        if ($payerAuth['verdict']->hasLiabilityShift() === false) {
            // U/B/error shapes: place WITHOUT the shift, exactly as the legacy SOAP path did. No
            // values from the record are logged — it holds the CAVV.
            $this->helper->log(
                Config::CODE,
                'Payer Authentication: verdict=' . $payerAuth['verdict']->value
                . '; placing without liability shift.'
            );

            return $payerAuth;
        }

        $mapped = $this->passThroughMapper->map($payerAuth['ca'], $ccType);

        if ($mapped['consumerAuthenticationInformation'] !== []) {
            $request->setConsumerAuthenticationInformation($mapped['consumerAuthenticationInformation']);
        }

        if ($mapped['commerceIndicator'] !== null) {
            $request->setCommerceIndicator($mapped['commerceIndicator']);
        }

        return $payerAuth;
    }

    /**
     * Refuse the placement when the store demands Payer Authentication and none was consumed.
     *
     * Called only when BindingValidator resolved to null — the one outcome indistinguishable from
     * "3DS was never asked for". Every consumed verdict (including UNAVAILABLE) places, every
     * failed/obligated/abandoned shape has already thrown, and the caller has applied the
     * exemptions (Payer Auth off, MIT, non-frontend origin) via shouldConsumePayerAuth().
     *
     * @param InfoInterface $payment
     * @param string $ccType Magento card type code of the instrument being charged.
     * @return void
     * @throws CommandException When Payer Authentication is required but none was consumed.
     */
    protected function enforcePayerAuthRequired(InfoInterface $payment, string $ccType): void
    {
        $storeId = $this->getPayerAuthStoreId($payment);

        if ($this->config->isPayerAuthRequired($storeId) === false) {
            return;
        }

        // A type outside cardinal_card_types is never authenticated by the client, so it is not
        // blocked; an unknown type is not excluded (mirrors Management::isTypeExcluded()).
        if ($ccType !== '' && $this->config->isPayerAuthEnabledForType($ccType, $storeId) === false) {
            return;
        }

        $this->helper->log(
            Config::CODE,
            'Payer Authentication: required by configuration but no usable result was present; refusing.'
        );

        throw new CommandException(
            __(
                'Payer Authentication (3DS) is required for this payment. Please complete payer'
                . ' authentication before placing the order.'
            )
        );
    }

    /**
     * Finish the one-shot: surface the consumed result, then drop the record.
     *
     * Only ever reached on an APPROVED reply — interpretResponse() throws on decline/error — which is
     * exactly the intended one-shot timing: a declined or errored place leaves the record in place so
     * the customer can retry the same authenticated attempt within its TTL.
     *
     * Commit timing: OrderService::place() wraps neither $order->place() nor orderRepository->save()
     * in a DB transaction, so this clear COMMITS IMMEDIATELY. Accepted gap: if the order save then
     * fails after a successful charge, no record survives and the customer must re-authenticate.
     *
     * @param InfoInterface $payment
     * @param GatewayResponse $gatewayResponse
     * @param array{verdict: \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict,
     *              ca: array<string, mixed>}|null $payerAuth
     * @return void
     */
    protected function completePayerAuth(
        InfoInterface $payment,
        GatewayResponse $gatewayResponse,
        ?array $payerAuth
    ): void {
        if ($payerAuth === null) {
            return;
        }

        $this->surfaceConsumerAuthentication($gatewayResponse, $payerAuth['ca']);
        $this->payerAuthPersistor->clear($payment);
    }

    /**
     * Merge the consumed authentication result onto the response's consumer_authentication tree.
     *
     * Live probe: the /pts/v2/payments reply does NOT echo the authentication fields back (its
     * consumerAuthenticationInformation carries only `token`), so the persisted record is the real
     * source. The reply stays as the base and the record overrides it field by field, keeping
     * reply-only values while the authoritative auth values win.
     *
     * @param GatewayResponse $gatewayResponse
     * @param array<string, mixed> $consumerAuthenticationInformation
     * @return void
     */
    protected function surfaceConsumerAuthentication(
        GatewayResponse $gatewayResponse,
        array $consumerAuthenticationInformation
    ): void {
        $fromRecord = $this->filterConsumerAuthenticationFields($consumerAuthenticationInformation);

        if ($fromRecord === []) {
            return;
        }

        $fromReply = $gatewayResponse->getData('consumer_authentication');

        $gatewayResponse->setData(
            'consumer_authentication',
            array_merge(is_array($fromReply) ? $fromReply : [], $fromRecord)
        );
    }

    /**
     * Read the transient-token `jti` binding for the card being charged, or '' when unreadable.
     *
     * Fail-closed: an unreadable binding never matches a persisted record, so a record from a
     * DIFFERENT card entry is refused rather than honored.
     *
     * @param InfoInterface $payment
     * @return string
     */
    protected function getTransientTokenBinding(InfoInterface $payment): string
    {
        $transientToken = $this->getTransientToken($payment);

        if ($transientToken === null) {
            return '';
        }

        return $this->transientTokenReader->readJti($transientToken) ?? '';
    }

    /**
     * Read the card network of the transient token being charged, or '' when unreadable.
     *
     * @param InfoInterface $payment
     * @return string
     */
    protected function getTransientTokenCardType(InfoInterface $payment): string
    {
        $transientToken = $this->getTransientToken($payment);

        if ($transientToken === null) {
            return '';
        }

        return $this->stringOrNull($this->transientTokenReader->read($transientToken)['cc_type'] ?? null) ?? '';
    }

    /**
     * Tokenize a card from its transient token WITHOUT a purchase (zero-dollar add-card).
     *
     * This is the REST analog of the SOAP paySubscriptionCreate tokenize-without-charge used by the
     * admin "add card" and customer "save card" flows. We POST /pts/v2/payments with a $0 amount and
     * capture=false (authorize-only) plus actionList:[TOKEN_CREATE] to mint the TMS vault ids, then let
     * interpretResponse() expose token_information / card_information for CardBuilder to map onto a new
     * CardInterface. The caller supplies the transient token on the payment; currency is passed in. A
     * billing address (billTo) is sent for AVS when one is reachable from the payment (via its order's
     * billing address) — many processors require it for a $0 auth — and is omitted when unavailable.
     *
     * VERIFY: against the live API once TMS is provisioned (UC-API-REFERENCE.md §4). The documented
     * shape is a $0 authorize-only (totalAmount "0.00", capture=false). If CyberSource rejects a $0
     * authorization for the configured processor, the fallback is a small auth + immediate void (A3
     * owns the void). Built to the documented $0 shape here; confirm before enabling in production.
     *
     * @param InfoInterface $payment
     * @param string $currencyCode
     * @param int|null $storeId
     * @param CustomerAddressInterface|null $billingAddress Card billing address for AVS; the paymentinfo
     *        flows carry no order, so the payment-derived fallback inside buildZeroDollarRequest() finds
     *        nothing without it and CyberSource rejects the $0 auth with MISSING_FIELD billTo.*.
     * @param string|null $email Customer email for the billTo (customer addresses carry none).
     * @return GatewayResponse
     * @throws CommandException On a declined transaction.
     * @throws RuntimeException On an error/invalid response, or a missing transient token.
     * @throws Throwable
     */
    public function tokenizeCard(
        InfoInterface $payment,
        string $currencyCode,
        ?int $storeId = null,
        ?CustomerAddressInterface $billingAddress = null,
        ?string $email = null
    ): GatewayResponse {
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request  = $this->buildZeroDollarRequest($payment, $currencyCode, $billingAddress, $email);
        $response = $this->rest->post(self::PAYMENTS_PATH, $request->toArray());

        $gatewayResponse = $this->interpretResponse($response, $payment, $this->requestsTokenCreate($request));
        $this->seedCardInformationFromTransientToken($payment, $gatewayResponse);

        return $gatewayResponse;
    }

    /**
     * Assemble a $0 TOKEN_CREATE /pts/v2/payments request DTO for the zero-dollar add-card path.
     *
     * No order context: capture is forced false (authorize-only) and the amount is "0.00". The
     * transient token is still required; an empty token is a hard error (same as the purchase path).
     *
     * @param InfoInterface $payment
     * @param string $currencyCode
     * @param CustomerAddressInterface|null $billingAddress
     * @param string|null $email
     * @return PaymentRequest
     * @throws RuntimeException When no transient token is present on the payment.
     */
    public function buildZeroDollarRequest(
        InfoInterface $payment,
        string $currencyCode,
        ?CustomerAddressInterface $billingAddress = null,
        ?string $email = null
    ): PaymentRequest {
        $transientToken = $this->getTransientToken($payment);
        if ($transientToken === null || $transientToken === '') {
            throw new RuntimeException(
                __('Missing Unified Checkout payment token. Please re-enter your payment information.')
            );
        }

        /** @var PaymentRequest $request */
        $request = $this->requestFactory->create();

        $request->setTransientTokenJwt($transientToken)
            ->setActionList([self::ACTION_TOKEN_CREATE])
            ->setActionTokenTypes(self::ACTION_TOKEN_TYPES)
            ->setCapture(false)
            ->setTotalAmount(self::ZERO_DOLLAR_AMOUNT)
            ->setCurrency($this->sanitizer->alpha($currencyCode, 3))
            ->setSolutionId($this->config->getSolutionId())
            ->setApplicationName($this->config->getClientName())
            ->setApplicationVersion($this->config->getClientVersion());

        // 3.x parity (validate_card_storage): card storage is not fraud-screened unless the merchant
        // opts in; opting in leaves the flag unset so the account profile governs.
        if ($this->config->isCardStorageValidationEnabled() === false) {
            $request->setEnableDecisionManager(false);
        }

        // A $0 add-card auth REQUIRES billTo (CyberSource rejects it with MISSING_FIELD
        // billTo.administrativeArea otherwise — verified live 2026-07-24). Prefer the card's own
        // billing address when the caller supplies one (the paymentinfo add/edit flows have no order
        // to derive one from); otherwise fall back to the payment's order billing address.
        $billTo = $this->getBillToFromCustomerAddress($billingAddress, $email);
        if ($billTo === []) {
            $billTo = $this->getBillTo($this->getPaymentBillingAddress($payment));
        }

        if ($billTo !== []) {
            $request->setBillTo($billTo);
        }

        return $request;
    }

    /**
     * Map a customer (card) billing address to the /pts/v2/payments billTo field tree.
     *
     * Customer-address analog of getBillTo(): same field mapping, but reads the customer address
     * accessors (region object rather than flat regionCode) and takes the email separately, since
     * customer addresses carry none. Returns an empty array when no address is supplied.
     *
     * @param CustomerAddressInterface|null $address
     * @param string|null $email
     * @return array<string, string>
     */
    protected function getBillToFromCustomerAddress(?CustomerAddressInterface $address, ?string $email): array
    {
        if ($address === null) {
            return [];
        }

        $street   = (array)$address->getStreet();
        $address1 = (string)($street[0] ?? '');

        try {
            $cleanEmail = ($email !== null && $email !== '') ? $this->sanitizer->email($email) : null;
        } catch (Throwable) {
            $cleanEmail = null;
        }

        return array_filter([
            'firstName' => $this->sanitizer->alphanumericPunc($address->getFirstname(), 60),
            'lastName' => $this->sanitizer->alphanumericPunc($address->getLastname(), 60),
            'address1' => $this->sanitizer->alphanumericPunc($address1, 60),
            'address2' => $this->sanitizer->alphanumericPunc($street[1] ?? null, 60),
            'locality' => $this->sanitizer->alphanumericPunc($address->getCity(), 50),
            'administrativeArea' => $this->sanitizer->alphanumericPunc(
                strtoupper((string)$address->getRegion()?->getRegionCode()),
                20
            ),
            'postalCode' => $this->sanitizer->postcode($address->getPostcode(), (string)$address->getCountryId()),
            'country' => $this->sanitizer->alpha(strtoupper((string)$address->getCountryId()), 2),
            'email' => $cleanEmail,
            'phoneNumber' => $this->sanitizer->phone($address->getTelephone(), 15),
        ], static fn($value): bool => $value !== null && $value !== '');
    }

    /**
     * Best-effort billing address for the add-card payment (no order context guaranteed).
     *
     * The zero-dollar payment may be a quote payment or a freshly built order payment; either way the
     * billing address, when present, hangs off the payment's order. Returns null when none is reachable.
     *
     * @param InfoInterface $payment
     * @return OrderAddressInterface|null
     */
    protected function getPaymentBillingAddress(InfoInterface $payment): ?OrderAddressInterface
    {
        if (!$payment instanceof Payment) {
            return null;
        }

        $order = $payment->getOrder();
        if (!$order instanceof OrderInterface) {
            return null;
        }

        $billingAddress = $order->getBillingAddress();

        return $billingAddress instanceof OrderAddressInterface ? $billingAddress : null;
    }

    /**
     * Assemble the /pts/v2/payments request DTO from the order/payment and the re-validated amount.
     *
     * The capture flag is the caller's OPERATION intent when given (never anything client-supplied —
     * Gateway passes false for authorize(), true for a bundled capture). When null it is derived from
     * the SERVER-SIDE payment_action: authorize_capture -> capture=true (sale); otherwise capture=false.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param bool|null $capture Operation intent: true=sale, false=auth-only; null=derive from config.
     * @param array<int|string, mixed> $lineItems Sales items to send as orderInformation.lineItems.
     * @return PaymentRequest
     * @throws RuntimeException When no transient token is present on the payment.
     */
    public function buildRequest(
        InfoInterface $payment,
        float $amount,
        ?bool $capture = null,
        array $lineItems = []
    ): PaymentRequest {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $transientToken = $this->getTransientToken($payment);
        if ($transientToken === null || $transientToken === '') {
            throw new RuntimeException(
                __('Missing Unified Checkout payment token. Please re-enter your payment information.')
            );
        }

        /** @var PaymentRequest $request */
        $request = $this->requestFactory->create();

        $request->setTransientTokenJwt($transientToken)
            ->setClientReferenceCode((string)$order->getIncrementId())
            ->setActionList([self::ACTION_TOKEN_CREATE])
            ->setActionTokenTypes(self::ACTION_TOKEN_TYPES)
            ->setCapture($capture ?? $this->isCapture((int)$order->getStoreId()))
            ->setTotalAmount(number_format((float)$this->sanitizer->amount($amount), 2, '.', ''))
            ->setCurrency($this->sanitizer->alpha((string)$order->getBaseCurrencyCode(), 3))
            ->setBillTo($this->getBillTo($order->getBillingAddress()))
            ->setShipTo($this->getShipTo($order))
            ->setLineItems($this->lineItemsBuilder->build($lineItems))
            ->setSolutionId($this->config->getSolutionId())
            ->setApplicationName($this->config->getClientName())
            ->setApplicationVersion($this->config->getClientVersion());

        // Legacy SOAP parity: suppress Decision Manager on a follow-on / subscription-generated charge so
        // DM is not re-run on a transaction it already screened (or an MIT rebill the cardholder isn't on).
        if ($this->shouldDisableDecisionManager($payment, (int)$order->getStoreId())) {
            $request->setEnableDecisionManager(false);
        }

        // Decision Manager device-fingerprint parity (legacy SOAP Gateway::authorize sent
        // deviceFingerprintID keyed on the quote session): forward the online-metrix session id so DM can
        // correlate the client-side device signal. Set only when fingerprinting is enabled + reachable.
        $fingerprintSessionId = $this->getFingerprintSessionId($order);
        if ($fingerprintSessionId !== null) {
            $request->setFingerprintSessionId($fingerprintSessionId);
        }

        return $request;
    }

    /**
     * Resolve the Decision Manager device-fingerprint session id for this order, or null when disabled.
     *
     * Legacy SOAP parity, translated to REST: the SOAP Gateway sent the RAW quote id (apiScope=true) as
     * deviceFingerprintID because the SOAP service prepended the merchant id server-side to match the
     * merchant-prefixed session id the online-metrix tag collected client-side. The REST API does NOT
     * prepend — deviceInformation.fingerprintSessionId must be the SAME session id the profiling tag used
     * (per CyberSource DM guidance, "use the same session ID for both the device fingerprint provider and
     * the payment provider request"). The frontend tag is loaded from Config::getFingerprintUrl(quoteId),
     * whose session_id is merchant_id + quoteId, so we request the same default-scope (merchant-prefixed)
     * value here — never the bare quote id, which matches no profiled session. Config returns null when
     * fingerprinting is disabled/unconfigured, so callers emit deviceInformation only when a non-empty
     * value comes back and a quote session id is reachable.
     *
     * @param OrderInterface $order
     * @return string|null
     */
    protected function getFingerprintSessionId(OrderInterface $order): ?string
    {
        $quoteId = $order->getQuoteId();
        if ($quoteId === null || (string)$quoteId === '') {
            return null;
        }

        $sessionId = $this->config->getFingerprintSessionId((string)$quoteId);

        return ($sessionId !== null && $sessionId !== '') ? $sessionId : null;
    }

    /**
     * Translate the /pts/v2/payments JSON reply into a gateway Response object.
     *
     * Mirrors the SOAP-era Gateway::interpretTransaction(): sets transaction_id / response_code /
     * response_reason_code / response_reason_text / auth_code, plus the ccAuthReply.* keys that
     * Method::storeTransactionStatuses() reads for AVS/CVV/approval. Throws CommandException on a
     * decline and RuntimeException on an error, so Method handles it identically to the SOAP path.
     *
     * $tokenCreateRequested tells the token-missing logic what the REQUEST asked for. A reply with no
     * tokenInformation is only anomalous when actionList:[TOKEN_CREATE] was sent (new-card auth, $0
     * add-card); the stored-card/MIT path never asks for a token, so its token-less reply is normal and
     * must NOT be flagged. Absent this, a stored-card charge flagged its own vaulted card unusable.
     *
     * @param array<string, mixed> $response
     * @param InfoInterface|null $payment
     * @param bool $tokenCreateRequested Whether the request carried actionList:[TOKEN_CREATE].
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     */
    public function interpretResponse(
        array $response,
        ?InfoInterface $payment = null,
        bool $tokenCreateRequested = true
    ): GatewayResponse {
        $status       = (string)($response['status'] ?? '');
        $approvalCode = (string)($response['processorInformation']['approvalCode'] ?? '');
        $responseCode = (string)($response['processorInformation']['responseCode'] ?? '');
        $errorReason  = (string)($response['errorInformation']['reason'] ?? '');
        $errorMessage = (string)($response['errorInformation']['message'] ?? '');
        $authorizedAmount = (string)($response['orderInformation']['amountDetails']['authorizedAmount'] ?? '');

        $statusApproved = in_array($status, self::APPROVED_STATUSES, true);

        // Decision Manager / risk REJECT (Iter 4, D6 parity): the processor can APPROVE the auth while DM
        // declines the order — status AUTHORIZED_RISK_DECLINED / REJECTED with reason
        // DECISION_PROFILE_REJECT. Unlike the token-forbidden case (which we let stand token-less), a DM
        // reject MUST fail the transaction so the order is not placed. We override the status-based
        // approval here, mirroring the legacy SOAP REJECT decision -> CommandException.
        // VERIFY (live, AVS/CVV soft-decline parity): the legacy SOAP path carved AVS/CVV soft declines
        // (reasonCodes 200/230) OUT of the REJECT decline — it kept the auth and accepted with the fraud flag.
        // In REST, AVS/CVV results surface on an otherwise AUTHORIZED reply via
        // processorInformation.avs.code / cardVerification.resultCode, NOT as a RISK_DECLINED status, so they are
        // expected to pass through as approved here. Confirm on a boarded MID with an AVS/CVV-mismatch test card
        // that such a reply is not surfaced as AUTHORIZED_RISK_DECLINED/DECISION_PROFILE_REJECT before relying on
        // this; if it is, restore the soft-decline carve-out (approve + setIsFraud(true)).
        $isRiskDeclined = in_array($status, self::RISK_DECLINED_STATUSES, true)
            || $errorReason === self::REASON_DECISION_PROFILE_REJECT;

        // Token-forbidden carve-out (§4): a TOKEN_CREATE failure flips status to DECLINED + PROCESSOR_ERROR
        // with the auth approved. Requires processor-agnostic evidence an auth was issued; fails closed.
        // VERIFY (live): the reply shape is unrecorded — the spike MID was provisioned 2026-07-22.
        $tokenForbiddenApproval = $tokenCreateRequested
            && $status === 'DECLINED'
            && $errorReason === self::REASON_PROCESSOR_ERROR
            && empty($response['tokenInformation'])
            && ($approvalCode !== '' || $authorizedAmount !== '');

        $isApproved    = !$isRiskDeclined && ($statusApproved || $tokenForbiddenApproval);
        $isUnderReview = $this->isUnderReview($status);

        // Flatten the raw reply so Method::storeTransactionStatuses() can read ccAuthReply.* keys, and
        // expose the same processorInformation tree for downstream (A2) consumption.
        $data = $response;

        $data['transaction_id']       = $response['id'] ?? null;
        $data['response_code']        = $responseCode !== '' ? $responseCode : $status;
        $data['response_reason_code'] = $responseCode !== '' ? $responseCode : $status;
        $data['response_reason_text'] = $errorMessage !== '' ? $errorMessage : $status;
        $data['auth_code']            = $approvalCode;

        // Map the processorInformation tree to the SOAP-style ccAuthReply.* keys the module reads.
        // Method::storeTransactionStatuses() reads these via $response->getData('ccAuthReply.avsCode'),
        // i.e. literal dotted keys (not nested), so store them flat to match that contract.
        $avsCode = $response['processorInformation']['avs']['code'] ?? null;
        $cvCode  = $response['processorInformation']['cardVerification']['resultCode'] ?? null;

        if ($approvalCode !== '') {
            $data['ccAuthReply.authorizationCode'] = $approvalCode;
        }
        if ($avsCode !== null && $avsCode !== '') {
            $data['ccAuthReply.avsCode'] = $avsCode;
        }
        if ($cvCode !== null && $cvCode !== '') {
            $data['ccAuthReply.cvCode'] = $cvCode;
        }

        $this->extractTokenInformation($response, $data, $isApproved, $tokenCreateRequested);
        $this->extractCardMetadata($response, $data);
        $this->extractConsumerAuthentication($response, $data);

        /** @var GatewayResponse $gatewayResponse */
        $gatewayResponse = $this->responseFactory->create(['data' => $data]);

        // A successful auth that CyberSource held for Decision Manager review is still approved.
        $gatewayResponse->setIsFraud($isUnderReview);

        if ($isApproved) {
            $gatewayResponse->setIsError(false);

            // TOKEN_CREATE may fail while the auth approves (carve-out above, or an approved status
            // carrying PROCESSOR_ERROR). Either way the auth stands: proceed token-less and flag it.
            if ($tokenCreateRequested
                && $errorReason === self::REASON_PROCESSOR_ERROR
                && empty($data['token_information'])
            ) {
                $this->helper->log(
                    Config::CODE,
                    sprintf(
                        'Unified Checkout auth approved (responseCode=%s, status=%s) but TOKEN_CREATE failed:'
                        . ' %s. Proceeding token-less.',
                        $responseCode,
                        $status,
                        $errorMessage
                    )
                );
                $gatewayResponse->setData('uc_token_missing', true);
            }

            // TODO (A3): PARTIAL_AUTHORIZED is surfaced but not reconciled. The processor approved a
            // smaller amount than requested; downstream capture/order totals must be adjusted to the
            // authorizedAmount rather than treating this as a full approval. Surface it here so A3 can act.
            if ($status === 'PARTIAL_AUTHORIZED') {
                $authorizedAmount = $response['orderInformation']['amountDetails']['authorizedAmount'] ?? null;
                $gatewayResponse->setData('uc_partial_authorized', true);
                if ($authorizedAmount !== null && $authorizedAmount !== '') {
                    $gatewayResponse->setData('uc_authorized_amount', $authorizedAmount);
                }
            }

            return $gatewayResponse;
        }

        // The exception code below is the raw processor responseCode when numeric — diagnostics only.
        // Nothing on this path keys on it; the SOAP retry codes (102/242/241) are consumed only by FollowOn.
        return $this->throwForFailure(
            $gatewayResponse,
            $status,
            $responseCode,
            $errorMessage,
            $errorReason,
            $payment
        );
    }

    /**
     * Whether the configured payment_action is a sale (auth+capture) rather than authorize-only.
     *
     * Sourced strictly from server-side config — never from client input. This is only the FALLBACK
     * for callers that pass no explicit operation intent; Gateway operations pass theirs explicitly.
     *
     * @param int $storeId
     * @return bool
     */
    protected function isCapture(int $storeId): bool
    {
        return $this->config->getUcCompleteMandateType($storeId) === 'CAPTURE';
    }

    /**
     * Whether the given status is a Decision Manager / pending-review hold (no token returned inline).
     *
     * @param string $status
     * @return bool
     */
    protected function isUnderReview(string $status): bool
    {
        return str_contains($status, 'REVIEW') || str_contains($status, 'PENDING');
    }

    /**
     * Read the transient-token JWT from the payment's additional information.
     *
     * @param InfoInterface $payment
     * @return string|null
     */
    protected function getTransientToken(InfoInterface $payment): ?string
    {
        $token = $payment->getAdditionalInformation('transient_token');

        return $token !== null && $token !== '' ? (string)$token : null;
    }

    /**
     * Whether the given request actually asks CyberSource to mint TMS vault ids.
     *
     * Derived from the request we are about to send rather than hardcoded per call site, so that if
     * actionList ever becomes conditional (e.g. tokenize only when the shopper opts to save the card),
     * the token-missing logic follows automatically instead of silently drifting.
     *
     * @param PaymentRequest $request
     * @return bool
     */
    protected function requestsTokenCreate(PaymentRequest $request): bool
    {
        return in_array(self::ACTION_TOKEN_CREATE, $request->getActionList(), true);
    }

    /**
     * Defensively extract the TMS ids from tokenInformation into the result, when present.
     *
     * On AUTHORIZED_PENDING_REVIEW (or any approval without tokenInformation) the ids are absent; we
     * record that no token was returned so A2 can decide whether to defer/reconcile tokenization,
     * rather than crashing on missing keys.
     *
     * $tokenCreateRequested is what makes "missing" meaningful: only a reply to a TOKEN_CREATE request
     * can be missing a token. When no token was asked for (stored-card / MIT), both keys are left unset
     * so Method::applyUnifiedCheckoutToken() leaves the already-vaulted card alone.
     *
     * @param array<string, mixed> $response
     * @param array<string, mixed> $data
     * @param bool $isApproved
     * @param bool $tokenCreateRequested Whether the request carried actionList:[TOKEN_CREATE].
     * @return void
     */
    protected function extractTokenInformation(
        array $response,
        array &$data,
        bool $isApproved,
        bool $tokenCreateRequested = true
    ): void {
        $tokenInformation = $response['tokenInformation'] ?? null;

        $paymentInstrumentId    = $tokenInformation['paymentInstrument']['id'] ?? null;
        $instrumentIdentifierId = $tokenInformation['instrumentIdentifier']['id'] ?? null;

        $tokens = array_filter([
            'paymentInstrument' => $paymentInstrumentId,
            'instrumentIdentifier' => $instrumentIdentifierId,
        ], static fn($value): bool => $value !== null && $value !== '');

        if (!empty($tokens)) {
            $data['token_information'] = $tokens;
            $data['uc_token_missing']  = false;

            return;
        }

        // No TOKEN_CREATE was requested: this is the stored-card / MIT path, whose reply carries no
        // tokenInformation BY DESIGN (the card is already vaulted; StoredCardRequest sends no actionList).
        // "Missing" is only meaningful relative to what was asked for — flagging here would mark the card's
        // own good token as missing, and Method::applyUnifiedCheckoutToken() would then stamp
        // uc_token_missing='1' onto the vaulted card, so Gateway::buildStoredCardAuth() refuses the NEXT
        // charge ("This saved card is no longer usable"). That killed every subscription rebill after the
        // first. Leave both keys unset so Method's guard leaves the already-vaulted card untouched.
        if ($tokenCreateRequested === false) {
            return;
        }

        // No TMS ids returned — valid on DM review, or when TMS isn't provisioned. Flag, don't fail.
        $data['uc_token_missing'] = true;

        if ($isApproved) {
            $this->helper->log(
                Config::CODE,
                sprintf(
                    'Unified Checkout auth approved (%s) with no tokenInformation; no TMS ids returned.',
                    (string)($response['status'] ?? '')
                )
            );
        }
    }

    /**
     * Defensively extract card metadata (last4/type/bin/exp) from paymentInformation.card, when present.
     *
     * @param array<string, mixed> $response
     * @param array<string, mixed> $data
     * @return void
     */
    protected function extractCardMetadata(array $response, array &$data): void
    {
        $paymentInformation = $response['paymentInformation'] ?? null;
        if (empty($paymentInformation) || !is_array($paymentInformation)) {
            return;
        }

        $card = $paymentInformation['card'] ?? [];
        if (!is_array($card)) {
            $card = [];
        }

        $type = isset($card['type']) ? $this->cardType->getType((string)$card['type']) : null;

        // bin can live at card.bin, card.prefix (the 6-digit lead), or paymentInformation.bin.
        $bin = $card['bin'] ?? $card['prefix'] ?? $paymentInformation['bin'] ?? null;

        $data['card_information'] = array_filter([
            'cc_type' => $type,
            'cc_last4' => $card['suffix'] ?? null,
            'cc_bin' => $bin,
            'cc_exp_month' => $card['expirationMonth'] ?? null,
            'cc_exp_year' => $card['expirationYear'] ?? null,
        ], static fn($value): bool => $value !== null && $value !== '');
    }

    /**
     * Seed card_information from the transient-token JWT, keeping gateway-reply fields where present.
     *
     * The /pts/v2/payments reply for a transient-token auth returns ONLY paymentInformation.card.type
     * (no suffix/bin/expiry), so extractCardMetadata() alone yields just cc_type and the vault card /
     * order payment lose their last4/bin/expiration. The token payload carries the missing display
     * metadata; decode it (TransientTokenReader — unverified by design, see its docblock) as the BASE
     * and let every field the reply DID return override it: the reply is authoritative when present.
     *
     * Runs only on the new-card paths (place() / tokenizeCard()) where a transient token exists; the
     * stored-card path has no token and its card already carries metadata. Decoding is best-effort —
     * a wallet or malformed token contributes nothing and the reply-derived data stands untouched.
     *
     * DEPLOY NOTE: Response is consumed through a generated Proxy (etc/di.xml, Card's lazy ucResponse
     * argument) — any public-signature change here (including the constructor) requires setup:di:compile
     * on deployment. Prefer adding behavior via protected helpers like this one.
     *
     * @param InfoInterface $payment
     * @param GatewayResponse $gatewayResponse
     * @return void
     */
    protected function seedCardInformationFromTransientToken(
        InfoInterface $payment,
        GatewayResponse $gatewayResponse
    ): void {
        $transientToken = $this->getTransientToken($payment);
        if ($transientToken === null) {
            return;
        }

        $decoded = $this->transientTokenReader->read($transientToken);
        if ($decoded === []) {
            return;
        }

        $replyCardInformation = $gatewayResponse->getData('card_information');
        if (!is_array($replyCardInformation)) {
            $replyCardInformation = [];
        }

        // Token-decoded values as the base; reply fields override wherever the gateway returned one.
        $gatewayResponse->setData('card_information', array_merge($decoded, $replyCardInformation));
    }

    /**
     * Surface the 3DS authentication-result / liability-shift fields from consumerAuthenticationInformation.
     *
     * D6 parity: with 3DS folded into UC completeMandate, the authenticated result rides on the transient
     * token and the /pts/v2/payments reply returns consumerAuthenticationInformation. We copy the known
     * authentication-result fields (eci, cavv, paresStatus, xid, veresEnrolled, specificationVersion, …)
     * into a structured consumer_authentication tree so they persist on the transaction record (TokenBase
     * stores the whole response data tree as transaction additional info). These are the liability-shift
     * indicators (eci/cavv) the merchant relies on; we surface them rather than letting them be dropped.
     *
     * NOTE: interpretResponse() copies the whole gateway reply onto the response data, so the raw
     * consumerAuthenticationInformation tree also rides along verbatim on the transaction record. The
     * allowlist here is a stable read contract for downstream consumers, NOT a filtering/redaction boundary.
     *
     * VERIFY (live-UNVERIFIED — no boarded 3DS test card on the sandbox MID): the exact field set returned
     * for a UC-authenticated transaction is SDK/reference-derived (PtsV2PaymentsPost201Response
     * ConsumerAuthenticationInformation). Confirm against a live 3DS challenge + frictionless run that the
     * liability-shift fields (eci/eciRaw + cavv, or ucafAuthenticationData for Mastercard) are present and
     * correctly populated before treating UC 3DS as full Cardinal/Songbird parity (the Iter 6 deletion gate).
     *
     * @param array<string, mixed> $response
     * @param array<string, mixed> $data
     * @return void
     */
    protected function extractConsumerAuthentication(array $response, array &$data): void
    {
        $authInformation = $response['consumerAuthenticationInformation'] ?? null;
        if (empty($authInformation) || !is_array($authInformation)) {
            return;
        }

        $authentication = $this->filterConsumerAuthenticationFields($authInformation);

        if ($authentication !== []) {
            $data['consumer_authentication'] = $authentication;
        }
    }

    /**
     * Reduce a consumerAuthenticationInformation tree to the surfaced whitelist, scalars only.
     *
     * Shared by the reply-sourced path (extractConsumerAuthentication) and the record-sourced path
     * (surfaceConsumerAuthentication) so both emit exactly the same key contract.
     *
     * @param array<string, mixed> $consumerAuthenticationInformation
     * @return array<string, scalar>
     */
    protected function filterConsumerAuthenticationFields(array $consumerAuthenticationInformation): array
    {
        $authentication = [];

        foreach (self::CONSUMER_AUTHENTICATION_FIELDS as $field) {
            $value = $consumerAuthenticationInformation[$field] ?? null;
            // Scalar guard: a future schema change to an array/object value must not land a non-scalar
            // on the persisted record.
            if ($value !== null && $value !== '' && is_scalar($value)) {
                $authentication[$field] = $value;
            }
        }

        return $authentication;
    }

    /**
     * Build the failure message, log it, and throw the matching exception type.
     *
     * Mirrors the SOAP-era Gateway::interpretTransaction(): a declined transaction throws CommandException (so
     * Method's recapture/decline handling is identical), everything else throws RuntimeException. A
     * Decision Manager REJECT (status AUTHORIZED_RISK_DECLINED / REJECTED, or reason
     * DECISION_PROFILE_REJECT) is treated as a decline too — the legacy SOAP REJECT decision did the same,
     * EXCEPT for the AVS/CVV soft-decline carve-out (reasonCodes 200/230), which is live-UNVERIFIED on REST
     * (see VERIFY note at interpretResponse()'s $isRiskDeclined). Do not claim full legacy-REJECT parity until
     * that live check is recorded.
     *
     * @param GatewayResponse $response
     * @param string $status
     * @param string $responseCode
     * @param string $errorMessage
     * @param string $errorReason
     * @param InfoInterface|null $payment
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     */
    protected function throwForFailure(
        GatewayResponse $response,
        string $status,
        string $responseCode,
        string $errorMessage,
        string $errorReason,
        ?InfoInterface $payment
    ): GatewayResponse {
        $response->setIsError(true);

        $reason  = $errorMessage !== '' ? $errorMessage : $status;
        $message = __('Transaction Failed: %1', __($reason));

        if ($payment !== null) {
            $this->helper->log(
                Config::CODE,
                sprintf('%s (%s)', (string)$message, $status)
            );
        }

        $code = ctype_digit($responseCode) ? (int)$responseCode : 0;

        $isRiskDeclined = in_array($status, self::RISK_DECLINED_STATUSES, true)
            || $errorReason === self::REASON_DECISION_PROFILE_REJECT;

        // A DM reject must never carry the approval code (responseCode 100 with a rejected order); force
        // the exception code to 0, which matches nothing in the retry code space.
        if ($isRiskDeclined) {
            $code = 0;
        }

        $isDecline = $status === 'DECLINED' || $isRiskDeclined;

        if ($isDecline) {
            throw new CommandException($message, null, $code);
        }

        throw new RuntimeException($message, null, $code);
    }

    /**
     * Map a billing address to the /pts/v2/payments billTo field tree.
     *
     * Returns an empty array when no usable address is supplied, so the caller can omit billTo.
     *
     * @param OrderAddressInterface|null $billingAddress
     * @return array<string, string|null>
     */
    protected function getBillTo(?OrderAddressInterface $billingAddress): array
    {
        if (!$billingAddress instanceof OrderAddressInterface) {
            return [];
        }

        $street   = $billingAddress->getStreet();
        $address1 = (string)($street[0] ?? '');

        return array_filter([
            'firstName' => $this->sanitizer->alphanumericPunc($billingAddress->getFirstname(), 60),
            'lastName' => $this->sanitizer->alphanumericPunc($billingAddress->getLastname(), 60),
            'address1' => $this->sanitizer->alphanumericPunc($address1, 60),
            'address2' => $this->sanitizer->alphanumericPunc($street[1] ?? null, 60),
            'locality' => $this->sanitizer->alphanumericPunc($billingAddress->getCity(), 50),
            'administrativeArea' => $this->sanitizer->alphanumericPunc(
                strtoupper((string)$billingAddress->getRegionCode()),
                20
            ),
            'postalCode' => $this->sanitizer->postcode(
                $billingAddress->getPostcode(),
                (string)$billingAddress->getCountryId()
            ),
            'country' => $this->sanitizer->alpha(strtoupper((string)$billingAddress->getCountryId()), 2),
            'email' => $this->getEmail($billingAddress),
            'phoneNumber' => $this->sanitizer->phone($billingAddress->getTelephone(), 15),
        ], static fn($value): bool => $value !== null && $value !== '');
    }

    /**
     * Map the order shipping address to the /pts/v2/payments shipTo field tree.
     *
     * SOAP parity (ObjectBuilder::getOrderShipTo() + the Gateway's is-virtual gate): a virtual order
     * has no shipping address to send, so the tree is empty and orderInformation.shipTo is omitted.
     * Same field mapping as getBillTo() minus email — the SOAP shipTo never carried one.
     *
     * @param OrderInterface $order
     * @return array<string, string>
     */
    protected function getShipTo(OrderInterface $order): array
    {
        if ((bool)$order->getIsVirtual() === true || !$order instanceof Order) {
            return [];
        }

        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress instanceof OrderAddressInterface) {
            return [];
        }

        $street   = $shippingAddress->getStreet();
        $address1 = (string)($street[0] ?? '');

        return array_filter([
            'firstName' => $this->sanitizer->alphanumericPunc($shippingAddress->getFirstname(), 60),
            'lastName' => $this->sanitizer->alphanumericPunc($shippingAddress->getLastname(), 60),
            'address1' => $this->sanitizer->alphanumericPunc($address1, 60),
            'address2' => $this->sanitizer->alphanumericPunc($street[1] ?? null, 60),
            'locality' => $this->sanitizer->alphanumericPunc($shippingAddress->getCity(), 50),
            'administrativeArea' => $this->sanitizer->alphanumericPunc(
                strtoupper((string)$shippingAddress->getRegionCode()),
                20
            ),
            'postalCode' => $this->sanitizer->postcode(
                $shippingAddress->getPostcode(),
                (string)$shippingAddress->getCountryId()
            ),
            'country' => $this->sanitizer->alpha(strtoupper((string)$shippingAddress->getCountryId()), 2),
            'phoneNumber' => $this->sanitizer->phone($shippingAddress->getTelephone(), 15),
        ], static fn($value): bool => $value !== null && $value !== '');
    }

    /**
     * Sanitize the billing email, tolerating an invalid/missing value (Sanitizer::email() may throw).
     *
     * @param OrderAddressInterface $billingAddress
     * @return string|null
     */
    protected function getEmail(OrderAddressInterface $billingAddress): ?string
    {
        $email = $billingAddress->getEmail();
        if ($email === null || $email === '') {
            return null;
        }

        try {
            return $this->sanitizer->email((string)$email);
        } catch (Throwable) {
            return null;
        }
    }
}
