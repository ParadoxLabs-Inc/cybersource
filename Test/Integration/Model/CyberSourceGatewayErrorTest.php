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
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Model\Gateway;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\OomProbeTrait;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * CyberSource Unified Checkout error / edge handling across the real Method + Gateway stack.
 *
 * Covers the two "approved but token-less" replies the spike found on live traffic (TMS unprovisioned,
 * Decision Manager review), and the follow-on retry-code fail-safes in FollowOn::interpretResponse() /
 * normalizeFollowOnException() that decide whether a failed capture/refund may be silently re-run.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CyberSourceGatewayErrorTest extends TestCase
{
    use OomProbeTrait;

    private const AUTH_TXN_ID = '7810198061286032204805';
    private const CAPTURE_TXN_ID = 'CAP7810198061286032204805';

    private ?ObjectManager $objectManager = null;
    private ?OrderRepositoryInterface $orderRepository = null;
    private ?OrderCollectionFactory $orderCollectionFactory = null;
    private ?CardRepositoryInterface $cardRepository = null;
    private ?InvoiceService $invoiceService = null;
    private ?CreditmemoFactory $creditmemoFactory = null;
    private ?CreditmemoService $creditmemoService = null;
    private ?CyberSourceRestStub $restStub = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->probeMemory();

        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
        $this->orderCollectionFactory = $this->objectManager->get(OrderCollectionFactory::class);
        $this->cardRepository = $this->objectManager->get(CardRepositoryInterface::class);
        $this->invoiceService = $this->objectManager->get(InvoiceService::class);
        $this->creditmemoFactory = $this->objectManager->get(CreditmemoFactory::class);
        $this->creditmemoService = $this->objectManager->get(CreditmemoService::class);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->objectManager->removeSharedInstance(Rest::class);
        $this->objectManager->removeSharedInstance(Gateway::class);

        parent::tearDown();
    }

    /**
     * REGRESSION LOCK — TMS unprovisioned: the auth stands, the order places, the card is flagged.
     *
     * The live account blocker. CyberSource replies HTTP 201 (a 2xx — no transport error) with a body
     * that LOOKS like a decline: status=DECLINED + errorInformation.reason=PROCESSOR_ERROR ("Requested
     * service is forbidden"). But processorInformation.responseCode is '100' with a real approvalCode:
     * the AUTH APPROVED; only the TOKEN_CREATE sub-service was forbidden. Response::interpretResponse()
     * keys approval off responseCode, so the order must place, no exception may escape, and the card is
     * left un-tokenized and flagged for reconciliation.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testTmsUnprovisionedReplyStillPlacesTheOrder(): void
    {
        $this->registerStub(
            static fn (): array => [
                'id' => self::AUTH_TXN_ID,
                'status' => 'DECLINED',
                'errorInformation' => [
                    'reason' => 'PROCESSOR_ERROR',
                    'message' => 'Decline - General decline by the processor.'
                        . ' Requested service is forbidden for this merchant.',
                ],
                'processorInformation' => [
                    'responseCode' => '100',
                    'approvalCode' => '888888',
                ],
            ]
        );

        $order = $this->loadOrder('100000562');
        $cardId = (string)$order->getPayment()->getData('tokenbase_id');

        // Must not throw: the auth approved.
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $reloaded = $this->orderRepository->get((int)$order->getId());
        $this->assertNotFalse(
            $reloaded->getPayment()->getAuthorizationTransaction(),
            'A responseCode=100 reply must record an authorization even when status=DECLINED.'
        );
        $this->assertSame(
            self::AUTH_TXN_ID,
            (string)$reloaded->getPayment()->getLastTransId(),
            'The approved auth id must be stored.'
        );

        $card = $this->cardRepository->getById($cardId);
        $this->assertSame(
            '1',
            $card->getAdditional(CardBuilder::CARD_FLAG_TOKEN_MISSING),
            'A token-less approval must flag the card uc_token_missing for reconciliation.'
        );
        $this->assertEmpty(
            (string)$card->getPaymentId(),
            'No TMS ids were minted, so none may be written; the card must not carry a fake token.'
        );
    }

    /**
     * AUTHORIZED_PENDING_REVIEW: approved + fraud-flagged + order held for payment review, card intact.
     *
     * Decision Manager holds the transaction, so CyberSource returns no tokenInformation inline. The auth
     * is still approved: the order must place into payment_review with the fraud flag set, and the card
     * must be left un-tokenized-but-flagged rather than stamped with empty ids.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testPendingReviewApprovesAndHoldsOrderWithoutCorruptingCard(): void
    {
        $this->registerStub(
            static fn (): array => [
                'id' => self::AUTH_TXN_ID,
                'status' => 'AUTHORIZED_PENDING_REVIEW',
                'processorInformation' => [
                    'responseCode' => '100',
                    'approvalCode' => '888888',
                    'avs' => ['code' => 'X'],
                ],
                'orderInformation' => [
                    'amountDetails' => ['authorizedAmount' => '24.00'],
                ],
            ]
        );

        $order = $this->loadOrder('100000562');
        $cardId = (string)$order->getPayment()->getData('tokenbase_id');

        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $reloaded = $this->orderRepository->get((int)$order->getId());

        $this->assertNotFalse(
            $reloaded->getPayment()->getAuthorizationTransaction(),
            'A pending-review auth is approved and must record an authorization.'
        );
        $this->assertSame(
            Order::STATE_PAYMENT_REVIEW,
            $reloaded->getState(),
            'A pending-review auth must hold the order in payment_review.'
        );
        $this->assertTrue(
            (bool)$reloaded->getPayment()->getIsFraudDetected(),
            'A pending-review auth must set the fraud flag on the payment.'
        );

        $card = $this->cardRepository->getById($cardId);
        $this->assertSame(
            '1',
            $card->getAdditional(CardBuilder::CARD_FLAG_TOKEN_MISSING),
            'A review-held auth returns no TMS ids; the card must be flagged, not silently broken.'
        );
        $this->assertEmpty((string)$card->getPaymentId(), 'No empty/fake TMS id may be written to the card.');
        $this->assertEmpty((string)$card->getProfileId(), 'No empty/fake TMS id may be written to the card.');
    }

    /**
     * FAIL-SAFE: a follow-on carrying a processor responseCode is a DECLINE, never a 242/241 retry.
     *
     * FollowOn::interpretResponse() must surface any reply that carries processorInformation.responseCode
     * as a plain CommandException with that code. Mapping it to 242 would make Gateway::capture() drop the
     * auth id and run a FRESH bundled auth+capture — a silent second charge against the customer.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testProcessorErrorOnCaptureDeclinesWithoutSilentRecharge(): void
    {
        $this->registerStub(
            function (string $method, string $path): array {
                if (str_ends_with($path, '/captures')) {
                    return [
                        'id' => self::CAPTURE_TXN_ID,
                        'status' => 'DECLINED',
                        'errorInformation' => [
                            'reason' => 'PROCESSOR_ERROR',
                            'message' => 'Decline - General decline by the processor.',
                        ],
                        'processorInformation' => ['responseCode' => '233'],
                    ];
                }

                return $this->approvedAuthReply();
            }
        );

        $order = $this->loadOrder('100000562');
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);

        $thrown = null;
        try {
            $invoice->register();
        } catch (Throwable $exception) {
            $thrown = $exception;
        }

        $this->assertInstanceOf(
            CommandException::class,
            $thrown,
            'A processor decline on capture must surface as a plain CommandException.'
        );
        $this->assertSame(
            233,
            $thrown->getCode(),
            'The decline must carry the processor responseCode, never the 242 retry code.'
        );
        $this->assertNotSame(242, $thrown->getCode(), 'A processor decline must never be mapped to 242.');

        // The critical assertion: no fresh sale was posted behind the customer's back.
        $this->assertCount(
            1,
            $this->restStub->getCallsMatching('/captures'),
            'The failed capture must not be retried.'
        );
        $freshCharges = array_filter(
            $this->restStub->getCalledPaths(),
            static fn (string $path): bool => $path === '/pts/v2/payments'
        );
        $this->assertSame(
            [],
            array_values($freshCharges),
            'A declined capture must not trigger a second /pts/v2/payments charge.'
        );
    }

    /**
     * 242 fallback: a genuinely NOT_FOUND capture target drops the auth id and re-runs a bundled sale.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testNotFoundCaptureFallsBackToRecapture(): void
    {
        $this->registerStub(
            function (string $method, string $path): array {
                if (str_ends_with($path, '/captures')) {
                    // 2xx body whose errorInformation.reason is EXACTLY NOT_FOUND -> maps to SOAP 242.
                    return [
                        'id' => self::CAPTURE_TXN_ID,
                        'status' => 'INVALID_REQUEST',
                        'errorInformation' => [
                            'reason' => 'NOT_FOUND',
                            'message' => 'The requested transaction was not found.',
                        ],
                    ];
                }

                return $this->approvedAuthReply();
            }
        );

        $order = $this->loadOrder('100000562');
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();

        $paths = $this->restStub->getCalledPaths();

        $this->assertContains(
            '/pts/v2/payments/' . self::AUTH_TXN_ID . '/captures',
            $paths,
            'The linked capture must be attempted first.'
        );
        $this->assertContains(
            '/pts/v2/payments',
            $paths,
            'A 242 (NOT_FOUND) capture target must fall back to a fresh bundled auth+capture.'
        );
        $this->assertSame(Invoice::STATE_PAID, (int)$invoice->getState(), 'The recapture should pay the invoice.');
    }

    /**
     * 241 fallback: a NOT_FOUND linked-refund target falls back to an UNLINKED credit.
     *
     * The unlinked credit must post against the original PAYMENT id resolved by getPriorTransactionId(),
     * i.e. /pts/v2/payments/{id}/refunds, never the linked /pts/v2/captures/{id}/refunds path.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testNotFoundLinkedRefundFallsBackToUnlinkedCredit(): void
    {
        $this->registerStub(
            function (string $method, string $path): array {
                // The LINKED refund (off the capture id) is not followable; the UNLINKED credit succeeds.
                if (str_starts_with($path, '/pts/v2/captures/') && str_ends_with($path, '/refunds')) {
                    return [
                        'status' => 'INVALID_REQUEST',
                        'errorInformation' => [
                            'reason' => 'NOT_FOUND',
                            'message' => 'The requested transaction was not found.',
                        ],
                    ];
                }

                if (str_ends_with($path, '/refunds')) {
                    return ['id' => 'REF' . self::AUTH_TXN_ID, 'status' => 'PENDING'];
                }

                if (str_ends_with($path, '/captures')) {
                    return ['id' => self::CAPTURE_TXN_ID, 'status' => 'PENDING'];
                }

                return $this->approvedAuthReply();
            }
        );

        $order = $this->loadOrder('100000562');
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $order = $this->loadOrder('100000562');
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();
        $order->setIsInProcess(true);
        $this->objectManager->create(DbTransaction::class)
            ->addObject($invoice)
            ->addObject($order)
            ->save();

        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        /** @var Invoice $savedInvoice */
        $savedInvoice = $order->getInvoiceCollection()->getFirstItem();
        $creditmemo = $this->creditmemoFactory->createByInvoice($savedInvoice);
        $this->creditmemoService->refund($creditmemo, false);

        $refunds = $this->restStub->getCallsMatching('/refunds');
        $this->assertCount(2, $refunds, 'The linked refund must be attempted, then the unlinked credit.');
        $this->assertStringStartsWith(
            '/pts/v2/captures/',
            $refunds[0]['path'],
            'The first attempt must be the LINKED refund off the capture id.'
        );
        $this->assertStringStartsWith(
            '/pts/v2/payments/',
            $refunds[1]['path'],
            'A 241 (NOT_FOUND) linked refund must fall back to an UNLINKED credit on the payment id.'
        );
        $this->assertSame(
            Creditmemo::STATE_REFUNDED,
            (int)$creditmemo->getState(),
            'The unlinked credit should still refund the credit memo.'
        );
    }

    /**
     * The approved new-card auth reply (TOKEN_CREATE succeeded), shared by the follow-on tests.
     *
     * @return array<string, mixed>
     */
    private function approvedAuthReply(): array
    {
        return [
            'id' => self::AUTH_TXN_ID,
            'status' => 'AUTHORIZED',
            'processorInformation' => [
                'approvalCode' => '888888',
                'responseCode' => '100',
                'avs' => ['code' => 'X'],
                'cardVerification' => ['resultCode' => 'M'],
            ],
            'orderInformation' => [
                'amountDetails' => ['authorizedAmount' => '24.00'],
            ],
            'paymentInformation' => [
                'card' => [
                    'type' => '001',
                    'suffix' => '1111',
                    'prefix' => '411111',
                    'expirationMonth' => '09',
                    'expirationYear' => '2029',
                ],
                'bin' => '601100',
            ],
            'tokenInformation' => [
                'customer' => ['id' => 'C123'],
                'paymentInstrument' => ['id' => 'P456'],
                'instrumentIdentifier' => ['id' => 'I789'],
            ],
        ];
    }

    /**
     * Register the Rest double with the given responder.
     *
     * @param callable $responder
     * @return void
     */
    private function registerStub(callable $responder): void
    {
        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder($responder);

        $this->objectManager->removeSharedInstance(Gateway::class);
        $this->objectManager->addSharedInstance($this->restStub, Rest::class);
    }

    /**
     * Load a fixture order by its increment id. Reloads deliberately: OrderRepository::get() caches.
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
