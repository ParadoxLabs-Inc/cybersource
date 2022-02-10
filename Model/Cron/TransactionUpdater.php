<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model\Cron;

use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;

/**
 * TransactionUpdater Class
 */
class TransactionUpdater
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Rest
     */
    protected $restClient;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterfaceFactory
     */
    protected $orderFactory;

    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulator;

    /**
     * TransactionUpdater constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Service\Rest $restClient
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Store\Model\App\Emulation $emulator
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Service\Rest $restClient,
        Config $config,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \ParadoxLabs\CyberSource\Helper\Data $helper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Store\Model\App\Emulation $emulator
    ) {
        $this->restClient = $restClient;
        $this->config = $config;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->helper = $helper;
        $this->storeRepository = $storeRepository;
        $this->emulator = $emulator;
    }

    /**
     * Get any decision manager updates from the last 24 hours, and update corresponding orders if appropriate.
     *
     * @return void
     */
    public function execute()
    {
        $processedAccounts = [];

        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            if ($store->getIsActive()
                && $this->config->moduleIsActive($store->getId())
                && !isset($processedAccounts[ $this->config->getMerchantId($store->getId()) ])) {
                try {
                    $processedAccounts[ $this->config->getMerchantId($store->getId()) ] = 1;

                    $this->emulator->startEnvironmentEmulation($store->getId());

                    $this->runTransactionUpdates((int)$store->getId());

                    $this->emulator->stopEnvironmentEmulation();
                } catch (\Exception $exception) {
                    $this->helper->log(Config::CODE, $exception->getMessage());
                }
            }
        }
    }

    /**
     * Fetch and process transaction updates for the given store
     *
     * @param int $storeId
     * @return void
     * @throws \Zend_Http_Client_Exception
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
                } catch (\Exception $exception) {
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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processChange($change)
    {
        if ($change['originalDecision'] === 'REVIEW'
            && in_array($change['newDecision'], ['ACCEPT', 'REJECT'], true) === true) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderFactory->create();
            $order->loadByIncrementId($change['merchantReferenceNumber']);

            if ($order->getId() && $order->getState() === \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW) {
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
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $change
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function updateOrderStatus(\Magento\Sales\Api\Data\OrderInterface $order, $change)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $order->getPayment();
        if ($change['newDecision'] === 'ACCEPT') {
            $payment->setData('parent_transaction_id', $payment->getTransactionId());
            $transaction = $payment->getAuthorizationTransaction();
            $transaction->setAdditionalInformation('is_transaction_fraud', false);

            $payment->setIsTransactionApproved(true);
        } elseif ($change['newDecision'] === 'REJECT') {
            $payment->setIsTransactionDenied(true);
        }

        $payment->update(false);

        $this->orderRepository->save($order);
    }
}
