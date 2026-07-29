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
 * DEFECT (Bug A) -- what actually happens today:
 *
 *  1. The UC token exchange for a checkout-sourced card happens ONLY in Model/Method.php, in the
 *     afterAuthorize() (:135-143) and afterCapture() (:161-169) hooks, both of which delegate to
 *     applyUnifiedCheckoutToken() (:186-205).
 *  2. Those hooks are invoked by TokenBase's AbstractMethod::authorize()/capture(), which BOTH
 *     early-return on `if ($amount <= 0) { return $this; }` (AbstractMethod.php:372-374 and :447-449)
 *     -- placed AFTER loadOrCreateCard() but BEFORE any gateway call and before the hooks.
 *  3. So on a $0 order the card is loaded/created and SAVED, no gateway call is ever made, the
 *     transient token is never exchanged, and applyUnifiedCheckoutToken() never runs. The card
 *     persists with an empty payment_id and, critically, WITHOUT the uc_token_missing flag that
 *     would otherwise mark it as known-bad.
 *  4. Card::exchangeTransientToken() (Model/Card.php:147-210) cannot rescue it: it is guarded to
 *     `tokenbase_source === paymentinfo` (:154-160), and a checkout-sourced card never matches.
 *
 * The failure is silent and deferred: the order completes, the customer sees a saved card, and the
 * first rebill/reorder blows up in Gateway::buildStoredCardAuth() for want of a payment_id.
 *
 * This class carries BOTH a defect-documentation test (current behavior, runs) and the
 * correct-behavior test (skipped until fixed). Invert the former and unskip the latter on fix.
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
     * DEFECT DOCUMENTATION (Bug A) -- INVERT THIS TEST WHEN THE DEFECT IS FIXED.
     *
     * Authorizing a $0 order with a new card + transient token makes ZERO gateway calls and leaves the
     * saved card un-tokenized and unflagged. Both assertions below are assertions of the bug:
     * post-fix, the REST call count becomes 1 and the payment_id becomes non-empty.
     *
     * Note the card row exists at all because AbstractMethod::authorize() runs loadOrCreateCard()
     * BEFORE its `$amount <= 0` early return -- that is exactly what makes this failure silent rather
     * than loud. The absent uc_token_missing flag is the other half: nothing downstream can tell this
     * card apart from a healthy one until a charge is attempted against it.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_zero_total_order.php
     * @return void
     */
    public function testZeroTotalAuthorizeLeavesCardUntokenizedAndUnflagged(): void
    {
        $this->registerRestStub();

        $order = $this->reloadOrder();
        $this->assertSame(0.0, (float)$order->getBaseGrandTotal(), 'Fixture must be a $0 order.');

        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        // DEFECT: the transient token is never exchanged, because AbstractMethod::authorize() returns
        // at `$amount <= 0` before reaching the gateway or Method::afterAuthorize().
        $this->assertCount(
            0,
            $this->restStub->getCalledPaths(),
            'DEFECT (Bug A): a $0 order makes no gateway call at all, so the UC transient token is'
            . ' never exchanged for a TMS token. Post-fix this must be exactly one $0 TOKEN_CREATE.'
        );

        $card = $this->loadFixtureCard();

        // DEFECT: the card is saved (loadOrCreateCard ran) but carries no gateway instrument.
        $this->assertSame(
            '',
            (string)$card->getPaymentId(),
            'DEFECT (Bug A): the vaulted card has an empty payment_id, so any later rebill throws in'
            . ' Gateway::buildStoredCardAuth(). Post-fix this must be the minted TMS instrument id.'
        );

        // DEFECT: and it is not even flagged, so nothing downstream knows it is unusable. This is the
        // half of the bug that makes it silent -- applyUnifiedCheckoutToken() sets uc_token_missing on
        // a token-less UC reply, but it never runs here.
        $this->assertNull(
            $card->getAdditional('uc_token_missing'),
            'DEFECT (Bug A): the card is not flagged uc_token_missing, so it is indistinguishable from'
            . ' a healthy vaulted card until a charge is attempted months later.'
        );
    }

    /**
     * CORRECT BEHAVIOR (Bug A) -- unskip when the $0 checkout token exchange is implemented.
     *
     * A $0 checkout must still exchange the transient token for a TMS instrument, exactly the way the
     * add-card path already does via Response::tokenizeCard() /
     * Response::buildZeroDollarRequest(): a single POST to /pts/v2/payments carrying
     * tokenInformation.transientTokenJwt, actionList TOKEN_CREATE, capture false, totalAmount "0.00".
     * The minted ids must then land on the vault card, leaving it usable for the later rebill that is
     * the entire reason a $0 order collects a card.
     *
     * A fix has to run the exchange on a path the `$amount <= 0` early return does not skip -- e.g.
     * performing it before that return in the module's own Method::authorize()/capture() override, or
     * broadening the Card::exchangeTransientToken() source guard (Model/Card.php:154-160) beyond
     * paymentinfo. Either way the assertions below are the contract.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_zero_total_order.php
     * @return void
     */
    public function testZeroTotalAuthorizeShouldMintTokenAndVaultUsableCard(): void
    {
        $this->markTestSkipped(
            'Bug A: the UC token exchange for a checkout-sourced card lives only in'
            . ' Model/Method.php afterAuthorize()/afterCapture() -> applyUnifiedCheckoutToken()'
            . ' (:135-143, :161-169, :186-205), but TokenBase AbstractMethod::authorize()/capture()'
            . ' early-return on `$amount <= 0` (AbstractMethod.php:372-374, :447-449) before those'
            . ' hooks run. On a $0 order the transient token is therefore never exchanged and the card'
            . ' is saved with an empty payment_id and no uc_token_missing flag;'
            . ' Card::exchangeTransientToken() cannot rescue it because it is guarded to'
            . ' tokenbase_source === paymentinfo (Model/Card.php:154-160). The later rebill throws in'
            . ' Gateway::buildStoredCardAuth().'
        );

        // @phpstan-ignore-next-line deadCode.unreachable -- retained for the post-fix unskip.
        $this->registerRestStub();

        $order = $this->reloadOrder();
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
     * DEFECT DOCUMENTATION (Bug A, capture path) -- INVERT THIS TEST WHEN THE DEFECT IS FIXED.
     *
     * The sibling of the authorize case. With payment_action=authorize_capture, order placement routes
     * through the Adapter's CaptureCommand -> AbstractMethod::capture() -> Method::afterCapture(); that
     * override exists precisely so the sale response's token_information reaches the card. It is dead
     * on a $0 order for the same reason: AbstractMethod::capture() early-returns at `$amount <= 0`
     * (AbstractMethod.php:447-449) before calling it.
     *
     * Kept as a separate test because the two entry points are separately overridden in Model/Method.php
     * and a fix applied to only one of them would leave the other broken.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize_capture
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_zero_total_order.php
     * @return void
     */
    public function testZeroTotalCaptureLeavesCardUntokenized(): void
    {
        $this->registerRestStub();

        $order = $this->reloadOrder();
        $order->getPayment()->capture(null);
        $this->orderRepository->save($order);

        // DEFECT: same early return, so afterCapture() -> applyUnifiedCheckoutToken() never runs.
        $this->assertCount(
            0,
            $this->restStub->getCalledPaths(),
            'DEFECT (Bug A): a $0 sale makes no gateway call, so afterCapture() never maps a token.'
        );

        $this->assertSame(
            '',
            (string)$this->loadFixtureCard()->getPaymentId(),
            'DEFECT (Bug A): the card is vaulted with no gateway instrument on the sale path too.'
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
     * Reload the fixture order. OrderRepository::get() caches, so this goes through the collection.
     *
     * @return Order
     */
    private function reloadOrder(): Order
    {
        $this->resetTransactionCaches();

        return $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
    }
}
