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
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/**
 * An active GUEST cart with a non-zero base grand total and a masked cart id.
 *
 * This is the shape the Payer Authentication webapi routes operate on: everything the service reads
 * -- base grand total, base currency, billTo, quote payment -- has to be really persisted, because
 * the point of the integration coverage is that setup() and authenticate() are separate requests
 * that share nothing but the database row.
 */

// product_simple.php carries REQUIRED custom options, which makes Quote::addProduct() refuse the item
// and return an error string -- silently leaving an empty, zero-total cart behind. The option-less
// product is what this cart needs: a real base grand total for the amount binding to be worth anything.
Resolver::getInstance()->requireDataFixture(
    'Magento/Catalog/_files/product_without_options_with_stock_data.php'
);

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
    'email' => 'payer-auth-guest@example.com',
];

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId())
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->setCustomerIsGuest(true)
    ->setCustomerEmail($addressData['email'])
    ->setReservedOrderId('test_payer_auth_guest');

$quote->getBillingAddress()->addData($addressData);
$quote->getShippingAddress()->addData($addressData);

$quote->addProduct($product, 2);
$quote->getPayment()->setMethod('paradoxlabs_cybersource');
$quote->collectTotals();

/** @var CartRepositoryInterface $cartRepository */
$cartRepository = $objectManager->get(CartRepositoryInterface::class);
$cartRepository->save($quote);

$quoteIdMask = $objectManager->get(QuoteIdMaskFactory::class)->create();
$quoteIdMask->setQuoteId((int)$quote->getId());
$objectManager->get(QuoteIdMaskResource::class)->save($quoteIdMask);
