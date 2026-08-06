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

namespace ParadoxLabs\CyberSource\Test\Integration\Controller\Adminhtml\Order;

use Magento\Framework\Message\MessageInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\TestFramework\TestCase\AbstractBackendController;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\RestStubTrait;
use ParadoxLabs\TokenBase\Model\AbstractMethod;

/**
 * Admin "Void" against a CyberSource order with an open, uncaptured authorization.
 *
 * sales/order/voidPayment is the only admin route that reaches Gateway::void(), and the branch it takes is
 * money-relevant: an uncaptured authorization must be REVERSED (/pts/v2/payments/{id}/reversals), never
 * capture-voided. The rejected-reversal path is covered too: a failure there must reach the admin and leave
 * the authorization open, which REQUIRES TokenBase's void-error-surfacing change (unmerged TokenBase PR #7,
 * branch fix/void-error-surfacing) — see testRejectedReversalIsReportedAsAnErrorAndLeavesTheAuthOpen().
 *
 * The inherited testAclHasAccess/testAclNoAccess cover the ACL contract for this route.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 * They are declared at BOTH default and default_store scope on purpose: the integration App\Config keeps a
 * pre-merged snapshot per scope, so a value written at default scope is invisible to the store-scoped read
 * the payment method performs. Without the default_store copy, config.xml's shipped value silently wins.
 *
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class VoidPaymentTest extends AbstractBackendController
{
    use RestStubTrait;

    private const ORDER_INCREMENT_ID = '100000570';

    /**
     * @var string
     */
    protected $uri = 'backend/sales/order/voidPayment';

    /**
     * @var string
     */
    protected $resource = 'Magento_Sales::sales_order';

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registerRestStub();
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
     * Voiding an authorized-but-uncaptured order reverses the authorization for the amount still due.
     *
     * @magentoConfigFixture payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testVoidReversesTheOpenAuthorization(): void
    {
        $order = $this->authorizeFixtureOrder();

        $this->restStub->resetCalls();

        $this->getRequest()->setParams(['order_id' => (int)$order->getId()]);
        $this->dispatch($this->uri);

        $this->assertSessionMessages(
            $this->equalTo([(string)__('The payment has been voided.')]),
            MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('sales/order/view/order_id/' . (int)$order->getId()));

        $this->assertSame(
            ['/pts/v2/payments/' . CyberSourceRestStub::PAYMENT_ID_PREFIX . '1/reversals'],
            $this->restStub->getCalledPaths(),
            'An uncaptured authorization must be REVERSED, not capture-voided.'
        );
        $this->assertSame(
            '34.00',
            $this->restStub->calls[0]['params']['reversalInformation']['amountDetails']['totalAmount'] ?? null,
            'The reversal must carry the amount still due.'
        );

        // Assert against what the void actually persisted, not what the dispatch left memoised: the
        // transaction repository handed the controller the authorization while it was still open, and
        // would keep handing that same open copy back here.
        $this->simulateNewRequest();

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $this->assertSame(0.0, (float)$order->getBaseTotalPaid(), 'A void captures nothing.');

        $authTransaction = $order->getPayment()->getAuthorizationTransaction();
        $this->assertTrue(
            $authTransaction === false || (bool)$authTransaction->getIsClosed(),
            'The authorization transaction must be closed after a void.'
        );
        $this->assertContains(
            Transaction::TYPE_VOID,
            $this->transactionTypes($order),
            'The void must be recorded as a transaction on the payment.'
        );
    }

    /**
     * A REJECTED reversal must surface as an admin error, with the authorization left open.
     *
     * REQUIRES TokenBase's void-error-surfacing change (unmerged TokenBase PR #7, branch
     * fix/void-error-surfacing, tokenbase issue #5). On TokenBase master this test FAILS, because
     * AbstractMethod::void() still catches \Throwable around the gateway call and swallows it ("Ignore void
     * errors, let Magento proceed like it happened. Most likely the auth already expired."), then
     * unconditionally sets shouldCloseParentTransaction/isTransactionClosed — producing the success message,
     * a recorded VOID transaction and a closed authorization with nothing reversed at the processor.
     *
     * With that change, a gateway failure that the method does not classify as a benign no-op throws
     * PaymentException from the admin controller, and shouldCloseParentTransaction/isTransactionClosed are
     * explicitly false so the authorization stays open. HTTP 400 "reversal not permitted" — what CyberSource
     * returns for an authorization that has already settled — is exactly such a failure: it is not a 404 and
     * carries no NOT_FOUND reason, so Method::isExpectedVoidFailure() rejects it. (The benign counterpart, a
     * 404/NOT_FOUND reversal target, is unit-tested in Test/Unit/Model/MethodTest.)
     *
     * @magentoConfigFixture payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testRejectedReversalIsReportedAsAnErrorAndLeavesTheAuthOpen(): void
    {
        if (method_exists(AbstractMethod::class, 'isExpectedVoidFailure') === false) {
            $this->markTestSkipped(
                'Requires the TokenBase void-error-surfacing change (TokenBase PR #7, branch'
                . ' fix/void-error-surfacing, tokenbase issue #5). On TokenBase master AbstractMethod::void()'
                . ' still swallows gateway failures, so there is no behavior here to assert.'
            );
        }

        $order = $this->authorizeFixtureOrder();

        // Re-arm the stub to fail the reversal exactly as the real client fails a non-2xx response.
        $this->restStub->setResponder(
            static function (string $method, string $path, array $params): array {
                throw CyberSourceRestStub::httpError('Reversal not permitted for this transaction.', 400);
            }
        );
        $this->restStub->resetCalls();

        $this->getRequest()->setParams(['order_id' => (int)$order->getId()]);
        $this->dispatch($this->uri);

        $this->assertSame(
            ['/pts/v2/payments/' . CyberSourceRestStub::PAYMENT_ID_PREFIX . '1/reversals'],
            $this->restStub->getCalledPaths(),
            'The controller must have attempted the reversal exactly once.'
        );

        // The admin must be told the void failed, and must not be told it succeeded.
        //
        // DEFECT, asserted as-is rather than as-wanted: the gateway's own reason ("Reversal not
        // permitted for this transaction.") does NOT reach the admin. Rest::throwOnHttpError()
        // raises RuntimeException carrying the generic SHOPPER-facing decline phrase and demotes
        // the gateway message to the previous exception, which nothing unwraps. So an admin whose
        // void was rejected is told to "verify your payment details and try again" -- advice that
        // makes no sense in the admin, about a reason they are never shown.
        //
        // This assertion used to check for the gateway reason and passed, because the stub threw a
        // plain \Exception whose getMessage() WAS that reason. Correcting the stub to reproduce the
        // real exception structure exposed it. Fixing the surfacing is a separate call: the wrapper
        // wording is TokenBase's (issue #5), and deciding what an admin should see versus a shopper
        // is a product decision, not a test fix.
        $this->assertSessionMessages(
            $this->callback(
                static fn (array $messages): bool => count($messages) === 1
                    && str_contains((string)$messages[0], 'Unable to void payment:')
                    && str_contains((string)$messages[0], 'authorization may still be open')
            ),
            MessageInterface::TYPE_ERROR
        );
        $this->assertSessionMessages(
            $this->isEmpty(),
            MessageInterface::TYPE_SUCCESS
        );

        // Read the persisted state, not the dispatch's memoised copy — otherwise "the authorization is
        // still open" would pass on a stale object even if the void HAD closed it on disk.
        $this->simulateNewRequest();

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $authTransaction = $order->getPayment()->getAuthorizationTransaction();
        $this->assertNotFalse(
            $authTransaction,
            'The authorization transaction must still be there after a failed void.'
        );
        $this->assertFalse(
            (bool)$authTransaction->getIsClosed(),
            'Nothing was reversed at the processor, so the authorization must be left OPEN.'
        );
        $this->assertNotContains(
            Transaction::TYPE_VOID,
            $this->transactionTypes($order),
            'No void transaction may be recorded for a reversal the processor rejected.'
        );
    }

    /**
     * Return the transaction types recorded against the order, for membership assertions.
     *
     * @param Order $order
     * @return array<int, string>
     */
    private function transactionTypes(Order $order): array
    {
        $collection = $this->_objectManager->get(TransactionCollectionFactory::class)
            ->create()
            ->addOrderIdFilter((int)$order->getId());

        return array_map(
            static fn (TransactionInterface $transaction): string => (string)$transaction->getTxnType(),
            array_values($collection->getItems())
        );
    }

    /**
     * Authorize the fixture order so there is an open authorization to void.
     *
     * @return Order
     */
    private function authorizeFixtureOrder(): Order
    {
        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->_objectManager->get(OrderRepositoryInterface::class)->save($order);

        $this->simulateNewRequest();

        return $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
    }
}
