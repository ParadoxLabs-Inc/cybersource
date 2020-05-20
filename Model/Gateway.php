<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model;

use ParadoxLabs\CyberSource\Model\Service\Sanitizer;

/**
 * CyberSource API Gateway - custom built for perfection.
 */
class Gateway extends \ParadoxLabs\TokenBase\Model\AbstractGateway
{
    /**
     * @var string
     */
    protected $code = \ParadoxLabs\CyberSource\Model\Config\Config::CODE;

    /**
     * @var array
     */
    protected $fields = [
        'auth_code' => [],
        'transaction_id' => [],
    ];

    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\CyberSource\Gateway\Api\TransactionProcessor
     */
    protected $soapClient;

    /**
     * @var \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder
     */
    protected $objectBuilder;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Source\ResponseCode
     */
    protected $responseCodeSource;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Rest
     */
    protected $restClient;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor
     */
    protected $payerAuthPersistor;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder
     */
    protected $payerAuthJWTEncoder;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams
     */
    protected $payerAuthEnrollParams;

    /**
     * Constructor, yeah!
     *
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml
     * @param \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \ParadoxLabs\CyberSource\Model\Gateway\Context $context
     * @param array $data
     */
    public function __construct(
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml,
        \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \ParadoxLabs\CyberSource\Model\Gateway\Context $context,
        array $data = []
    ) {
        parent::__construct($helper, $xml, $responseFactory, $httpClientFactory, $data);

        $this->config = $context->getConfig();
        $this->objectBuilder = $context->getObjectBuilder();
        $this->responseCodeSource = $context->getResponseCodeSource();
        $this->restClient = $context->getRestClient();
        $this->payerAuthPersistor = $context->getPayerAuthPersistor();
        $this->payerAuthJWTEncoder = $context->getPayerAuthJWTEncoder();
        $this->payerAuthEnrollParams = $context->getPayerAuthEnrollParams();
    }

    /**
     * Initialize the gateway. Input is taken as an array for greater flexibility.
     *
     * @param array $parameters
     * @return $this
     */
    public function init(array $parameters)
    {
        try {
            $this->soapClient = $this->objectBuilder->getProcessor(
                $this->config->getSoapWsdl()
            );

            $this->soapClient->__setSoapHeaders(
                $this->objectBuilder->getSecurityHeader(
                    $this->config->getMerchantId(),
                    $this->config->getSoapTransactionKey()
                )
            );

            $this->initialized  = true;
        } catch (\SoapFault $exception) {
            $this->helper->log($this->code, trim($exception->getMessage()));
            throw new \Magento\Framework\Exception\RuntimeException(
                __('Server Error: Could not connect to CyberSource payment gateway.')
            );
        }

        return $this;
    }

