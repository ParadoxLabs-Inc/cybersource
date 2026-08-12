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

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var OrderCollectionFactory $orderCollectionFactory */
$orderCollectionFactory = $objectManager->get(OrderCollectionFactory::class);
$orders = $orderCollectionFactory->create()
    ->addFieldToFilter('increment_id', 'test_pa_checkout_stored');

/** @var Order $order */
foreach ($orders as $order) {
    $order->delete();
}

/** @var QuoteCollectionFactory $quoteCollectionFactory */
$quoteCollectionFactory = $objectManager->get(QuoteCollectionFactory::class);
$quotes = $quoteCollectionFactory->create()
    ->addFieldToFilter('reserved_order_id', 'test_pa_checkout_stored');

/** @var Quote $quote */
foreach ($quotes as $quote) {
    $quote->delete();
}

$registry->unregister('isSecureArea');

// The vault card goes with its customer (paradoxlabs_stored_card has a customer foreign key).
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');
Resolver::getInstance()->requireDataFixture(
    'Magento/Catalog/_files/product_without_options_with_stock_data_rollback.php'
);
