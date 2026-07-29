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

/**
 * Admin "Void" against a CyberSource order with an open, uncaptured authorization.
 *
 * sales/order/voidPayment is the only admin route that reaches Gateway::void(), and the branch it takes is
 * money-relevant: an uncaptured authorization must be REVERSED (/pts/v2/payments/{id}/reversals), never
 * capture-voided. The rejected-reversal path is covered too, because TokenBase swallows gateway failures
 * during void and the resulting (risky) admin-visible outcome is worth pinning.
 *
 * The inherited testAclHasAccess/testAclNoAccess cover the ACL contract for this route.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS,
 * and use DEFAULT scope because the admin request does not run in the order's store scope.
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
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
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
     * BEHAVIOUR PIN — a REJECTED reversal is reported to the admin as a successful void.
     *
     * ParadoxLabs\TokenBase\Model\AbstractMethod::void() catches \Throwable around the gateway call and
     * deliberately swallows it ("Ignore void errors, let Magento proceed like it happened. Most likely the
     * auth already expired."), then unconditionally sets shouldCloseParentTransaction/isTransactionClosed.
     * So a hard gateway rejection — e.g. HTTP 400 "reversal not permitted", which is what CyberSource
     * returns for an authorization that has already settled — produces the SUCCESS message, a recorded VOID
     * transaction and a closed authorization, with nothing reversed at the processor.
     *
     * That is a deliberate upstream choice, not a CyberSource-layer bug, so this test pins the behaviour
     * rather than failing on it: if the swallow is ever narrowed (e.g. only for not-found/expired auths,
     * which is what the comment actually describes), this test must be revisited along with it.
     *
     * @magentoConfigFixture payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testRejectedReversalIsSwallowedAndStillReportedAsVoided(): void
    {
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
        $this->assertSessionMessages(
            $this->equalTo([(string)__('The payment has been voided.')]),
            MessageInterface::TYPE_SUCCESS
        );

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $authTransaction = $order->getPayment()->getAuthorizationTransaction();
        $this->assertTrue(
            $authTransaction === false || (bool)$authTransaction->getIsClosed(),
            'The swallowed failure still closes the authorization — the pinned (risky) behaviour.'
        );
        $this->assertContains(
            Transaction::TYPE_VOID,
            $this->transactionTypes($order),
            'A void transaction is recorded even though the processor rejected the reversal.'
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

        $this->resetTransactionCaches();

        return $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
    }
}
