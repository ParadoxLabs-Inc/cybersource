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

namespace ParadoxLabs\CyberSource\Model\Service\SecureAcceptance;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Model\Session\Quote;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Store\Model\StoreManagerInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Helper\Data;
use Throwable;

class BackendRequest extends AbstractRequestHandler
{
    /**
     * BackendRequest constructor.
     *
     * @param Config $config
     * @param Hmac $hmac
     * @param Sanitizer $sanitizer
     * @param Address $addressHelper
     * @param CardRepositoryInterface $cardRepository
     * @param RequestInterface $request
     * @param Data $tokenbaseHelper
     * @param UrlInterface $urlBuilder
     * @param Quote $backendSession
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        Hmac $hmac,
        Sanitizer $sanitizer,
        Address $addressHelper,
        CardRepositoryInterface $cardRepository,
        RequestInterface $request,
        protected readonly Data $tokenbaseHelper,
        protected readonly UrlInterface $urlBuilder,
        protected readonly Quote $backendSession,
        protected readonly StoreManagerInterface $storeManager
    ) {
        parent::__construct($config, $hmac, $sanitizer, $addressHelper, $cardRepository, $request);
    }

    /**
     * Get Secure Acceptance billing address input parameters
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    #[\Override]
    public function getBillingAddressParams()
    {
        try {
            $billingAddress = parent::getBillingAddressParams();
        } catch (InputException) {
            $billingAddress = [];
        }

        // If no input, pull from quote
        if (empty($this->request->getParam('billing'))) {
            $billingAddress = $this->getAddressFromObject(
                $this->backendSession->getQuote()->getBillingAddress()->getDataModel()
            );
        }

        return $billingAddress;
    }

    /**
     * Get customer email for the Secure Acceptance request.
     *
     * @return string|null
     */
    protected function getEmail()
    {
        try {
            if ($this->request->getParam('source') === 'paymentinfo') {
                return $this->tokenbaseHelper->getCurrentCustomer()->getEmail();
            }

            return $this->backendSession->getQuote()->getBillingAddress()->getEmail();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Get customer ID for the Secure Acceptance request.
     *
     * @return int|null
     */
    protected function getCustomerId()
    {
        return $this->tokenbaseHelper->getCurrentCustomer()->getId();
    }

    /**
     * Get currency for the Secure Acceptance request.
     *
     * @return string
     */
    protected function getCurrencyCode()
    {
        if ($this->backendSession->getQuoteId()) {
            return strtoupper((string)$this->backendSession->getQuote()->getBaseCurrencyCode());
        }

        return strtoupper((string)$this->storeManager->getStore()->getBaseCurrencyCode());
    }

    /**
     * Get a return URL for the Secure Acceptance request.
     *
     * @param string $route
     * @return string
     * @throws InputException
     */
    protected function getSecureAcceptUrl($route)
    {
        return $this->sanitizer->url(
            $this->urlBuilder->getUrl('pdl_cybs/secureAccept/' . $route)
        );
    }

    /**
     * Get the current user's session ID, for persistence around potential SameSite cookie restrictions.
     *
     * @return string
     */
    protected function getSessionId()
    {
        return $this->backendSession->getSessionId();
    }

    /**
     * Get the current store ID, for config scoping.
     *
     * @return string
     */
    protected function getStoreId()
    {
        try {
            if ($this->request->getParam('source') === 'paymentinfo') {
                return $this->tokenbaseHelper->getCurrentCustomer()->getStoreId();
            }

            return $this->backendSession->getQuote()->getStoreId();
        } catch (Throwable) {
            return $this->storeManager->getStore()->getId();
        }
    }
}
