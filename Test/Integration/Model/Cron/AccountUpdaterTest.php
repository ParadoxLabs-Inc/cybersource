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

namespace ParadoxLabs\CyberSource\Test\Integration\Model\Cron;

use Magento\Store\Api\StoreRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Model\Cron\AccountUpdater;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Model\Card;
use ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory as CardCollectionFactory;
use ParadoxLabs\TokenBase\Model\ResourceModel\CardRepository;
use PHPUnit\Framework\TestCase;

/**
 * Account Updater batch/report processing for stored CyberSource cards.
 *
 * The {@see Rest} HTTP client is replaced with {@see CyberSourceRestStub}, so the cron runs against canned
 * /accountupdater/v1/batches and report replies (raw JSON strings, as the real client returns for get()).
 * The card repository is wrapped in a counting proxy so "the cron must not save" is a real assertion
 * rather than an inference from unchanged data.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * @magentoDbIsolation enabled
 */
class AccountUpdaterTest extends TestCase
{
    private const REPORT_PATH = '/accountupdater/v1/batches/CS-BATCH-1/report';
    private const REPORT_HREF = 'https://apitest.cybersource.com' . self::REPORT_PATH;

    private ?ObjectManager $objectManager = null;
    private ?CardCollectionFactory $cardCollectionFactory = null;
    private ?CyberSourceRestStub $restStub = null;
    private int $cardSaveCount = 0;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->cardCollectionFactory = $this->objectManager->get(CardCollectionFactory::class);
        $this->cardSaveCount = 0;
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->objectManager->removeSharedInstance(Rest::class);
        $this->objectManager->removeSharedInstance(CardRepository::class);

