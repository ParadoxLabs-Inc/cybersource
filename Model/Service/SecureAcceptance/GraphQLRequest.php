<?php
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
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Service\SecureAcceptance;

use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;

/**
 * GraphQLRequest Class
 */
class GraphQLRequest extends AbstractRequestHandler
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\GraphQl\Model\Query\Resolver\Context
     */
    protected $graphQlContext;

    /**
     * @var array
     */
    protected $graphQlArgs;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Quote\Api\Data\CartInterface
     */
    protected $quote;

    /**
     * @var \ParadoxLabs\TokenBase\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * FrontendRequest constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac
     * @param \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \ParadoxLabs\TokenBase\Model\Api\GraphQL $graphQL
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac,
        \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \ParadoxLabs\TokenBase\Model\Api\GraphQL $graphQL
    ) {
        parent::__construct($config, $hmac, $sanitizer, $addressHelper, $cardRepository, $request);

        $this->remoteAddress = $remoteAddress;
        $this->urlBuilder = $urlBuilder;
        $this->customerRepository = $customerRepository;
        $this->graphQL = $graphQL;
    }

    /**
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param array $args
     * @return void
     */
    public function setGraphQLContext(ContextInterface $context, array $args)
    {
        $this->graphQlContext = $context;
        $this->graphQlArgs = $args;
    }

    /**
     * Get general input parameters for Secure Acceptance checkout.
     *
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function getGeneralParams()
    {
        $params = parent::getGeneralParams();
        $params['customer_ip_address'] = $this->sanitizer->ipAddress($this->remoteAddress->getRemoteAddress());

        return $params;
    }

    /**
     * Get Secure Acceptance billing address input parameters
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBillingAddressParams()
    {
        if (!empty($this->graphQlArgs['billingAddress'])) {
            return $this->getAddressFromObject(
                $this->addressHelper->buildAddressFromInput($this->graphQlArgs['billingAddress'])
            );
        }

        return $this->getAddressFromObject(
            $this->getQuote()->getBillingAddress()->getDataModel()
        );
    }

    /**
     * Get customer email for the Secure Acceptance request.
     *
     * @return string|null
     */
    protected function getEmail()
    {
        if ($this->graphQlArgs['source'] === 'paymentinfo') {
            $customer = $this->customerRepository->getById(
                $this->graphQlContext->getUserId()
            );

            return $customer->getEmail();
        }

        if (!empty($this->getQuote()->getBillingAddress()->getEmail())) {
            return $this->getQuote()->getBillingAddress()->getEmail();
        }

        // Fall back to guest email parameter iff there's none on the quote.
        return $this->graphQlArgs['guestEmail'] ?? null;
    }

    /**
     * Get customer ID for the Secure Acceptance request.
     *
     * @return int|null
     */
    protected function getCustomerId()
    {
        return $this->graphQlContext->getUserId();
    }

    /**
     * Get currency for the Secure Acceptance request.
     *
     * @return string
     */
    protected function getCurrencyCode()
    {
        if ($this->getQuote()) {
            return strtoupper((string)$this->getQuote()->getBaseCurrencyCode());
        }

        return strtoupper((string)$this->graphQlContext->getExtensionAttributes()->getStore()->getBaseCurrencyCode());
    }

    /**
     * Get a return URL for the Secure Acceptance request.
     *
     * @param string $route
     * @return string
     * @throws \Magento\Framework\Exception\InputException
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
        return $this->graphQlArgs['cartId'];
    }

    /**
     * Get quote for the GraphQL request
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    protected function getQuote()
    {
        if ($this->quote instanceof \Magento\Quote\Api\Data\CartInterface) {
            return $this->quote;
        }

        $customerId = $this->graphQlContext->getUserId();
        $quoteHash  = $this->graphQlArgs['cartId'];

        $this->quote = $this->graphQL->getQuote($customerId, $quoteHash);

        return $this->quote;
    }

    /**
     * Get the stored card from the request's card_id card hash, or null if none.
     *
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface|null
     */
    protected function getCard()
    {
        if (empty($this->graphQlArgs['cardId'])) {
            return null;
        }

        try {
            return $this->cardRepository->getByHash($this->graphQlArgs['cardId']);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Get the current store ID, for config scoping.
     *
     * @return string
     */
    protected function getStoreId()
    {
        return $this->graphQlContext->getExtensionAttributes()->getStore()->getId();
    }
}
