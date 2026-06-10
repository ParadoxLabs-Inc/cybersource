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

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout;

use Magento\Backend\Model\Session\Quote as BackendSession;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Override;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Helper\Data;
use Throwable;

/**
 * Admin (backend) capture-context generator.
 *
 * Sources amount/currency/billTo from the admin order-create quote, falling back to the current
 * customer registry for the admin add-card billing-only case.
 */
class Backend extends CaptureContext
{
    /**
     * Backend constructor.
     *
     * @param Config $config
     * @param Rest $rest
     * @param Sanitizer $sanitizer
     * @param Address $addressHelper
     * @param CaptureContextRequestFactory $requestFactory
     * @param Data $tokenbaseHelper
     * @param BackendSession $backendSession
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param BackendUrlInterface $backendUrl
     */
    public function __construct(
        Config $config,
        Rest $rest,
        Sanitizer $sanitizer,
        Address $addressHelper,
        CaptureContextRequestFactory $requestFactory,
        protected readonly Data $tokenbaseHelper,
        protected readonly BackendSession $backendSession,
        protected readonly StoreManagerInterface $storeManager,
        protected readonly RequestInterface $request,
        protected readonly BackendUrlInterface $backendUrl
    ) {
        parent::__construct($config, $rest, $sanitizer, $addressHelper, $requestFactory);
    }

    /**
     * Get the order total amount, or null for the admin add-card billing-only context.
     *
     * @return string|null
     */
    protected function getAmount(): ?string
    {
        try {
            if (!$this->backendSession->getQuoteId()) {
                return null;
            }

            $total = $this->backendSession->getQuote()->getBaseGrandTotal();

            return $total !== null ? (string)$total : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Get currency for the capture-context amount.
     *
     * @return string
     */
    protected function getCurrencyCode(): string
    {
        try {
            if ($this->backendSession->getQuoteId()) {
                return strtoupper((string)$this->backendSession->getQuote()->getBaseCurrencyCode());
            }
        } catch (Throwable) {
            // Fall through to store default.
        }

        return strtoupper((string)$this->storeManager->getStore()->getBaseCurrencyCode());
    }

    /**
     * Get the UC billTo field tree from the request input or the order-create quote billing address.
     *
     * @return array<string, string|null>
     */
    protected function getBillTo(): array
    {
        try {
            $billing = (array)$this->request->getPostValue('billing');
            if (!empty($billing)) {
                return $this->mapBillTo(
                    $this->addressHelper->buildAddressFromInput($this->normalizeBillingInputKeys($billing))
                );
            }

            if ($this->backendSession->getQuoteId()) {
                return $this->mapBillTo(
                    $this->backendSession->getQuote()->getBillingAddress()->getDataModel()
                );
            }
        } catch (Throwable) {
            // Billing-only context with no resolvable address.
        }

        return [];
    }

    /**
     * Get customer email for the capture-context billing address.
     *
     * @return string|null
     */
    protected function getEmail(): ?string
    {
        try {
            if ($this->backendSession->getQuoteId()
                && !empty($this->backendSession->getQuote()->getBillingAddress()->getEmail())) {
                return $this->backendSession->getQuote()->getBillingAddress()->getEmail();
            }

            return $this->tokenbaseHelper->getCurrentCustomer()->getEmail();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Save-card is always available in admin add-card / order-create flows.
     *
     * @return bool
     */
    #[Override]
    protected function canRequestSaveCard(): bool
    {
        return true;
    }

    /**
     * Get the current store ID, for config scoping.
     *
     * @return int|null
     */
    protected function getStoreId(): ?int
    {
        try {
            if ($this->backendSession->getQuoteId()) {
                return (int)$this->backendSession->getQuote()->getStoreId();
            }

            return (int)$this->tokenbaseHelper->getCurrentCustomer()->getStoreId();
        } catch (Throwable) {
            try {
                return (int)$this->storeManager->getStore()->getId();
            } catch (Throwable) {
                return null;
            }
        }
    }

    /**
     * Derive the admin origin from the backend base URL (admin order-create and customer-card
     * mounts live under the admin host).
     *
     * @return string[]
     */
    protected function deriveTargetOrigins(): array
    {
        try {
            $origin = $this->normalizeOrigin($this->backendUrl->getBaseUrl());

            return $origin !== null ? [$origin] : [];
        } catch (Throwable) {
            return [];
        }
    }
}
