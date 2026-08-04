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

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');

/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
$storeId = $objectManager->get(StoreManagerInterface::class)->getStore()->getId();

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
 * CyberSource orders that have already settled one way or the other. Deliberately no payment_review
 * state anywhere: this is the "nothing left to resolve" baseline the cron must recognise and skip on.
 */
$makeOrder = static function (
    string $incrementId,
    string $state,
    string $status,
    string $txnId
) use ($objectManager, $orderRepository, $product, $addressData, $storeId): Order {
    $billingAddress = $objectManager->create(OrderAddress::class, ['data' => $addressData]);
    $billingAddress->setAddressType('billing');

    $shippingAddress = clone $billingAddress;
    $shippingAddress->setId(null)->setAddressType('shipping');

    /** @var Payment $payment */
    $payment = $objectManager->create(Payment::class);
    $payment->setMethod('paradoxlabs_cybersource');
    $payment->setLastTransId($txnId);
    $payment->setBaseAmountAuthorized(10);
    $payment->setAmountAuthorized(10);

    /** @var OrderItem $orderItem */
    $orderItem = $objectManager->create(OrderItem::class);
    $orderItem->setProductId($product->getId())
        ->setQtyOrdered(1)
        ->setBasePrice($product->getPrice())
        ->setPrice($product->getPrice())
        ->setRowTotal($product->getPrice())
        ->setBaseRowTotal($product->getPrice())
        ->setRowTotalInclTax($product->getPrice())
        ->setBaseRowTotalInclTax($product->getPrice())
        ->setProductType('simple')
        ->setName($product->getName())
        ->setSku($product->getSku());

    /** @var Order $order */
    $order = $objectManager->create(Order::class);
    $order->setIncrementId($incrementId)
        ->setState($state)
        ->setStatus($status)
        ->setSubtotal(10)
        ->setGrandTotal(10)
        ->setBaseSubtotal(10)
        ->setBaseGrandTotal(10)
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

    return $order;
};

$makeOrder('100000781', Order::STATE_PROCESSING, 'processing', 'CS-AUTH-781');
$makeOrder('100000782', Order::STATE_NEW, 'pending', 'CS-AUTH-782');
