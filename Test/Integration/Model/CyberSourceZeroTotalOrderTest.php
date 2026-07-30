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

namespace ParadoxLabs\CyberSource\Test\Integration\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Test\Integration\OomProbeTrait;
use ParadoxLabs\CyberSource\Test\Integration\RestStubTrait;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory as CardCollectionFactory;
use PHPUnit\Framework\TestCase;

/**
 * $0-order card tokenization, with the REST HTTP boundary stubbed.
 *
 * A $0 order is a real checkout, not an add-card form: 100%-off coupon, free trial, comped first
 * subscription period, free replacement. The shopper enters a card through the Unified Checkout drop-in
 * and it must end up vaulted and usable, because the whole point of most $0 orders is that something
 * rebills against that card later.
 *
 * REGRESSION COVERAGE (Bug A, fixed). What used to happen:
 *
 *  1. The UC token exchange for a checkout-sourced card happened ONLY in Model/Method.php, in the
 *     afterAuthorize()/afterCapture() hooks, both of which delegate to applyUnifiedCheckoutToken().
 *  2. Those hooks are invoked by TokenBase's AbstractMethod::authorize()/capture(), which BOTH
 *     early-return on `if ($amount <= 0) { return $this; }` (AbstractMethod.php:372-374 and :447-449)
 *     -- placed AFTER loadOrCreateCard() but BEFORE any gateway call and before the hooks.
 *  3. So on a $0 order the card was loaded/created and SAVED, no gateway call was ever made, the
 *     transient token was never exchanged, and applyUnifiedCheckoutToken() never ran. The card
 *     persisted with an empty payment_id and, critically, WITHOUT the uc_token_missing flag that
 *     would otherwise mark it as known-bad.
 *  4. Card::exchangeTransientToken() (Model/Card.php) could not rescue it: it is guarded to
 *     `tokenbase_source === paymentinfo`, and a checkout-sourced card never matches.
 *
 * The failure was silent and deferred: the order completed, the customer saw a saved card, and the
 * first rebill/reorder blew up in Gateway::buildStoredCardAuth() for want of a payment_id.
 *
 * Method::authorize()/capture() now run tokenizeZeroTotalOrder() after the parent returns, issuing
 * the same $0 TOKEN_CREATE the paymentinfo add-card path uses. Both entry points are covered here
 * because they are separately overridden and a one-sided fix would leave the other broken.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CyberSourceZeroTotalOrderTest extends TestCase
{
    use OomProbeTrait;
    use RestStubTrait;

    private const ORDER_INCREMENT_ID = '100000575';
    private const CARD_OWNER_EMAIL = 'zero-total-card@example.com';

    private ?ObjectManager $objectManager = null;
    private ?OrderRepositoryInterface $orderRepository = null;
    private ?CardCollectionFactory $cardCollectionFactory = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->probeMemory();

        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
        $this->cardCollectionFactory = $this->objectManager->get(CardCollectionFactory::class);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->unregisterRestStub();

        parent::tearDown();
    }

    /**
     * A $0 checkout must still exchange the transient token for a TMS instrument, exactly the way the
     * add-card path does via Response::tokenizeCard() / Response::buildZeroDollarRequest(): a single
     * POST to /pts/v2/payments carrying tokenInformation.transientTokenJwt, actionList TOKEN_CREATE,
     * capture false, totalAmount "0.00". The minted ids must then land on the vault card, leaving it
     * usable for the later rebill that is the entire reason a $0 order collects a card.
     *
     * The card row exists at all because AbstractMethod::authorize() runs loadOrCreateCard() BEFORE
     * its `$amount <= 0` early return -- that is exactly what made the old failure silent rather than
     * loud, and it is why Method::tokenizeZeroTotalOrder() has to re-save the card itself: the
     * parent's own save lives after that return.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_zero_total_order.php
     * @return void
     */
    public function testZeroTotalAuthorizeMintsTokenAndVaultsUsableCard(): void
    {
        $this->registerRestStub();

        $order = $this->reloadOrder();
        $this->assertSame(0.0, (float)$order->getBaseGrandTotal(), 'Fixture must be a $0 order.');

        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $calls = $this->restStub->getCallsMatching('/pts/v2/payments');
        $this->assertCount(1, $calls, 'A $0 order mints its token with exactly one payments call.');

        $params = $calls[0]['params'];
        $this->assertNotEmpty(
            $params['tokenInformation']['transientTokenJwt'] ?? null,
            'The exchange must post the transient token collected by the drop-in.'
        );
        $this->assertSame(
            '0.00',
            $params['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'Nothing is charged: the exchange is a $0 auth.'
        );
        $this->assertFalse(
            $params['processingInformation']['capture'] ?? null,
            'A $0 exchange is authorize-only; there is nothing to capture.'
        );

        $card = $this->loadFixtureCard();
        $this->assertNotSame(
            '',
            (string)$card->getPaymentId(),
            'The minted TMS instrument id must land on the vault card, or the later rebill throws.'
        );
        $this->assertNull(
            $card->getAdditional('uc_token_missing'),
            'A successful exchange leaves the card unflagged.'
        );
    }

    /**
     * The sibling of the authorize case. With payment_action=authorize_capture, order placement routes
     * through the Adapter's CaptureCommand -> AbstractMethod::capture(), which early-returns at
     * `$amount <= 0` (AbstractMethod.php:447-449) exactly like authorize().
     *
     * Kept as a separate test because the two entry points are separately overridden in Model/Method.php
     * and a fix applied to only one of them would leave the other broken.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize_capture
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_zero_total_order.php
     * @return void
     */
    public function testZeroTotalCaptureMintsTokenAndVaultsUsableCard(): void
    {
        $this->registerRestStub();

        $order = $this->reloadOrder();
        $order->getPayment()->capture(null);
        $this->orderRepository->save($order);

        $calls = $this->restStub->getCallsMatching('/pts/v2/payments');
        $this->assertCount(1, $calls, 'A $0 sale mints its token with exactly one payments call.');
        $this->assertFalse(
            $calls[0]['params']['processingInformation']['capture'] ?? null,
            'The $0 exchange is authorize-only even on the sale path; there is nothing to capture.'
        );

        $this->assertNotSame(
            '',
            (string)$this->loadFixtureCard()->getPaymentId(),
            'The minted TMS instrument id must land on the vault card on the sale path too.'
        );
    }

    /**
     * Load this fixture's card fresh from the DB, by its dedicated owner email.
     *
     * Deliberately re-queried rather than reused: the method instance holds its own card object, and
     * the assertions here are about what was actually PERSISTED.
     *
     * @return CardInterface
     */
    private function loadFixtureCard(): CardInterface
    {
        /** @var CardInterface $card */
        $card = $this->cardCollectionFactory->create()
            ->addFieldToFilter('customer_email', self::CARD_OWNER_EMAIL)
            ->setPageSize(1)
            ->getFirstItem();

        $this->assertGreaterThan(0, (int)$card->getId(), 'Fixture should have created the card.');

        return $card;
    }

    /**
     * Reload the fixture order at a request boundary. OrderRepository::get() caches, so this drops the
     * in-memory sales registries and goes through the collection.
     *
     * @return Order
     */
    private function reloadOrder(): Order
    {
        $this->simulateNewRequest();

        return $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
    }
}