    /**
     * Create a SOAP request object with standard parameters filled in.
     *
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
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
     * @param \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $requestMessage
     * @param bool $log
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     * @throws \Magento\Framework\Exception\RuntimeException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function run(\ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $requestMessage, $log = true)
    {
        if ($this->soapClient instanceof \ParadoxLabs\CyberSource\Gateway\Api\TransactionProcessor === false) {
            throw new \Magento\Framework\Exception\StateException(__('CyberSource gateway has not been initialized'));
        }

        try {
            $reply = $this->soapClient->runTransaction($requestMessage);
        } catch (\Exception $exception) {
            if ($log === true) {
                $this->helper->log(
                    $this->code,
                    sprintf('CyberSource Gateway error: %s', trim($exception->getMessage()))
                );
            }

            throw new \Magento\Framework\Exception\RuntimeException(
                __('CyberSource Gateway error: %1', trim($exception->getMessage())),
                $exception
            );
        } finally {
            $response = $this->sanitizeLog($this->soapClient->__getLastResponse());

            if ($this->config->isSandboxMode()) {
                $request = $this->sanitizeLog($this->soapClient->__getLastRequest());

                $this->helper->log(
                    $this->code,
                    $request . "\n" . $response,
                    true
                );
            }

            if ($log === true) {
                $this->helper->log($this->code, $response);
            }

            // Parse response into array for easier handling
            $this->lastResponse = $this->xmlToArray($this->soapClient->__getLastResponse());
            $this->helper->log(
                $this->code,
                json_encode($this->lastResponse),
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
    protected function xmlToArray($xml)
    {
        // Strip namespaces out of element keys
        $xml = preg_replace('/(<\/|<)[a-zA-Z]+:([a-zA-Z0-9]+[ =>\/])/', '$1$2', $xml);

        $array = parent::xmlToArray($xml);

        if (isset($array['Body']['replyMessage'])) {
            return $array['Body']['replyMessage'];
        }

        return $array;
    }

    /**
     * Mask certain values in the XML for secure logging purposes.
     *
     * @param string $string
     * @return string
     */
    protected function sanitizeLog($string)
    {
        $maskAll = ['cvNumber'];
        $maskFour = ['Password', 'accountNumber'];

        foreach ($maskAll as $val) {
            $string = preg_replace('#' . $val . '>(.+?)</(.+?):' . $val . '#', $val . '>XXX</$2:' . $val, $string);
        }

        foreach ($maskFour as $val) {
            $start = strpos($string, $val . '>');
            $end = strpos($string, '</', $start);
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
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var \Magento\Sales\Model\Order $order */
        $order  = $payment->getOrder();

        $purchaseTotals = $this->getOrderPurchaseTotals($payment, $order, $amount);

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setDeviceFingerprintID($this->config->getFingerprintSessionId($order->getQuoteId()));
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
        }

        $this->requestPayerAuthentication($payment, $request);

        $reply = $this->run($request);

        return $this->interpretTransaction($reply, $payment);
    }

    /**
     * Run a capture transaction for $amount with the given payment info
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount, $transactionId = null)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var \Magento\Sales\Model\Order $order */
        $order  = $payment->getOrder();

        $purchaseTotals = $this->getOrderPurchaseTotals($payment, $order, $amount);

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setDeviceFingerprintID($this->config->getFingerprintSessionId($order->getQuoteId()));
        $request->setItem($this->objectBuilder->getOrderItems($this->lineItems));
        $request->setPurchaseTotals($purchaseTotals);

        $transactionId = $transactionId !== null ? $transactionId : $this->getTransactionId();

        // If we don't have a transaction ID to capture, run a 'bundled' auth+capture; otherwise, prior-auth capture.
        if (empty($transactionId) || !$this->getHaveAuthorized()) {
            $this->captureInitBundledRequest($payment, $request);
            $this->requestPayerAuthentication($payment, $request);
        } else {
            $this->captureInitLinkedRequest($transactionId, $request);
        }

        $reply = $this->run($request);

