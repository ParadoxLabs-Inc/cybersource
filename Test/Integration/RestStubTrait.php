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

namespace ParadoxLabs\CyberSource\Test\Integration;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction\Manager as TransactionManager;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface as TransactionManagerInterface;
use Magento\Sales\Model\Order\Payment\Transaction\Repository as TransactionRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Model\Gateway;
use ParadoxLabs\CyberSource\Model\Service\Rest;

/**
 * Shared plumbing for integration tests that drive the module against a stubbed REST boundary.
 *
 * Kept separate from {@see CyberSourcePaymentLifecycleTest}, which carries its own private copies, so the
 * existing green suite is not disturbed. Everything here resolves collaborators through
 * {@see Bootstrap::getObjectManager()} rather than host-class properties, so the trait works equally in a
 * plain TestCase and in an AbstractBackendController subclass.
 */
trait RestStubTrait
{
    /**
     * @var CyberSourceRestStub|null
     */
    protected ?CyberSourceRestStub $restStub = null;

    /**
     * Install the REST double, wired to the sequenced success responder unless one is supplied.
     *
     * @param callable|null $responder
     * @return CyberSourceRestStub
     */
    protected function registerRestStub(?callable $responder = null): CyberSourceRestStub
    {
        $this->restStub = new CyberSourceRestStub();
        $this->restStub->setResponder($responder ?? $this->sequencedResponder());

        $objectManager = Bootstrap::getObjectManager();
        $objectManager->removeSharedInstance(Gateway::class);
        $objectManager->addSharedInstance($this->restStub, Rest::class);

        return $this->restStub;
    }

    /**
     * Drop the doubles so later tests get the real gateway/HTTP client.
     *
     * @return void
     */
    protected function unregisterRestStub(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->removeSharedInstance(Rest::class);
        $objectManager->removeSharedInstance(Gateway::class);

        $this->restStub = null;
    }

    /**
     * Approved-for-everything responder that mints a DISTINCT id per call, per operation family.
     *
     * Distinct ids matter here: a multi-capture order records several capture/auth transactions against
     * one payment, and sales_payment_transaction is unique on (payment, txn_id) — reusing one canned id
     * would collide, and would also make it impossible to tell which capture a later refund was linked to.
     * Ids run PAY1, PAY2, ... / CAP1, CAP2, ... in call order.
     *
     * The reply echoes the requested amount back as authorizedAmount, so amount handling stays honest.
     *
     * @return callable
     */
    protected function sequencedResponder(): callable
    {
        $counters = [
            'payments' => 0,
            'captures' => 0,
            'refunds' => 0,
            'reversals' => 0,
            'voids' => 0,
        ];

        return function (string $method, string $path, array $params) use (&$counters): array {
            if (str_ends_with($path, '/reversals')) {
                $counters['reversals']++;

                return [
                    'id' => CyberSourceRestStub::REVERSAL_ID_PREFIX . $counters['reversals'],
                    'status' => 'REVERSED',
                ];
            }

            if (str_ends_with($path, '/voids')) {
                $counters['voids']++;

                return [
                    'id' => CyberSourceRestStub::VOID_ID_PREFIX . $counters['voids'],
                    'status' => 'VOIDED',
                ];
            }

            if (str_ends_with($path, '/captures')) {
                $counters['captures']++;

                return [
                    'id' => CyberSourceRestStub::CAPTURE_ID_PREFIX . $counters['captures'],
                    'status' => 'PENDING',
                ];
            }

            if (str_ends_with($path, '/refunds')) {
                $counters['refunds']++;

                return [
                    'id' => CyberSourceRestStub::REFUND_ID_PREFIX . $counters['refunds'],
                    'status' => 'PENDING',
                ];
            }

            $counters['payments']++;
            $amount = $params['orderInformation']['amountDetails']['totalAmount'] ?? null;

            $reply = [
                'id' => CyberSourceRestStub::PAYMENT_ID_PREFIX . $counters['payments'],
                'status' => 'AUTHORIZED',
                'processorInformation' => [
                    'approvalCode' => '888888',
                    'responseCode' => '100',
                    'avs' => ['code' => 'X'],
                    'cardVerification' => ['resultCode' => 'M'],
                ],
                'orderInformation' => [
                    'amountDetails' => ['authorizedAmount' => $amount],
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

            // TOKEN_CREATE was requested (new card) => CyberSource mints and returns the TMS ids.
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
     * Load a fixture order by increment id. Reloads deliberately: OrderRepository::get() caches.
     *
     * @param string $incrementId
     * @return Order
     */
    protected function loadOrderByIncrementId(string $incrementId): Order
    {
        /** @var Order $order */
        $order = Bootstrap::getObjectManager()
            ->get(OrderCollectionFactory::class)
            ->create()
            ->addFieldToFilter('increment_id', $incrementId)
            ->setPageSize(1)
            ->getFirstItem();

        $this->assertGreaterThan(0, (int)$order->getId(), 'Fixture should have created order ' . $incrementId);

        return $order;
    }

    /**
     * Drop Magento's in-memory sales registries, as the next HTTP request would.
     *
     * Every payment operation is its own admin request in production; running several of them in one PHP
     * process is a test-only situation, and three Magento caches make that visibly different:
     *
     * - {@see \Magento\Sales\Model\Order\Payment\Transaction\Repository} memoises
     *   getByTransactionType()/getByTransactionId() by (type, payment id), so a second capture keeps seeing
     *   the authorization that was current before the first one. Payment::canCapture() then finds a closed
     *   authorization and skips the capture, or getAuthorizationTransaction() hands back the superseded
     *   auth instead of the reauthorization of record.
     * - {@see \Magento\Sales\Model\Order\Payment\Transaction\Manager} is what Payment actually calls, and
     *   it holds its OWN reference to the repository — dropping the repository alone leaves the manager
     *   handing out memoised results through the old instance.
     * - {@see \Magento\Sales\Model\OrderRepository} memoises orders by id (save() registers them too), so
     *   a controller dispatch gets back the very payment object an earlier operation left transient state
     *   on. transaction_id is not a database column: Payment::_generateTransactionId() skips setting
     *   parent_transaction_id when one is already present, which leaves the gateway void with an empty
     *   target id, and Payment::_void() then decides the transaction already exists and records nothing.
     *
     * @return void
     */
    protected function simulateNewRequest(): void
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->removeSharedInstance(TransactionRepositoryInterface::class, true);
        $objectManager->removeSharedInstance(TransactionRepositoryInterface::class);
        $objectManager->removeSharedInstance(TransactionRepository::class);
        $objectManager->removeSharedInstance(TransactionManagerInterface::class, true);
        $objectManager->removeSharedInstance(TransactionManagerInterface::class);
        $objectManager->removeSharedInstance(TransactionManager::class);
        $objectManager->removeSharedInstance(OrderRepositoryInterface::class, true);
        $objectManager->removeSharedInstance(OrderRepositoryInterface::class);
        $objectManager->removeSharedInstance(OrderRepository::class);
    }
}
