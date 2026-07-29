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
use Magento\Framework\Message\MessageInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\TestFramework\TestCase\AbstractBackendController;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\RestStubTrait;

/**
 * Admin partial-invoice (online capture) against a CyberSource-authorized order.
 *
 * Drives the real backend controller — ACL, form key, request parsing, invoice registration and the
 * gateway call — with only the CyberSource REST HTTP boundary stubbed, so the admin path is covered
 * end to end rather than through the service layer alone.
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
class InvoiceSaveTest extends AbstractBackendController
{
    use RestStubTrait;

    private const ORDER_INCREMENT_ID = '100000570';

    /**
     * @var string
     */
    protected $uri = 'backend/sales/order_invoice/save';

    /**
     * @var string
     */
    protected $resource = 'Magento_Sales::invoice';

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
     * Invoicing one line item online creates a paid partial invoice and captures only that amount.
     *
     * @magentoConfigFixture payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture payment/paradoxlabs_cybersource/reauthorize_partial_invoice 0
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testPartialInvoiceCapturesOnlyTheInvoicedAmount(): void
    {
        $order = $this->authorizeFixtureOrder();
        $firstItemId = $this->itemId($order, 'simple');

        $this->restStub->resetCalls();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST)
            ->setParams(['order_id' => (int)$order->getId()])
            ->setPostValue([
                'invoice' => [
                    'items' => [$firstItemId => 2],
                    'capture_case' => Invoice::CAPTURE_ONLINE,
                    'comment_text' => '',
                    'do_shipment' => false,
                    'send_email' => false,
                ],
            ]);

        $this->dispatch($this->uri);

        $this->assertSessionMessages(
            $this->equalTo([(string)__('The invoice has been created.')]),
            MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('sales/order/view/order_id/' . (int)$order->getId()));

        // Exactly one linked capture, for the invoiced amount only.
        $this->assertSame(
            ['/pts/v2/payments/' . CyberSourceRestStub::PAYMENT_ID_PREFIX . '1/captures'],
            $this->restStub->getCalledPaths(),
            'A partial admin invoice must issue exactly one capture, linked to the stored authorization.'
        );
        $this->assertSame(
            '24.00',
            $this->restStub->calls[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The capture must carry the invoiced amount, not the order total.'
        );

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $invoices = $order->getInvoiceCollection();
        $this->assertCount(1, $invoices, 'The controller must have created exactly one invoice.');

        /** @var Invoice $invoice */
        $invoice = $invoices->getFirstItem();
        $this->assertSame(Invoice::STATE_PAID, (int)$invoice->getState(), 'An online capture pays the invoice.');
        $this->assertSame(24.0, (float)$invoice->getBaseGrandTotal(), 'Only the first line item was invoiced.');
        $this->assertSame(
            CyberSourceRestStub::CAPTURE_ID_PREFIX . '1',
            (string)$invoice->getTransactionId(),
            'The invoice must record the gateway capture id.'
        );

        $this->assertSame(24.0, (float)$order->getBaseTotalPaid(), 'Only the invoiced amount is paid.');
        $this->assertSame(10.0, (float)$order->getTotalDue(), 'The remaining line item is still due.');
        $this->assertTrue($order->canInvoice(), 'The order remains partially invoiceable.');
        $this->assertSame(Order::STATE_PROCESSING, $order->getState(), 'A partly-paid order is processing.');
    }

    /**
     * A second admin invoice for the remaining item completes the order.
     *
     * @magentoConfigFixture payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoConfigFixture payment/paradoxlabs_cybersource/reauthorize_partial_invoice 0
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_two_items.php
     * @return void
     */
    public function testSecondAdminInvoiceCompletesTheOrder(): void
    {
        $order = $this->authorizeFixtureOrder();

        $this->postInvoice($order, [$this->itemId($order, 'simple') => 2]);

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $this->restStub->resetCalls();

        $this->postInvoice($order, [$this->itemId($order, 'simple2') => 1]);

        $this->assertSessionMessages(
            $this->contains((string)__('The invoice has been created.')),
            MessageInterface::TYPE_SUCCESS
        );

        $this->assertCount(
            1,
            $this->restStub->calls,
            'The second admin invoice must issue exactly one gateway call.'
        );
        $this->assertSame(
            '10.00',
            $this->restStub->calls[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The second capture must carry only the remaining invoiced amount.'
        );

        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $this->assertCount(2, $order->getInvoiceCollection(), 'Both invoices must exist.');
        $this->assertFalse($order->canInvoice(), 'Nothing is left to invoice.');
        $this->assertSame(34.0, (float)$order->getBaseTotalPaid(), 'The order is fully paid.');
        $this->assertSame(0.0, (float)$order->getTotalDue(), 'Nothing is left due.');
    }

    /**
     * Authorize the fixture order through the payment method so the admin invoice has an auth to capture.
     *
     * @return Order
     */
    private function authorizeFixtureOrder(): Order
    {
        $order = $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->_objectManager->get(OrderRepositoryInterface::class)->save($order);

        return $this->loadOrderByIncrementId(self::ORDER_INCREMENT_ID);
    }

    /**
     * POST an online invoice for the given order item quantities.
     *
     * @param Order $order
     * @param array<int, int> $itemQtys
     * @return void
     */
    private function postInvoice(Order $order, array $itemQtys): void
    {
        $this->resetRequest();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST)
            ->setParams(['order_id' => (int)$order->getId()])
            ->setPostValue([
                'invoice' => [
                    'items' => $itemQtys,
                    'capture_case' => Invoice::CAPTURE_ONLINE,
                    'comment_text' => '',
                    'do_shipment' => false,
                    'send_email' => false,
                ],
            ]);

        $this->dispatch($this->uri);
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
