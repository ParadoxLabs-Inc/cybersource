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
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;

/**
 * A CUSTOMER cart ready to place against a fully-tokenized vault card.
 *
 * Stored-card Payer Authentication is customer-only by construction (a guest cart has no vault), and
 * the card is addressed by hash on the way in and by tokenbase id on the way out, so both the card and
 * the cart have to belong to the same real customer for the flow to resolve at all.
 */

Resolver::getInstance()->requireDataFixture(
    'Magento/Catalog/_files/product_without_options_with_stock_data.php'
);
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);
$customer = $customerRepository->get('customer@example.com');

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
    'email' => $customer->getEmail(),
];

/**
 * A fully-tokenized Unified Checkout card: paymentId is the TMS paymentInstrument, which is both the
 * id the stored-card authentication is set up against and the id the money call charges.
 */
/** @var CardInterfaceFactory $cardFactory */
$cardFactory = $objectManager->get(CardInterfaceFactory::class);
/** @var CardRepositoryInterface $cardRepository */
$cardRepository = $objectManager->get(CardRepositoryInterface::class);

/** @var CardInterface $card */
$card = $cardFactory->create();
$card->setCustomerId((int)$customer->getId());
$card->setCustomerEmail((string)$customer->getEmail());
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

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId())
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->setCustomerId((int)$customer->getId())
    ->setCustomerIsGuest(false)
    ->setCustomerEmail((string)$customer->getEmail())
    ->setReservedOrderId('test_pa_checkout_stored');

$quote->getBillingAddress()->addData($addressData);
$quote->getShippingAddress()->addData($addressData);

$quote->addProduct($product, 2);

$quote->getShippingAddress()
    ->setCollectShippingRates(true)
    ->collectShippingRates()
    ->setShippingMethod('flatrate_flatrate');

$quote->getPayment()->setMethod('paradoxlabs_cybersource');
$quote->getPayment()->setData('tokenbase_id', (int)$card->getId());

$quote->collectTotals();

/** @var CartRepositoryInterface $cartRepository */
$cartRepository = $objectManager->get(CartRepositoryInterface::class);
$cartRepository->save($quote);
