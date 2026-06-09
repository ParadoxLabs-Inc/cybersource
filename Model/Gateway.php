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

use Exception;
use Override;
use ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder;
use ParadoxLabs\CyberSource\Model\Source\ResponseCode;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UnifiedCheckoutResponse;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams;
use ParadoxLabs\TokenBase\Model\Gateway\Response;
use ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals;
use Magento\Framework\Phrase;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\HTTP\ClientInterfaceFactory;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order;
use ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage;
use ParadoxLabs\CyberSource\Gateway\Api\RequestMessage;
use ParadoxLabs\CyberSource\Gateway\Api\TransactionProcessor;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Gateway\Context;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\AbstractGateway;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use ParadoxLabs\TokenBase\Model\Gateway\Xml;
use SoapFault;
use Throwable;

/**
 * CyberSource API Gateway - custom built for perfection.
 */
class Gateway extends AbstractGateway
{
    /**
     * @var string
     */
    protected $code = Config::CODE;

    /**
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
     * @var TransactionProcessor
     */
    protected $soapClient;

    /**
     * @var ObjectBuilder
     */
    protected $objectBuilder;

    /**
     * @var ResponseCode
     */
    protected $responseCodeSource;

    /**
     * @var Rest
     */
    protected $restClient;

    /**
     * @var Persistor
     */
    protected $payerAuthPersistor;

    /**
     * @var JsonWebTokenEncoder
     */
    protected $payerAuthJWTEncoder;

    /**
     * @var EnrollmentParams
     */
    protected $payerAuthEnrollParams;

    /**
     * @var UnifiedCheckoutResponse
     */
    protected $unifiedCheckoutResponse;

    /**
     * @var FollowOn
     */
    protected $unifiedCheckoutFollowOn;

    /**
     * Constructor, yeah!
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

        $this->config                = $context->getConfig();
        $this->objectBuilder         = $context->getObjectBuilder();
        $this->responseCodeSource    = $context->getResponseCodeSource();
        $this->restClient            = $context->getRestClient();
        $this->payerAuthPersistor    = $context->getPayerAuthPersistor();
        $this->payerAuthJWTEncoder   = $context->getPayerAuthJWTEncoder();
        $this->payerAuthEnrollParams = $context->getPayerAuthEnrollParams();
        $this->unifiedCheckoutResponse = $context->getUnifiedCheckoutResponse();
        $this->unifiedCheckoutFollowOn = $context->getUnifiedCheckoutFollowOn();
    }

    /**
     * Initialize the gateway. Input is taken as an array for greater flexibility.
     *
     * @param array $parameters
     * @return $this
     */
    #[Override]
    public function init(array $parameters)
    {
        try {
            $this->config->setStoreId($parameters['store_id'] ?? null);

            $this->soapClient = $this->objectBuilder->getProcessor(
                $this->config,
                (array)($this->getSoapOptions() ?: [])
            );

            $this->initialized = true;
        } catch (SoapFault $exception) {
            $this->helper->log($this->code, trim((string)$exception->getMessage()));
            throw new RuntimeException(
                __('Server Error: Could not connect to CyberSource payment gateway.')
            );
        }

        return $this;
    }

    /**
     * Create a SOAP request object with standard parameters filled in.
     *
     * @return RequestMessage
     */
    public function createRequest()
    {
        $request = $this->objectBuilder->getRequest($this->config->getMerchantId());
        $request->setPartnerSolutionID(Config::SOLUTION_ID);
        $request->setClientLibrary($this->config->getClientName());
        $request->setClientLibraryVersion($this->config->getClientVersion());
        $request->setClientEnvironment('Magento 2');

        // Fields 1 and 2 have special meaning for certain processors, so skip them.
        // We pass the origin (store name and URL) for identifying where transactions came from.
        $merchantDefinedData = $this->objectBuilder->getMerchantDefinedData([
            3 => $this->getTransactionOrigin(),
        ]);
        $request->setMerchantDefinedData($merchantDefinedData);

        return $request;
    }

