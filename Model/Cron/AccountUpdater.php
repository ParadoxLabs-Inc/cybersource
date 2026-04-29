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
use ParadoxLabs\TokenBase\Model\Card;
use ParadoxLabs\TokenBase\Model\ResourceModel\Card\Collection;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory;
use Throwable;

class AccountUpdater
{
    const UPDATE_CODES = [
        'NAN', // New account number
        'NED', // New expiration date
    ];

    const INVALID_CODES = [
        'ACL', // Account closed
        'CCH', // Contact cardholder
        'DEC', // Declined
        'ERR', // Error
    ];

    /**
     * TransactionUpdater constructor.
     *
     * @param Rest $restClient
     * @param Config $config
     * @param Data $helper
     * @param CardRepositoryInterface $cardRepository
     * @param \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory
     * @param CardType $cardType
     * @param StoreRepositoryInterface $storeRepository
     * @param Emulation $emulator
     */
    public function __construct(
        protected readonly Rest $restClient,
        protected readonly Config $config,
        protected readonly Data $helper,
        protected readonly CardRepositoryInterface $cardRepository,
        protected readonly CollectionFactory $cardCollectionFactory,
        protected readonly CardType $cardType,
        protected readonly StoreRepositoryInterface $storeRepository,
        protected readonly Emulation $emulator
    ) {
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
            $merchantId = (string)$this->config->getMerchantId($store->getId());

            if ($merchantId !== ''
                && $store->getIsActive()
                && $this->config->moduleIsActive($store->getId())
                && !isset($processedAccounts[$merchantId])) {
                try {
                    $processedAccounts[$merchantId] = 1;

                    $this->emulator->startEnvironmentEmulation($store->getId());

                    $this->runAccountUpdater((int)$store->getId());

                    $this->emulator->stopEnvironmentEmulation();
                } catch (Throwable $exception) {
                    $this->helper->log(Config::CODE, $exception->getMessage());
                }
            }
        }
    }

    /**
     * Fetch and process Account Updater changes for the given store.
     *
     * @param int $storeId
     * @return void
     * @throws \Exception
     */
    protected function runAccountUpdater(int $storeId)
    {
        $this->restClient->setStoreId($storeId);

        $reply = $this->restClient->get(
            '/accountupdater/v1/batches',
            [],
            'application/json'
        );

        $reply = json_decode((string)$reply, true);

        if ($reply !== false && !empty($reply['_embedded']['batches'])) {
            $mostRecentBatches = [];

            foreach ($reply['_embedded']['batches'] as $batch) {
                if (!isset($mostRecentBatches[ $batch['batchSource'] ]) && $batch['status'] === 'COMPLETE') {
                    $mostRecentBatches[ $batch['batchSource'] ] = $batch;
                }
            }

            foreach ($mostRecentBatches as $batch) {
                if (!empty($batch['totals']['updatedRecords'])) {
                    try {
                        $this->processBatch($batch);
                    } catch (Throwable $exception) {
                        $this->helper->log(Config::CODE, $exception->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Fetch and process card updates for the given updates report batch
     *
     * @param array $batch
     * @return void
     * @throws LocalizedException
     */
    public function processBatch($batch)
    {
        $path = substr(
            (string)$batch['_links']['reports'][0]['href'],
            strpos((string)$batch['_links']['reports'][0]['href'], '.com') + 4
        );

        $reply  = $this->restClient->get($path, [], 'application/json');
        $report = json_decode((string)$reply, true);

        if (!empty($report['records'])) {
            foreach ($report['records'] as $update) {
                // If response is NAN, NED, update BIN/last4/expir
                // If response is ACL, CCH, DEC, ERR, mark inactive
                // If response is CUR, NUP, UNA, ignore
                if (in_array($update['responseRecord']['response'] ?? null, static::UPDATE_CODES, true)) {
                    $this->updateCard($update);
                } elseif (in_array($update['responseRecord']['response'] ?? null, static::INVALID_CODES, true)) {
                    $this->disableCard($update);
                }
            }
        }
    }

    /**
     * Update the given token with new card data, if it's on file and any info has changed.
     *
     * @param array $update
     * @return void
     */
    public function updateCard($update)
    {
        $cards = $this->loadCards($update['sourceRecord']['token']);

        /** @var Card $card */
        foreach ($cards as $card) {
            $yr         = $update['responseRecord']['cardExpiryYear'];
            $mo         = $update['responseRecord']['cardExpiryMonth'];
            $day        = date('t', strtotime($yr . '-' . $mo));
            $newExpires = sprintf('%s-%s-%s 23:59:59', $yr, $mo, $day);

            $newType = $card->getType();
            if (isset($update['responseRecord']['cardType'])) {
                $newType = $this->cardType->getType($update['responseRecord']['cardType']);
            }

            $newLast4 = $card->getAdditional('cc_last4');
            $newBin   = $card->getAdditional('cc_bin');
            if (isset($update['responseRecord']['cardNumber'])) {
                $newLast4 = substr((string) $update['responseRecord']['cardNumber'], -4);
                $newBin   = substr((string) $update['responseRecord']['cardNumber'], 0, 6);
            }

            if ($card->getExpires() !== $newExpires
                || $card->getType() !== $newType
                || $card->getAdditional('cc_last4') !== $newLast4
                || $card->getAdditional('cc_bin') !== $newBin) {
                $card->setExpires($newExpires);
                $card->setAdditional('cc_exp_year', $yr);
                $card->setAdditional('cc_exp_month', $mo);
                $card->setAdditional('cc_type', $newType);
                $card->setAdditional('cc_last4', $newLast4);
                $card->setAdditional('cc_bin', $newBin);

                $this->cardRepository->save($card);

                $this->helper->log(
                    Config::CODE,
                    sprintf(
                        'Account Updater: Updated card %s (%s): %s (%s)',
                        $card->getId(),
                        $card->getPaymentId(),
                        $update['responseRecord']['response'],
                        $update['responseRecord']['reason']
                    )
                );
            }
        }
    }

    /**
     * Disable the given token, if applicable.
     *
     * @param array $update
     * @return void
     */
    public function disableCard($update)
    {
        $cards = $this->loadCards($update['sourceRecord']['token']);
        $cards->addFieldToFilter('active', 1);

        foreach ($cards as $card) {
            $card->setActive(0);
            $this->cardRepository->save($card);

            $this->helper->log(
                Config::CODE,
                sprintf(
                    'Account Updater: Disabled card %s (%s): %s (%s)',
                    $card->getId(),
                    $card->getPaymentId(),
                    $update['responseRecord']['response'],
                    $update['responseRecord']['reason']
                )
            );
        }
    }

    /**
     * Load any cards having the given token.
     *
     * @param string $paymentId
     * @return Collection
     */
    public function loadCards($paymentId)
    {
        $collection = $this->cardCollectionFactory->create();
        $collection->addFieldToFilter('method', Config::CODE);
        $collection->addFieldToFilter('payment_id', $paymentId);

        return $collection;
    }
}
