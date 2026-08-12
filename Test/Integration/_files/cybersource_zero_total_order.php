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

$billingAddress = $objectManager->create(OrderAddress::class, ['data' => $addressData]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

/**
 * Same shape as cybersource_order_with_new_card.php -- a brand-new, NOT-yet-tokenized card (no
 * profileId, no paymentId, no instrument_identifier) whose order payment carries a Unified Checkout
 * transient token -- but the order total is ZERO.
 *
 * A $0 order is a real, shippable checkout: 100%-off coupon, free trial, free replacement, or a
 * subscription whose first period is comped and which will rebill later off the vaulted card. The card
 * must still end up tokenized. This fixture exists to pin what actually happens instead.
 */
/** @var CardInterfaceFactory $cardFactory */
$cardFactory = $objectManager->get(CardInterfaceFactory::class);
/** @var CardRepositoryInterface $cardRepository */
$cardRepository = $objectManager->get(CardRepositoryInterface::class);

/** @var CardInterface $card */
$card = $cardFactory->create();
$card->setCustomerId(0);
$card->setCustomerEmail('zero-total-card@example.com');
$card->setMethod('paradoxlabs_cybersource');
$card->setActive(1);
$card->setExpires(date('Y-m-d 23:59:59', strtotime('+2 years')));
$card = $cardRepository->save($card);

/** @var Payment $payment */
$payment = $objectManager->create(Payment::class);
$payment->setMethod('paradoxlabs_cybersource');
$payment->setData('tokenbase_id', (int)$card->getId());
$payment->setAdditionalInformation('transient_token', 'eyJhbGciOiJSUzI1NiJ9.integration.transient');

// Qty 1 at 0.00 -- e.g. a 100%-off promotion. Invoiceable, so the capture path can be driven too.
/** @var OrderItem $orderItem */
$orderItem = $objectManager->create(OrderItem::class);
$orderItem->setProductId($product->getId())
    ->setQtyOrdered(1)
    ->setBasePrice(0)
    ->setPrice(0)
    ->setRowTotal(0)
    ->setBaseRowTotal(0)
    ->setRowTotalInclTax(0)
    ->setBaseRowTotalInclTax(0)
    ->setProductType('simple')
    ->setName($product->getName())
    ->setSku($product->getSku());

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId('100000575')
    ->setState(Order::STATE_NEW)
    ->setStatus('pending')
    ->setSubtotal(0)
    ->setGrandTotal(0)
    ->setBaseSubtotal(0)
    ->setBaseGrandTotal(0)
    ->setOrderCurrencyCode('USD')
    ->setBaseCurrencyCode('USD')
    ->setCustomerIsGuest(true)
    ->setCustomerEmail('customer@example.com')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId())
    ->addItem($orderItem)
    ->setPayment($payment);

/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
$orderRepository->save($order);
