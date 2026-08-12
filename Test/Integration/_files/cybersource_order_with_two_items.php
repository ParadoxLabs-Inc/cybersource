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
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/second_product_simple.php');

$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$firstProduct = $productRepository->get('simple');
$secondProduct = $productRepository->get('simple2');

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
 * A brand-new, NOT-yet-tokenized card carrying a Unified Checkout transient token on the payment, so the
 * first authorize() takes the new-card place() path and the reply's TMS ids land on the card. Every
 * follow-on operation in the multi-capture lifecycle then runs off the vaulted ids.
 */
/** @var CardInterfaceFactory $cardFactory */
$cardFactory = $objectManager->get(CardInterfaceFactory::class);
/** @var CardRepositoryInterface $cardRepository */
$cardRepository = $objectManager->get(CardRepositoryInterface::class);

/** @var CardInterface $card */
$card = $cardFactory->create();
$card->setCustomerId(0);
$card->setCustomerEmail('two-item-card@example.com');
$card->setMethod('paradoxlabs_cybersource');
$card->setActive(1);
$card->setExpires(date('Y-m-d 23:59:59', strtotime('+2 years')));
$card = $cardRepository->save($card);

/** @var Payment $payment */
$payment = $objectManager->create(Payment::class);
$payment->setMethod('paradoxlabs_cybersource');
$payment->setData('tokenbase_id', (int)$card->getId());
$payment->setAdditionalInformation('transient_token', 'eyJhbGciOiJSUzI1NiJ9.integration.transient');

/**
 * TWO line items, each invoiceable on its own:
 *  - simple  qty 2 @ 12.00 = 24.00  (invoice #1)
 *  - simple2 qty 1 @ 10.00 = 10.00  (invoice #2, the remainder)
 * Grand total 34.00. The two subtotals are distinct so a test can tell which invoice drove which capture.
 */
/** @var OrderItem $firstItem */
$firstItem = $objectManager->create(OrderItem::class);
$firstItem->setProductId($firstProduct->getId())
    ->setQtyOrdered(2)
    ->setBasePrice(12)
    ->setPrice(12)
    ->setRowTotal(24)
    ->setBaseRowTotal(24)
    ->setRowTotalInclTax(24)
    ->setBaseRowTotalInclTax(24)
    ->setProductType('simple')
    ->setName($firstProduct->getName())
    ->setSku($firstProduct->getSku());

/** @var OrderItem $secondItem */
$secondItem = $objectManager->create(OrderItem::class);
$secondItem->setProductId($secondProduct->getId())
    ->setQtyOrdered(1)
    ->setBasePrice(10)
    ->setPrice(10)
    ->setRowTotal(10)
    ->setBaseRowTotal(10)
    ->setRowTotalInclTax(10)
    ->setBaseRowTotalInclTax(10)
    ->setProductType('simple')
    ->setName($secondProduct->getName())
    ->setSku($secondProduct->getSku());

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId('100000570')
    ->setState(Order::STATE_NEW)
    ->setStatus('pending')
    ->setSubtotal(34)
    ->setGrandTotal(34)
    ->setBaseSubtotal(34)
    ->setBaseGrandTotal(34)
    ->setOrderCurrencyCode('USD')
    ->setBaseCurrencyCode('USD')
    ->setCustomerIsGuest(true)
    ->setCustomerEmail('customer@example.com')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId())
    ->addItem($firstItem)
    ->addItem($secondItem)
    ->setPayment($payment);

/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
$orderRepository->save($order);
