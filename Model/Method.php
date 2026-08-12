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

namespace ParadoxLabs\CyberSource\Model;

use Magento\Framework\Registry;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment\Transaction\Repository;
use Override;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UnifiedCheckoutResponse;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\AbstractGateway;
use ParadoxLabs\TokenBase\Model\AbstractMethod;
use ParadoxLabs\TokenBase\Model\Gateway\Response;

/**
 * Payment method
 */
class Method extends AbstractMethod
{
    /**
     * Method constructor.
     *
     * Extends the TokenBase method with the Unified Checkout token->card mapper, used post-auth to
     * persist the inline-minted TMS ids onto the vault card (A2). All other args forward to the parent.
     *
     * @param Repository $transactionRepository
     * @param Data $helper
     * @param AbstractGateway $gateway
     * @param CardInterfaceFactory $cardFactory
     * @param CardRepositoryInterface $cardRepository
     * @param Address $addressHelper
     * @param ConfigInterface $config
     * @param Registry $registry
     * @param CardBuilder $cardBuilder
     * @param UnifiedCheckoutResponse $ucResponse *Proxy
     * @param string $methodCode
     * @param array<string, mixed> $data
     */
    public function __construct(
        Repository $transactionRepository,
        Data $helper,
        AbstractGateway $gateway,
        CardInterfaceFactory $cardFactory,
        CardRepositoryInterface $cardRepository,
        Address $addressHelper,
        ConfigInterface $config,
        Registry $registry,
        protected readonly CardBuilder $cardBuilder,
        protected readonly UnifiedCheckoutResponse $ucResponse,
        string $methodCode = '',
        array $data = []
    ) {
        parent::__construct(
            $transactionRepository,
            $helper,
            $gateway,
            $cardFactory,
            $cardRepository,
            $addressHelper,
            $config,
            $registry,
            $methodCode,
            $data
        );
    }

    /**
     * Initialize/return the API gateway class.
     *
     * @return Gateway
     * @api
     */
    #[Override]
    public function gateway()
    {
        if ($this->gateway->isInitialized() !== true) {
            $this->gateway->init(['store_id' => $this->getData('store')]);
        }

        return $this->gateway;
    }

    /**
     * Authorize a transaction, minting the vault token even when there is nothing to authorize.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Throwable
     */
    #[Override]
    public function authorize(InfoInterface $payment, $amount)
    {
        parent::authorize($payment, $amount);

        $this->tokenizeZeroTotalOrder($payment, (float)$amount);

        return $this;
    }

    /**
     * Capture a transaction, minting the vault token even when there is nothing to capture.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Throwable
     */
    #[Override]
    public function capture(InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);

        $this->tokenizeZeroTotalOrder($payment, (float)$amount);

