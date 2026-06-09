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

use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\HTTP\ClientInterfaceFactory;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order;
use Override;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Gateway\Context;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UnifiedCheckoutResponse;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\AbstractGateway;
use ParadoxLabs\TokenBase\Model\Gateway\Response;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use ParadoxLabs\TokenBase\Model\Gateway\Xml;
use Throwable;

/**
 * CyberSource REST/Unified Checkout gateway: routes TokenBase gateway operations to the REST services.
 */
class Gateway extends AbstractGateway
{
    /**
     * @var string
     */
    protected $code = Config::CODE;

    /**
     * Retained for the parent's setParameter() plumbing (setTransactionId/setAuthCode).
     *
     * @var array
     */
    protected $fields = [
        'auth_code' => [],
        'transaction_id' => [],
    ];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Rest
     */
    protected $restClient;

    /**
     * @var UnifiedCheckoutResponse
     */
    protected $unifiedCheckoutResponse;

    /**
     * @var FollowOn
     */
    protected $unifiedCheckoutFollowOn;

    /**
     * Note: Xml $xml and ClientInterfaceFactory $communicatorFactory are retained solely to satisfy the
     * TokenBase AbstractGateway parent constructor signature; this gateway does not use them directly.
     *
     * @param Data $helper
     * @param Xml $xml
     * @param \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory
     * @param \Magento\Framework\HTTP\ClientInterfaceFactory $communicatorFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Data $helper,
        Xml $xml,
        ResponseFactory $responseFactory,
        ClientInterfaceFactory $communicatorFactory,
        Context $context,
        array $data = [],
    ) {
        parent::__construct($helper, $xml, $responseFactory, $communicatorFactory, $data);

        $this->config                  = $context->getConfig();
        $this->restClient              = $context->getRestClient();
        $this->unifiedCheckoutResponse = $context->getUnifiedCheckoutResponse();
        $this->unifiedCheckoutFollowOn = $context->getUnifiedCheckoutFollowOn();
    }

    /**
     * Initialize the gateway. Input is taken as an array for greater flexibility.
     *
     * All transactions run over the CyberSource REST API; the REST services resolve credentials per
     * store scope, so initialization only pins the config scope for this gateway instance.
     *
     * @param array $parameters
     * @return $this
     */
    #[Override]
    public function init(array $parameters)
    {
        $this->config->setStoreId($parameters['store_id'] ?? null);

        $this->initialized = true;

        return $this;
    }

    /**
     * Run an auth transaction for $amount with the given payment info, over the CyberSource REST API.
     *
     * New-card path: the payment carries a Unified Checkout transient token (additional_data.transient_token)
     * captured client-side; we delegate to the A1 UnifiedCheckout\Response service, which POSTs
     * /pts/v2/payments (auth or sale per server-side payment_action) and returns the gateway Response the
     * rest of the module consumes (Method::afterAuthorize() then maps any minted TMS ids onto the card).
     *
     * Stored-card (vault / MIT) path: there is no transient token; the card already holds its TMS ids. We
     * delegate to buildStoredCardAuth(), which builds the stored-credential request from the vaulted card.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return Response
     * @throws CommandException
     * @throws RuntimeException
     * @throws Throwable
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        if ($this->hasTransientToken($payment)) {
            // New-card Unified Checkout auth/sale (A1).
            return $this->unifiedCheckoutResponse->place($payment, (float)$amount);
        }

        // Stored-card / MIT auth from the vaulted TMS ids.
        return $this->buildStoredCardAuth($payment, (float)$amount);
    }

    /**
     * Build and run a stored-card (vault / MIT) authorization from the card's stored TMS ids.
     *
     * Delegates to the UnifiedCheckout\Response stored-credential request builder, which assembles the
     * /pts/v2/payments body from the card's stored ids — paymentInformation.customer.id (card profileId),
     * paymentInformation.paymentInstrument.id (card paymentId, the MIT key), and
     * paymentInformation.instrumentIdentifier.id (card additional[instrument_identifier]) — plus the
     * merchant-initiated / stored-credential initiator block (processingInformation.authorizationOptions
     * .initiator + commerceIndicator).
     *
     * Guards first: a stored-card auth needs a real vaulted card. If there is no card, or the card was
     * never tokenized (uc_token_missing flag set, or no paymentId), we fail loudly and log rather than
     * silently attempt an unrunnable auth — the card must be re-entered.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return Response
     * @throws RuntimeException When there is no usable vaulted card to run the stored-credential auth.
     * @throws Throwable
     */
    protected function buildStoredCardAuth(InfoInterface $payment, float $amount)
    {
        $card = $this->getCard();

        if (!$card instanceof CardInterface) {
            throw new RuntimeException(
                __(
                    'Stored-card payments require a saved card. Please re-enter your card details.'
                )
            );
        }

        if ($card->getAdditional(CardBuilder::CARD_FLAG_TOKEN_MISSING) === '1'
            || (string)$card->getPaymentId() === ''
        ) {
            $this->helper->log(
                $this->code,
                'Stored-card authorization requested but the card has no Unified Checkout token'
                . ' (uc_token_missing / no paymentId). The card must be re-entered.'
            );

            throw new RuntimeException(
                __(
                    'This saved card is no longer usable. Please re-enter your card details.'
                )
            );
        }

        return $this->unifiedCheckoutResponse->placeStored($payment, $card, (float)$amount);
    }

