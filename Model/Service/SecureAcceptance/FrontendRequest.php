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

use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Override;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Helper\Address;
use Throwable;

class FrontendRequest extends AbstractRequestHandler
{
    /**
     * FrontendRequest constructor.
     *
     * @param Config $config
     * @param Hmac $hmac
     * @param Sanitizer $sanitizer
     * @param Address $addressHelper
     * @param CardRepositoryInterface $cardRepository
     * @param RequestInterface $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Session $customerSession
     * @param RemoteAddress $remoteAddress
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        Hmac $hmac,
        Sanitizer $sanitizer,
        Address $addressHelper,
        CardRepositoryInterface $cardRepository,
        RequestInterface $request,
        protected readonly \Magento\Checkout\Model\Session $checkoutSession,
        protected readonly Session $customerSession,
        protected readonly RemoteAddress $remoteAddress,
        protected readonly UrlInterface $urlBuilder,
        protected readonly StoreManagerInterface $storeManager
    ) {
        parent::__construct($config, $hmac, $sanitizer, $addressHelper, $cardRepository, $request);
    }

    /**
     * Get general input parameters for Secure Acceptance checkout.
     *
     * @return array
     * @throws InputException
     * @throws StateException
     */
    #[Override]
    protected function getGeneralParams()
    {
        $params                        = parent::getGeneralParams();
        $params['customer_ip_address'] = $this->sanitizer->ipAddress($this->remoteAddress->getRemoteAddress());

        return $params;
    }

    /**
     * Get Secure Acceptance billing address input parameters
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    #[Override]
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
                $this->checkoutSession->getQuote()->getBillingAddress()->getDataModel()
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
                return $this->customerSession->getCustomerData()->getEmail();
            }

            if (!empty($this->checkoutSession->getQuote()->getBillingAddress()->getEmail())) {
                return $this->checkoutSession->getQuote()->getBillingAddress()->getEmail();
            }

            // Fall back to guest email parameter iff there's none on the quote.
            return $this->request->getParam('guest_email');
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Get customer ID for the Secure Acceptance request.
     *
     * @return int|null
     */
    protected function getCustomerId()
    {
        if ($this->checkoutSession->getQuoteId()) {
            return $this->checkoutSession->getQuote()->getCustomerId();
        }

        return $this->customerSession->getCustomerId();
    }

    /**
     * Get currency for the Secure Acceptance request.
     *
     * @return string
     */
    protected function getCurrencyCode()
    {
        if ($this->checkoutSession->getQuoteId()) {
            return strtoupper((string)$this->checkoutSession->getQuote()->getBaseCurrencyCode());
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
        return $this->checkoutSession->getSessionId();
    }

    /**
     * Get the current store ID, for config scoping.
     *
     * @return string
     */
    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