        return $this;
    }

    /**
     * Exchange the Unified Checkout transient token on a $0 order, which never reaches the gateway.
     *
     * AbstractMethod::authorize()/capture() both early-return on `$amount <= 0` AFTER
     * loadOrCreateCard() has already persisted the new card row, but BEFORE any gateway call and
     * before afterAuthorize()/afterCapture() — the only places a checkout-sourced card's TMS token is
     * ever minted. A $0 order (free trial, 100%-off coupon, comped first period) therefore used to
     * vault a card with an empty payment_id and no uc_token_missing flag: silently unusable, blowing
     * up months later in Gateway::buildStoredCardAuth() on the first rebill.
     *
     * So run the exchange here, on the same $0 TOKEN_CREATE request the paymentinfo add-card path
     * uses (Response::buildZeroDollarRequest()). It only fires for a non-positive amount with an
     * unconsumed transient token: a normal purchase mints its token inline through the auth/capture
     * response, and applyUnifiedCheckoutToken() clears the single-use token afterwards, so an
     * authorize followed by a $0 capture (or a re-entrant call) cannot double-exchange.
     *
     * Failures are deliberately NOT swallowed, unlike the approved-purchase path: there is no
     * approved purchase to protect here, and the entire reason a $0 order collects a card is the
     * later rebill. Failing at checkout is preferable to a subscription that can never charge.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return void
     * @throws LocalizedException When the $0 auth returned no vault token (uc_token_missing).
     * @throws \Throwable On any tokenize failure (propagated to fail the order).
     */
    protected function tokenizeZeroTotalOrder(InfoInterface $payment, float $amount): void
    {
        if ($amount > 0
            || (string)$payment->getAdditionalInformation('transient_token') === '') {
            return;
        }

        /** @var Payment $payment */
        $order = $payment->getOrder();

        // billTo is required on a $0 auth (CyberSource rejects it MISSING_FIELD otherwise); passing
        // no card address makes buildZeroDollarRequest() derive it from the order billing address.
        $response = $this->ucResponse->tokenizeCard(
            $payment,
            (string)$order->getBaseCurrencyCode(),
            (int)$order->getStoreId()
        );

        $this->applyUnifiedCheckoutToken($payment, $response);

        // The parent's card save lives after its `$amount <= 0` return, so persist the minted ids here.
        $card = $this->getCard();
        if ($card instanceof CardInterface) {
            $this->card = $this->cardRepository->save($card);
        }

        if ((bool)$response->getData('uc_token_missing') === true) {
            throw new LocalizedException(
                __('The card could not be saved. Please check your payment information and try again.')
            );
        }
    }

    /**
     * Classify a void failure: only a missing auth-reversal target counts as a successful no-op.
     *
     * TokenBase's void() used to swallow every gateway failure and still report a successful void; it now
     * surfaces them and asks the gateway which failures are benign. The only CyberSource void failure that
     * genuinely needs no reversal is "the authorization this reversal targets is unknown to the processor"
     * — it already expired, was already reversed, or never settled. FollowOn maps exactly that condition
     * (HTTP 404 on the reversal path, or a 2xx body whose errorInformation.reason is the exact string
     * NOT_FOUND with no processorInformation.responseCode) onto a CommandException carrying
     * SOAP_CODE_CAPTURE_NOT_FOLLOWABLE, and deliberately never maps processor decisions onto it.
     *
     * Nothing else qualifies. In particular SOAP_CODE_REFUND_NOT_FOLLOWABLE (the capture-void path's code)
     * is NOT treated as benign: a capture whose id the processor does not recognize may still have settled,
     * and reporting that as a completed void would hide captured money. Declines, PROCESSOR_ERROR, and any
     * raw transport failure surface as real failures.
     *
     * Known limitation: a misconfigured endpoint/credential set can also produce a 404 on the reversal path,
     * which would be classified benign here. That is the same fail-safe trade-off FollowOn already makes for
     * its recapture mapping, and the alternative (substring-matching processor text) is less reliable.
     *
     * No #[Override] and no parent:: call without a guard: this method does not exist on TokenBase before
     * the void-error-surfacing change, where the override is simply never invoked.
     *
     * @param \Throwable $exception
     * @return bool
     */
    protected function isExpectedVoidFailure(\Throwable $exception): bool
    {
        if ($exception instanceof CommandException
            && (int)$exception->getCode() === FollowOn::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE) {
            return true;
        }

        // Forward-compatible: defer to the base classification (VoidNotNeededException) once it exists.
        return method_exists(AbstractMethod::class, 'isExpectedVoidFailure')
            && parent::isExpectedVoidFailure($exception) === true;
    }

    /**
     * Resync billing address et al. before auth/capture.
     *
     * @param InfoInterface $payment
     * @return $this
     */
    #[Override]
    protected function resyncStoredCard(InfoInterface $payment)
    {
        // All card updates are done via Unified Checkout payment responses; skip server-side resync saves.
        return $this;
    }

    /**
     * Persist the Unified Checkout TMS token onto the vault card after a successful auth.
     *
     * Runs before AbstractMethod::authorize() saves the card (legacy SA-era cards established their token
     * out-of-band, so they carry none of these keys and are untouched here). When
     * the gateway returns a UC response (identified by the token_information / uc_token_missing keys set
     * by UnifiedCheckout\Response::interpretResponse()), we map the inline-minted ids onto the loaded
     * card via CardBuilder; the surrounding authorize() flow then saves it. Token-less successes leave
     * the card un-tokenized and flagged, never throwing.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param Response $response
     * @return void
     */
    #[Override]
    protected function afterAuthorize(
        InfoInterface $payment,
        $amount,
        Response $response
    ) {
        parent::afterAuthorize($payment, $amount, $response);

        $this->applyUnifiedCheckoutToken($payment, $response);
    }

    /**
     * Persist the Unified Checkout TMS token onto the vault card after a successful capture/sale.
     *
     * With payment_action=authorize_capture, order placement routes through the Adapter's
     * CaptureCommand -> AbstractMethod::capture() -> afterCapture(), never afterAuthorize(). Without
     * this override the sale response's token_information would never be mapped onto the card, leaving
     * it saved with an empty paymentId and no uc_token_missing flag -- permanently unusable (Gateway::
     * buildStoredCardAuth would throw). Mirror the afterAuthorize() override: run the parent first
     * (which performs partial-invoice reauthorization), then map the inline-minted ids onto the card.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param Response $response
     * @return void
     */
    #[Override]
    protected function afterCapture(
        InfoInterface $payment,
        $amount,
        Response $response
    ) {
        parent::afterCapture($payment, $amount, $response);

        $this->applyUnifiedCheckoutToken($payment, $response);
    }

    /**
     * Map a Unified Checkout token result onto the currently loaded card, when present.
     *
     * After the token result is applied (or determined absent), the consumed single-use transient_token
     * is removed from the payment's additional_information. AbstractMethod::authorize()/capture() merge the
     * gateway response into additional_information AFTER this hook runs, and the response carries no
     * transient_token key, so the unset here sticks. This prevents the 242 recapture fallback in
     * Gateway::capture() and admin re-auth from re-posting the expired JWT via place() months later
     * instead of falling back to the vaulted card via buildStoredCardAuth(). Only clear it on a UC
     * new-card response (token_information / uc_token_missing set) -- other responses are left untouched.
     *
     * @param InfoInterface $payment
     * @param Response $response
     * @return void
     */
    protected function applyUnifiedCheckoutToken(InfoInterface $payment, Response $response): void
    {
        // Only act on Unified Checkout responses. The SOAP/SA path never sets these keys.
        if ($response->getData('token_information') === null
            && $response->getData('uc_token_missing') === null) {
            return;
        }

        $card = $this->getCard();
        if ($card instanceof CardInterface) {
            $this->cardBuilder->applyTokenToCard($card, $response);
        }

        // Re-sync payment cc_* from the response: AbstractMethod copies cc fields from the card PRE-auth
        // (while the card is still empty), so without this sales_order_payment keeps no card identity.
        // Runs regardless of card presence — guest/unsaved-card orders have no vault card at all.
        $this->applyCardInformationToPayment($payment, $response);

        // Single-use token has been consumed by place(); drop it so recapture/re-auth use the vaulted card.
        $payment->unsAdditionalInformation('transient_token');
    }

    /**
     * Fill empty payment cc_* fields from the UC response's card_information (post-auth re-sync).
     *
     * card_information is assembled by UnifiedCheckout\Response from the transient-token payload with
     * gateway-reply fields taking precedence. Only EMPTY payment fields are filled — anything already
     * set (e.g. by a checkout flow that provided card details up front) is never overwritten.
     *
     * @param InfoInterface $payment
     * @param Response $response
     * @return void
     */
    protected function applyCardInformationToPayment(InfoInterface $payment, Response $response): void
    {
        $cardInformation = $response->getData('card_information');
        if (!is_array($cardInformation) || $cardInformation === []) {
            return;
        }

        /** @var Payment $payment */
        $fieldMap = [
            'cc_type' => 'cc_type',
            'cc_last4' => 'cc_last_4',
            'cc_exp_month' => 'cc_exp_month',
            'cc_exp_year' => 'cc_exp_year',
        ];

        foreach ($fieldMap as $sourceKey => $paymentField) {
            $value = $cardInformation[$sourceKey] ?? null;
            if ($value === null || $value === '' || !empty($payment->getData($paymentField))) {
                continue;
            }

            $payment->setData($paymentField, (string)$value);
        }
    }

    /**
     * Store response statuses persistently.
     *
     * @param InfoInterface $payment
     * @param Response $response
     * @return InfoInterface
     */
    #[Override]
    protected function storeTransactionStatuses(
        InfoInterface $payment,
        Response $response
    ) {
        /** @var Payment $payment */
        if (empty($payment->getData('cc_avs_status'))) {
            $payment->setData('cc_avs_status', $response->getData('ccAuthReply.avsCode'));
        }

        if (empty($payment->getData('cc_cid_status'))) {
            $payment->setData('cc_cid_status', $response->getData('ccAuthReply.cvCode'));
        }

        if (!empty($response->getData('ccAuthReply.authorizationCode'))) {
            $payment->setData('cc_approval', $response->getData('ccAuthReply.authorizationCode'));
        }

        return $payment;
    }
}
