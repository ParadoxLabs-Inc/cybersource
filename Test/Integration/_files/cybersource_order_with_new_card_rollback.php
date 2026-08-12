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
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory as CardCollectionFactory;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var OrderCollectionFactory $orderCollectionFactory */
$orderCollectionFactory = $objectManager->get(OrderCollectionFactory::class);
$orders = $orderCollectionFactory->create()
    ->addFieldToFilter('increment_id', '100000562');

/** @var Order $order */
foreach ($orders as $order) {
    $order->delete();
}

/**
 * Delete ONLY this fixture's card, matched by its dedicated owner email. Filtering on method alone would
 * also destroy the cards owned by the sibling cron fixtures (cybersource_cron_cards.php).
 */
/** @var CardCollectionFactory $cardCollectionFactory */
$cardCollectionFactory = $objectManager->get(CardCollectionFactory::class);
$cards = $cardCollectionFactory->create()
    ->addFieldToFilter('customer_email', 'new-card@example.com');

foreach ($cards as $card) {
    $card->delete();
}

$registry->unregister('isSecureArea');

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_rollback.php');
