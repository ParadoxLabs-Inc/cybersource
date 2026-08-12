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

use Magento\TestFramework\Helper\Bootstrap;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;

$objectManager = Bootstrap::getObjectManager();

/** @var CardInterfaceFactory $cardFactory */
$cardFactory = $objectManager->get(CardInterfaceFactory::class);
/** @var CardRepositoryInterface $cardRepository */
$cardRepository = $objectManager->get(CardRepositoryInterface::class);

/**
 * Stored CyberSource cards for the Account Updater cron. payment_id carries the TMS paymentInstrument id
 * per the D5 vault mapping; the TMS instrumentIdentifier id lives in additional[instrument_identifier].
 * Both are seeded so the cron's token matching can be exercised against either shape.
 *
 * customer_id 0 (guest) is intentional and irrelevant to the cron, which matches on method + payment_id.
 */
$makeCard = static function (
    string $paymentId,
    string $instrumentIdentifier,
    array $additional,
    string $expires,
    int $active = 1
) use ($cardFactory, $cardRepository): CardInterface {
    /** @var CardInterface $card */
    $card = $cardFactory->create();
    $card->setCustomerId(0);
    $card->setCustomerEmail('customer@example.com');
    $card->setMethod('paradoxlabs_cybersource');
    $card->setProfileId('CS-CUST-CRON');
    $card->setPaymentId($paymentId);
    $card->setActive($active);
    $card->setExpires($expires);

    foreach ($additional + ['instrument_identifier' => $instrumentIdentifier] as $key => $value) {
        $card->setAdditional($key, $value);
    }

    return $cardRepository->save($card);
};

$baseCard = [
    'cc_type' => 'VI',
    'cc_last4' => '1111',
    'cc_bin' => '411111',
    'cc_exp_month' => '01',
    'cc_exp_year' => '2027',
];

// NED target: expiry changes, everything else stays put.
$makeCard('CS-PI-EXPIRY', 'CS-II-EXPIRY', $baseCard, '2027-01-31 23:59:59');

// NAN target: card number changes.
$makeCard('CS-PI-NUMBER', 'CS-II-NUMBER', $baseCard, '2027-01-31 23:59:59');

// NAN target with no expiry in the report (D5 pin).
$makeCard('CS-PI-NOEXPIRY', 'CS-II-NOEXPIRY', $baseCard, '2027-01-31 23:59:59');

// Record that reports exactly what we already have: the cron must not save.
$makeCard('CS-PI-NOCHANGE', 'CS-II-NOCHANGE', $baseCard, '2027-01-31 23:59:59');

// ACL target: gets deactivated.
$makeCard('CS-PI-CLOSED', 'CS-II-CLOSED', $baseCard, '2027-01-31 23:59:59');

// Ignored-code target (CUR/NUP): must be untouched.
$makeCard('CS-PI-IGNORED', 'CS-II-IGNORED', $baseCard, '2027-01-31 23:59:59');

// D6 token-shape probe: matched by paymentInstrument id or by instrumentIdentifier id?
$makeCard('CS-PI-TOKENSHAPE', 'CS-II-TOKENSHAPE', $baseCard, '2027-01-31 23:59:59');
