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

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Model\Cron\TransactionUpdater;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use PHPUnit\Framework\TestCase;

/**
 * Decision Manager review resolution via the hourly conversion-details cron.
 *
 * The {@see Rest} HTTP client is replaced with {@see CyberSourceRestStub}, so the cron runs against canned
 * /reporting/v3/conversion-details replies and we assert on the sales records it produces. Note the stub's
 * get() returns a raw string body, exactly like the real client: the cron json_decodes it itself.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * @magentoDbIsolation enabled
 */
class TransactionUpdaterTest extends TestCase
{
    private const REVIEW_ORDER = '100000771';
    private const REJECT_ORDER = '100000772';
    private const NEW_ORDER = '100000773';

    private ?ObjectManager $objectManager = null;
    private ?OrderCollectionFactory $orderCollectionFactory = null;
    private ?CyberSourceRestStub $restStub = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->orderCollectionFactory = $this->objectManager->get(OrderCollectionFactory::class);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->objectManager->removeSharedInstance(Rest::class);

        parent::tearDown();
    }

    /**
     * An ACCEPT decision on an order in payment review approves the payment and clears the fraud flag.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testAcceptedReviewOrderIsApproved(): void
    {
        $this->registerStub([$this->change(self::REVIEW_ORDER, 'REVIEW', 'ACCEPT')]);

        $this->runCron();

        $order = $this->loadOrder(self::REVIEW_ORDER);
        $this->assertSame(
            Order::STATE_PROCESSING,
            $order->getState(),
            'An ACCEPTed review order should leave payment review for processing.'
        );

        /**
         * Pins a defect: updateOrderStatus() mutates the authorization transaction it loads via
         * Payment::getAuthorizationTransaction(), but nothing ever saves it. Only transactions built
         * through Transaction\Builder are registered as order related-objects, so this write is dropped
         * and the order stays flagged as fraudulent after approval.
         */
        $authTxn = $order->getPayment()->getAuthorizationTransaction();
        $this->assertNotFalse($authTxn, 'The fixture authorization transaction should still be present.');
        $this->assertFalse(
            (bool)$authTxn->getAdditionalInformation('is_transaction_fraud'),
            'Approving the payment should clear is_transaction_fraud on the authorization transaction.'
        );
    }

    /**
     * The cron requests the last 24 hours of conversion details for the store's organization.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testRequestsConversionDetailsForLastDay(): void
    {
        $this->registerStub([]);

        $this->runCron();

        $calls = $this->restStub->getCallsMatching('/reporting/v3/conversion-details');
        $this->assertCount(1, $calls, 'The cron should request conversion details once for the store.');
        $this->assertSame('GET', $calls[0]['method']);
        $this->assertSame('CRONORG', $calls[0]['params']['organizationId'] ?? null);

        $start = strtotime((string)($calls[0]['params']['startTime'] ?? ''));
        $end = strtotime((string)($calls[0]['params']['endTime'] ?? ''));
        $this->assertNotFalse($start, 'startTime should be a parseable ISO timestamp.');
        $this->assertNotFalse($end, 'endTime should be a parseable ISO timestamp.');
        $this->assertEqualsWithDelta(86400, $end - $start, 120, 'The window should span the last 24 hours.');
    }

    /**
     * A REJECT decision on an order in payment review denies the payment and cancels the order.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testRejectedReviewOrderIsCanceled(): void
    {
        $this->registerStub([$this->change(self::REJECT_ORDER, 'REVIEW', 'REJECT')]);

        $this->runCron();

        $order = $this->loadOrder(self::REJECT_ORDER);
        $this->assertSame(
            Order::STATE_CANCELED,
            $order->getState(),
            'A REJECTed review order should be canceled.'
        );
    }

    /**
     * Orders that are not in payment review are never touched, whatever the report says about them.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testNonReviewOrderIsUntouched(): void
    {
        $this->registerStub([$this->change(self::NEW_ORDER, 'REVIEW', 'ACCEPT')]);

        $this->runCron();

        $order = $this->loadOrder(self::NEW_ORDER);
        $this->assertSame(Order::STATE_NEW, $order->getState(), 'An order outside payment review must not move.');
        $this->assertSame('pending', $order->getStatus(), 'An order outside payment review must not change status.');
    }

    /**
     * Decisions that did not come out of review are not ours to act on.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testNonReviewOriginalDecisionIsIgnored(): void
    {
        $this->registerStub([$this->change(self::REVIEW_ORDER, 'ACCEPT', 'REJECT')]);

        $this->runCron();

        $order = $this->loadOrder(self::REVIEW_ORDER);
        $this->assertSame(
            Order::STATE_PAYMENT_REVIEW,
            $order->getState(),
            'Only originalDecision REVIEW rows may change an order.'
        );
    }

    /**
     * A malformed row must be skipped without aborting the rest of the batch.
     *
     * Pins candidate defect D5: TransactionUpdater::processChange() reads $change['originalDecision'],
     * ['newDecision'] and ['merchantReferenceNumber'] unconditionally, so a partial row raises an
     * undefined-key error before the good row behind it is ever processed.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testMalformedRowDoesNotAbortBatch(): void
    {
        $this->registerStub([
            ['requestId' => 'CS-BAD-ROW'],
            ['originalDecision' => 'REVIEW', 'newDecision' => 'ACCEPT'],
            $this->change(self::REVIEW_ORDER, 'REVIEW', 'ACCEPT'),
        ]);

        $this->runCron();

        $order = $this->loadOrder(self::REVIEW_ORDER);
        $this->assertSame(
            Order::STATE_PROCESSING,
            $order->getState(),
            'A bad row earlier in the batch must not stop the good row behind it.'
        );
    }

    /**
     * A non-JSON response body is handled cleanly rather than fataling.
     *
     * Pins candidate defect D8: the `$reply !== false` guard on TransactionUpdater:119 can never fire,
     * because json_decode() returns null (not false) for an undecodable body.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testMalformedResponseBodyIsHandledCleanly(): void
    {
        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder(static fn (): string => '<html>gateway is having a day</html>');
        $this->objectManager->addSharedInstance($this->restStub, Rest::class);

        $this->runCron();

        $order = $this->loadOrder(self::REVIEW_ORDER);
        $this->assertSame(
            Order::STATE_PAYMENT_REVIEW,
            $order->getState(),
            'An undecodable body must leave orders alone.'
        );
    }

    /**
     * A 404 'Requested Resource Not Found' means no updates in the window, and is swallowed.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testEmptyReportResponseIsSwallowed(): void
    {
        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder(
            static function (): string {
                throw CyberSourceRestStub::httpError('Requested Resource Not Found', 404);
            }
        );
        $this->objectManager->addSharedInstance($this->restStub, Rest::class);

        $this->runCron();

        $order = $this->loadOrder(self::REVIEW_ORDER);
        $this->assertSame(Order::STATE_PAYMENT_REVIEW, $order->getState(), 'A 404 must be a no-op.');
    }

    /**
     * Stores sharing one merchant id are polled once, not once per store.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoConfigFixture fixture_second_store_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture fixture_second_store_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture fixture_second_store_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testDedupesRequestsPerMerchantId(): void
    {
        // Both stores are configured with the same merchant id above.
        $storeRepository = $this->objectManager->get(StoreRepositoryInterface::class);
        $storeRepository->clean();

        $stores = array_filter(
            $storeRepository->getList(),
            static fn ($store): bool => (bool)$store->getIsActive()
        );
        $this->assertGreaterThan(1, count($stores), 'The fixture should give us two active stores to dedupe.');

        $this->registerStub([]);

        $this->runCron();

        $this->assertCount(
            1,
            $this->restStub->getCallsMatching('/reporting/v3/conversion-details'),
            'One merchant id shared across stores should be polled exactly once.'
        );
    }

    /**
     * With nothing in payment review the reporting endpoint is never touched.
     *
     * Conversion details only ever resolve orders already sitting in review, so a run with none
     * outstanding cannot do anything -- and on an account with no fraud product it is an hourly 404
     * against a report the merchant does not have.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_settled_orders.php
     * @return void
     */
    public function testSkipsPollingWhenNothingAwaitsReview(): void
    {
        $this->registerStub([]);

        $this->runCron();

        $this->assertCount(
            0,
            $this->restStub->getCallsMatching('/reporting/v3/conversion-details'),
            'The cron should not poll conversion details with no order awaiting review.'
        );
    }

    /**
     * An order in review is polled for whatever uc_decision_manager says.
     *
     * That setting only rides completeMandate on the capture context; the /pts/v2/payments call carries
     * no fraud toggle, so a review hold is decided by the CyberSource account's fraud configuration --
     * Decision Manager, Fraud Management Essentials, or a processor rule. Gating the cron on the setting
     * would leave orders held by any of the others stuck in payment review permanently.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/merchant_id CRONMERCHANT
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/organization_id CRONORG
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/uc_decision_manager 0
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_cron_review_orders.php
     * @return void
     */
    public function testResolvesReviewWithDecisionManagerSettingOff(): void
    {
        $this->registerStub([$this->change(self::REVIEW_ORDER, 'REVIEW', 'ACCEPT')]);

        $this->runCron();

        $this->assertCount(
            1,
            $this->restStub->getCallsMatching('/reporting/v3/conversion-details'),
            'A pending review must be polled regardless of the uc_decision_manager setting.'
        );

        $order = $this->loadOrder(self::REVIEW_ORDER);
        $this->assertSame(
            Order::STATE_PROCESSING,
            $order->getState(),
            'The review must still resolve with the setting off -- FME and DM both produce review holds.'
        );
    }

    /**
     * Build a conversion-details row for the given order.
     *
     * @param string $incrementId
     * @param string $originalDecision
     * @param string $newDecision
     * @return array<string, string>
     */
    private function change(string $incrementId, string $originalDecision, string $newDecision): array
    {
        return [
            'requestId' => 'CS-REQ-' . $incrementId,
            'merchantReferenceNumber' => $incrementId,
            'originalDecision' => $originalDecision,
            'newDecision' => $newDecision,
        ];
    }

    /**
     * Register the Rest double, returning the given conversion-details rows as a raw JSON body.
     *
     * @param array<int, array> $changes
     * @return void
     */
    private function registerStub(array $changes): void
    {
        $body = (string)json_encode(['conversionDetails' => $changes]);

        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder(static fn (): string => $body);

        $this->objectManager->addSharedInstance($this->restStub, Rest::class);
    }

    /**
     * Run the cron with the doubles in place.
     *
     * @return void
     */
    private function runCron(): void
    {
        /** @var TransactionUpdater $cron */
        $cron = $this->objectManager->create(TransactionUpdater::class);
        $cron->execute();
    }

    /**
     * Load the fixture order fresh by increment id. Deliberately not OrderRepository::get(), which hands
     * back the stale instance the cron already saved.
     *
     * @param string $incrementId
     * @return Order
     */
    private function loadOrder(string $incrementId): Order
    {
        /** @var Order $order */
        $order = $this->orderCollectionFactory->create()
            ->addFieldToFilter('increment_id', $incrementId)
            ->setPageSize(1)
            ->getFirstItem();

        $this->assertGreaterThan(0, (int)$order->getId(), 'Fixture should have created order ' . $incrementId);

        return $order;
    }
}