        try {
            return $this->interpretTransaction($reply, $payment);
        } catch (\Exception $exception) {
            // Handle 'transaction not found' error (expired authorization).
            if ($exception->getCode() === 242) {
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
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
     * @return void
     */
    protected function captureInitBundledRequest(
        \Magento\Payment\Model\InfoInterface $payment,
        \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
    ) {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var \Magento\Sales\Model\Order $order */
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
        }
    }

    /**
     * Set linked-capture (prior-auth capture) parameters for a capture request.
     *
     * @param string $transactionId
     * @param \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
     * @return void
     */
    protected function captureInitLinkedRequest(
        $transactionId,
        \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
    ) {
        $ccCaptureService = $this->objectBuilder->getCaptureService();
        $ccCaptureService->setAuthRequestID($transactionId);
        $ccCaptureService->setAuthRequestToken($this->getParameter('auth_code') ?: null);

        $request->setCcCaptureService($ccCaptureService);
    }

    /**
     * Run a refund transaction for $amount with the given payment info
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount, $transactionId = null)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var \Magento\Sales\Model\Order $order */
        $order  = $payment->getOrder();

        $purchaseTotals = $this->objectBuilder->getPurchaseTotals($order->getBaseCurrencyCode(), $amount);
        if ($payment->getCreditmemo() instanceof \Magento\Sales\Api\Data\CreditmemoInterface) {
            if ($payment->getCreditmemo()->getTaxAmount()) {
                $purchaseTotals->setTaxAmount($payment->getCreditmemo()->getTaxAmount());
            }
            if ($payment->getCreditmemo()->getShippingAmount()) {
                $purchaseTotals->setShippingAmount($payment->getCreditmemo()->getShippingAmount());
            }
        }

        $ccCreditService = $this->objectBuilder->getCreditService(
            'internet',
            $transactionId !== null ? $transactionId : $this->getTransactionId()
        );

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcCreditService($ccCreditService);

        $reply = $this->run($request);

        try {
            return $this->interpretTransaction($reply, $payment);
        } catch (\Exception $exception) {
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
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment, $transactionId = null)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var \Magento\Sales\Model\Order $order */
        $order  = $payment->getOrder();

        $purchaseTotals = $this->objectBuilder->getPurchaseTotals(
            $order->getBaseCurrencyCode(),
            $order->getTotalDue()
        );

        $ccAuthReversalService = $this->objectBuilder->getAuthReversalService(
            $transactionId ?: $this->getTransactionId()
        );

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcAuthReversalService($ccAuthReversalService);

        $reply = $this->run($request);

        return $this->interpretTransaction($reply, $payment);
    }

    /**
     * Fetch a transaction status update
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function fraudUpdate(\Magento\Payment\Model\InfoInterface $payment, $transactionId)
    {
        /**
         * NB: We can only request up to 24 hours of data, which means 'fetch update' will only work if the decision
         * was made within 24 hours. But our hourly updater cron should mean that never comes up unless cron isn't
         * running at all. This just allows people to pull updates through immediately.
         */

        $reply = $this->restClient->get(
            '/reporting/v3/conversion-details',
            [
                'startTime' => date(Sanitizer::ISO_FORMAT, strtotime('-24 hour')),
                'endTime' => date(Sanitizer::ISO_FORMAT),
                'organizationId' => $this->config->getMerchantId(),
            ]
        );

        /** @var \ParadoxLabs\TokenBase\Model\Gateway\Response $response */
        $response = $this->responseFactory->create();
        $response->setData(['is_approved' => false, 'is_denied' => false]);

