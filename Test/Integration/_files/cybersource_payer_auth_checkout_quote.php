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
 * A guest cart that is ready to PLACE: addresses, a shipping method, and a Unified Checkout transient
 * token on the payment.
 *
 * The cross-request suite drives the real checkout submit against this cart, so everything the place
 * path reads has to be genuinely persisted -- the point of the coverage is that the Payer
 * Authentication record survives setup -> authenticate -> quote-to-order conversion on its own.
 *
 * The transient token lives on the quote payment (where the checkout client posts it) and carries the
 * `jti` the record binds to; the test reads it back off the payment rather than minting its own, so
 * the binding the money path recomputes is provably the one setup() stored.
 */

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
    'email' => 'payer-auth-checkout@example.com',
];

// An unsigned transient-token JWT: TransientTokenReader does not verify signatures (a forged jti binds
// an attempt to itself and nothing else), so a hand-built payload is a faithful stand-in. Visa (001).
$tokenPayload = [
    'jti' => 'c7a1f0b2-3d4e-4f5a-9b6c-7d8e9f0a1b2c',
    'content' => [
        'paymentInformation' => [
            'card' => [
                'type' => ['value' => '001'],
                'expirationMonth' => ['value' => '01'],
                'expirationYear' => ['value' => '2029'],
                'number' => [
                    'maskedValue' => ['value' => 'XXXXXXXXXXXX1111'],
                    'bin' => ['value' => '411111'],
                ],
            ],
        ],
    ],
];

$transientToken = 'eyJhbGciOiJSUzI1NiJ9.'
    . rtrim(strtr(base64_encode(json_encode($tokenPayload, JSON_THROW_ON_ERROR)), '+/', '-_'), '=')
    . '.integrationsignature';

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId())
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->setCustomerIsGuest(true)
    ->setCustomerEmail($addressData['email'])
    ->setReservedOrderId('test_pa_checkout_guest');

$quote->getBillingAddress()->addData($addressData);
$quote->getShippingAddress()->addData($addressData);

$quote->addProduct($product, 2);

$quote->getShippingAddress()
    ->setCollectShippingRates(true)
    ->collectShippingRates()
    ->setShippingMethod('flatrate_flatrate');

$quote->getPayment()->setMethod('paradoxlabs_cybersource');
$quote->getPayment()->setAdditionalInformation('transient_token', $transientToken);

$quote->collectTotals();

/** @var CartRepositoryInterface $cartRepository */
$cartRepository = $objectManager->get(CartRepositoryInterface::class);
$cartRepository->save($quote);

$quoteIdMask = $objectManager->get(QuoteIdMaskFactory::class)->create();
$quoteIdMask->setQuoteId((int)$quote->getId());
$objectManager->get(QuoteIdMaskResource::class)->save($quoteIdMask);
