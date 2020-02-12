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

/**
 * CyberSource API Gateway - custom built for perfection.
 */
class Gateway extends \ParadoxLabs\TokenBase\Model\AbstractGateway
{
    /**
     * @var string
     */
    protected $code = 'paradoxlabs_cybersource';

    /**
     * $fields defines validation for each API parameter or input.
     *
     * key => [
     *    'maxLength' => int,
     *    'noSymbols' => true|false,
     *    'charMask'  => (allowed characters in regex form),
     *    'enum'      => [ values ]
     * ]
     *
     * @var array
     */
    protected $fields = [
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
     * Constructor, yeah!
     *
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml
     * @param \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder $objectBuilder
     * @param \ParadoxLabs\CyberSource\Model\Source\ResponseCode $responseCodeSource
     * @param array $data
     */
    public function __construct(
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml,
        \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder $objectBuilder,
        \ParadoxLabs\CyberSource\Model\Source\ResponseCode $responseCodeSource,
        array $data = []
    ) {
        parent::__construct($helper, $xml, $responseFactory, $httpClientFactory, $data);

        $this->config = $config;
        $this->objectBuilder = $objectBuilder;
        $this->responseCodeSource = $responseCodeSource;
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
            // TODO: Get current store ID for these config lookups?
            $this->soapClient = $this->objectBuilder->getProcessor(
                $this->config->getSoapWsdl()
            );

            $this->soapClient->__setSoapHeaders(
                $this->objectBuilder->getSecurityHeader(
                    $this->config->getMerchantId(),
                    $this->config->getSoapTransactionKey()
                )
            );
        } catch (\SoapFault $exception) {
            $this->helper->log($this->code, trim($exception->getMessage()));
            throw new \Magento\Framework\Exception\RuntimeException(
                __('Server Error: Could not connect to CyberSource payment gateway.')
            );
        }

        return $this;
    }