    /**
     * Run the given request via SOAP API.
     *
     * @param RequestMessage $requestMessage
     * @param bool $log
     * @return ReplyMessage
     * @throws RuntimeException
     * @throws StateException
     */
    public function run(RequestMessage $requestMessage, $log = true)
    {
        if ($this->soapClient instanceof TransactionProcessor === false) {
            throw new StateException(__('CyberSource gateway has not been initialized'));
        }

        try {
            $reply = $this->soapClient->runTransaction($requestMessage);
        } catch (Throwable $exception) {
            if ($log === true) {
                $this->helper->log(
                    $this->code,
                    sprintf('CyberSource Gateway error: %s', trim((string)$exception->getMessage()))
                );
            }

            throw new RuntimeException(
                __('CyberSource Gateway error: %1', trim((string)$exception->getMessage())),
                $exception instanceof Exception ? $exception : null
            );
        } finally {
            $response = $this->sanitizeLog($this->soapClient->__getLastResponse());

            if ($this->config->isSandboxMode()) {
                $request = $this->sanitizeLog($this->soapClient->__getLastRequest());

                $this->helper->log(
                    $this->code,
                    'REQUEST: ' . $request . "\nRESPONSE: " . $response,
                    true
                );
            }

            if ($log === true) {
                $this->helper->log($this->code, 'RESPONSE: ' . $response);
            }

            // Parse response into array for easier handling
            $this->lastResponse = $this->xmlToArray($this->soapClient->__getLastResponse());
            $this->helper->log(
                $this->code,
                'RESPONSE: ' . json_encode($this->lastResponse),
                true
            );
        }

        return $reply;
    }

    /**
     * Convert XML string to array. See \ParadoxLabs\TokenBase\Model\Gateway\Xml
     *
     * @param string $xml
     * @return array
     * @throws \Exception
     */
    #[Override]
    protected function xmlToArray($xml)
    {
        if (empty($xml)) {
            return [];
        }

        // Strip namespaces out of element keys
        $xml = preg_replace('/(<\/|<)[a-zA-Z]+:([a-zA-Z0-9]+[ =>\/])/', '$1$2', (string)$xml);

        $array = parent::xmlToArray($xml);

        return $array['Body']['replyMessage'] ?? $array;
    }

    /**
     * Mask certain values in the XML for secure logging purposes.
     *
     * @param string $string
     * @return string
     */
    #[Override]
    protected function sanitizeLog($string)
    {
        $string = (string)$string;

        $maskAll  = ['cvNumber'];
        $maskFour = ['Password', 'accountNumber'];

        foreach ($maskAll as $val) {
            $string = preg_replace('#' . $val . '>(.+?)</(.+?):' . $val . '#', $val . '>XXX</$2:' . $val, (string) $string);
        }

        foreach ($maskFour as $val) {
            $start = strpos((string) $string, $val . '>');

            if ($start === false) {
                continue;
            }

            $end    = strpos((string) $string, '</', $start);
            $tagLen = strlen($val) + 1;

            if ($end !== false && $end > ($start + $tagLen + 4)) {
                $string = substr_replace($string, 'XXXX', $start + $tagLen, $end - 4 - ($start + $tagLen));
            }
        }

        return str_replace("\n", '', $string);
    }

    /**
     * Run an auth transaction for $amount with the given payment info, over the CyberSource REST API.
     *
     * New-card path: the payment carries a Unified Checkout transient token (additional_data.transient_token)
     * captured client-side; we delegate to the A1 UnifiedCheckout\Response service, which POSTs
     * /pts/v2/payments (auth or sale per server-side payment_action) and returns the gateway Response the
     * rest of the module consumes (Method::afterAuthorize() then maps any minted TMS ids onto the card).
     *
     * Stored-card (vault / MIT) path: there is no transient token; the card already holds its TMS ids. The
     * full stored-credential / MIT request-builder is A4 — see buildStoredCardAuth() for the seam.
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

        // Stored-card / MIT auth from the vaulted TMS ids — A4 owns the request builder.
        return $this->buildStoredCardAuth($payment, (float)$amount);
    }

    /**
     * Build and run a stored-card (vault / MIT) authorization from the card's stored TMS ids.
     *
     * A4 SEAM. A3 leaves this as a clear, documented extension point rather than faking the
     * stored-credential request. A4 will assemble the /pts/v2/payments body from the card's stored ids —
     * paymentInformation.customer.id (card profileId), paymentInformation.paymentInstrument.id (card
     * paymentId), paymentInformation.instrumentIdentifier.id (card additional[instrument_identifier]) —
     * plus the merchant-initiated / stored-credential initiator block (processingInformation
     * .authorizationOptions.initiator + commerceIndicator) that A4 owns. Until A4 lands, a stored-card
     * auth is not yet implemented and we fail loudly rather than silently mis-charging.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return Response
     * @throws RuntimeException Always, until A4 implements the stored-credential request builder.
     */
    protected function buildStoredCardAuth(InfoInterface $payment, float $amount)
    {
        $this->helper->log(
            $this->code,
            'Stored-card (MIT) authorization requested but the Unified Checkout stored-credential request'
            . ' builder is not yet implemented (A4).'
        );

        throw new RuntimeException(
            __(
                'Stored-card payments are not yet available for this payment method.'
                . ' Please re-enter your card details.'
            )
        );
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
     * bundling is A4 (the buildStoredCardAuth seam); for now a token-less bundled capture fails loudly.
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

                // Stash the id we are dropping so the unlinked retry can still target the original payment.
                $this->setData('refund_fallback_txn_id', (string)$transactionId);
                $this->setTransactionId(null)
                     ->setCard($this->getData('card'));

                return $this->refund($payment, $amount, '');
            }

