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

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

use ParadoxLabs\TokenBase\Model\Card;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\App\RequestInterface;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Data;

class EnrollmentParams
{
    /**
     * EnrollmentParams constructor.
     *
     * @param RequestInterface $request
     * @param Data $helper
     * @param \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory
     * @param CollectionFactoryInterface $orderCollectionFactory
     * @param CollectionFactory $newsletterCollectionFactory
     * @param AddressRegistry $addressRegistry
     */
    public function __construct(
        protected readonly RequestInterface $request,
        protected readonly Data $helper,
        protected readonly \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory,
        protected readonly CollectionFactoryInterface $orderCollectionFactory,
        protected readonly CollectionFactory $newsletterCollectionFactory,
        protected readonly AddressRegistry $addressRegistry
    ) {
    }

    /**
     * Add user/order/card context data to the Payer Authentication enrollment service request
     *
     * All of this data is used to determine whether the user must perform 3D Secure authentication for the transaction.
     *
     * @param PayerAuthEnrollService $enrollService
     * @param OrderInterface $order
     * @param CardInterface $card
     * @return void
     */
    public function populateEnrollmentService(
        PayerAuthEnrollService $enrollService,
        OrderInterface $order,
        CardInterface $card
    ) {
        $enrollService->setAccountPurchases($this->get6moPurchasesCount($card));
        // NB: SOAP interface does not output this string value correctly; strips leading 0.
        // $enrollService->setAuthenticationIndicator($this->getAuthIndicator($order));
        $enrollService->setDefaultCard($this->getIsCardDefault($card));
        $enrollService->setDeviceChannel('Browser');
        $enrollService->setHttpAccept($this->request->getHeader('accept'));
        $enrollService->setHttpUserAgent($this->request->getHeader('user-agent'));
        $enrollService->setMarketingOptIn($this->getIsNewsletterSubscribed($order));
        $enrollService->setMerchantNewCustomer($this->getIsNewCustomer($order));
        $enrollService->setMerchantURL($this->getSiteUrl());
        $enrollService->setMobilePhone($order->getBillingAddress()->getTelephone());
        $enrollService->setPaymentAccountDate($this->getCardAddedDate($card));
        $enrollService->setTotalOffersCount($this->getOrderItemsCount($order));
        $enrollService->setTransactionCountDay($this->getOrderCountLastDay($order));
        $enrollService->setTransactionCountYear($this->getOrderCountLastYear($order));
        $enrollService->setTransactionMode('S');

        if ($card->getAdditional('cc_type') === 'DI') {
            $enrollService->setShipAddressUsageDate($this->getShippingAddressAddDate($order));
        }
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
     * Get the number of purchases with the given card in the last 6 months
     *
     * @param CardInterface $card
     * @return int
     */
    protected function get6moPurchasesCount(CardInterface $card)
    {
        // Don't bother lookup for guests or cards added within 15 min
        if ($card->getCustomerId() < 1 || strtotime((string)$card->getCreatedAt()) - 900 > time()) {
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
     * Get whether the card is the user's default
     *
     * @param CardInterface $card
     * @return string
     */
    protected function getIsCardDefault(CardInterface $card)
    {
        if ($card->getCustomerId() < 1 || (bool)$card->getActive() === false) {
            return 'false';
        }

        $cardCollection = $this->cardCollectionFactory->create();
        $cardCollection->addFieldToFilter('active', 1);
        $cardCollection->addFieldToFilter('customer_id', $card->getCustomerId());
        $cardCollection->setOrder('created_at', 'desc');

        /** @var Card $latestCard */
        $latestCard = $cardCollection->getFirstItem();

        if ($latestCard->getId() === $card->getId()) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Get whether the user is subscribed to the newsletter
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getIsNewsletterSubscribed(OrderInterface $order)
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
     * Get the merchant site URL
     *
     * @return string
     */
    protected function getSiteUrl()
    {
        return $this->helper->getCurrentStore()->getBaseUrl();
    }

    /**
     * Get the date the shipping address was added (if relevant)
     *
     * @param OrderInterface $order
     * @return string|null
     */
    protected function getShippingAddressAddDate(OrderInterface $order)
    {
        /** @var Order $order */
        if ((bool)$order->getIsVirtual() === true) {
            return null;
        }

        // Guest order
        if ($order->getCustomerId() < 1) {
            return '-1';
        }

        // First address use
        if ($order->getShippingAddress()->getCustomerAddressId() < 1) {
            return '0';
        }

        // Stored address -> added date
        $address = $this->addressRegistry->retrieve(
            $order->getShippingAddress()->getCustomerAddressId()
        );

        return date('Ymd', strtotime((string)$address->getData('created_at')));
    }

    /**
     * Get the number of orders placed by the user in the last 24 hours
     *
     * @param OrderInterface $order
     * @return int
     */
    protected function getOrderCountLastDay(OrderInterface $order)
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
     * Get the number of orders placed by the user in the last year
     *
     * @param OrderInterface $order
     * @return int
     */
    protected function getOrderCountLastYear(OrderInterface $order)
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
     * Get whether the order is being placed by a new customer
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getIsNewCustomer(OrderInterface $order)
    {
        return $order->getCustomerId() > 0 ? 'false' : 'true';
    }

    /**
     * Get the date the payment card was added
     *
     * @param CardInterface $card
     * @return false|string
     */
    protected function getCardAddedDate(CardInterface $card)
    {
        return date('Ymd', strtotime((string)$card->getCreatedAt()));
    }

    /**
     * Get the number if line items in the order
     *
     * @param OrderInterface $order
     * @return mixed
     */
    protected function getOrderItemsCount(OrderInterface $order)
    {
        /** @var Order $order */
        return count($order->getAllVisibleItems());
    }

    /**
     * Get the order auth indicator (transaction type/source)
     *
     * @param OrderInterface $order
     * @return string
     */
    protected function getAuthIndicator(OrderInterface $order)
    {
        /** @var Payment $payment */
        $payment = $order->getPayment();

        $isSubscription = (bool)$payment->getAdditionalInformation('is_subscription_generated');

        return $isSubscription ? '02' : '01';
    }
}
