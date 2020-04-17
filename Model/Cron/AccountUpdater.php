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

/**
 * AccountUpdater Class
 */
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
     * @var \ParadoxLabs\CyberSource\Model\Service\Rest
     */
    protected $restClient;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $helper;

    /**
     * @var \ParadoxLabs\TokenBase\Api\CardRepositoryInterface
     */
    protected $cardRepository;

    /**
     * @var \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory
     */
    protected $cardCollectionFactory;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Source\CardType
     */
    protected $cardType;

    /**
     * TransactionUpdater constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Service\Rest $restClient
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory
     * @param \ParadoxLabs\CyberSource\Model\Source\CardType $cardType
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Service\Rest $restClient,
        Config $config,
        \ParadoxLabs\CyberSource\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory,
        \ParadoxLabs\CyberSource\Model\Source\CardType $cardType
    ) {
        $this->restClient = $restClient;
        $this->config = $config;
        $this->helper = $helper;
        $this->cardRepository = $cardRepository;
        $this->cardCollectionFactory = $cardCollectionFactory;
        $this->cardType = $cardType;
    }

    /**
     * Get any decision manager updates from the last 24 hours, and update corresponding orders if appropriate.
     *
     * @return void
     */
    public function execute()
    {
        // TODO: This doesn't work. "One or more fields failed validation" Emailed Trevor 2/17
        $reply = $this->restClient->get(
            '/accountupdater/v1/batches',
            [
                // 'offset' => 1,
                // 'limit' => 1,
            ],
            'application/json'
        );

        // $this->helper->log(Config::CODE, $reply, true);

        $reply = json_decode($reply, JSON_OBJECT_AS_ARRAY);

        // echo json_encode($reply, JSON_PRETTY_PRINT) . "\n";

        if (!empty($reply['_embedded']['batches'])) {
            $mostRecentBatches = [];

            foreach ($reply['_embedded']['batches'] as $batch) {
                if (!isset($mostRecentBatches[ $batch['batchSource'] ]) && $batch['status'] === 'COMPLETE') {
                    $mostRecentBatches[ $batch['batchSource'] ] = $batch;
                }
            }

            foreach ($mostRecentBatches as $batch) {
                if (!empty($batch['updatedRecords'])) {
                    try {
                        $this->processBatch($batch);
                    } catch (\Exception $exception) {
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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processBatch($batch)
    {
        $reply = $this->restClient->get($batch['_links']['reports'][0]['href']);
        $report = json_decode($reply, JSON_OBJECT_AS_ARRAY);

        if (!empty($report['records'])) {
            foreach ($report['records'] as $update) {
                // If response is NAN, NED, update BIN/last4/expir
                // If response is ACL, CCH, DEC, ERR, mark inactive
                // If response is CUR, NUP, UNA, ignore
                if (in_array($update['responseRecord']['response'], static::UPDATE_CODES, true)) {
                    $this->updateCard($update);
                } elseif (in_array($update['responseRecord']['response'], static::INVALID_CODES, true)) {
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

        /** @var \ParadoxLabs\TokenBase\Model\Card $card */
        foreach ($cards as $card) {
            // TODO: Will update response always contain expiry, number, and type?
            $yr = $update['responseRecord']['cardExpiryYear'];
            $mo = $update['responseRecord']['cardExpiryMonth'];
            $day = date('t', strtotime($yr . '-' . $mo));
            $newExpires = sprintf('%s-%s-%s 23:59:59', $yr, $mo, $day);
            $newType = $this->cardType->getType($update['responseRecord']['cardType']);
            $newLast4 = substr($update['responseRecord']['cardNumber'], -4);
            $newBin = substr($update['responseRecord']['cardNumber'], 0, 6);

            $card->setExpires($newExpires);
            $card->setAdditional('cc_exp_year', $yr);
            $card->setAdditional('cc_exp_month', $mo);
            $card->setAdditional('cc_type', $newType);
            $card->setAdditional('cc_last4', $newLast4);
            $card->setAdditional('cc_bin', $newBin);

            if ($card->getExpires() !== $newExpires
                || $card->getType() !== $newType
                || $card->getAdditional('cc_last4') !== $newLast4
                || $card->getAdditional('cc_bin') !== $newBin) {
                $this->cardRepository->save($card);

                $this->helper->log(
                    Config::CODE,
                    sprintf(
                        'Account Updater: Updated card %s (%s): %s',
                        $card->getId(),
                        $card->getPaymentId(),
                        $update['responseRecord']['response']
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
                    'Account Updater: Disabled card %s (%s): %s',
                    $card->getId(),
                    $card->getPaymentId(),
                    $update['responseRecord']['response']
                )
            );
        }
    }

    /**
     * Load any cards having the given token.
     *
     * @param string $paymentId
     * @return \ParadoxLabs\TokenBase\Model\ResourceModel\Card\Collection
     */
    public function loadCards($paymentId)
    {
        $collection = $this->cardCollectionFactory->create();
        $collection->addFieldToFilter('method', Config::CODE);
        $collection->addFieldToFilter('payment_id', $paymentId);

        return $collection;
    }
}