    /**
     * Whether the payment carries a Unified Checkout transient token (new-card path).
     *
     * @param InfoInterface $payment
     * @return bool
     */
    protected function hasTransientToken(InfoInterface $payment)
    {
        $token = $payment->getAdditionalInformation('transient_token');

        return $token !== null && $token !== '';
    }

    /**
     * Run a capture transaction for $amount with the given payment info
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param string $transactionId
     * @return Response
     */
    public function capture(InfoInterface $payment, $amount, $transactionId = null)
    {
        $transactionId ??= $this->getTransactionId();

        // Bundled vs linked: with no prior auth we run a bundled auth+capture (a UC sale); with a prior
        // auth we settle that auth via a linked REST capture on its stored transaction id. This preserves
        // the SOAP-era bundled/linked decision (empty txn id or !haveAuthorized => bundled).
        if (empty($transactionId) || !$this->getHaveAuthorized()) {
            return $this->captureBundled($payment, (float)$amount);
        }

        try {
            return $this->unifiedCheckoutFollowOn->capture($payment, (float)$amount, (string)$transactionId);
        } catch (Throwable $exception) {
            // Handle 'transaction not found' (expired/unusable authorization). The REST follow-on service
            // re-throws the SOAP-equivalent code (242) for that condition, so the SOAP-era recapture logic
            // (which keyed on 102/242) fires unchanged: drop the stored id and run a bundled auth+capture.
            if ($this->getHaveAuthorized() && in_array($exception->getCode(), [102, 242], true) === true) {
                $this->helper->log($this->code, 'Transaction not found. Attempting to recapture.');

                $this->setTransactionId(null)
                     ->setHaveAuthorized(false)
                     ->setCard($this->getData('card'));

                return $this->capture($payment, $amount, '');
            }

            // Pass any other errors through.
            throw $exception;
        }
    }

    /**
     * Run a bundled auth+capture (a Unified Checkout sale) for the given payment and amount.
     *
     * New-card: delegate to the A1 auth/sale service, which sends capture=true (sale). Stored-card MIT
     * bundling routes through buildStoredCardAuth(), which builds the stored-credential request from the
     * vaulted card (capture is derived server-side from payment_action).
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return Response
     * @throws CommandException
     * @throws RuntimeException
     * @throws Throwable
     */
    protected function captureBundled(InfoInterface $payment, float $amount)
    {
        if ($this->hasTransientToken($payment)) {
            return $this->unifiedCheckoutResponse->place($payment, $amount);
        }

        return $this->buildStoredCardAuth($payment, $amount);
    }

    /**
     * Run a refund transaction for $amount with the given payment info
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param string $transactionId
     * @return Response
     */
    public function refund(InfoInterface $payment, $amount, $transactionId = null)
    {
        $transactionId ??= $this->getTransactionId();

        // The (possibly partial) refund amount is passed straight through to the REST follow-on body's
        // orderInformation.amountDetails.totalAmount, preserving partial-refund handling.

        // No stored id (the unlinked-credit fallback below already cleared it): unlinked credit. REST
        // unlinked refunds still post against the original payment id; we use the txn id we just cleared,
        // recovered from the payment's last/parent transaction. A true tokenized standalone credit (no
        // prior payment at all) is a stored-card/MIT operation owned by A4.
        if (empty($transactionId)) {
            return $this->unifiedCheckoutFollowOn->refundUnlinked(
                $payment,
                (float)$amount,
                (string)$this->getRefundFallbackTransactionId($payment)
            );
        }

        try {
            return $this->unifiedCheckoutFollowOn->refund($payment, (float)$amount, (string)$transactionId);
        } catch (Throwable $exception) {
            // Handle 'not valid for follow-on transaction' (past allowed period). The REST follow-on
            // service re-throws the SOAP-equivalent code (241) for that condition, so the SOAP-era
            // unlinked-credit fallback fires unchanged: drop the stored id and retry as an unlinked credit.
            if ($exception->getCode() === 241) {
                $this->helper->log($this->code, 'Transaction not refundable. Attempting unlinked credit.');

                // Drop the (capture) id and retry as an unlinked credit. We deliberately do NOT stash the
                // capture id: an unlinked credit must post against the original PAYMENT id, which
                // getRefundFallbackTransactionId() resolves from the parent/last transaction id (with the
                // -capture suffix stripped). Stashing the capture id here would post the credit against the
                // capture, the wrong target.
                $this->setTransactionId(null)
                     ->setCard($this->getData('card'));

                return $this->refund($payment, $amount, '');
            }

            // Pass any other errors through.
            throw $exception;
        }
    }

