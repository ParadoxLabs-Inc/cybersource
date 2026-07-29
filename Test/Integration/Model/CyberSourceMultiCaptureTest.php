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

use Magento\Framework\DB\Transaction as DbTransaction;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\RestStubTrait;
use PHPUnit\Framework\TestCase;

/**
 * Multi-capture / multi-item CyberSource lifecycle with the REST HTTP boundary stubbed.
 *
 * Covers the shape a single-item order cannot: two line items, invoiced separately, each invoice driving
 * its own online capture, then credit memos pinned to a specific invoice. The exact REST calls per step
 * (path + amount) are asserted, because this is where the module has gone wrong before: a bundled capture
 * used to post an AUTHORIZATION while Magento recorded a paid invoice (fixed in a045ffc). Every capture
 * assertion below therefore pins BOTH the path family (linked capture vs bare /pts/v2/payments sale) and,
 * where a sale is expected, processingInformation.capture.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CyberSourceMultiCaptureTest extends TestCase
{
    use RestStubTrait;

    private const ORDER_INCREMENT_ID = '100000570';
    private const FIRST_ITEM_SKU = 'simple';
    private const SECOND_ITEM_SKU = 'simple2';
    private const FIRST_INVOICE_AMOUNT = 24.0;
    private const SECOND_INVOICE_AMOUNT = 10.0;
    private const ORDER_TOTAL = 34.0;

    private ?ObjectManager $objectManager = null;
    private ?OrderRepositoryInterface $orderRepository = null;
    private ?InvoiceService $invoiceService = null;
    private ?CreditmemoFactory $creditmemoFactory = null;
    private ?CreditmemoService $creditmemoService = null;
    private ?TransactionCollectionFactory $transactionCollectionFactory = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
        $this->invoiceService = $this->objectManager->get(InvoiceService::class);
        $this->creditmemoFactory = $this->objectManager->get(CreditmemoFactory::class);
        $this->creditmemoService = $this->objectManager->get(CreditmemoService::class);
        $this->transactionCollectionFactory = $this->objectManager->get(TransactionCollectionFactory::class);
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
     * Two invoices by item qty, with reauthorization of the balance ON (the shipped default).
     *
     * Step by step, the calls the module must make:
     *  1. authorize 34.00            -> POST /pts/v2/payments                 (capture=false)  => PAY1
     *  2. invoice item #1 (24.00)    -> POST /pts/v2/payments/PAY1/captures   (24.00)          => CAP1
     *                               then POST /pts/v2/payments                (10.00 reauth)   => PAY2
     *  3. invoice item #2 (10.00)    -> POST /pts/v2/payments/PAY2/captures   (10.00)          => CAP2
     *
     * Step 3 is the load-bearing one: the remainder must settle against the OPEN authorization, not go out
     * as a fresh bare /pts/v2/payments charge.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/reauthorize_partial_invoice 1
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testTwoInvoicesCaptureEachItemAgainstTheOpenAuthorization(): void
    {
        $this->registerRestStub();

        // 1. Authorize the full order.
        $order = $this->reloadOrder();
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $authCalls = $this->restStub->getCallsMatching('/pts/v2/payments');
        $this->assertCount(1, $authCalls, 'The authorization posts exactly one /pts/v2/payments.');
        $this->assertSame(
            '34.00',
            $authCalls[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The authorization must cover the whole order.'
        );
        $this->assertFalse(
            $authCalls[0]['params']['processingInformation']['capture'] ?? null,
            'payment_action=authorize must post an authorization-only request.'
        );

        // 2. Invoice line item #1 only: a partial online capture of 24.00 against the 34.00 auth.
        $this->restStub->resetCalls();

        $order = $this->reloadOrder();
        $firstInvoice = $this->invoiceOnline($order, [$this->itemId($order, self::FIRST_ITEM_SKU) => 2]);

        $this->assertSame(Invoice::STATE_PAID, (int)$firstInvoice->getState(), 'Online capture pays invoice #1.');
        $this->assertSame(
            self::FIRST_INVOICE_AMOUNT,
            (float)$firstInvoice->getBaseGrandTotal(),
            'Invoice #1 must cover only the first line item.'
        );

        $captures = $this->restStub->getCallsMatching('/captures');
        $this->assertCount(1, $captures, 'Invoice #1 must issue exactly one capture.');
        $this->assertSame(
            '/pts/v2/payments/' . CyberSourceRestStub::PAYMENT_ID_PREFIX . '1/captures',
            $captures[0]['path'],
            'The first capture must be LINKED to the stored authorization id.'
        );
        $this->assertSame(
            '24.00',
            $captures[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The first capture must send the invoiced amount, not the order total.'
        );

        // With reauthorize_partial_invoice on, the outstanding 10.00 is re-authorized off the vaulted card.
        $reauths = $this->restStub->getCallsMatching('/pts/v2/payments');
        $reauths = array_values(
            array_filter($reauths, static fn (array $call): bool => !str_ends_with($call['path'], '/captures'))
        );
        $this->assertCount(1, $reauths, 'The outstanding balance must be re-authorized exactly once.');
        $this->assertSame(
            '10.00',
            $reauths[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The reauthorization must cover only the outstanding balance.'
        );
        $this->assertFalse(
            $reauths[0]['params']['processingInformation']['capture'] ?? null,
            'A balance reauthorization is an authorization, never a sale.'
        );
        $this->assertSame(
            'P456',
            $reauths[0]['params']['paymentInformation']['paymentInstrument']['id'] ?? null,
            'The reauthorization runs off the card vaulted by the original auth, not a transient token.'
        );

        $order = $this->reloadOrder();
        $this->assertSame(
            self::FIRST_INVOICE_AMOUNT,
            (float)$order->getPayment()->getBaseAmountPaid(),
            'Only invoice #1 has been paid so far.'
        );
        $this->assertSame(self::SECOND_INVOICE_AMOUNT, (float)$order->getTotalDue(), 'The balance is still due.');
        $this->assertTrue($order->canInvoice(), 'The second line item is still invoiceable.');

        // 3. Invoice line item #2: the remainder must settle against the OPEN authorization.
        $this->restStub->resetCalls();

        $order = $this->reloadOrder();
        $authTransaction = $order->getPayment()->getAuthorizationTransaction();
        $this->assertNotFalse($authTransaction, 'The order must still hold an open authorization to capture.');
        $this->assertSame(
            CyberSourceRestStub::PAYMENT_ID_PREFIX . '2',
            (string)$authTransaction->getTxnId(),
            'The reauthorization must be the authorization of record for the remaining balance.'
        );

        $secondInvoice = $this->invoiceOnline($order, [$this->itemId($order, self::SECOND_ITEM_SKU) => 1]);

        $this->assertSame(Invoice::STATE_PAID, (int)$secondInvoice->getState(), 'Online capture pays invoice #2.');
        $this->assertSame(
            self::SECOND_INVOICE_AMOUNT,
            (float)$secondInvoice->getBaseGrandTotal(),
            'Invoice #2 must cover only the second line item.'
        );

        $captures = $this->restStub->getCallsMatching('/captures');
        $this->assertCount(1, $captures, 'Invoice #2 must issue exactly one capture.');
        $this->assertSame(
            '/pts/v2/payments/' . CyberSourceRestStub::PAYMENT_ID_PREFIX . '2/captures',
            $captures[0]['path'],
            'The second capture must be LINKED to the reauthorized balance, not sent as a fresh charge.'
        );
        $this->assertSame(
            '10.00',
            $captures[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The second capture must send the remaining invoiced amount.'
        );
        $this->assertSame(
            [],
            array_values(
                array_filter(
                    $this->restStub->getCalledPaths(),
                    static fn (string $path): bool => $path === '/pts/v2/payments'
                )
            ),
            'The remainder must NOT go out as a bare /pts/v2/payments charge (double-charge guard).'
        );

        // 4. The order is now fully invoiced and fully paid, with one capture transaction per invoice.
        $order = $this->reloadOrder();
        $this->assertFalse($order->canInvoice(), 'Nothing is left to invoice.');
        $this->assertSame(self::ORDER_TOTAL, (float)$order->getBaseTotalInvoiced(), 'The order is fully invoiced.');
        $this->assertSame(self::ORDER_TOTAL, (float)$order->getBaseTotalPaid(), 'The order is fully paid.');
        $this->assertSame(0.0, (float)$order->getTotalDue(), 'Nothing is left due.');
        $this->assertSame(Order::STATE_PROCESSING, $order->getState(), 'A fully-captured order is processing.');

        $this->assertSame(
            [CyberSourceRestStub::CAPTURE_ID_PREFIX . '1', CyberSourceRestStub::CAPTURE_ID_PREFIX . '2'],
            $this->transactionIds($order, Transaction::TYPE_CAPTURE),
            'Each invoice must record its own capture transaction, keyed on the gateway capture id.'
        );

        $authTransaction = $this->reloadOrder()->getPayment()->getAuthorizationTransaction();
        $this->assertNotFalse($authTransaction, 'The authorization transaction must survive full capture.');
        $this->assertTrue(
            (bool)$authTransaction->getIsClosed(),
            'The authorization of record must be closed once the balance is fully captured.'
        );
    }

    /**
     * The same two-invoice flow with reauthorization of the balance OFF.
     *
     * Without a reauthorization there is no usable authorization left for the remainder (TokenBase records
     * a placeholder "{captureId}-auth" transaction it explicitly refuses to capture against), so the second
     * invoice MUST go out as a bundled auth+capture on the vaulted card — a sale. That is exactly the path
     * that used to post capture=false while Magento marked the invoice paid (a045ffc), so the capture flag
     * is pinned here.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/reauthorize_partial_invoice 0
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testRemainderIsChargedAsABundledSaleWhenReauthorizationIsDisabled(): void
    {
        $this->registerRestStub();

        $order = $this->reloadOrder();
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        // Invoice #1: linked capture, and NO reauthorization call.
        $this->restStub->resetCalls();

        $order = $this->reloadOrder();
        $this->invoiceOnline($order, [$this->itemId($order, self::FIRST_ITEM_SKU) => 2]);

        $this->assertSame(
            ['/pts/v2/payments/' . CyberSourceRestStub::PAYMENT_ID_PREFIX . '1/captures'],
            $this->restStub->getCalledPaths(),
            'With reauthorization off, invoice #1 issues a linked capture and nothing else.'
        );

        // Invoice #2: bundled auth+capture on the vaulted card.
        $this->restStub->resetCalls();

        $order = $this->reloadOrder();
        $secondInvoice = $this->invoiceOnline($order, [$this->itemId($order, self::SECOND_ITEM_SKU) => 1]);

        $this->assertSame(Invoice::STATE_PAID, (int)$secondInvoice->getState(), 'Online capture pays invoice #2.');
        $this->assertSame(
            ['/pts/v2/payments'],
            $this->restStub->getCalledPaths(),
            'With no usable authorization left, the remainder is a bundled sale on the vaulted card.'
        );

        $sale = $this->restStub->getCallsMatching('/pts/v2/payments')[0];
        $this->assertTrue(
            $sale['params']['processingInformation']['capture'] ?? null,
            'A bundled capture must post processingInformation.capture=true; a paid invoice against an'
            . ' authorization-only request means the money is never captured.'
        );
        $this->assertSame(
            '10.00',
            $sale['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The bundled sale must charge only the remaining invoiced amount.'
        );
        $this->assertSame(
            'P456',
            $sale['params']['paymentInformation']['paymentInstrument']['id'] ?? null,
            'The bundled sale runs off the vaulted TMS payment instrument.'
        );

        $order = $this->reloadOrder();
        $this->assertFalse($order->canInvoice(), 'Nothing is left to invoice.');
        $this->assertSame(self::ORDER_TOTAL, (float)$order->getBaseTotalPaid(), 'The order is fully paid.');
        $this->assertSame(0.0, (float)$order->getTotalDue(), 'Nothing is left due.');
    }

    /**
     * A credit memo pinned to invoice #1 refunds THAT capture, for THAT invoice's amount, and nothing else.
     *
     * The wrong-capture failure mode is silent (the gateway happily refunds the other capture), so the
     * linked path is asserted exactly.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/reauthorize_partial_invoice 1
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testCreditMemoPinnedToFirstInvoiceRefundsOnlyThatCapture(): void
    {
        $this->registerRestStub();
        $this->captureBothInvoices();

        $this->restStub->resetCalls();

        $order = $this->reloadOrder();
        $firstInvoice = $this->invoiceByIncrementOrder($order, 0);
        $this->assertSame(
            CyberSourceRestStub::CAPTURE_ID_PREFIX . '1',
            (string)$firstInvoice->getTransactionId(),
            'Invoice #1 must carry the id of the capture that paid it.'
        );

        $creditmemo = $this->creditmemoFactory->createByInvoice($firstInvoice);
        $this->creditmemoService->refund($creditmemo, false);

        $this->assertSame(
            Creditmemo::STATE_REFUNDED,
            (int)$creditmemo->getState(),
            'An online refund marks the credit memo refunded.'
        );
        $this->assertSame(
            ['/pts/v2/captures/' . CyberSourceRestStub::CAPTURE_ID_PREFIX . '1/refunds'],
            $this->restStub->getCalledPaths(),
            'The refund must be LINKED to invoice #1s capture, and issue no other call.'
        );
        $this->assertSame(
            '24.00',
            $this->restStub->calls[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The refund must send invoice #1s amount, not the order total.'
        );

        $order = $this->reloadOrder();
        $this->assertSame(
            self::FIRST_INVOICE_AMOUNT,
            (float)$order->getPayment()->getBaseAmountRefunded(),
            'Only invoice #1s amount may be refunded.'
        );
        $this->assertSame(
            self::SECOND_INVOICE_AMOUNT,
            (float)$order->getBaseTotalPaid() - (float)$order->getBaseTotalRefunded(),
            'Invoice #2 must remain paid and unrefunded.'
        );
        $this->assertTrue($order->canCreditmemo(), 'Invoice #2 is still refundable.');
    }

    /**
     * Refunding both invoices after a multi-capture credits each capture separately and closes the order.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/reauthorize_partial_invoice 1
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testFullRefundAfterMultiCaptureCreditsEachCapture(): void
    {
        $this->registerRestStub();
        $this->captureBothInvoices();

        $this->restStub->resetCalls();

        foreach ([0, 1] as $offset) {
            $order = $this->reloadOrder();
            $invoice = $this->invoiceByIncrementOrder($order, $offset);
            $creditmemo = $this->creditmemoFactory->createByInvoice($invoice);
            $this->creditmemoService->refund($creditmemo, false);
        }

        $this->assertSame(
            [
                '/pts/v2/captures/' . CyberSourceRestStub::CAPTURE_ID_PREFIX . '1/refunds',
                '/pts/v2/captures/' . CyberSourceRestStub::CAPTURE_ID_PREFIX . '2/refunds',
            ],
            $this->restStub->getCalledPaths(),
            'A full refund after a multi-capture must credit each capture individually.'
        );
        $this->assertSame(
            ['24.00', '10.00'],
            array_map(
                static fn (array $call): ?string
                    => $call['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
                $this->restStub->calls
            ),
            'Each refund must carry its own invoice amount.'
        );

        $order = $this->reloadOrder();
        $this->assertSame(self::ORDER_TOTAL, (float)$order->getBaseTotalRefunded(), 'The order is fully refunded.');
        $this->assertFalse($order->canCreditmemo(), 'Nothing is left to refund.');
        $this->assertSame(Order::STATE_CLOSED, $order->getState(), 'A fully-refunded order is closed.');

        $this->assertSame(
            [CyberSourceRestStub::REFUND_ID_PREFIX . '1', CyberSourceRestStub::REFUND_ID_PREFIX . '2'],
            $this->transactionIds($this->reloadOrder(), Transaction::TYPE_REFUND),
            'Each credit memo must record its own refund transaction.'
        );
    }

    /**
     * Authorize the order, then invoice each line item online, leaving it fully captured.
     *
     * @return void
     */
    private function captureBothInvoices(): void
    {
        $order = $this->reloadOrder();
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $order = $this->reloadOrder();
        $this->invoiceOnline($order, [$this->itemId($order, self::FIRST_ITEM_SKU) => 2]);

        $order = $this->reloadOrder();
        $this->invoiceOnline($order, [$this->itemId($order, self::SECOND_ITEM_SKU) => 1]);
    }

    /**
     * Register an online invoice for the given order item quantities and persist it with the order.
     *
     * @param Order $order
     * @param array<int, int> $itemQtys
     * @return Invoice
     */
    private function invoiceOnline(Order $order, array $itemQtys): Invoice
    {
        $invoice = $this->invoiceService->prepareInvoice($order, $itemQtys);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();

        $order->setIsInProcess(true);
        $this->objectManager->create(DbTransaction::class)
            ->addObject($invoice)
            ->addObject($order)
            ->save();

        return $invoice;
    }

    /**
     * Resolve the order item id for a SKU, so quantity maps never depend on item ordering.
     *
     * @param Order $order
     * @param string $sku
     * @return int
     */
    private function itemId(Order $order, string $sku): int
    {
        foreach ($order->getAllVisibleItems() as $item) {
            if ($item->getSku() === $sku) {
                return (int)$item->getId();
            }
        }

        $this->fail('Fixture order is missing the ' . $sku . ' line item.');
    }

    /**
     * Fetch the Nth invoice of the order in creation order.
     *
     * @param Order $order
     * @param int $offset
     * @return Invoice
     */
    private function invoiceByIncrementOrder(Order $order, int $offset): Invoice
    {
        $invoices = array_values($order->getInvoiceCollection()->setOrder('entity_id', 'ASC')->getItems());

        $this->assertArrayHasKey($offset, $invoices, 'The order should have an invoice at offset ' . $offset);

        /** @var Invoice $invoice */
        $invoice = $invoices[$offset];

        return $invoice;
    }

    /**
     * Return the order's transaction ids of the given type, oldest first.
     *
     * @param Order $order
     * @param string $type
     * @return array<int, string>
     */
    private function transactionIds(Order $order, string $type): array
    {
        $collection = $this->transactionCollectionFactory->create()
            ->addOrderIdFilter((int)$order->getId())
            ->addTxnTypeFilter($type)
            ->setOrder('transaction_id', 'ASC');

        return array_map(
            static fn (TransactionInterface $transaction): string => (string)$transaction->getTxnId(),
            array_values($collection->getItems())
        );
    }

    /**
     * Reload the fixture order at a request boundary: Magento's in-memory sales registries are dropped
     * first, exactly as a fresh admin request would, so each phase sees the transactions on disk.
     *
     * @return Order
     */
    private function reloadOrder(): Order
    {
        $this->simulateNewRequest();

        return $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
    }
}