        $reply = json_decode($reply, JSON_OBJECT_AS_ARRAY);
        if (!empty($reply['conversionDetails'])) {
            foreach ($reply['conversionDetails'] as $change) {
                if ($change['requestId'] === $transactionId) {
                    $response->addData($this->flattenArray($change));

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

        return $response;
    }

    /**
     * Delete the given card token from CyberSource TMS.
     *
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
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
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function testConnection()
    {
        $request = $this->createRequest();
        $reply = $this->run($request, false);

        return $this->interpretTransaction($reply);
    }

    /**
     * Translate SOAP reply into a Magento-compatible transaction data object. Throw exception on any error cases.
     *
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api
     * @param \Magento\Payment\Model\InfoInterface|null $payment
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     * @throws \Magento\Framework\Exception\PaymentException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    protected function interpretTransaction(
        \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api,
        \Magento\Payment\Model\InfoInterface $payment = null
    ) {
        // NB: Temporal coupling, we assume interpretTransaction will always be run immediately after the transaction
        // it's intended to interpret. Otherwise lastResponse will be the wrong data.
        $data = $this->lastResponse;
        $data['transaction_id']       = $api->getRequestID();
        $data['response_code']        = $api->getReasonCode();
        $data['response_reason_code'] = $api->getReasonCode();
        $data['response_reason_text'] = $this->responseCodeSource->getMessage($api->getReasonCode());
        $data['auth_code']            = $api->getRequestToken(); // Not auth code, but it functions the same way.

        /** @var \ParadoxLabs\TokenBase\Model\Gateway\Response $response */
        $response = $this->responseFactory->create(['data' => $this->flattenArray($data)]);
        $response->setIsError($api->getDecision() === 'ERROR' || $api->getDecision() === 'REJECT');
        $response->setIsFraud($api->getDecision() === 'REVIEW');

        if ($payment !== null && $api->getReasonCode() === 475) {
            $this->payerAuthPersistor->savePayerAuthEnrollReply($payment, $api);
        }

        if (in_array($api->getDecision(), ['ERROR', 'REJECT'], true)) {
            $message = __('Transaction Failed: %1', __($response->getResponseReasonText()));

            // Don't log API test errors
            if ($payment !== null || $api->getReasonCode() !== 101) {
                $this->helper->log($this->code, $message . ' (' . $api->getReasonCode() . ')');
            }

            if ($api->getDecision() === 'REJECT') {
                throw new \Magento\Framework\Exception\PaymentException($message, null, $api->getReasonCode());
            }
            throw new \Magento\Framework\Exception\RuntimeException($message, null, $api->getReasonCode());
        }

        return $response;
    }

    /**
     * Turn multi-dimensional array into 1D, concatenating keys
     *
     * @param mixed $array
     * @param string|null $prefix
     * @return array
     * @see http://stackoverflow.com/a/9546215/2336164
     */
    protected function flattenArray($array, $prefix = null)
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result += $this->flattenArray($value, $prefix . $key . '.');
            } elseif (is_bool($value)) {
                $result[$prefix . $key] = $value ? '1' : '0';
            } else {
                $result[$prefix . $key] = $value;
            }
        }

        return $result;
    }

    /**
     * Get a PurchaseTotals amounts object for authorization or capture.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \Magento\Sales\Model\Order $order
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    protected function getOrderPurchaseTotals(
        \Magento\Payment\Model\InfoInterface $payment,
        \Magento\Sales\Model\Order $order,
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
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getTransactionOrigin()
    {
        $store  = $this->helper->getCurrentStore();

        return __('%1 (%2)', $store->getName(), $store->getBaseUrl());
    }

    /**
     * Add Payer Auth enrollment check or verification to the auth/capture, when relevant.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
     * @return void
     */
    protected function requestPayerAuthentication(
        \Magento\Payment\Model\InfoInterface $payment,
        \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
    ) {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        // If Payer Authentication isn't enabled, or we've already processed payment, don't ... run payer auth.
        // NB/Future: May need to enroll with prior-auth info in the prior payment case.
        if ($this->config->isPayerAuthEnabled() === false
            || $order->getTotalInvoiced() > 0) {
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
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function requestPayerAuthenticationValidate(
        \Magento\Payment\Model\InfoInterface $payment,
        \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
    ) {
        // Note: We unpack the JWT to confirm its signature and validity before passing it on.
        $decodedJWT = $this->payerAuthJWTEncoder->unpack(
            $payment->getAdditionalInformation('response_jwt')
        );

        $validateService = $this->objectBuilder->getPayerAuthValidateService(
            $decodedJWT['Payload']['Payment']['ProcessorTransactionId'],
            $payment->getAdditionalInformation('response_jwt')
        );

        $request->setPayerAuthValidateService($validateService);
    }

    /**
     * Add Payer Auth enrollment service to the auth/capture.
     *
     * This involves a substantial amount of context data on the user/card/order, which we hand off to a service class.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
     * @return void
     */
    protected function requestPayerAuthenticationEnroll(
        \Magento\Payment\Model\InfoInterface $payment,
        \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $request
    ) {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        $referenceId = $this->config->getFingerprintSessionId(
            $order->getQuoteId()
        );

        $enrollService = $this->objectBuilder->getPayerAuthEnrollService($referenceId);
        $this->payerAuthEnrollParams->populateEnrollmentService(
            $enrollService,
            $order,
            $this->getCard()
        );

        $request->setPayerAuthEnrollService($enrollService);
    }
}
