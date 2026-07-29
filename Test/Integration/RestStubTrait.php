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

use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
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
     * Drop the sales transaction repository's in-memory caches between lifecycle phases.
     *
     * Transaction\Repository memoises getByTransactionType()/getByTransactionId() results for the life of
     * the (shared) instance. A multi-capture flow writes NEW auth/capture transactions mid-test, so without
     * this a later Payment::getAuthorizationTransaction() can hand back the transaction that was current
     * during an earlier phase. Reloading the order is not enough — the repository is the shared singleton.
     *
     * @return void
     */
    protected function resetTransactionCaches(): void
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->removeSharedInstance(TransactionRepositoryInterface::class, true);
        $objectManager->removeSharedInstance(TransactionRepositoryInterface::class);
    }
}
