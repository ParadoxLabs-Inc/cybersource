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
     * Constructor, yeah!
     *
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml
     * @param \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param array $data
     */
    public function __construct(
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Model\Gateway\Xml $xml,
        \ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory $responseFactory,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        array $data = []
    ) {
        parent::__construct($helper, $xml, $responseFactory, $httpClientFactory, $data);

        $this->config = $config;
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
        return $this->soapClient->runTransaction($requestMessage);
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
        $order = $payment->getOrder();

        /** @var \Magento\Sales\Model\Order\Address $billingAddress */
        $billingAddress = $order->getBillingAddress();

        $billTo = new Api\BillTo();
        $billTo->setFirstName($billingAddress->getFirstname());
        $billTo->setLastName($billingAddress->getLastname());
        $billTo->setStreet1($billingAddress->getStreetLine(1));
        $billTo->setStreet2($billingAddress->getStreetLine(2));
        $billTo->setCity($billingAddress->getCity());
        $billTo->setState($billingAddress->getRegionCode());
        $billTo->setPostalCode($billingAddress->getPostcode());
        $billTo->setCountry($billingAddress->getCountryId());
        $billTo->setPhoneNumber($billingAddress->getTelephone());
        $billTo->setEmail($billingAddress->getEmail());
        $billTo->setIpAddress($order->getRemoteIp());

        if ((bool)$order->getIsVirtual() === false) {
            /** @var \Magento\Sales\Model\Order\Address $shippingAddress */
            $shippingAddress = $order->getShippingAddress();

            $shipTo = new Api\ShipTo();
            $shipTo->setFirstName($shippingAddress->getFirstname());
            $shipTo->setLastName($shippingAddress->getLastname());
            $shipTo->setStreet1($shippingAddress->getStreetLine(1));
            $shipTo->setStreet2($shippingAddress->getStreetLine(2));
            $shipTo->setCity($shippingAddress->getCity());
            $shipTo->setState($shippingAddress->getRegionCode());
            $shipTo->setPostalCode($shippingAddress->getPostcode());
            $shipTo->setCountry($shippingAddress->getCountryId());
            $shipTo->setPhoneNumber($shippingAddress->getTelephone());
        }

        $items = [];
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        foreach ($order->getAllVisibleItems() as $k => $orderItem) {
            $soapItem = new Api\Item($k);
            $soapItem->setUnitPrice($orderItem->getPrice());
            $soapItem->setQuantity($orderItem->getQtyOrdered());
            $soapItem->setProductSKU($orderItem->getSku());
            $soapItem->setProductName($orderItem->getName());
            $soapItem->setTaxAmount($orderItem->getTaxAmount()); // TODO: Verify unit or row amount on both sides
            $soapItem->setDiscountAmount($orderItem->getDiscountAmount()); // TODO: Verify unit or row amount
            $items[] = $soapItem;
        }

        $tokenInfo = new Api\RecurringSubscriptionInfo();
        $tokenInfo->setSubscriptionID($this->getCard()->getPaymentId());

        $purchaseTotals = new Api\PurchaseTotals();
        $purchaseTotals->setCurrency(strtoupper($order->getOrderCurrencyCode())); // TODO: Verify currency handling
        $purchaseTotals->setGrandTotalAmount($order->getGrandTotal());

        $ccAuthService = new Api\CCAuthService('true');
        $ccAuthService->setCommerceIndicator('moto'); // TODO: Make this correct based on source ??

        $request = $this->createRequest();
        $request->setBillTo($billTo);
        $request->setShipTo($shipTo);
        $request->setItem($items);
        $request->setRecurringSubscriptionInfo($tokenInfo);
        $request->setPurchaseTotals($purchaseTotals);
        $request->setCcAuthService($ccAuthService);

        $response = $this->run($request);
        // TODO: Handle response
        // TODO: Split all the above objects out

        return new \ParadoxLabs\TokenBase\Model\Gateway\Response([]);
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
            new Api\PaySubscriptionDeleteService(true) // TODO: Fix missing class
        );

        return $this->run($request);
    }
}