    /**
     * Resolve the original PAYMENT id to target for an unlinked-credit refund fallback.
     *
     * An unlinked credit must hit the original payment id, NOT the capture id the linked refund failed
     * against. We resolve it from the payment's parent/last transaction id, stripped of any
     * -capture/-refund suffix (REST and SOAP share the same transaction-id space, D4).
     *
     * @param InfoInterface $payment
     * @return string
     */
    protected function getRefundFallbackTransactionId(InfoInterface $payment)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $txnId = $payment->getParentTransactionId() ?: $payment->getLastTransId();

        return substr((string)$txnId, 0, strcspn((string)$txnId, '-'));
    }

    /**
     * Run a void transaction for the given payment info
     *
     * @param InfoInterface $payment
     * @param string $transactionId
     * @return Response
     */
    public function void(InfoInterface $payment, $transactionId = null)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();

        $transactionId = (string)($transactionId ?: $this->getTransactionId());

        // Restore the SOAP auth-reversal vs capture-void distinction. An uncaptured auth (amount still
        // due) is reversed via POST /pts/v2/payments/{authId}/reversals. Once captured/settled the auth
        // reversal is rejected by the processor, so the CAPTURE itself must be voided via
        // POST /pts/v2/captures/{captureId}/voids (the stored parent/current txn id is the capture's REST
        // id once settled). Keying on totalDue mirrors the SOAP-era branch exactly.
        if ($order->getTotalDue() > 0) {
            // Reverse the amount still due, falling back to the amount paid.
            $amount = (float)($order->getTotalDue() ?: $order->getTotalPaid());

            return $this->unifiedCheckoutFollowOn->void($payment, $amount, $transactionId);
        }

        return $this->unifiedCheckoutFollowOn->voidCapture($payment, $transactionId);
    }

    /**
     * Fetch a transaction status update
     *
     * @param InfoInterface $payment
     * @param string $transactionId
     * @return Response
     */
    public function fraudUpdate(InfoInterface $payment, $transactionId)
    {
        /**
         * NB: We can only request up to 24 hours of data, which means 'fetch update' will only work if the decision
         * was made within 24 hours. But our hourly updater cron should mean that never comes up unless cron isn't
         * running at all. This just allows people to pull updates through immediately.
         */

        $storeId = (int)$payment->getOrder()->getStoreId();
        $this->restClient->setStoreId($storeId);

        /** @var Response $response */
        $response = $this->responseFactory->create();
        $response->setData(['is_approved' => false, 'is_denied' => false]);

        try {
            $reply = $this->restClient->get(
                '/reporting/v3/conversion-details',
                [
                    'startTime' => date(Sanitizer::ISO_FORMAT, strtotime('-24 hour')),
                    'endTime' => date(Sanitizer::ISO_FORMAT),
                    'organizationId' => $this->config->getOrganizationId($storeId),
                ]
            );

            $reply = json_decode((string)$reply, true);
            if ($reply !== false && !empty($reply['conversionDetails'])) {
                foreach ($reply['conversionDetails'] as $change) {
                    if ($change['requestId'] === $transactionId) {
                        $response->addData($change);

                        if ($change['newDecision'] === 'ACCEPT') {
                            $response->setData('is_approved', true);
                        }
                        if ($change['newDecision'] === 'REJECT') {
                            $response->setData('is_denied', true);
                        }

                        break;
                    }
                }
            }
        } catch (Throwable $exception) {
            // A 404 'resource not found' response means there are no updates in the requested timespan. Ignore.
            if ($exception->getMessage() !== 'Requested Resource Not Found') {
                throw $exception;
            }
        }

        return $response;
    }

    /**
     * Delete the given card token from CyberSource TMS.
     *
     * @return Response
     */
    public function deleteCard()
    {
        /** @var \ParadoxLabs\CyberSource\Model\Card $card */
        $card = $this->getCard();

        // paymentId == TMS paymentInstrument id (the MIT key); profileId == TMS customer id, when present.
        $paymentInstrumentId = (string)$card->getPaymentId();
        $customerId          = $card->getProfileId() !== null ? (string)$card->getProfileId() : null;

        // Cards are not store-scoped; merchant credentials resolve at the gateway's initialized scope
        // (assumed scope), mirroring the SOAP-era card delete which carried no per-card store id.
        return $this->unifiedCheckoutFollowOn->deleteCard($paymentInstrumentId, $customerId);
    }
}
