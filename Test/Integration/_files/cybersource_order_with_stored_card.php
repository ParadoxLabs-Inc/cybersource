<?php declare(strict_types=1);
/**
 * ParadoxLabs, Inc.
 * https://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  https://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     https://store.paradoxlabs.com/license.html
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');

$addressData = [
    'firstname' => 'John',
    'lastname' => 'Smith',
    'street' => "6161 Test Street\n",
    'city' => 'Los Angeles',
    'region' => 'California',
    'region_id' => '12',
    'postcode' => '90210',
    'country_id' => 'US',
    'telephone' => '3105551234',
    'email' => 'customer@example.com',
];

/**
 * Persist a fully-tokenized Unified Checkout card: the three TMS ids the stored-card (vault / MIT) auth
 * reads back per the D5 mapping — profileId = TMS customer, paymentId = TMS paymentInstrument (the MIT
 * key), additional[instrument_identifier] = TMS instrumentIdentifier. customer_id 0 (guest) is
 * intentional: AbstractMethod::loadAndSetCard accepts an ownerless card for any order.
 */
/** @var CardInterfaceFactory $cardFactory */
$cardFactory = $objectManager->get(CardInterfaceFactory::class);
/** @var CardRepositoryInterface $cardRepository */
$cardRepository = $objectManager->get(CardRepositoryInterface::class);

/** @var CardInterface $card */
$card = $cardFactory->create();
$card->setCustomerId(0);
$card->setCustomerEmail('stored-card@example.com');
$card->setMethod('paradoxlabs_cybersource');
$card->setProfileId('C123');
$card->setPaymentId('P456');
$card->setActive(1);
$card->setExpires(date('Y-m-d 23:59:59', strtotime('+2 years')));
$card->setAdditional('instrument_identifier', 'I789');
$card->setAdditional('fingerprint', 'I789');
$card->setAdditional('cc_type', 'VI');
$card->setAdditional('cc_last4', '1111');
$card->setAdditional('cc_exp_month', '09');
$card->setAdditional('cc_exp_year', '2029');
$card = $cardRepository->save($card);

$storeId = $objectManager->get(StoreManagerInterface::class)->getStore()->getId();

/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);

/**
 * Two orders share the one stored card, so a test can run two CONSECUTIVE stored-card charges against
 * the same vaulted token (the subscription-rebill shape that defect D1 breaks).
 */
foreach (['100000560', '100000561'] as $incrementId) {
    $billingAddress = $objectManager->create(OrderAddress::class, ['data' => $addressData]);
    $billingAddress->setAddressType('billing');

    $shippingAddress = clone $billingAddress;
    $shippingAddress->setId(null)->setAddressType('shipping');

    /** @var Payment $payment */
    $payment = $objectManager->create(Payment::class);
    $payment->setMethod('paradoxlabs_cybersource');
    $payment->setData('tokenbase_id', (int)$card->getId());

    /** @var OrderItem $orderItem */
    $orderItem = $objectManager->create(OrderItem::class);
    $orderItem->setProductId($product->getId())
        ->setQtyOrdered(1)
        ->setBasePrice(24)
        ->setPrice(24)
        ->setRowTotal(24)
        ->setBaseRowTotal(24)
        ->setRowTotalInclTax(24)
        ->setBaseRowTotalInclTax(24)
        ->setProductType('simple')
        ->setName($product->getName())
        ->setSku($product->getSku());

    /** @var Order $order */
    $order = $objectManager->create(Order::class);
    $order->setIncrementId($incrementId)
        ->setState(Order::STATE_NEW)
        ->setStatus('pending')
        ->setSubtotal(24)
        ->setGrandTotal(24)
        ->setBaseSubtotal(24)
        ->setBaseGrandTotal(24)
        ->setOrderCurrencyCode('USD')
        ->setBaseCurrencyCode('USD')
        ->setCustomerIsGuest(true)
        ->setCustomerEmail('customer@example.com')
        ->setBillingAddress($billingAddress)
        ->setShippingAddress($shippingAddress)
        ->setStoreId($storeId)
        ->addItem($orderItem)
        ->setPayment($payment);

    $orderRepository->save($order);
}
