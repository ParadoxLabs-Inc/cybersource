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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\PaymentException;
use ParadoxLabs\CyberSource\Gateway\Api;

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
     * @var string
     */
    protected $endpointLive = 'https://secureacceptance.cybersource.com';

    /**
     * @var string
     */
    protected $endpointTest = 'https://testsecureacceptance.cybersource.com';

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
     * @var Api\TransactionProcessor
     */
    protected $soapClient;

    /**
     * @var \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder
     */
    protected $objectBuilder;

    /**
     * Constructor, yeah!
     *
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml
     * @param \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder $objectBuilder
     * @param array $data
     */
    public function __construct(
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml,
        \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        Api\ObjectBuilder $objectBuilder,
        array $data = []
    ) {
        parent::__construct($helper, $xml, $responseFactory, $httpClientFactory, $data);

        $this->config = $config;
        $this->objectBuilder = $objectBuilder;
    }

    /**
     * Initialize the gateway. Input is taken as an array for greater flexibility.
     *
     * @param array $parameters
     * @return $this
     */
    public function init(array $parameters)
    {
        $soapOptions = [
            'encoding' => 'utf-8',
            'exceptions' => true,
            'connection_timeout' => 3, // TODO: make this configurable?
        ];

        if ($this->config->isSandboxMode()) {
            $soapOptions['cache_wsdl'] = WSDL_CACHE_NONE;
            $soapOptions['trace'] = true;
        }

        // TODO: Get current store ID for these?
        $this->soapClient = new Api\TransactionProcessor(
            $soapOptions,
            $this->config->getSoapWsdl()
        );

        $this->soapClient->__setSoapHeaders(
            new Api\WsseHeader(
                '',
                '',
                null,
                true,
                null,
                $this->config->getMerchantId(),
                $this->config->getSoapTransactionKey()
            )
        );

        return $this;
    }

    /**
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function createRequest()
    {
        $request = new Api\RequestMessage();
        $request->setMerchantID($this->config->getMerchantId());
        $request->setMerchantReferenceCode(uniqid('', true));
        $request->setPartnerSolutionID($this->config->getSolutionId());
        $request->setClientLibrary($this->config->getClientName());
        $request->setClientLibraryVersion($this->config->getClientVersion());
        $request->setClientEnvironment('Magento 2');

        // TODO: Verify this comes out the same as setting Field3
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
    public function run(Api\RequestMessage $requestMessage)
    {
        if ($this->soapClient instanceof Api\TransactionProcessor === false) {
            throw new \Magento\Framework\Exception\StateException(__('CyberSource gateway has not been initialized'));
        }

        // TODO: Error handling
        try {
            $reply = $this->soapClient->runTransaction($requestMessage);
        } catch (\Exception $exception) {
            $this->helper->log(
                $this->code,
                $exception->getMessage()
            );

            throw new LocalizedException(__('CyberSource Gateway error: %1', $exception->getMessage()), $exception);
        } finally {
            // TODO: Sanitize logs of keys/CVVs
            if ($this->config->isSandboxMode()) {
                $this->helper->log(
                    $this->code,
                    str_replace("\n", '', $this->soapClient->__getLastRequest()) . "\n"
                    . str_replace("\n", '', $this->soapClient->__getLastResponse()),
                    true
                );
            }

            $this->helper->log(
                $this->code,
                str_replace("\n", '', $this->soapClient->__getLastResponse())
            );

            // TODO: This probably isn't a good way to do this.
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

        // TODO: Handle response
        $response = $this->interpretTransaction($reply);

        return $response;
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

        $transactionId = $transactionId ?: $this->getTransactionId();
        $ccCaptureService = new Api\CCCaptureService('true');
        if ($this->getHaveAuthorized() && !empty($transactionId)) {
            $ccCaptureService->setAuthRequestID($transactionId);
        } else {
            // If we don't have a transaction ID to capture, run a 'bundled' auth+capture instead.
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

        // TODO: Handle no-such-auth/expired-auth error

        // TODO: Handle response
        $response = $this->interpretTransaction($reply);

        return $response;
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

        $commerceIndicator = $this->helper->getIsFrontend() ? 'internet' : 'moto';

        $ccCreditService = new Api\CCCreditService('true');
        $ccCreditService->setCommerceIndicator($commerceIndicator);
        $ccCreditService->setCaptureRequestID($transactionId ?: $this->getTransactionId());

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcCreditService($ccCreditService);

        $reply = $this->run($request);

        // TODO: Handle not-settled-yet error (auto void IF full amount AND do an auth reversal)
        // TODO: Handle past-refund-period error (run unlinked refund, or error out?)

        // TODO: Handle response
        $response = $this->interpretTransaction($reply);

        return $response;
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

        $ccAuthReversalService = new Api\CCAuthReversalService('true');
        $ccAuthReversalService->setAuthRequestID($transactionId ?: $this->getTransactionId());

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcAuthReversalService($ccAuthReversalService);

        $reply = $this->run($request);

        // TODO: Handle response
        $response = $this->interpretTransaction($reply);

        return $response;
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
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteCard()
    {
        // TODO: Test this! NB: API indicates token can't be removed until all instrs referencing it are. Problematic?
        $info = new Api\RecurringSubscriptionInfo();
        $info->setSubscriptionID($this->getCard()->getPaymentId());

        $request = $this->createRequest();
        $request->setRecurringSubscriptionInfo($info);
        $request->setPaySubscriptionDeleteService(
            new Api\PaySubscriptionDeleteService('true')
        );

        $reply = $this->run($request);

        return $this->interpretTransaction($reply);
    }

    /**
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    protected function interpretTransaction(Api\ReplyMessage $api)
    {
        // TODO: Can we convert the entire $api object to array? This isn't great.
        $data = $this->lastResponse;
        $data['transaction_id'] = $data['requestID'];

        /** @var \ParadoxLabs\TokenBase\Model\Gateway\Response $response */
        $response = $this->responseFactory->create(['data' => $data]);
        $response->setIsError($api->getDecision() === 'ERROR' || $api->getDecision() === 'REJECT');
        $response->setIsFraud($api->getDecision() === 'REVIEW');

        // TODO: Error handling / payment exceptions

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
