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

use Magento\Framework\Exception\RuntimeException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
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
 *     approves. CRITICALLY, the spike confirmed CyberSource reports this as top-level status=DECLINED
 *     with errorInformation.reason=PROCESSOR_ERROR EVEN THOUGH processorInformation.responseCode=100
 *     (the auth approved). The authoritative "auth approved" signal is therefore responseCode === '100',
 *     NOT the top-level status. We must not fail an approved auth for a token-service error -- proceed
 *     token-less. A genuine auth decline (responseCode != '100', e.g. '202') still fails.
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
     */
    public const APPROVED_STATUSES = [
        'AUTHORIZED',
        'AUTHORIZED_PENDING_REVIEW',
        'PARTIAL_AUTHORIZED',
        'PENDING',
        'PENDING_AUTHENTICATION',
        'PENDING_REVIEW',
    ];

    /**
     * The processor responseCode that maps to a clean approval (mirrors SOAP reasonCode 100).
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
     */
    public function __construct(
        protected readonly Rest $rest,
        protected readonly Config $config,
        protected readonly Sanitizer $sanitizer,
        protected readonly Data $helper,
        protected readonly CardType $cardType,
        protected readonly ResponseFactory $responseFactory,
        protected readonly PaymentRequestFactory $requestFactory,
        protected readonly StoredCardRequestFactory $storedCardRequestFactory
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
     * @return GatewayResponse
     * @throws CommandException On a declined transaction (mirrors the SA/SOAP decline path).
     * @throws RuntimeException On an error/invalid response, or a missing transient token.
     * @throws Throwable
     */
    public function place(InfoInterface $payment, float $amount, ?bool $capture = null): GatewayResponse
    {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order   = $payment->getOrder();
        $storeId = $order->getStoreId() !== null ? (int)$order->getStoreId() : null;

        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request  = $this->buildRequest($payment, $amount, $capture);
        $response = $this->rest->post(self::PAYMENTS_PATH, $request->toArray());

        return $this->interpretResponse($response, $payment, $this->requestsTokenCreate($request));
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
     * @return GatewayResponse
     * @throws CommandException On a declined transaction (mirrors the SA/SOAP decline path).
     * @throws RuntimeException On an error/invalid response, or a card with no vaulted token.
     * @throws Throwable
     */
    public function placeStored(
        InfoInterface $payment,
        CardInterface $card,
        float $amount,
        ?bool $capture = null
    ): GatewayResponse {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order   = $payment->getOrder();
        $storeId = $order->getStoreId() !== null ? (int)$order->getStoreId() : null;

        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request  = $this->buildStoredCardRequest($payment, $card, $amount, $capture);
        $response = $this->rest->post(self::PAYMENTS_PATH, $request->toArray());

        // StoredCardRequest carries no actionList: the card is already vaulted, so no token is requested
        // and the token-less reply is expected. Never flag uc_token_missing off this path.
        return $this->interpretResponse($response, $payment, false);
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
     * @return StoredCardRequest
     * @throws RuntimeException When the card carries no vaulted paymentInstrument id.
     */
    public function buildStoredCardRequest(
        InfoInterface $payment,
        CardInterface $card,
        float $amount,
        ?bool $capture = null
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
            ->setPaymentInstrumentId($paymentInstrumentId)
            ->setStoredCredentialUsed(true)
            ->setSolutionId($this->config->getSolutionId())
            ->setApplicationName($this->config->getClientName())
            ->setApplicationVersion($this->config->getClientVersion());

        // Legacy SOAP parity (Gateway::authorize on feature/php81): a subscription-generated rebill (MIT)
        // or any follow-on charge with an amount already paid must NOT re-run Decision Manager. UC analog
        // is processingInformation.enableDecisionManager=false. Done here (not just in the MIT branch) so a
        // CIT follow-on with amountPaid>0 is suppressed too, exactly as the SOAP condition did.
        if ($this->shouldSuppressDecisionManager($payment)) {
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
     * @return GatewayResponse
     * @throws CommandException On a declined transaction.
     * @throws RuntimeException On an error/invalid response, or a missing transient token.
     * @throws Throwable
     */
    public function tokenizeCard(
        InfoInterface $payment,
        string $currencyCode,
        ?int $storeId = null
    ): GatewayResponse {
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request  = $this->buildZeroDollarRequest($payment, $currencyCode);
        $response = $this->rest->post(self::PAYMENTS_PATH, $request->toArray());

        return $this->interpretResponse($response, $payment, $this->requestsTokenCreate($request));
    }

    /**
     * Assemble a $0 TOKEN_CREATE /pts/v2/payments request DTO for the zero-dollar add-card path.
     *
     * No order context: capture is forced false (authorize-only) and the amount is "0.00". The
     * transient token is still required; an empty token is a hard error (same as the purchase path).
     *
     * @param InfoInterface $payment
     * @param string $currencyCode
     * @return PaymentRequest
     * @throws RuntimeException When no transient token is present on the payment.
     */
    public function buildZeroDollarRequest(InfoInterface $payment, string $currencyCode): PaymentRequest
    {
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

        // A $0 add-card auth still wants AVS/billTo where the processor requires it. Source the billing
        // address from the payment when reachable; omit billTo entirely when none is available.
        $billTo = $this->getBillTo($this->getPaymentBillingAddress($payment));
        if ($billTo !== []) {
            $request->setBillTo($billTo);
        }

        return $request;
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
     * @return PaymentRequest
     * @throws RuntimeException When no transient token is present on the payment.
     */
    public function buildRequest(InfoInterface $payment, float $amount, ?bool $capture = null): PaymentRequest
    {
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
            ->setSolutionId($this->config->getSolutionId())
            ->setApplicationName($this->config->getClientName())
            ->setApplicationVersion($this->config->getClientVersion());

        // Legacy SOAP parity: suppress Decision Manager on a follow-on / subscription-generated charge so
        // DM is not re-run on a transaction it already screened (or an MIT rebill the cardholder isn't on).
        if ($this->shouldSuppressDecisionManager($payment)) {
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

        // processorInformation.responseCode is the AUTHORITY for approved-vs-declined, NOT the top-level
        // status. Per the spike (UC-API-REFERENCE §4), a token sub-service failure surfaces as
        // status=DECLINED + reason=PROCESSOR_ERROR while responseCode stays '100' (the auth approved). We
        // key approval off responseCode === '100' so that scenario succeeds; status is only a fallback when
        // CyberSource returns no responseCode (e.g. INVALID_REQUEST / error replies).
        $authApproved  = $responseCode === self::RESPONSE_CODE_APPROVED;

        // Decision Manager / risk REJECT (Iter 4, D6 parity): the processor can APPROVE the auth
        // (responseCode=100) while DM declines the order — status AUTHORIZED_RISK_DECLINED / REJECTED with
        // reason DECISION_PROFILE_REJECT. Unlike the token-forbidden case (which we let stand token-less), a
        // DM reject MUST fail the transaction so the order is not placed. We override the responseCode-based
        // approval here, mirroring the legacy SOAP REJECT decision -> CommandException.
        // VERIFY (live, AVS/CVV soft-decline parity): the legacy SOAP path carved AVS/CVV soft declines
        // (reasonCodes 200/230) OUT of the REJECT decline — it kept the auth and accepted with the fraud flag.
        // In REST, AVS/CVV results surface on an otherwise AUTHORIZED/responseCode=100 reply via
        // processorInformation.avs.code / cardVerification.resultCode, NOT as a RISK_DECLINED status, so they are
        // expected to pass through as approved here. Confirm on a boarded MID with an AVS/CVV-mismatch test card
        // that such a reply is not surfaced as AUTHORIZED_RISK_DECLINED/DECISION_PROFILE_REJECT before relying on
        // this; if it is, restore the soft-decline carve-out (approve + setIsFraud(true)).
        $isRiskDeclined = in_array($status, self::RISK_DECLINED_STATUSES, true)
            || $errorReason === self::REASON_DECISION_PROFILE_REJECT;

        $isApproved    = !$isRiskDeclined
            && ($authApproved || ($responseCode === '' && in_array($status, self::APPROVED_STATUSES, true)));
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

            // TOKEN_CREATE may fail ("Requested service is forbidden") while the auth approves. The spike
            // confirmed this surfaces as status=DECLINED + PROCESSOR_ERROR with responseCode=100; because
            // we key approval off responseCode (not status), we land here and proceed token-less. The
            // discriminator for "auth stands but token forbidden" is responseCode === '100' (already
            // established by $authApproved) + PROCESSOR_ERROR + no token returned.
            if ($tokenCreateRequested
                && $authApproved
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

        // A3 HANDOFF: the exception code below is the UC processorInformation.responseCode space
        // (100=approved, 2xx=declines). This is NOT the SOAP reasonCode space (102/242/241) that
        // Gateway::capture()/refund() recapture logic keys on. A3 (gateway wiring) must reconcile the two
        // code spaces when routing UC through Method/Gateway, or recapture/decline handling will misfire.
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

        $authentication = [];
        foreach (self::CONSUMER_AUTHENTICATION_FIELDS as $field) {
            $value = $authInformation[$field] ?? null;
            // Scalar guard: a future schema change to an array/object value must not land a non-scalar
            // on the persisted record.
            if ($value !== null && $value !== '' && is_scalar($value)) {
                $authentication[$field] = $value;
            }
        }

        if ($authentication !== []) {
            $data['consumer_authentication'] = $authentication;
        }
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
