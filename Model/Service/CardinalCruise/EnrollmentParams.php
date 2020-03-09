<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

/**
 * EnrollmentParams Class
 */
class EnrollmentParams
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    protected $helper;

    /**
     * @var \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory
     */
    protected $cardCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $newsletterCollectionFactory;

    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    protected $addressRegistry;

    /**
     * EnrollmentParams constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface $orderCollectionFactory
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $newsletterCollectionFactory
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface $orderCollectionFactory,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $newsletterCollectionFactory,
        \Magento\Customer\Model\AddressRegistry $addressRegistry
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->cardCollectionFactory = $cardCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->newsletterCollectionFactory = $newsletterCollectionFactory;
        $this->addressRegistry = $addressRegistry;
    }

    /**
     * @param \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService $enrollService
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return void
     */
    public function populateEnrollmentService(
        \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService $enrollService,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
    ) {
        $enrollService->setAccountPurchases($this->get6moPurchasesCount($card));
        $enrollService->setAuthenticationIndicator($this->getAuthIndicator($order));
        $enrollService->setDefaultCard($this->getIsCardDefault($card));
        $enrollService->setDeviceChannel('Browser');
        $enrollService->setHttpAccept($this->request->getHeader('accept'));
        $enrollService->setHttpUserAgent($this->request->getHeader('user-agent'));
        $enrollService->setMarketingOptIn($this->getIsNewsletterSubscribed($order));
        $enrollService->setMerchantNewCustomer($this->getIsNewCustomer($order));
        $enrollService->setMerchantURL($this->getSiteUrl());
        $enrollService->setMobilePhone($order->getBillingAddress()->getTelephone());
        $enrollService->setPaymentAccountDate($this->getCardAddedDate($card));
        $enrollService->setShipAddressUsageDate($this->getShippingAddressAddDate($order));
        $enrollService->setTotalOffersCount($this->getOrderItemsCount($order));
        $enrollService->setTransactionCountDay($this->getOrderCountLastDay($order));
        $enrollService->setTransactionCountYear($this->getOrderCountLastYear($order));
        $enrollService->setTransactionMode('S');

        /**
         * NB: Skipping prior authentication fields as we want it to reauthenticate on stored card reuse if
         * the bank deems that appropriate. Security measure.
         *  PriorAuthenticationData
         *  PriorAuthenticationMethod
         *  PriorAuthenticationReferenceID
         *  PriorAuthenticationTime
         */
    }

    /**
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return int
     */
    protected function get6moPurchasesCount(\ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
    {
        // Don't bother lookup for guests or cards added within 15 min
        if ($card->getCustomerId() < 1 || strtotime($card->getCreatedAt()) - 900 > time()) {
            return 0;
        }

        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->join(
            [
                'payment' => $orderCollection->getResource()->getTable('sales_order_payment'),
            ],
            'payment.parent_id=main_table.entity_id',
            'tokenbase_id'
        );

        $orderCollection->addFieldToFilter('tokenbase_id', $card->getId());
        $orderCollection->addFieldToFilter('created_at', ['gt' => date('Y-m-d H:i:s', strtotime('-6 month'))]);

        return $orderCollection->getSize();
    }

    /**
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return string
     */
    protected function getIsCardDefault(\ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
    {
        if ($card->getCustomerId() < 1 || (bool)$card->getActive() === false) {
            return 'false';
        }

        $cardCollection = $this->cardCollectionFactory->create();
        $cardCollection->addFieldToFilter('active', 1);
        $cardCollection->addFieldToFilter('customer_id', $card->getCustomerId());
        $cardCollection->setOrder('created_at', 'desc');

        /** @var \ParadoxLabs\TokenBase\Model\Card $latestCard */
        $latestCard = $cardCollection->getFirstItem();

        if ($latestCard->getId() === $card->getId()) {
            return 'true';
        }

        return 'false';
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    protected function getIsNewsletterSubscribed(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $subscribers = $this->newsletterCollectionFactory->create();
        $subscribers->addFieldToFilter('subscriber_email', $order->getCustomerEmail());
        $subscribers->addFieldToFilter('subscriber_status', 1);

        if ($subscribers->getSize() > 0) {
            return 'true';
        }

        return 'false';
    }

    /**
     * @return string
     */
    protected function getSiteUrl()
    {
        return $this->helper->getCurrentStore()->getBaseUrl();
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string|null
     */
    protected function getShippingAddressAddDate(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order $order */
        if ((bool)$order->getIsVirtual() === true
            || $order->getCustomerId() < 1
            || $order->getShippingAddress()->getCustomerAddressId() < 1) {
            return null;
        }

        $address = $this->addressRegistry->retrieve(
            $order->getShippingAddress()->getCustomerAddressId()
        );

        return date('Ymd', strtotime($address->getData('created_at')));
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    protected function getOrderCountLastDay(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        if ($order->getCustomerId() < 1) {
            return 0;
        }

        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('customer_id', $order->getCustomerId());
        $orderCollection->addFieldToFilter('created_at', ['gt' => date('Y-m-d H:i:s', strtotime('-1 day'))]);

        return $orderCollection->getSize();
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    protected function getOrderCountLastYear(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        if ($order->getCustomerId() < 1) {
            return 0;
        }

        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('customer_id', $order->getCustomerId());
        $orderCollection->addFieldToFilter('created_at', ['gt' => date('Y-m-d H:i:s', strtotime('-1 year'))]);

        return $orderCollection->getSize();
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    protected function getIsNewCustomer(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $order->getCustomerId() > 0 ? 'false' : 'true';
    }

    /**
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return false|string
     */
    protected function getCardAddedDate(\ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
    {
        return date('Ymd', strtotime($card->getCreatedAt()));
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return mixed
     */
    protected function getOrderItemsCount(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order $order */
        return count($order->getAllVisibleItems());
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    protected function getAuthIndicator(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $order->getPayment();

        $isSubscription = (bool)$payment->getAdditionalInformation('is_subscription_generated');

        return $isSubscription ? '02' : '01';
    }
}
