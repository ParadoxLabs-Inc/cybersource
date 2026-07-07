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

namespace ParadoxLabs\CyberSource\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context as ModelContext;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Model\InfoInterface;
use Magento\Store\Model\StoreManagerInterface;
use Override;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response;
use ParadoxLabs\TokenBase\Model\Card\Context;
use Throwable;

/**
 * CyberSource card model
 */
class Card extends \ParadoxLabs\TokenBase\Model\Card
{
    /**
     * Payment source flag (set by both TokenBase Save controllers and CardRepository::updatePaymentInfo)
     * marking a card save that originated from the add-card-without-order forms rather than checkout.
     */
    private const SOURCE_PAYMENTINFO = 'paymentinfo';

    /**
     * Card constructor.
     *
     * Extends the TokenBase card with the zero-dollar add-card token exchange dependencies (used only by
     * the paymentinfo add-card path in beforeSave()). Card models are instantiated for every vault-card
     * load, and the UC Response service drags in the REST client, so it is injected lazily via a Proxy
     * (see etc/di.xml). All leading args forward to the parent.
     *
     * @param ModelContext $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Context $cardContext
     * @param Response $ucResponse *Proxy
     * @param CardBuilder $cardBuilder
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array<string, mixed> $data
     */
    public function __construct(
        ModelContext $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Context $cardContext,
        protected readonly Response $ucResponse,
        protected readonly CardBuilder $cardBuilder,
        protected readonly StoreManagerInterface $storeManager,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $cardContext,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Finalize before saving.
     *
     * return $this
     */
    #[Override]
    public function beforeSave()
    {
        // Note: All gateway syncing happens via the Unified Checkout payment response.
        // @see \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder for the response handling.

        $payment = $this->getInfoInstance();

        // Zero-dollar add-card (paymentinfo) token exchange: the customer "My Payment Data" and admin
        // customer payment tab save a card WITHOUT an order, so no auth/capture ran to mint the TMS vault
        // ids. Exchange the single-use transient token for the token+card metadata here. Guarded to the
        // paymentinfo source only -- during checkout the card save carries the SAME payment with an
        // already-consumed token, and Method::afterAuthorize/afterCapture already handled that path.
        $this->exchangeTransientToken($payment);

        // If this is a new card, set its active state to the given value (if any)
        if ($payment instanceof InfoInterface
            && $payment->getAdditionalInformation('save') !== null
            && $this->getOrigData('last_use') === null) {
            $this->setActive((bool)$payment->getAdditionalInformation('save') ? 1 : 0);
        }

        parent::beforeSave();

        return $this;
    }

    /**
     * Exchange a paymentinfo transient token for the vault card token (zero-dollar add-card path).
     *
     * Deliberate difference from the checkout path: there (Method::afterAuthorize/afterCapture) an
     * approved purchase must NOT fail on a token-service error and proceeds token-less. Here an add-card
     * with no vault token is useless (no MIT key was minted), so a token-less result throws
     * LocalizedException -- the Save controllers catch it and surface the message to the user.
     *
     * @param InfoInterface|null $payment
     * @return void
     * @throws LocalizedException When the auth approved but returned no vault token (uc_token_missing).
     * @throws Throwable On any tokenize failure (propagated; the Save controllers catch and display it).
     */
    protected function exchangeTransientToken(?InfoInterface $payment): void
    {
        // Only the paymentinfo (add-card-without-order) source; a genuinely new card (no existing
        // payment_id); and only when a transient token is actually present to consume.
        if (!$payment instanceof InfoInterface
            || $payment->getData('tokenbase_source') !== self::SOURCE_PAYMENTINFO
            || (string)$payment->getAdditionalInformation('transient_token') === ''
            || (string)$this->getPaymentId() !== '') {
            return;
        }

        /** @var \Magento\Store\Model\Store $store */
        $store        = $this->storeManager->getStore();
        $storeId      = (int)$store->getId();
        $currencyCode = (string)$store->getBaseCurrencyCode();

        $gatewayResponse = $this->ucResponse->tokenizeCard($payment, $currencyCode, $storeId);

        $this->cardBuilder->applyTokenToCard($this, $gatewayResponse);

        // Single-use token has been consumed; drop it so a re-save cannot re-post the expired JWT.
        $payment->unsAdditionalInformation('transient_token');

        // No vault token returned (uc_token_missing): fail loudly so the Save controllers surface the
        // message rather than persisting a dead, un-tokenized card.
        if ((bool)$gatewayResponse->getData('uc_token_missing') === true) {
            throw new LocalizedException(
                __('The card could not be saved. Please check your payment information and try again.')
            );
        }
    }

    /**
     * Finalize before deleting.
     *
     * @return $this
     */
    #[Override]
    public function beforeDelete()
    {
        /**
         * Delete from the gateway if we have a valid record.
         */
        if (!empty($this->getPaymentId())) {
            /** @var Gateway $gateway */
            $gateway = $this->getMethodInstance()->gateway();

            try {
                $gateway->setCard($this);
                $gateway->deleteCard();
            } catch (Throwable $e) {
                $this->helper->log(
                    $this->getMethod(),
                    sprintf(
                        'Failed to delete card from gateway: %s',
                        $e->getMessage()
                    )
                );
            }
        }

        parent::beforeDelete();

        return $this;
    }
}
