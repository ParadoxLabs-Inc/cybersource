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
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var QuoteCollectionFactory $quoteCollectionFactory */
$quoteCollectionFactory = $objectManager->get(QuoteCollectionFactory::class);
$quotes = $quoteCollectionFactory->create()
    ->addFieldToFilter('reserved_order_id', 'test_payer_auth_guest');

/** @var Quote $quote */
foreach ($quotes as $quote) {
    // quote_id_mask has an ON DELETE CASCADE foreign key to quote, so the mask goes with it.
    $quote->delete();
}

$registry->unregister('isSecureArea');

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_rollback.php');
