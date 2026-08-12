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

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\DB\Transaction as DbTransaction;
use Magento\Framework\Message\MessageInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\TestFramework\TestCase\AbstractBackendController;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\RestStubTrait;

/**
 * Admin online credit memo against a CyberSource-captured order, pinned to a specific invoice.
 *
 * The order is authorized and invoiced twice (one invoice per line item) through the service layer, then
 * the real backend credit-memo controller is dispatched with invoice_id set. The point of the test is
 * WHICH capture the refund is linked to and for HOW MUCH: refunding invoice #1 must never touch the
 * capture that paid invoice #2.
 *
 * The inherited testAclHasAccess/testAclNoAccess cover the ACL contract for this route.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 * They are declared at BOTH default and default_store scope on purpose: the integration App\Config keeps a
 * pre-merged snapshot per scope, so a value written at default scope is invisible to the store-scoped read
 * the payment method performs (Payment::capture() calls $method->setStore($order->getStoreId())). Without
 * the default_store copy, config.xml's shipped value silently wins.
 *
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CreditmemoSaveTest extends AbstractBackendController
{
    use RestStubTrait;

    private const ORDER_INCREMENT_ID = '100000570';

    /**
     * @var string
     */
    protected $uri = 'backend/sales/order_creditmemo/save';

    /**
     * @var string
     */
    protected $resource = 'Magento_Sales::creditmemo';

    /**
     * @var string
     */
    protected $httpMethod = HttpRequest::METHOD_POST;

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
     * @inheritDoc
     *
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     */
    public function testAclHasAccess()
    {
        $this->prepareAclRequest();

        parent::testAclHasAccess();
    }

    /**
     * @inheritDoc
     *
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     */
    public function testAclNoAccess()
    {
        $this->prepareAclRequest();

        parent::testAclNoAccess();
    }

    /**
     * Put a request the credit-memo controller can actually process in place, for the inherited ACL tests.
     *
     * AbstractBackendController::testAclHasAccess()/testAclNoAccess() dispatch $uri with an empty request.
     * Creditmemo\Save is HttpPostActionInterface, so a bare GET is routed to noroute (404) and says nothing
     * about ACL; and as an empty POST the controller fatals in its own null handling —
     * adjustCreditMemoItemQuantities(false) — because _initCreditmemo() has nothing to load. So the smallest
     * request that reaches the ACL check with a loadable credit memo is built here.
     *
     * The invoice is captured OFFLINE and the memo posted with do_offline=1 deliberately: the ACL contract
     * is the subject, and an offline refund keeps the gateway entirely out of it.
     *
     * @return void
     */
    private function prepareAclRequest(): void
    {
        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);

        $invoice = $this->_objectManager->get(InvoiceService::class)
            ->prepareInvoice($order, [$this->itemId($order, 'simple') => 2]);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
        $invoice->register();

        $order->setIsInProcess(true);
        $this->_objectManager->create(DbTransaction::class)
            ->addObject($invoice)
            ->addObject($order)
            ->save();

        $this->simulateNewRequest();

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST)
            ->setParams([
                'order_id' => (int)$order->getId(),
                'invoice_id' => (int)$invoice->getId(),
            ])
            ->setPostValue([
                'creditmemo' => [
                    'do_offline' => '1',
                    'comment_text' => '',
                    'shipping_amount' => '0',
                    'adjustment_positive' => '0',
                    'adjustment_negative' => '0',
                ],
            ]);
    }

    /**
     * An online credit memo for invoice #1 refunds invoice #1's capture, for invoice #1's amount only.
     *
     * @magentoConfigFixture payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture payment/paradoxlabs_cybersource/reauthorize_partial_invoice 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/reauthorize_partial_invoice 1
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testOnlineCreditMemoRefundsTheSelectedInvoiceCapture(): void
    {
        $order = $this->captureBothInvoices();
        $firstInvoice = $this->invoiceAt($order, 0);

        $this->assertSame(
            CyberSourceRestStub::CAPTURE_ID_PREFIX . '1',
            (string)$firstInvoice->getTransactionId(),
            'Invoice #1 must carry the id of the capture that paid it.'
        );

        $this->restStub->resetCalls();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST)
            ->setParams([
                'order_id' => (int)$order->getId(),
                'invoice_id' => (int)$firstInvoice->getId(),
            ])
            ->setPostValue([
                'creditmemo' => [
                    'do_offline' => '0',
                    'comment_text' => '',
                    'shipping_amount' => '0',
                    'adjustment_positive' => '0',
                    'adjustment_negative' => '0',
                ],
            ]);

        $this->dispatch($this->uri);

        $this->assertSessionMessages(
            $this->equalTo([(string)__('You created the credit memo.')]),
            MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('sales/order/view/order_id/' . (int)$order->getId()));

        $this->assertSame(
            ['/pts/v2/captures/' . CyberSourceRestStub::CAPTURE_ID_PREFIX . '1/refunds'],
            $this->restStub->getCalledPaths(),
            'The refund must be LINKED to the selected invoice capture, and issue no other call.'
        );
        $this->assertSame(
            '24.00',
            $this->restStub->calls[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The refund must carry the selected invoice amount, not the order total.'
        );

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $creditmemos = $order->getCreditmemosCollection();
        $this->assertCount(1, $creditmemos, 'The controller must have created exactly one credit memo.');

        /** @var Creditmemo $creditmemo */
        $creditmemo = $creditmemos->getFirstItem();
        $this->assertSame(
            Creditmemo::STATE_REFUNDED,
            (int)$creditmemo->getState(),
            'An online refund marks the credit memo refunded.'
        );
        $this->assertSame(24.0, (float)$creditmemo->getBaseGrandTotal(), 'The credit memo covers invoice #1 only.');

        $this->assertSame(24.0, (float)$order->getBaseTotalRefunded(), 'Only invoice #1s amount is refunded.');
        $this->assertSame(34.0, (float)$order->getBaseTotalPaid(), 'The captured total is unchanged by a refund.');
        $this->assertTrue($order->canCreditmemo(), 'Invoice #2 remains refundable.');
        $this->assertSame(Order::STATE_PROCESSING, $order->getState(), 'A partly-refunded order stays processing.');
    }

    /**
     * Refunding the second invoice as well closes the order.
     *
     * Credit memo #1 is issued through the service layer rather than a second controller dispatch: the
     * harness shares one request/response/front-controller for the whole test method, so a second dispatch
     * silently produces no gateway call. The memo under test — the one pinned to invoice #2 — is the one
     * that goes through the real controller.
     *
     * @magentoConfigFixture payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture payment/paradoxlabs_cybersource/reauthorize_partial_invoice 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/reauthorize_partial_invoice 1
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testRefundingBothInvoicesClosesTheOrder(): void
    {
        $order = $this->captureBothInvoices();

        $this->refundOnline($this->invoiceAt($order, 0));

        $this->simulateNewRequest();

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $this->restStub->resetCalls();

        $this->postCreditmemo($order, $this->invoiceAt($order, 1));

        $this->assertSame(
            ['/pts/v2/captures/' . CyberSourceRestStub::CAPTURE_ID_PREFIX . '2/refunds'],
            $this->restStub->getCalledPaths(),
            'The second credit memo must be linked to the SECOND capture.'
        );
        $this->assertSame(
            '10.00',
            $this->restStub->calls[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The second refund must carry invoice #2s amount.'
        );

        $this->simulateNewRequest();

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $this->assertCount(2, $order->getCreditmemosCollection(), 'Both credit memos must exist.');
        $this->assertSame(34.0, (float)$order->getBaseTotalRefunded(), 'The order is fully refunded.');
        $this->assertFalse($order->canCreditmemo(), 'Nothing is left to refund.');
        $this->assertSame(Order::STATE_CLOSED, $order->getState(), 'A fully-refunded order is closed.');
    }

    /**
     * Authorize the fixture order and invoice each line item online.
     *
     * @return Order
     */
    private function captureBothInvoices(): Order
    {
        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->_objectManager->get(OrderRepositoryInterface::class)->save($order);

        foreach (['simple' => 2, 'simple2' => 1] as $sku => $qty) {
            $this->simulateNewRequest();
            $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
            $this->invoiceOnline($order, [$this->itemId($order, (string)$sku) => $qty]);
        }

        $this->simulateNewRequest();

        return $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
    }

    /**
     * Register an online invoice for the given order item quantities and persist it with the order.
     *
     * @param Order $order
     * @param array<int, int> $itemQtys
     * @return void
     */
    private function invoiceOnline(Order $order, array $itemQtys): void
    {
        $invoice = $this->_objectManager->get(InvoiceService::class)->prepareInvoice($order, $itemQtys);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();

        $order->setIsInProcess(true);
        $this->_objectManager->create(DbTransaction::class)
            ->addObject($invoice)
            ->addObject($order)
            ->save();
    }

    /**
     * Issue an online credit memo for the whole of the given invoice through the service layer.
     *
     * Used to build up prior refunds for a test whose subject is a LATER admin dispatch; the controller
     * can only be dispatched once per test method.
     *
     * @param Invoice $invoice
     * @return void
     */
    private function refundOnline(Invoice $invoice): void
    {
        $creditmemo = $this->_objectManager->get(CreditmemoFactory::class)->createByInvoice($invoice);

        $this->_objectManager->get(CreditmemoService::class)->refund($creditmemo, false);
    }

    /**
     * POST an online credit memo for the whole of the given invoice.
     *
     * @param Order $order
     * @param Invoice $invoice
     * @return void
     */
    private function postCreditmemo(Order $order, Invoice $invoice): void
    {
        $this->resetRequest();
        $this->simulateNewRequest();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST)
            ->setParams([
                'order_id' => (int)$order->getId(),
                'invoice_id' => (int)$invoice->getId(),
            ])
            ->setPostValue([
                'creditmemo' => [
                    'do_offline' => '0',
                    'comment_text' => '',
                    'shipping_amount' => '0',
                    'adjustment_positive' => '0',
                    'adjustment_negative' => '0',
                ],
            ]);

        $this->dispatch($this->uri);
    }

    /**
     * Fetch the Nth invoice of the order in creation order.
     *
     * @param Order $order
     * @param int $offset
     * @return Invoice
     */
    private function invoiceAt(Order $order, int $offset): Invoice
    {
        $invoices = array_values($order->getInvoiceCollection()->setOrder('entity_id', 'ASC')->getItems());

        $this->assertArrayHasKey($offset, $invoices, 'The order should have an invoice at offset ' . $offset);

        /** @var Invoice $invoice */
        $invoice = $invoices[$offset];

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
}
