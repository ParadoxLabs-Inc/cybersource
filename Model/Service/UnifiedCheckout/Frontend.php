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

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Override;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use Throwable;

/**
 * Frontend (storefront) capture-context generator.
 *
 * Sources amount/currency/billTo from the checkout quote; falls back to the customer session for
 * the no-quote add-card / "save card" billing-only case.
 */
class Frontend extends CaptureContext
{
    /**
     * Frontend constructor.
     *
     * @param Config $config
     * @param Rest $rest
     * @param Sanitizer $sanitizer
     * @param Address $addressHelper
     * @param CaptureContextRequestFactory $requestFactory
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     */
    public function __construct(
        Config $config,
        Rest $rest,
        Sanitizer $sanitizer,
        Address $addressHelper,
        CaptureContextRequestFactory $requestFactory,
        protected readonly CheckoutSession $checkoutSession,
        protected readonly CustomerSession $customerSession,
        protected readonly StoreManagerInterface $storeManager,
        protected readonly RequestInterface $request
    ) {
        parent::__construct($config, $rest, $sanitizer, $addressHelper, $requestFactory);
    }

    /**
     * Get the order total amount, or null when there is no quote (billing-only add-card context).
     *
     * @return string|null
     */
    protected function getAmount(): ?string
    {
        try {
            if (!$this->checkoutSession->getQuoteId()) {
                return null;
            }

            $total = $this->checkoutSession->getQuote()->getBaseGrandTotal();

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
            if ($this->checkoutSession->getQuoteId()) {
                return strtoupper((string)$this->checkoutSession->getQuote()->getBaseCurrencyCode());
            }
        } catch (Throwable) {
            // Fall through to store default.
        }

        return strtoupper((string)$this->storeManager->getStore()->getBaseCurrencyCode());
    }

    /**
     * Get the UC billTo field tree from the request input or the quote billing address.
     *
     * @return array<string, string|null>
     */
    protected function getBillTo(): array
    {
        try {
            $billing = (array)$this->request->getPostValue('billing');
            if (!empty($billing)) {
                $billing['country_id']  ??= $billing['countryId'] ?? null;
                $billing['region_id']   ??= $billing['regionId'] ?? null;
                $billing['region_code'] ??= $billing['regionCode'] ?? null;

                return $this->mapBillTo($this->addressHelper->buildAddressFromInput($billing));
            }

            if ($this->checkoutSession->getQuoteId()) {
                return $this->mapBillTo(
                    $this->checkoutSession->getQuote()->getBillingAddress()->getDataModel()
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
            if ($this->checkoutSession->getQuoteId()
                && !empty($this->checkoutSession->getQuote()->getBillingAddress()->getEmail())) {
                return $this->checkoutSession->getQuote()->getBillingAddress()->getEmail();
            }

            if ($this->customerSession->isLoggedIn()) {
                return $this->customerSession->getCustomerData()->getEmail();
            }

            return $this->request->getParam('guest_email');
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Surface the "save card" checkbox for logged-in customers.
     *
     * @return bool
     */
    #[Override]
    protected function canRequestSaveCard(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get the current store ID, for config scoping.
     *
     * @return int|null
     */
    protected function getStoreId(): ?int
    {
        try {
            return (int)$this->storeManager->getStore()->getId();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Derive the storefront origin from the store's secure base URL.
     *
     * @return string[]
     */
    protected function deriveTargetOrigins(): array
    {
        try {
            $origin = $this->normalizeOrigin(
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB, true)
            );

            return $origin !== null ? [$origin] : [];
        } catch (Throwable) {
            return [];
        }
    }
}