        parent::tearDown();
    }

    /**
     * A new-expiration-date record rewrites the expiry, and only the expiry.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testNewExpirationDateRecordUpdatesExpiryOnly(): void
    {
        $this->registerStub([
            $this->record('CS-PI-EXPIRY', [
                'response' => 'NED',
                'reason' => 'New expiration date',
                'cardExpiryMonth' => '09',
                'cardExpiryYear' => '2029',
            ]),
        ]);

        $this->runCron();

        $card = $this->loadCard('CS-PI-EXPIRY');
        $this->assertSame('2029-09-30 23:59:59', $card->getExpires(), 'NED should roll the stored expiry.');
        $this->assertSame('09', $card->getAdditional('cc_exp_month'));
        $this->assertSame('2029', $card->getAdditional('cc_exp_year'));
        $this->assertSame('1111', $card->getAdditional('cc_last4'), 'NED must not touch the card number.');
        $this->assertSame('411111', $card->getAdditional('cc_bin'), 'NED must not touch the card number.');
        $this->assertSame('VI', $card->getAdditional('cc_type'), 'NED must not touch the card type.');
    }

    /**
     * A new-account-number record rewrites last4 and BIN from the masked number.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testNewAccountNumberRecordUpdatesLast4AndBin(): void
    {
        $this->registerStub([
            $this->record('CS-PI-NUMBER', [
                'response' => 'NAN',
                'reason' => 'New account number',
                'cardNumber' => '541111XXXXXX9876',
                'cardType' => '002',
                'cardExpiryMonth' => '09',
                'cardExpiryYear' => '2029',
            ]),
        ]);

        $this->runCron();

        $card = $this->loadCard('CS-PI-NUMBER');
        $this->assertSame('9876', $card->getAdditional('cc_last4'), 'NAN should store the new last4.');
        $this->assertSame('541111', $card->getAdditional('cc_bin'), 'NAN should store the new BIN.');
        $this->assertSame('MC', $card->getAdditional('cc_type'), 'NAN should map the new CyberSource card type.');
        $this->assertSame('2029-09-30 23:59:59', $card->getExpires());
    }

    /**
     * A new-account-number record without expiry fields must be handled cleanly.
     *
     * Pins candidate defect D5: AccountUpdater::updateCard() reads responseRecord.cardExpiryYear and
     * .cardExpiryMonth unconditionally (:190-191), then feeds them to strtotime() (:192). A NAN record
     * need not carry an expiry, so this path is an undefined-key read plus a bogus expiry write.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testNewAccountNumberWithoutExpiryIsHandledCleanly(): void
    {
        $this->registerStub([
            $this->record('CS-PI-NOEXPIRY', [
                'response' => 'NAN',
                'reason' => 'New account number',
                'cardNumber' => '411111XXXXXX5544',
            ]),
        ]);

        $this->runCron();

        $card = $this->loadCard('CS-PI-NOEXPIRY');
        $this->assertSame('5544', $card->getAdditional('cc_last4'), 'The new card number should still be stored.');
        $this->assertSame('411111', $card->getAdditional('cc_bin'), 'The new BIN should still be stored.');
        $this->assertSame(
            '2027-01-31 23:59:59',
            $card->getExpires(),
            'A record with no expiry must leave the stored expiry alone, not write a fabricated one.'
        );
        $this->assertSame('01', $card->getAdditional('cc_exp_month'), 'Expiry month must survive an expiry-less NAN.');
        $this->assertSame('2027', $card->getAdditional('cc_exp_year'), 'Expiry year must survive an expiry-less NAN.');
    }

    /**
     * A record that reports what we already have must not cost a write.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testNoChangeRecordPerformsNoSave(): void
    {
        $this->registerStub([
            $this->record('CS-PI-NOCHANGE', [
                'response' => 'NED',
                'reason' => 'No change',
                'cardNumber' => '411111XXXXXX1111',
                'cardType' => '001',
                'cardExpiryMonth' => '01',
                'cardExpiryYear' => '2027',
            ]),
        ]);

        $this->runCron();

        $this->assertSame(0, $this->cardSaveCount, 'An unchanged card must not be saved.');
    }

    /**
     * An account-closed record deactivates the stored card.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testAccountClosedRecordDeactivatesCard(): void
    {
        $this->registerStub([
            $this->record('CS-PI-CLOSED', [
                'response' => 'ACL',
                'reason' => 'Account closed',
            ]),
        ]);

        $this->runCron();

        $card = $this->loadCard('CS-PI-CLOSED');
        $this->assertSame(0, (int)$card->getActive(), 'ACL should deactivate the stored card.');
    }

    /**
     * Response codes outside the update/invalid sets are ignored entirely.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testIgnoredResponseCodesAreNoOps(): void
    {
        $this->registerStub([
            $this->record('CS-PI-IGNORED', [
                'response' => 'CUR',
                'reason' => 'Card up to date',
                'cardNumber' => '411111XXXXXX0000',
                'cardExpiryMonth' => '11',
                'cardExpiryYear' => '2033',
            ]),
            $this->record('CS-PI-IGNORED', [
                'response' => 'NUP',
                'reason' => 'Not updated',
                'cardNumber' => '411111XXXXXX0000',
                'cardExpiryMonth' => '11',
                'cardExpiryYear' => '2033',
            ]),
        ]);

        $this->runCron();

        $card = $this->loadCard('CS-PI-IGNORED');
        $this->assertSame(0, $this->cardSaveCount, 'CUR/NUP records must not write.');
        $this->assertSame('1111', $card->getAdditional('cc_last4'));
        $this->assertSame('2027-01-31 23:59:59', $card->getExpires());
        $this->assertSame(1, (int)$card->getActive());
    }

    /**
     * Only the newest COMPLETE batch per source is pulled; incomplete and empty batches are skipped.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testOnlyNewestCompleteBatchPerSourceIsProcessed(): void
    {
        // The API returns batches newest-first, which is the ordering the cron's "first COMPLETE wins" relies on.
        $batches = [
            $this->batch('SOURCE_A', 'COMPLETE', 2, '/accountupdater/v1/batches/A-NEW/report'),
            $this->batch('SOURCE_A', 'COMPLETE', 2, '/accountupdater/v1/batches/A-OLD/report'),
            $this->batch('SOURCE_B', 'PROCESSING', 5, '/accountupdater/v1/batches/B-RUNNING/report'),
            $this->batch('SOURCE_C', 'COMPLETE', 0, '/accountupdater/v1/batches/C-EMPTY/report'),
        ];

        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder(
            function (string $method, string $path) use ($batches): string {
                if ($path === '/accountupdater/v1/batches') {
                    return (string)json_encode(['_embedded' => ['batches' => $batches]]);
                }

                return (string)json_encode(['records' => []]);
            }
        );
        $this->installStubs();

        $this->runCron();

        $reportCalls = $this->restStub->getCallsMatching('/report');
        $this->assertSame(
            ['/accountupdater/v1/batches/A-NEW/report'],
            array_column($reportCalls, 'path'),
            'Only the newest COMPLETE batch with updated records should be fetched.'
        );
    }

    /**
     * Which token shape does the report's sourceRecord.token have to be for a card to match?
     *
     * Pins candidate defect D6: the cron matches cards on payment_id, which per the D5 vault mapping is the
     * TMS paymentInstrument id — but the Account Updater report plausibly keys records by the
     * instrumentIdentifier (the PAN token that Account Updater actually updates). If the latter is what
     * CyberSource sends, nothing ever matches and the cron silently no-ops forever. Both shapes are driven
     * here to document which one the current code accepts; live confirmation still required.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testCardsMatchOnPaymentInstrumentTokenNotInstrumentIdentifier(): void
    {
        $this->registerStub([
            $this->record('CS-PI-TOKENSHAPE', [
                'response' => 'NAN',
                'reason' => 'Keyed by TMS paymentInstrument id',
                'cardNumber' => '411111XXXXXX2222',
                'cardExpiryMonth' => '09',
                'cardExpiryYear' => '2029',
            ]),
        ]);

        $this->runCron();

        $card = $this->loadCard('CS-PI-TOKENSHAPE');
        $this->assertSame(
            '2222',
            $card->getAdditional('cc_last4'),
            'A record keyed by the TMS paymentInstrument id matches: payment_id is what the cron filters on.'
        );

        // Same card, same batch shape, but keyed by the instrumentIdentifier id it also carries.
        $this->cardSaveCount = 0;
        $this->objectManager->removeSharedInstance(Rest::class);
        $this->registerStub([
            $this->record('CS-II-TOKENSHAPE', [
                'response' => 'NAN',
                'reason' => 'Keyed by TMS instrumentIdentifier id',
                'cardNumber' => '411111XXXXXX3333',
                'cardExpiryMonth' => '10',
                'cardExpiryYear' => '2030',
            ]),
        ]);

        $this->runCron();

        $card = $this->loadCard('CS-PI-TOKENSHAPE');
        $this->assertSame(0, $this->cardSaveCount, 'A record keyed by instrumentIdentifier matches no card today.');
        $this->assertSame(
            '2222',
            $card->getAdditional('cc_last4'),
            'D6: instrumentIdentifier-keyed records are silently dropped. If that is the shape CyberSource'
            . ' sends, the cron never updates anything.'
        );
    }

    /**
     * A non-JSON batches body is handled cleanly rather than fataling.
     *
     * Pins candidate defect D8: the `$reply !== false` guard on AccountUpdater:126 can never fire, because
     * json_decode() returns null (not false) for an undecodable body.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_cards.php
     * @return void
     */
    public function testMalformedBatchesBodyIsHandledCleanly(): void
    {
        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder(static fn (): string => '<html>gateway is having a day</html>');
        $this->installStubs();

        $this->runCron();

        $this->assertSame(
            ['/accountupdater/v1/batches'],
            $this->restStub->getCalledPaths(),
            'An undecodable batches body should stop the run, not drag a report fetch along.'
        );
        $this->assertSame(0, $this->cardSaveCount, 'An undecodable batches body must not write.');
    }

    /**
     * Stores sharing one merchant id are polled once, not once per store.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture fixture_second_store_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture fixture_second_store_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @return void
     */
    public function testDedupesRequestsPerMerchantId(): void
    {
        // Both stores are configured with the same merchant id above.
        $storeRepository = $this->objectManager->get(StoreRepositoryInterface::class);
        $storeRepository->clean();

        $this->registerStub([]);

        $this->runCron();

        $listCalls = array_filter(
            $this->restStub->getCalledPaths(),
            static fn (string $path): bool => $path === '/accountupdater/v1/batches'
        );
        $this->assertCount(
            1,
            $listCalls,
            'One merchant id shared across stores should be polled exactly once.'
        );
    }

    /**
     * Build an Account Updater report record for the given token.
     *
     * @param string $token
     * @param array<string, string> $responseRecord
     * @return array<string, array>
     */
    private function record(string $token, array $responseRecord): array
    {
        return [
            'sourceRecord' => ['token' => $token],
            'responseRecord' => $responseRecord,
        ];
    }

    /**
     * Build a batches-list entry.
     *
     * @param string $source
     * @param string $status
     * @param int $updatedRecords
     * @param string $reportPath
     * @return array<string, mixed>
     */
    private function batch(string $source, string $status, int $updatedRecords, string $reportPath): array
    {
        return [
            'batchId' => 'CS-BATCH-' . $source,
            'batchSource' => $source,
            'status' => $status,
            'totals' => ['updatedRecords' => $updatedRecords],
            '_links' => [
                'reports' => [
                    ['href' => 'https://apitest.cybersource.com' . $reportPath],
                ],
            ],
        ];
    }

    /**
     * Register the Rest double: one COMPLETE batch whose report carries the given records.
     *
     * @param array<int, array> $records
     * @return void
     */
    private function registerStub(array $records): void
    {
        $batches = (string)json_encode([
            '_embedded' => [
                'batches' => [
                    [
                        'batchId' => 'CS-BATCH-1',
                        'batchSource' => 'CYBS_TOKEN',
                        'status' => 'COMPLETE',
                        'totals' => ['updatedRecords' => max(count($records), 1)],
                        '_links' => ['reports' => [['href' => self::REPORT_HREF]]],
                    ],
                ],
            ],
        ]);
        $report = (string)json_encode(['records' => $records]);

        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder(
            static function (string $method, string $path) use ($batches, $report): string {
                return $path === self::REPORT_PATH ? $report : $batches;
            }
        );

        $this->installStubs();
    }

    /**
     * Share the Rest double, and wrap the real card repository in a save-counting proxy.
     *
     * @return void
     */
    private function installStubs(): void
    {
        $this->objectManager->addSharedInstance($this->restStub, Rest::class);

        if ($this->objectManager->get(CardRepositoryInterface::class) instanceof CardRepository) {
            /** @var CardRepository $realRepository */
            $realRepository = $this->objectManager->create(CardRepository::class);

            $proxy = $this->createMock(CardRepositoryInterface::class);
            $proxy->method('save')
                ->willReturnCallback(
                    function (CardInterface $card) use ($realRepository): CardInterface {
                        $this->cardSaveCount++;

                        return $realRepository->save($card);
                    }
                );

            $this->objectManager->addSharedInstance($proxy, CardRepository::class);
        }
    }

    /**
     * Run the cron with the doubles in place.
     *
     * @return void
     */
    private function runCron(): void
    {
        /** @var AccountUpdater $cron */
        $cron = $this->objectManager->create(AccountUpdater::class);
        $cron->execute();
    }

    /**
     * Load a fixture card fresh by its stored payment id.
     *
     * @param string $paymentId
     * @return Card
     */
    private function loadCard(string $paymentId): Card
    {
        /** @var Card $card */
        $card = $this->cardCollectionFactory->create()
            ->addFieldToFilter('method', 'paradoxlabs_cybersource')
            ->addFieldToFilter('payment_id', $paymentId)
            ->setPageSize(1)
            ->getFirstItem();

        $this->assertGreaterThan(0, (int)$card->getId(), 'Fixture should have created card ' . $paymentId);

        return $card;
    }
}
