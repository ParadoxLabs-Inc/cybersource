<?php declare(strict_types=1);
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use Throwable;

class TransactionUpdater
{
    /**
     * TransactionUpdater constructor.
     *
     * @param Rest $restClient
     * @param Config $config
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderInterfaceFactory $orderFactory
     * @param Data $helper
     * @param StoreRepositoryInterface $storeRepository
     * @param Emulation $emulator
     * @param TransactionRepositoryInterface $transactionRepository
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(
        protected readonly Rest $restClient,
        protected readonly Config $config,
        protected readonly OrderRepositoryInterface $orderRepository,
        protected readonly OrderInterfaceFactory $orderFactory,
        protected readonly Data $helper,
        protected readonly StoreRepositoryInterface $storeRepository,
        protected readonly Emulation $emulator,
        protected readonly TransactionRepositoryInterface $transactionRepository,
        protected readonly OrderCollectionFactory $orderCollectionFactory
    ) {
    }

    /**
     * Get any decision manager updates from the last 24 hours, and update corresponding orders if appropriate.
     *
     * @return void
     */
    public function execute()
    {
        /**
         * Conversion details only ever resolve orders sitting in payment review (see processChange()),
         * so with none outstanding the whole run is a provable no-op — and on an account with no fraud
         * product at all it is an hourly 404 against a reporting endpoint the merchant does not have.
         *
         * The gate is deliberately NOT the uc_decision_manager setting. That flag only rides
         * completeMandate on the capture context; the /pts/v2/payments call the module makes itself
         * carries no fraud toggle, so whether an auth comes back *_PENDING_REVIEW is decided entirely
         * by the account's fraud configuration -- Decision Manager, Fraud Management Essentials, or a
         * processor-level rule. Response::interpretResponse() reads that off the reply status alone,
         * so an order can land in payment review with the setting off, and gating on it would strand
         * that order in review permanently. Asking what is actually pending is both safer and tighter.
         */
        if ($this->hasOrdersAwaitingReview() === false) {
            return;
        }

        $processedAccounts = [];

        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            $merchantId = (string)$this->config->getMerchantId($store->getId());

            if ($merchantId !== ''
                && $store->getIsActive()
                && $this->config->moduleIsActive($store->getId())
                && !isset($processedAccounts[$merchantId])) {
                try {
                    $processedAccounts[$merchantId] = 1;

                    $this->emulator->startEnvironmentEmulation($store->getId());

                    $this->runTransactionUpdates((int)$store->getId());

                    $this->emulator->stopEnvironmentEmulation();
                } catch (Throwable $exception) {
                    // A 404 'resource not found' response means there are no updates in the requested timespan. Ignore.
                    if ($exception->getMessage() !== 'Requested Resource Not Found') {
                        $this->helper->log(Config::CODE, $exception->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Whether any order anywhere is still awaiting a Decision Manager review outcome.
     *
     * Checked across all stores rather than per store: processChange() resolves orders by increment id
     * with no store scoping, so a poll made under one store's merchant id can legitimately settle an
     * order belonging to another store on the same organization.
     *
     * Driven off state, which core indexes (SALES_ORDER_STATE), so the scan is bounded by the number of
     * orders in payment review rather than the size of sales_order -- payment_review is a transient state
     * holding a handful of rows even on a large catalog of orders. The join then hits
     * sales_order_payment.parent_id, also indexed (SALES_ORDER_PAYMENT_PARENT_ID), one row at a time;
     * method is unindexed but is only ever evaluated against those few rows. Existence is all we need,
     * so this stops at the first match instead of counting the set.
     *
     * @return bool
     */
    protected function hasOrdersAwaitingReview(): bool
    {
        $orders = $this->orderCollectionFactory->create();
        $orders->addFieldToFilter('main_table.state', Order::STATE_PAYMENT_REVIEW);
        $orders->getSelect()
            ->join(
                ['payment' => $orders->getTable('sales_order_payment')],
                'payment.parent_id = main_table.entity_id',
                []
            )
            ->where('payment.method = ?', Config::CODE);
        $orders->setPageSize(1)
            ->setCurPage(1);

        return $orders->getFirstItem()->getId() !== null;
    }

    /**
     * Fetch and process transaction updates for the given store
     *
     * @param int $storeId
     * @return void
     * @throws \Exception
     */
    protected function runTransactionUpdates(int $storeId)
    {
        $this->restClient->setStoreId($storeId);

        $reply = $this->restClient->get(
            '/reporting/v3/conversion-details',
            [
                'startTime' => date(Sanitizer::ISO_FORMAT, strtotime('-24 hour')),
                'endTime' => date(Sanitizer::ISO_FORMAT),
                'organizationId' => $this->config->getOrganizationId($storeId),
            ]
        );

        $reply = json_decode((string)$reply, true);
        if ($reply !== false && !empty($reply['conversionDetails'])) {
            foreach ($reply['conversionDetails'] as $change) {
                try {
                    $this->processChange($change);
                } catch (Throwable $exception) {
                    $this->helper->log(Config::CODE, $exception->getMessage());
                }
            }
        }
    }

    /**
     * Process the transaction decision for any order status change.
     *
     * @param array $change
     * @return void
     * @throws LocalizedException
     */
    protected function processChange($change)
    {
        if (($change['originalDecision'] ?? null) === 'REVIEW'
            && in_array($change['newDecision'] ?? null, ['ACCEPT', 'REJECT'], true) === true) {
            /** @var Order $order */
            $order = $this->orderFactory->create();
            $order->loadByIncrementId($change['merchantReferenceNumber'] ?? '');

            if ($order->getId() && $order->getState() === Order::STATE_PAYMENT_REVIEW) {
                $this->updateOrderStatus($order, $change);

                $this->helper->log(
                    Config::CODE,
                    sprintf(
                        'Updated fraud status of order %s to %s',
                        $order->getIncrementId(),
                        $order->getStatus()
                    )
                );
                $this->helper->log(Config::CODE, json_encode($change));
            }
        }
    }

    /**
     * Update order status to approved or denied to reflect the given transaction decision.
     *
     * @param OrderInterface $order
     * @param array $change
     * @return void
     * @throws LocalizedException
     */
    protected function updateOrderStatus(OrderInterface $order, $change)
    {
        /** @var Payment $payment */
        $payment = $order->getPayment();
        if ($change['newDecision'] === 'ACCEPT') {
            $payment->setData('parent_transaction_id', $payment->getLastTransId());
            $transaction = $payment->getAuthorizationTransaction();
            if ($transaction instanceof Transaction) {
                $transaction->setAdditionalInformation('is_transaction_fraud', false);

                // Persist explicitly: this transaction was loaded via the payment's transaction manager,
                // not built through Transaction\Builder, so it is NOT an order related-object and the
                // orderRepository->save() below does not cascade to it. Without this save the cleared
                // fraud flag is silently dropped and the approved order stays marked fraudulent.
                $this->transactionRepository->save($transaction);
            }

            $payment->setIsTransactionApproved(true);
        } elseif ($change['newDecision'] === 'REJECT') {
            $payment->setIsTransactionDenied(true);
        }

        $payment->update(false);

        $this->orderRepository->save($order);
    }
}
