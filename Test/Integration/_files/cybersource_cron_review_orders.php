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
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\TransactionFactory;
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
 * Build a CyberSource order in the given state. The cron matches orders by increment id
 * (merchantReferenceNumber), so the increment ids here are what the canned conversion-details rows carry.
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

/**
 * Order held for Decision Manager review, with the fraud-flagged authorization the cron is expected to
 * clear on ACCEPT. The transaction id must match last_trans_id: the cron copies that to
 * parent_transaction_id, which is how Payment::getAuthorizationTransaction() finds this record.
 */
$reviewOrder = $makeOrder('100000771', Order::STATE_PAYMENT_REVIEW, 'payment_review', 'CS-AUTH-771');

/** @var TransactionFactory $transactionFactory */
$transactionFactory = $objectManager->get(TransactionFactory::class);
/** @var TransactionRepositoryInterface $transactionRepository */
$transactionRepository = $objectManager->get(TransactionRepositoryInterface::class);

/** @var Transaction $transaction */
$transaction = $transactionFactory->create();
$transaction->setOrderId((int)$reviewOrder->getId())
    ->setPaymentId((int)$reviewOrder->getPayment()->getId())
    ->setTxnId('CS-AUTH-771')
    ->setTxnType(Transaction::TYPE_AUTH)
    ->setIsClosed(0);
$transaction->setAdditionalInformation('is_transaction_fraud', true);
$transactionRepository->save($transaction);

// Second review order, used by the REJECT path so the ACCEPT tests never race it.
$rejectOrder = $makeOrder('100000772', Order::STATE_PAYMENT_REVIEW, 'payment_review', 'CS-AUTH-772');

/** @var Transaction $rejectTransaction */
$rejectTransaction = $transactionFactory->create();
$rejectTransaction->setOrderId((int)$rejectOrder->getId())
    ->setPaymentId((int)$rejectOrder->getPayment()->getId())
    ->setTxnId('CS-AUTH-772')
    ->setTxnType(Transaction::TYPE_AUTH)
    ->setIsClosed(0);
$rejectTransaction->setAdditionalInformation('is_transaction_fraud', true);
$transactionRepository->save($rejectTransaction);

// Order that is NOT under review: the cron must leave it alone regardless of the decision reported.
$makeOrder('100000773', Order::STATE_NEW, 'pending', 'CS-AUTH-773');