            // Pass any other errors through.
            throw $exception;
        }
    }

    /**
     * Resolve the payment id to target for an unlinked-credit refund fallback.
     *
     * Prefers the id stashed when the linked refund was downgraded; otherwise falls back to the payment's
     * parent/last transaction id (stripped of any -capture/-refund suffix), which shares the REST id space.
     *
     * @param InfoInterface $payment
     * @return string
     */
    protected function getRefundFallbackTransactionId(InfoInterface $payment)
    {
        $stashed = (string)$this->getData('refund_fallback_txn_id');
        if ($stashed !== '') {
            return $stashed;
        }

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

        $transactionId = $transactionId ?: $this->getTransactionId();

        // REST collapses the SOAP auth-reversal vs void distinction into a single auth reversal against the
        // stored auth id. We reverse the amount still due, falling back to the amount paid.
        $amount = (float)($order->getTotalDue() ?: $order->getTotalPaid());

        return $this->unifiedCheckoutFollowOn->void($payment, $amount, (string)$transactionId);
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
        // (assumed scope), mirroring the SOAP paySubscriptionDelete which carried no per-card store id.
        return $this->unifiedCheckoutFollowOn->deleteCard($paymentInstrumentId, $customerId);
    }

    /**
     * Test the SOAP API connection. Runs a request with no indicators and no response logging.
     *
     * @return Response
     */
    public function testConnection()
    {
        $request = $this->createRequest();
        $reply   = $this->run($request, false);

        return $this->interpretTransaction($reply);
    }

    /**
     * Translate SOAP reply into a Magento-compatible transaction data object. Throw exception on any error cases.
     *
     * @param ReplyMessage $api
     * @param InfoInterface|null $payment
     * @return Response
     * @throws CommandException
     * @throws RuntimeException
     */
    protected function interpretTransaction(
        ReplyMessage $api,
        ?InfoInterface $payment = null
    ) {
        // NB: Temporal coupling, we assume interpretTransaction will always be run immediately after the transaction
        // it's intended to interpret. Otherwise, lastResponse will be the wrong data.
        $data                         = $this->lastResponse;
        $data['transaction_id']       = $api->getRequestID();
        $data['response_code']        = $api->getReasonCode();
        $data['response_reason_code'] = $api->getReasonCode();
        $data['response_reason_text'] = $this->responseCodeSource->getMessage($api->getReasonCode());
        $data['auth_code']            = $api->getRequestToken(); // Not auth code, but it functions the same way.
        /** @var Response $response */
        $response = $this->responseFactory->create(['data' => $data]);
        $response->setIsError($api->getDecision() === 'ERROR' || $api->getDecision() === 'REJECT');

        // Set fraud flag if marked for review or soft declines (AVS and CVV, respectively).
        if ($api->getDecision() === 'REVIEW' || in_array($api->getReasonCode(), [200, 230], true)) {
            $response->setIsFraud(true);
        }

        if ($payment !== null && in_array($api->getReasonCode(), [475, 478], true)) {
            $this->payerAuthPersistor->savePayerAuthEnrollReply($payment, $api);
        }

        // Soft declines come in as REJECT, but keep their auth -- just accept with the fraud flag.
        if (in_array($api->getDecision(), ['ERROR', 'REJECT'], true)
            && !in_array($api->getReasonCode(), [200, 230], true)) {
            $message = __('Transaction Failed: %1', __($response->getResponseReasonText()));

            // Don't log API test errors
            if ($payment !== null || $api->getReasonCode() !== 101) {
                $request = $this->sanitizeLog($this->soapClient->__getLastRequest());
                $this->helper->log($this->code, 'REQUEST: ' . $request);
                $this->helper->log($this->code, $message . ' (' . $api->getReasonCode() . ')');
            }

            if ($api->getDecision() === 'REJECT') {
                throw new CommandException($message, null, $api->getReasonCode());
            }
            throw new RuntimeException($message, null, $api->getReasonCode());
        }

        return $response;
    }

    /**
     * Turn multi-dimensional array into 1D, concatenating keys
     *
     * @param string|null $prefix
     * @return array
     * @deprecated since 1.3.1
     */
    protected function flattenArray(mixed $array, $prefix = null)
    {
        /**
         * Logic moved into TokenBase
         *
         * @see \ParadoxLabs\TokenBase\Model\Gateway\Response::getData()
         */

        return $array;
    }

    /**
     * Get a PurchaseTotals amounts object for authorization or capture.
     *
     * @param InfoInterface $payment
     * @param Order $order
     * @param float $amount
     * @return PurchaseTotals
     */
    protected function getOrderPurchaseTotals(
        InfoInterface $payment,
        Order $order,
        $amount
    ) {
        $purchaseTotals = $this->objectBuilder->getPurchaseTotals($order->getBaseCurrencyCode(), $amount);
        if ($this->getHaveAuthorized() !== true) {
            $purchaseTotals->setTaxAmount($order->getTaxAmount());
            $purchaseTotals->setShippingAmount($payment->getShippingAmount());
        }

        return $purchaseTotals;
    }

    /**
     * Get the transaction origin string (store name and URL) for identification purposes.
     *
     * @return Phrase
     * @throws NoSuchEntityException
     */
    protected function getTransactionOrigin()
    {
        $store = $this->helper->getCurrentStore();

        return __('%1 (%2)', $store->getName(), $store->getBaseUrl());
    }

    /**
     * Add Payer Auth enrollment check or verification to the auth/capture, when relevant.
     *
     * @param InfoInterface $payment
     * @param RequestMessage $request
     * @return void
     */
    protected function requestPayerAuthentication(
        InfoInterface $payment,
        RequestMessage $request
    ) {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();

        // If Payer Authentication isn't enabled, or we've already processed payment, don't ... run payer auth.
        // NB/Future: May need to enroll with prior-auth info in the prior payment case.
        if ($this->config->isPayerAuthEnabledForType((string)$payment->getCcType()) === false
            || $this->helper->getIsFrontend() === false
            || $order->getTotalPaid() > 0) {
            return;
        }

        // Validate instead of enroll if we have verification params
        if (!empty($payment->getAdditionalInformation('response_jwt'))) {
            $this->requestPayerAuthenticationValidate($payment, $request);
        } else {
            $this->requestPayerAuthenticationEnroll($payment, $request);
        }
    }

    /**
     * Add Payer Auth validation service to the auth/capture.
     *
     * @param InfoInterface $payment
     * @param RequestMessage $request
     * @return void
     * @throws InputException
     */
    protected function requestPayerAuthenticationValidate(
        InfoInterface $payment,
        RequestMessage $request
    ) {
        // Note: We unpack the JWT to confirm its signature and validity before passing it on.
        $decodedJWT = $this->payerAuthJWTEncoder->unpack(
            $payment->getAdditionalInformation('response_jwt')
        );

        $validateService = $this->objectBuilder->getPayerAuthValidateService(
            $decodedJWT['Payload']['Payment']['ProcessorTransactionId'] ?? null,
            $payment->getAdditionalInformation('response_jwt')
        );

        $request->setPayerAuthValidateService($validateService);
    }

    /**
     * Add Payer Auth enrollment service to the auth/capture.
     *
     * This involves a substantial amount of context data on the user/card/order, which we hand off to a service class.
     *
     * @param InfoInterface $payment
     * @param RequestMessage $request
     * @return void
     */
    protected function requestPayerAuthenticationEnroll(
        InfoInterface $payment,
        RequestMessage $request
    ) {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();

        $referenceId = $payment->getAdditionalInformation('payerauth_session_id');

        $enrollService = $this->objectBuilder->getPayerAuthEnrollService($referenceId);
        $this->payerAuthEnrollParams->populateEnrollmentService(
            $enrollService,
            $order,
            $this->getCard()
        );

        $request->setPayerAuthEnrollService($enrollService);
    }
}
