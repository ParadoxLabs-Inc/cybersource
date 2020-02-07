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
    protected $fields = [];

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
        // TODO: SolutionID
        // TODO: Client library info
        // TODO: site URL?

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

        $purchaseTotals = new Api\PurchaseTotals();
        $purchaseTotals->setCurrency(strtoupper($order->getOrderCurrencyCode())); // TODO: Verify currency handling
        $purchaseTotals->setGrandTotalAmount($amount);

        if ($this->getHaveAuthorized() !== true) {
            $purchaseTotals->setTaxAmount($order->getTaxAmount());
            $purchaseTotals->setShippingAmount($payment->getShippingAmount());
        }

        $ccAuthService = new Api\CCAuthService('true');
        // TODO: Make this correct based on source ?? internet, or moto for admin/followup
        $ccAuthService->setCommerceIndicator('internet');

        $merchantDefinedData = new Api\MerchantDefinedData();
        // Fields 1 and 2 have special meaning for certain processors, so skip them.
        $store = $this->helper->getCurrentStore();
        $merchantDefinedData->setField3(substr(__('%1 (%2)', $store->getName(), $store->getBaseUrl()), 0, 255));

        $request = $this->createRequest();
        $request->setMerchantReferenceCode($order->getIncrementId());
        $request->setBillTo($this->objectBuilder->getOrderBillTo($order));
        $request->setItem($this->objectBuilder->getOrderItems($this->lineItems));
        $request->setRecurringSubscriptionInfo($this->objectBuilder->getTokenInfo($this->getCard()));
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcAuthService($ccAuthService);
        $request->setMerchantDefinedData($merchantDefinedData);

        if ((bool)$order->getIsVirtual() === false) {
            $request->setShipTo($this->objectBuilder->getOrderShipTo($order));
        }

        // TODO: Card code not being validated?
        if ($payment->hasData('cc_cid') && !empty($payment->getData('cc_cid'))) {
            $card = new Api\Card();
            $card->setCvNumber($payment->getData('cc_cid'));
            $request->setCard($card);
        }

        $result = $this->run($request);
        // TODO: Handle response
        // TODO: Split all the above objects out

        return $this->interpretTransaction($result);
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
        // TODO: Implement capture() method.
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
        // TODO: Implement refund() method.
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
        // TODO: Implement void() method.
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
    }

    /**
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteCard()
    {
        $info = new Api\RecurringSubscriptionInfo();
        $info->setSubscriptionID($this->getCard()->getPaymentId());

        $request = $this->createRequest();
        $request->setRecurringSubscriptionInfo($info);
        $request->setPaySubscriptionDeleteService(
            new Api\PaySubscriptionDeleteService('true')
        );

        return $this->run($request);
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
        $response = $this->responseFactory->create();
        $response->setData($data);
        $response->setIsError($api->getDecision() === 'DECLINE'); // TODO
        $response->setIsFraud($api->getDecision() === 'REVIEW'); // TODO

        // TODO: Error handling / payment exceptions

        return $response;
    }
}
