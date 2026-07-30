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
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Model\Card;
use ParadoxLabs\CyberSource\Model\Gateway;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\OomProbeTrait;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * CyberSource Unified Checkout vault behaviour: the D5 TMS-id mapping onto the stored card, and the TMS
 * token deletion issued when a stored card is removed.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CyberSourceTokenVaultTest extends TestCase
{
    use OomProbeTrait;

    private const AUTH_TXN_ID = '7810198061286032204805';

    private ?ObjectManager $objectManager = null;
    private ?OrderRepositoryInterface $orderRepository = null;
    private ?OrderCollectionFactory $orderCollectionFactory = null;
    private ?CardRepositoryInterface $cardRepository = null;
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
     * D5 vault mapping: a new-card auth with TOKEN_CREATE persists the TMS ids onto the vault card.
     *
     *   card paymentId                        <- tokenInformation.paymentInstrument.id  (the MIT key)
     *   card additional[instrument_identifier] <- tokenInformation.instrumentIdentifier.id
     *
     * No customer token: cards are standalone TMS payment instruments (see Response::ACTION_TOKEN_TYPES),
     * so profileId is never written.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_new_card.php
     * @return void
     */
    public function testNewCardAuthPersistsTmsIdsToVault(): void
    {
        $this->registerStub(
            static fn (): array => [
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
                    'paymentInstrument' => ['id' => 'P456'],
                    'instrumentIdentifier' => ['id' => 'I789'],
                ],
            ]
        );

        $order = $this->loadOrder('100000562');
        $cardId = (string)$order->getPayment()->getData('tokenbase_id');

        $order->getPayment()->authorize(true, (float)$order->getBaseGrandTotal());
        $this->orderRepository->save($order);

        // The request must ask for the standalone token types only — never 'customer'.
        $authCalls = $this->restStub->getCallsMatching('/pts/v2/payments');
        $this->assertSame(
            ['paymentInstrument', 'instrumentIdentifier'],
            $authCalls[0]['params']['processingInformation']['actionTokenTypes'] ?? null,
            'A new-card auth must request the standalone TMS token types (no customer).'
        );

        $card = $this->cardRepository->getById($cardId);

        $this->assertEmpty((string)$card->getProfileId(), 'No TMS customer id may be written (standalone PI).');
        $this->assertSame('P456', (string)$card->getPaymentId(), 'paymentId <- TMS paymentInstrument id.');
        $this->assertSame(
            'I789',
            (string)$card->getAdditional('instrument_identifier'),
            'additional[instrument_identifier] <- TMS instrumentIdentifier id.'
        );
        $this->assertNotSame(
            '1',
            $card->getAdditional(CardBuilder::CARD_FLAG_TOKEN_MISSING),
            'A successfully tokenized card must not be flagged uc_token_missing.'
        );

        // Card metadata mirrors the legacy Secure Acceptance field writes.
        $this->assertSame('1111', (string)$card->getAdditional('cc_last4'), 'cc_last4 <- card.suffix.');
        $this->assertSame('09', (string)$card->getAdditional('cc_exp_month'), 'cc_exp_month <- expirationMonth.');
        $this->assertSame('2029', (string)$card->getAdditional('cc_exp_year'), 'cc_exp_year <- expirationYear.');
    }

    /**
     * Deleting a stored card issues a real DELETE request to TMS for the standalone payment-instrument.
     *
     * Rest::delete() forces the verb via CURLOPT_CUSTOMREQUEST and then calls get(); the double records
     * the verb it was invoked with, so this asserts the DELETE verb actually reaches the HTTP boundary
     * rather than a GET being issued against the delete path.
     *
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture current_store payment/paradoxlabs_cybersource/payment_action authorize
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_order_with_stored_card.php
     * @return void
     */
    public function testDeleteCardIssuesTmsDeletes(): void
    {
        // TMS deletes are 204s with an empty body; Rest::delete() returns the raw body as a string.
        $this->registerStub(static fn (): string => '');

        $order = $this->loadOrder('100000560');
        $cardId = (string)$order->getPayment()->getData('tokenbase_id');

        // Mirror the production deletion path (ParadoxLabs\TokenBase\Model\Cron\Clean::deleteCards):
        // CardRepository::delete() only performs a REAL delete once the card is inactive — for an active
        // card it merely queues deletion and saves. queueDeletion() deactivates, so the subsequent
        // delete() reaches Card::beforeDelete(), which is what issues the TMS token removal.
        // Card::beforeDelete() resolves its own method instance from the method_model config; it must not
        // be handed the payment's Adapter facade, which has no gateway().
        /** @var Card $card */
        $card = $this->cardRepository->getById($cardId)->getTypeInstance();
        $card->queueDeletion();

        $this->cardRepository->delete($card);

        $this->assertSame(
            [
                '/tms/v2/payment-instruments/P456',
            ],
            $this->restStub->getCalledPaths(),
            'Card delete must remove the TMS payment-instrument only (standalone PI, no customer).'
        );

        foreach ($this->restStub->calls as $call) {
            $this->assertSame(
                'DELETE',
                $call['method'],
                'TMS token removal must use the DELETE verb, not GET, against ' . $call['path']
            );
        }
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
