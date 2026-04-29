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

use ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder;
use ParadoxLabs\CyberSource\Model\Source\ResponseCode;
use ParadoxLabs\CyberSource\Model\Service\Rest;
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
    }

    /**
     * Initialize the gateway. Input is taken as an array for greater flexibility.
     *
     * @param array $parameters
     * @return $this
     */
    #[\Override]
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
        $request->setPartnerSolutionID(Config\Config::SOLUTION_ID);
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
                $exception instanceof \Exception ? $exception : null
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
    #[\Override]
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
    #[\Override]
    protected function sanitizeLog($string)
    {
        $string = (string)$string;

        $maskAll  = ['cvNumber'];
        $maskFour = ['Password', 'accountNumber'];

        foreach ($maskAll as $val) {
            $string = preg_replace('#' . $val . '>(.+?)</(.+?):' . $val . '#', $val . '>XXX</$2:' . $val, (string) $string);
        }

        foreach ($maskFour as $val) {
            $start  = strpos((string) $string, $val . '>');
            $end    = strpos((string) $string, '</', $start);
            $tagLen = strlen($val) + 1;

            if ($start !== false && $end > ($start + $tagLen + 4)) {
                $string = substr_replace($string, 'XXXX', $start + $tagLen, $end - 4 - ($start + $tagLen));
            }
        }

        return str_replace("\n", '', $string);
    }

    /**
     * Run an auth transaction for $amount with the given payment info
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return Response
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();

        $purchaseTotals = $this->getOrderPurchaseTotals($payment, $order, $amount);

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setDeviceFingerprintID($this->config->getFingerprintSessionId($order->getQuoteId(), null, true));
        $request->setBillTo($this->objectBuilder->getOrderBillTo($order));
        $request->setItem($this->objectBuilder->getOrderItems($this->lineItems));
        $request->setRecurringSubscriptionInfo($this->objectBuilder->getTokenInfo($this->getCard()));
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcAuthService(
            $this->objectBuilder->getAuthService(
                $this->helper->getIsFrontend() ? 'internet' : 'moto'
            )
        );

        if ((bool)$order->getIsVirtual() === false) {
            $request->setShipTo($this->objectBuilder->getOrderShipTo($order));
        }

        if (!empty($payment->getData('cc_cid'))) {
            $request->setCard($this->objectBuilder->getCardForCvn($payment->getData('cc_cid')));
        } else {
            $request->setBusinessRules($this->objectBuilder->getBusinessRules(true));
        }

        // If this is a follow-on transaction (some amount already captured), do not run decision manager again.
        if ($payment->getAmountPaid() > 0 || $payment->getAdditionalInformation('is_subscription_generated')) {
            $request->setDecisionManager($this->objectBuilder->enableDecisionManager(false));
        }

        $this->requestPayerAuthentication($payment, $request);

        $reply = $this->run($request);

        return $this->interpretTransaction($reply, $payment);
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
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();

        $purchaseTotals = $this->getOrderPurchaseTotals($payment, $order, $amount);

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setDeviceFingerprintID($this->config->getFingerprintSessionId($order->getQuoteId(), null, true));
        $request->setItem($this->objectBuilder->getOrderItems($this->lineItems));
        $request->setPurchaseTotals($purchaseTotals);

        $transactionId ??= $this->getTransactionId();

        // If we don't have a transaction ID to capture, run a 'bundled' auth+capture; otherwise, prior-auth capture.
        if (empty($transactionId) || !$this->getHaveAuthorized()) {
            $this->captureInitBundledRequest($payment, $request);
            $this->requestPayerAuthentication($payment, $request);
        } else {
            $this->captureInitLinkedRequest($transactionId, $request);
        }

        // If this is a follow-on transaction (some amount already captured), do not run decision manager again.
        if ($payment->getAmountPaid() > 0 || $payment->getAdditionalInformation('is_subscription_generated')) {
            $request->setDecisionManager($this->objectBuilder->enableDecisionManager(false));
        }

        $reply = $this->run($request);

        try {
            return $this->interpretTransaction($reply, $payment);
        } catch (Throwable $exception) {
            // Handle 'transaction not found' error (expired authorization).
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
     * Set bundled-auth-capture parameters for a capture request.
     *
     * @param InfoInterface $payment
     * @param RequestMessage $request
     * @return void
     */
    protected function captureInitBundledRequest(
        InfoInterface $payment,
        RequestMessage $request
    ) {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();

        // NB: Documentation says bundled capture varies by processor. Hoping this generic case works for all.

        $ccAuthService = $this->objectBuilder->getAuthService(
            $this->helper->getIsFrontend() ? 'internet' : 'moto'
        );
        $ccAuthService->setAuthType('AUTOCAPTURE');

        $request->setCcAuthService($ccAuthService);
        $request->setCcCaptureService($this->objectBuilder->getCaptureService());
        $request->setBillTo($this->objectBuilder->getOrderBillTo($order));
        $request->setRecurringSubscriptionInfo($this->objectBuilder->getTokenInfo($this->getCard()));

        if ((bool)$order->getIsVirtual() === false) {
            $request->setShipTo($this->objectBuilder->getOrderShipTo($order));
        }

        if (!empty($payment->getData('cc_cid'))) {
            $request->setCard($this->objectBuilder->getCardForCvn($payment->getData('cc_cid')));
        } else {
            $request->setBusinessRules($this->objectBuilder->getBusinessRules(true));
        }
    }

    /**
     * Set linked-capture (prior-auth capture) parameters for a capture request.
     *
     * @param string $transactionId
     * @param RequestMessage $request
     * @return void
     */
    protected function captureInitLinkedRequest(
        $transactionId,
        RequestMessage $request
    ) {
        $ccCaptureService = $this->objectBuilder->getCaptureService();
        $ccCaptureService->setAuthRequestID($transactionId);
        $ccCaptureService->setAuthRequestToken($this->getParameter('auth_code') ?: null);

        $request->setCcCaptureService($ccCaptureService);
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
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var Order $order */
        $order = $payment->getOrder();

        $purchaseTotals = $this->objectBuilder->getPurchaseTotals($order->getBaseCurrencyCode(), $amount);
        if ($payment->getCreditmemo() instanceof CreditmemoInterface) {
            if ($payment->getCreditmemo()->getTaxAmount()) {
                $purchaseTotals->setTaxAmount($payment->getCreditmemo()->getTaxAmount());
            }
            if ($payment->getCreditmemo()->getShippingAmount()) {
                $purchaseTotals->setShippingAmount($payment->getCreditmemo()->getShippingAmount());
            }
        }

        $ccCreditService = $this->objectBuilder->getCreditService(
            'internet',
            $transactionId ?? $this->getTransactionId()
        );

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcCreditService($ccCreditService);

        $reply = $this->run($request);

        try {
            return $this->interpretTransaction($reply, $payment);
        } catch (Throwable $exception) {
            // Handle 'not valid for follow-on transaction' error (past allowed period).
            if ($exception->getCode() === 241) {
                $this->helper->log($this->code, 'Transaction not refundable. Attempting unlinked credit.');

                $this->setTransactionId(null)
                     ->setCard($this->getData('card'));

                return $this->refund($payment, $amount, '');
            }

            // Pass any other errors through.
            throw $exception;
        }
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

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());

        if ($order->getTotalDue() > 0) {
            $purchaseTotals = $this->objectBuilder->getPurchaseTotals(
                $order->getBaseCurrencyCode(),
                $order->getTotalDue() ?: $order->getTotalPaid()
            );

            $ccAuthReversalService = $this->objectBuilder->getAuthReversalService(
                $transactionId ?: $this->getTransactionId()
            );

            $request->setPurchaseTotals($purchaseTotals);
            $request->setCcAuthReversalService($ccAuthReversalService);
        } else {
            $purchaseTotals = $this->objectBuilder->getPurchaseTotals(
                $order->getBaseCurrencyCode(),
                $order->getTotalPaid()
            );

            $voidService = $this->objectBuilder->getVoidService(
                $transactionId ?: $this->getTransactionId()
            );

            $request->setPurchaseTotals($purchaseTotals);
            $request->setVoidService($voidService);
        }

        $reply = $this->run($request);

        return $this->interpretTransaction($reply, $payment);
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
        $info = $this->objectBuilder->getTokenInfo($this->getCard());

        $request = $this->createRequest();
        $request->setRecurringSubscriptionInfo($info);
        $request->setPaySubscriptionDeleteService($this->objectBuilder->getPaySubscriptionDeleteService());

        $reply = $this->run($request);

        return $this->interpretTransaction($reply);
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
