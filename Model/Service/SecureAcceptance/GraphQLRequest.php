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

use Magento\GraphQl\Model\Query\Resolver\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Override;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use Throwable;

class GraphQLRequest extends AbstractRequestHandler
{
    /**
     * @var Context
     */
    protected $graphQlContext;

    /**
     * @var array
     */
    protected $graphQlArgs;

    /**
     * @var CartInterface
     */
    protected $quote;

    /**
     * FrontendRequest constructor.
     *
     * @param Config $config
     * @param Hmac $hmac
     * @param Sanitizer $sanitizer
     * @param Address $addressHelper
     * @param CardRepositoryInterface $cardRepository
     * @param RequestInterface $request
     * @param RemoteAddress $remoteAddress
     * @param UrlInterface $urlBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param GraphQL $graphQL
     */
    public function __construct(
        Config $config,
        Hmac $hmac,
        Sanitizer $sanitizer,
        Address $addressHelper,
        CardRepositoryInterface $cardRepository,
        RequestInterface $request,
        protected readonly RemoteAddress $remoteAddress,
        protected readonly UrlInterface $urlBuilder,
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly GraphQL $graphQL
    ) {
        parent::__construct($config, $hmac, $sanitizer, $addressHelper, $cardRepository, $request);
    }

    /**
     * @param ContextInterface $context
     * @param array $args
     * @return void
     */
    public function setGraphQLContext(ContextInterface $context, array $args)
    {
        $this->graphQlContext = $context;
        $this->graphQlArgs    = $args;
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
        return $this->graphQlArgs['cartId'];
    }

    /**
     * Get quote for the GraphQL request
     *
     * @return CartInterface
     */
    protected function getQuote()
    {
        if ($this->quote instanceof CartInterface) {
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
     * @return CardInterface|null
     */
    #[Override]
    protected function getCard()
    {
        if (empty($this->graphQlArgs['cardId'])) {
            return null;
        }

        try {
            return $this->cardRepository->getByHash($this->graphQlArgs['cardId']);
        } catch (Throwable) {
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
