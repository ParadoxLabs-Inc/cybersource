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
     * Exchange a paymentinfo transient token for the vault card token (add-card AND edit-card paths).
     *
     * Deliberate difference from the checkout path: there (Method::afterAuthorize/afterCapture) an
     * approved purchase must NOT fail on a token-service error and proceeds token-less. Here an add-card
     * with no vault token is useless (no MIT key was minted), so a token-less result throws
     * LocalizedException -- the Save controllers catch it and surface the message to the user.
     *
     * Edit-card REPLACE: the customer "My Payment Data" (Hyvä) and admin edit flows re-enter the card in
     * the Unified Checkout drop-in and post a fresh transient token against the SAME already-vaulted card
     * (loaded by hash). That card already carries a payment_id, so we must run the exchange and OVERWRITE
     * its gateway ids/metadata with the newly entered instrument's -- otherwise the address updates and the
     * success message shows while the stored TMS token still points at the OLD card, and future
     * orders/rebills silently charge the replaced instrument.
     *
     * @param InfoInterface|null $payment
     * @return void
     * @throws LocalizedException When the auth approved but returned no vault token (uc_token_missing).
     * @throws Throwable On any tokenize failure (propagated; the Save controllers catch and display it).
     */
    protected function exchangeTransientToken(?InfoInterface $payment): void
    {
        // Only the paymentinfo (add/edit-card-without-order) source, and only when a single-use transient
        // token is actually present to consume. A card that already carries a payment_id is deliberately
        // NOT excluded here: the edit-card flow re-enters the card and posts a fresh token to REPLACE the
        // vaulted instrument (see below). Re-saves with no fresh token (address/metadata-only edits)
        // short-circuit on the transient_token check and never re-exchange, preserving the original guard.
        if (!$payment instanceof InfoInterface
            || $payment->getData('tokenbase_source') !== self::SOURCE_PAYMENTINFO
            || (string)$payment->getAdditionalInformation('transient_token') === '') {
            return;
        }

        // A pre-existing payment_id marks an edit-in-place REPLACE rather than a first add-card.
        $priorPaymentId = (string)$this->getPaymentId();

        /** @var \Magento\Store\Model\Store $store */
        $store        = $this->storeManager->getStore();
        $storeId      = (int)$store->getId();
        $currencyCode = (string)$store->getBaseCurrencyCode();

        // The card's own billing address rides along for the $0 auth's billTo: these flows have no
        // order to derive one from, and CyberSource rejects a billTo-less $0 auth (MISSING_FIELD).
        // The Save controllers/repository set the address before save, so it is populated here.
        $gatewayResponse = $this->ucResponse->tokenizeCard(
            $payment,
            $currencyCode,
            $storeId,
            $this->getAddressObject(),
            (string)$this->getCustomerEmail() ?: null,
        );

        // CardBuilder replaces (not merges) the gateway ids + metadata, so on a REPLACE the card now points
        // at the newly entered instrument. On a token-less reply it leaves the ids untouched and flags the
        // card -- we throw below, aborting the save, so the old card is left intact in the DB.
        $this->cardBuilder->applyTokenToCard($this, $gatewayResponse);

        // Single-use token has been consumed; drop it so a re-save cannot re-post the expired JWT.
        $payment->unsAdditionalInformation('transient_token');

        $tokenMissing = (bool)$gatewayResponse->getData('uc_token_missing') === true;

        if ($priorPaymentId !== '' && $tokenMissing === false) {
            // Edit-card replace succeeded: the card's gateway ids were just overwritten with the new
            // instrument's. The previously vaulted TMS paymentInstrument is now superseded and is left
            // ORPHANED in the CyberSource vault -- we deliberately do NOT delete it inline here, because the
            // DB save has not happened yet and deleting before a save that could still fail would strand the
            // customer with no usable card. Log the swap (no token values -- TMS ids are treated as secrets)
            // so an orphan sweep can reconcile later.
            $this->helper->log(
                $this->getMethod(),
                'Unified Checkout: replaced the stored card instrument on an edit-card (paymentinfo) save;'
                . ' the prior TMS paymentInstrument is superseded and left orphaned for later reconciliation.'
            );
        }

        // No vault token returned (uc_token_missing): fail loudly so the Save controllers surface the
        // message rather than persisting a dead, un-tokenized card (on an edit, the old card is untouched).
        if ($tokenMissing === true) {
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