    /**
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function createRequest()
    {
        $request = $this->objectBuilder->getRequest($this->config->getMerchantId());
        $request->setPartnerSolutionID($this->config->getSolutionId());
        $request->setClientLibrary($this->config->getClientName());
        $request->setClientLibraryVersion($this->config->getClientVersion());
        $request->setClientEnvironment('Magento 2');

        // Fields 1 and 2 have special meaning for certain processors, so skip them.
        $merchantDefinedData = $this->objectBuilder->getMerchantDefinedData([
            3 => $this->getTransactionOrigin(),
        ]);
        $request->setMerchantDefinedData($merchantDefinedData);

        return $request;
    }

    /**
     * @param \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $requestMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function run(\ParadoxLabs\CyberSource\Gateway\Api\RequestMessage $requestMessage)
    {
        if ($this->soapClient instanceof \ParadoxLabs\CyberSource\Gateway\Api\TransactionProcessor === false) {
            throw new \Magento\Framework\Exception\StateException(__('CyberSource gateway has not been initialized'));
        }

        try {
            $reply = $this->soapClient->runTransaction($requestMessage);
        } catch (\Exception $exception) {
            $this->helper->log(
                $this->code,
                sprintf('CyberSource Gateway error: %s', trim($exception->getMessage()))
            );

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

            $this->helper->log($this->code, $response);

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

        $purchaseTotals = $this->objectBuilder->getPurchaseTotals($order->getOrderCurrencyCode(), $amount);
        if ($this->getHaveAuthorized() !== true) {
            $purchaseTotals->setTaxAmount($order->getTaxAmount());
            $purchaseTotals->setShippingAmount($payment->getShippingAmount());
        }

        $commerceIndicator = $this->helper->getIsFrontend() ? 'internet' : 'moto';

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setBillTo($this->objectBuilder->getOrderBillTo($order));
        $request->setItem($this->objectBuilder->getOrderItems($this->lineItems));
        $request->setRecurringSubscriptionInfo($this->objectBuilder->getTokenInfo($this->getCard()));
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcAuthService($this->objectBuilder->getAuthService($commerceIndicator));

        if ((bool)$order->getIsVirtual() === false) {
            $request->setShipTo($this->objectBuilder->getOrderShipTo($order));
        }

        // TODO: Card code not being validated? Emailed Trevor 2/07
        if ($payment->hasData('cc_cid') && !empty($payment->getData('cc_cid'))) {
            $request->setCard($this->objectBuilder->getCardForCvv($payment->getData('cc_cid')));
        }

        $reply = $this->run($request);

        return $this->interpretTransaction($reply);
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

        $purchaseTotals = $this->objectBuilder->getPurchaseTotals($order->getOrderCurrencyCode(), $amount);
        if ($this->getHaveAuthorized() !== true) {
            $purchaseTotals->setTaxAmount($order->getTaxAmount());
            $purchaseTotals->setShippingAmount($payment->getShippingAmount());
        }

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setItem($this->objectBuilder->getOrderItems($this->lineItems));
        $request->setPurchaseTotals($purchaseTotals);

        $transactionId = $transactionId !== null ? $transactionId: $this->getTransactionId();
        $ccCaptureService = $this->objectBuilder->getCaptureService();
        if ($this->getHaveAuthorized() && !empty($transactionId)) {
            // TODO: Handle request tokens? cf. http://apps.cybersource.com/library/documentation/dev_guides/
            //  Getting_Started_SO/html/wwhelp/wwhimpl/js/html/wwhelp.htm#href=basics_SO.6.3.html#1096692
            $ccCaptureService->setAuthRequestID($transactionId);
        } else {
            // If we don't have a transaction ID to capture, run a 'bundled' auth+capture instead.
            // NB: Documentation says this varies by processor. Hoping this generic case works for all.
            $commerceIndicator = $this->helper->getIsFrontend() ? 'internet' : 'moto';
            $ccAuthService = $this->objectBuilder->getAuthService($commerceIndicator);
            $ccAuthService->setAuthType('AUTOCAPTURE');
            $request->setCcAuthService($ccAuthService);

            $request->setBillTo($this->objectBuilder->getOrderBillTo($order));
            $request->setRecurringSubscriptionInfo($this->objectBuilder->getTokenInfo($this->getCard()));
            if ((bool)$order->getIsVirtual() === false) {
                $request->setShipTo($this->objectBuilder->getOrderShipTo($order));
            }
            // TODO: Card code not being validated? Emailed Trevor 2/07
            if ($payment->hasData('cc_cid') && !empty($payment->getData('cc_cid'))) {
                $request->setCard($this->objectBuilder->getCardForCvv($payment->getData('cc_cid')));
            }
        }
        $request->setCcCaptureService($ccCaptureService);

        $reply = $this->run($request);

        try {
            return $this->interpretTransaction($reply);
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

        $purchaseTotals = $this->objectBuilder->getPurchaseTotals($order->getOrderCurrencyCode(), $amount);
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
            return $this->interpretTransaction($reply);
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
            $order->getOrderCurrencyCode(),
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

        return $this->interpretTransaction($reply);
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
        // TODO: Implement fraudUpdate() method.
        //  Need to use https://developer.cybersource.com/api/developer-guides/dita-txn-search-details-rest-api-dev-guide-102718/txn_details_api/retrieve_a_txn.html
        //  or https://developer.cybersource.com/api/developer-guides/dita-reporting-rest-api-dev-guide-102718/reporting_api/download_ondemand_detail_report.html
        //  to get transaction updates ... separate API, separate creds
    }

    /**
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function deleteCard()
    {
        // TODO: Test this! NB: API indicates token can't be removed until all instrs referencing it are. Problematic?
        $info = $this->objectBuilder->getTokenInfo($this->getCard());

        $request = $this->createRequest();
        $request->setRecurringSubscriptionInfo($info);
        $request->setPaySubscriptionDeleteService($this->objectBuilder->getPaySubscriptionDeleteService());

        $reply = $this->run($request);

        return $this->interpretTransaction($reply);
    }

    /**
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    protected function interpretTransaction(\ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api)
    {
        // NB: Temporal coupling, we assume interpretTransaction will always be run immediately after the transaction
        // it's intended to interpret. Otherwise lastResponse will be the wrong data.
        $data = $this->lastResponse;
        $data['transaction_id']       = $api->getRequestID();
        $data['response_code']        = $api->getReasonCode();
        $data['response_reason_code'] = $api->getReasonCode();
        $data['response_reason_text'] = $this->responseCodeSource->getMessage($api->getReasonCode());

        /** @var \ParadoxLabs\TokenBase\Model\Gateway\Response $response */
        $response = $this->responseFactory->create(['data' => $data]);
        $response->setIsError($api->getDecision() === 'ERROR' || $api->getDecision() === 'REJECT');
        $response->setIsFraud($api->getDecision() === 'REVIEW');

        if ($api->getDecision() === 'ERROR') {
            throw new \Magento\Framework\Exception\RuntimeException(
                __('Transaction Failed: %1', __($response->getResponseReasonText())),
                null,
                $api->getReasonCode()
            );
        }
        if ($api->getDecision() === 'REJECT') {
            throw new \Magento\Framework\Exception\PaymentException(
                __('Transaction Failed: %1', __($response->getResponseReasonText())),
                null,
                $api->getReasonCode()
            );
        }

        return $response;
    }

    /**
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getTransactionOrigin()
    {
        $store  = $this->helper->getCurrentStore();

        return __('%1 (%2)', $store->getName(), $store->getBaseUrl());
    }
}
