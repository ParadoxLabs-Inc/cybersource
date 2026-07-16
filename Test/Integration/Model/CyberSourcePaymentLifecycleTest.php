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

use Magento\Framework\DataObject;
use Magento\Framework\DB\Transaction as DbTransaction;
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
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * End-to-end CyberSource Unified Checkout payment lifecycle with the REST HTTP boundary stubbed.
 *
 * The {@see Rest} client is replaced with {@see CyberSourceRestStub} via ObjectManager, so the whole
 * Method + Gateway + UnifiedCheckout service stack (auth, capture, refund, reversal) executes against
 * canned CyberSource replies. Each test drives the real Magento payment operations and asserts on the
 * persisted sales records and on the request bodies the module actually posted.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CyberSourcePaymentLifecycleTest extends TestCase
{
    private const AUTH_TXN_ID = '7810198061286032204805';
    private const CAPTURE_TXN_ID = 'CAP7810198061286032204805';
    private const REFUND_TXN_ID = 'REF7810198061286032204805';
    private const REVERSAL_TXN_ID = 'REV7810198061286032204805';

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
        // Drop the doubles so later tests get the real gateway/HTTP client.
        $this->objectManager->removeSharedInstance(Rest::class);
        $this->objectManager->removeSharedInstance(Gateway::class);

        parent::tearDown();
    }

    /**
     * DEFECT PIN — D1 (CRITICAL): a stored-card charge poisons its own vault card.
     *
     * placeStored() replies carry no tokenInformation BY DESIGN (no actionList/TOKEN_CREATE is sent for a
     * stored-card auth). Response::extractTokenInformation() nonetheless sets uc_token_missing=true;
     * Method::applyUnifiedCheckoutToken() only bails when BOTH token_information and uc_token_missing are
     * null, so it proceeds; CardBuilder::applyTokenToCard() then stamps additional[uc_token_missing]='1'
     * on the perfectly good vaulted card, which is saved by AbstractMethod::authorize().
     *
     * A stored-card charge must NOT flag its own card as un-tokenized. THIS TEST IS EXPECTED TO FAIL
     * until D1 is fixed; the failure is the point.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_stored_card.php
     * @return void
     */
    public function testStoredCardChargeDoesNotFlagItsOwnCardUnusable(): void
    {
        $this->registerStub($this->successResponder());

        $order = $this->loadOrder('100000560');
        $cardId = (string)$order->getPayment()->getData('tokenbase_id');

        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        // The stored-card auth must have gone out with the vaulted TMS ids and no tokenInformation.
        $authCalls = $this->restStub->getCallsMatching('/pts/v2/payments');
        $this->assertCount(1, $authCalls, 'A stored-card auth should post exactly one payment.');
        $this->assertArrayNotHasKey(
            'tokenInformation',
            $authCalls[0]['params'],
            'A stored-card auth carries no transient token.'
        );
        $this->assertSame(
            'P456',
            $authCalls[0]['params']['paymentInformation']['paymentInstrument']['id'] ?? null,
            'The stored-card auth should reference the vaulted TMS paymentInstrument id.'
        );

        $card = $this->cardRepository->getById($cardId);

        // D1: this is the poisoning. The card was and remains fully tokenized; nothing may flag it.
        $this->assertNotSame(
            '1',
            $card->getAdditional(CardBuilder::CARD_FLAG_TOKEN_MISSING),
            'D1: a stored-card charge must not flag its own vaulted card uc_token_missing.'
        );
        $this->assertSame('P456', (string)$card->getPaymentId(), 'The vaulted TMS ids must be preserved.');
        $this->assertSame('C123', (string)$card->getProfileId(), 'The vaulted TMS ids must be preserved.');
    }

    /**
     * DEFECT PIN — D1 (CRITICAL): the SECOND consecutive stored-card charge must still succeed.
     *
     * This is the subscription-rebill shape. Once the first charge stamps uc_token_missing='1' (see
     * testStoredCardChargeDoesNotFlagItsOwnCardUnusable), Gateway::buildStoredCardAuth() throws
     * "This saved card is no longer usable" for every subsequent charge — killing every rebill after
     * the first. THIS TEST IS EXPECTED TO FAIL until D1 is fixed.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_stored_card.php
     * @return void
     */
    public function testSecondConsecutiveStoredCardChargeSucceeds(): void
    {
        $this->registerStub($this->successResponder());

        $first = $this->loadOrder('100000560');
        $first->getPayment()->authorize(true, (float)$first->getBaseGrandTotal());
        $this->orderRepository->save($first);

        $this->restStub->resetCalls();

        // Second rebill on the same vaulted card, exactly as a subscription would run it.
        $second = $this->loadOrder('100000561');
        $second->getPayment()->authorize(true, (float)$second->getBaseGrandTotal());
        $this->orderRepository->save($second);

        $this->assertCount(
            1,
            $this->restStub->getCallsMatching('/pts/v2/payments'),
            'D1: the second consecutive stored-card charge must reach the gateway, not be refused locally.'
        );

        $reloaded = $this->orderRepository->get((int)$second->getId());
        $this->assertNotEmpty(
            $reloaded->getPayment()->getLastTransId(),
            'D1: the second stored-card charge must record a gateway transaction.'
        );
    }

    /**
     * DEFECT PIN — D2 (CRITICAL, money): a bundled capture must post processingInformation.capture=true.
     *
     * Response::isCapture() derives the capture flag SOLELY from config payment_action, ignoring which
     * operation is actually running. With payment_action=authorize (the default), invoicing an order that
     * has no prior auth routes CAPTURE -> Gateway::captureBundled() -> buildStoredCardAuth() ->
     * placeStored(), which posts processingInformation.capture=false — an AUTHORIZATION — while Magento
     * records a paid invoice. Money is authorized and never captured.
     *
     * THIS TEST IS EXPECTED TO FAIL until D2 is fixed.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_stored_card.php
     * @return void
     */
    public function testBundledCapturePostsCaptureTrue(): void
    {
        $this->registerStub($this->successResponder());

        // No prior authorize: invoicing online here is a BUNDLED auth+capture (a sale).
        $order = $this->loadOrder('100000560');
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();

        $order->setIsInProcess(true);
        $this->objectManager->create(DbTransaction::class)
            ->addObject($invoice)
            ->addObject($order)
            ->save();

        $this->assertSame(Invoice::STATE_PAID, (int)$invoice->getState(), 'Online capture pays the invoice.');

        $payments = $this->restStub->getCallsMatching('/pts/v2/payments');
        $this->assertCount(1, $payments, 'A bundled capture posts a single /pts/v2/payments sale.');

        // D2: Magento just recorded a PAID invoice. The request must therefore have captured the funds.
        $this->assertTrue(
            $payments[0]['params']['processingInformation']['capture'] ?? null,
            'D2: a bundled capture must post processingInformation.capture=true; a paid invoice against'
            . ' an authorization-only request means the money is never captured.'
        );
    }

    /**
     * Full new-card lifecycle: authorize, then online capture (linked), then online refund (linked).
     *
     * Also the first real check of is_gateway=1 (flipped 0->1 in commit 611568e with no test evidence):
     * it governs how the invoice/credit-memo transaction states are recorded.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testNewCardAuthorizeCaptureRefundLifecycle(): void
    {
        $this->registerStub($this->successResponder());

        // 1. Authorize the new card via its Unified Checkout transient token.
        $order = $this->loadOrder('100000562');
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $authCalls = $this->restStub->getCallsMatching('/pts/v2/payments');
        $this->assertCount(1, $authCalls, 'A new-card auth posts /pts/v2/payments.');
        $this->assertSame(
            'eyJhbGciOiJSUzI1NiJ9.integration.transient',
            $authCalls[0]['params']['tokenInformation']['transientTokenJwt'] ?? null,
            'The new-card auth must carry the transient token JWT.'
        );
        $this->assertSame(
            ['TOKEN_CREATE'],
            $authCalls[0]['params']['processingInformation']['actionList'] ?? null,
            'The new-card auth must request TOKEN_CREATE.'
        );
        $this->assertFalse(
            $authCalls[0]['params']['processingInformation']['capture'] ?? null,
            'payment_action=authorize must post an authorization-only request.'
        );

        $reloaded = $this->orderRepository->get((int)$order->getId());
        $this->assertNotFalse(
            $reloaded->getPayment()->getAuthorizationTransaction(),
            'Authorize should record an authorization transaction.'
        );
        $this->assertSame(
            self::AUTH_TXN_ID,
            (string)$reloaded->getPayment()->getLastTransId(),
            'The auth should store the CyberSource payment id as the transaction id.'
        );

        // 2. Capture the authorization online -> linked capture against the auth id.
        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();
        $order->setIsInProcess(true);
        $this->objectManager->create(DbTransaction::class)
            ->addObject($invoice)
            ->addObject($order)
            ->save();

        $this->assertSame(Invoice::STATE_PAID, (int)$invoice->getState(), 'Online capture pays the invoice.');
        $this->assertContains(
            '/pts/v2/payments/' . self::AUTH_TXN_ID . '/captures',
            $this->restStub->getCalledPaths(),
            'Capture must be LINKED to the stored auth id.'
        );

        // Reload from the collection, not OrderRepository::get(), which hands back a stale cached object.
        $this->assertSame(
            24.0,
            (float)$this->loadOrder('100000562')->getPayment()->getBaseAmountPaid(),
            'The full order amount should be recorded as paid.'
        );

        // 3. Refund the invoice online -> linked refund against the CAPTURE id.
        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        /** @var Invoice $savedInvoice */
        $savedInvoice = $order->getInvoiceCollection()->getFirstItem();
        $this->assertGreaterThan(0, (int)$savedInvoice->getId(), 'Capture should have created an invoice.');

        $creditmemo = $this->creditmemoFactory->createByInvoice($savedInvoice);
        $this->creditmemoService->refund($creditmemo, false);

        $this->assertSame(
            Creditmemo::STATE_REFUNDED,
            (int)$creditmemo->getState(),
            'Online refund should mark the credit memo refunded.'
        );
        $this->assertContains(
            '/pts/v2/captures/' . self::CAPTURE_TXN_ID . '/refunds',
            $this->restStub->getCalledPaths(),
            'Refund must be LINKED to the capture id.'
        );

        $this->assertSame(
            24.0,
            (float)$this->loadOrder('100000562')->getPayment()->getBaseAmountRefunded(),
            'The full order amount should be recorded as refunded.'
        );
    }

    /**
     * Voiding an authorized-but-uncaptured order reverses the auth via /pts/v2/payments/{id}/reversals.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testVoidReversesUncapturedAuthorization(): void
    {
        $this->registerStub($this->successResponder());

        $order = $this->loadOrder('100000562');
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        $payment = $order->getPayment();
        $payment->setParentTransactionId(self::AUTH_TXN_ID);
        $payment->void(new DataObject());
        $this->orderRepository->save($order);

        $reversals = $this->restStub->getCallsMatching('/reversals');
        $this->assertCount(1, $reversals, 'An uncaptured auth must be reversed, not capture-voided.');
        $this->assertSame(
            '/pts/v2/payments/' . self::AUTH_TXN_ID . '/reversals',
            $reversals[0]['path'],
            'The reversal must target the stored auth id.'
        );
        $this->assertSame(
            '24.00',
            $reversals[0]['params']['reversalInformation']['amountDetails']['totalAmount'] ?? null,
            'The reversal body must carry reversalInformation.amountDetails.'
        );

        $reloaded = $this->orderRepository->get((int)$order->getId());
        $authTxn = $reloaded->getPayment()->getAuthorizationTransaction();
        $this->assertTrue(
            $authTxn === false || (bool)$authTxn->getIsClosed(),
            'The authorization transaction should be closed after void.'
        );
    }

    /**
     * A partial online capture, then a partial online refund, pass the requested amounts to the gateway.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testPartialCaptureAndPartialRefundByAmount(): void
    {
        $this->registerStub($this->successResponder());

        $order = $this->loadOrder('100000562');
        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        // Invoice ONE of the two ordered units online: a partial capture of 12.00 against a 24.00 auth.
        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        $orderItemId = (int)$order->getAllVisibleItems()[0]->getId();
        $invoice = $this->invoiceService->prepareInvoice($order, [$orderItemId => 1]);
        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->register();
        $order->setIsInProcess(true);
        $this->objectManager->create(DbTransaction::class)
            ->addObject($invoice)
            ->addObject($order)
            ->save();

        $captures = $this->restStub->getCallsMatching('/captures');
        $this->assertNotEmpty($captures, 'A partial capture should hit the linked capture path.');
        $this->assertSame(
            '12.00',
            $captures[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The partial capture must send the invoiced amount, not the order total.'
        );
        $this->assertSame(
            12.0,
            (float)$this->loadOrder('100000562')->getPayment()->getBaseAmountPaid(),
            'Only the invoiced amount should be recorded as paid.'
        );

        // Refund 6.00 of the 12.00 captured: a partial refund off the capture.
        $this->restStub->resetCalls();

        $order = $this->loadOrder('100000562');
        /** @var Invoice $savedInvoice */
        $savedInvoice = $order->getInvoiceCollection()->getFirstItem();
        $creditmemo = $this->creditmemoFactory->createByInvoice($savedInvoice, ['adjustment_negative' => 6]);
        $this->creditmemoService->refund($creditmemo, false);

        $refunds = $this->restStub->getCallsMatching('/refunds');
        $this->assertNotEmpty($refunds, 'A partial refund should hit the linked refund path.');
        $this->assertSame(
            '6.00',
            $refunds[0]['params']['orderInformation']['amountDetails']['totalAmount'] ?? null,
            'The partial refund must send the credit-memo amount, not the invoice or order total.'
        );
        // Base currency: that is the amount the gateway is actually asked to move.
        $this->assertSame(
            6.0,
            (float)$this->loadOrder('100000562')->getPayment()->getBaseAmountRefunded(),
            'Only the credit-memo amount should be recorded as refunded.'
        );
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
     * Responder returning an approved CyberSource reply for every lifecycle path.
     *
     * The new-card auth reply carries tokenInformation (TOKEN_CREATE succeeded); the stored-card auth
     * reply does NOT — a stored-card request sends no actionList, so CyberSource mints no new token.
     * Distinct ids per phase let the tests assert which id each follow-on was linked to.
     *
     * @return callable
     */
    private function successResponder(): callable
    {
        return static function (string $method, string $path, array $params): array {
            if (str_ends_with($path, '/reversals')) {
                return ['id' => self::REVERSAL_TXN_ID, 'status' => 'REVERSED'];
            }

            if (str_ends_with($path, '/voids')) {
                return ['id' => 'VOID' . self::AUTH_TXN_ID, 'status' => 'VOIDED'];
            }

            if (str_ends_with($path, '/captures')) {
                return ['id' => self::CAPTURE_TXN_ID, 'status' => 'PENDING'];
            }

            if (str_ends_with($path, '/refunds')) {
                return ['id' => self::REFUND_TXN_ID, 'status' => 'PENDING'];
            }

            $reply = [
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
            ];

            // TOKEN_CREATE was requested (new card) => CyberSource mints and returns the three TMS ids.
            if (isset($params['tokenInformation']['transientTokenJwt'])) {
                $reply['tokenInformation'] = [
                    'customer' => ['id' => 'C123'],
                    'paymentInstrument' => ['id' => 'P456'],
                    'instrumentIdentifier' => ['id' => 'I789'],
                ];
            }

            return $reply;
        };
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
