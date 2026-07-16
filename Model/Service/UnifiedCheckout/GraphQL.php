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

use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Model\Api\GraphQL as GraphQLHelper;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * GraphQL (headless storefront) capture-context generator.
 *
 * Sources amount/currency/billTo from the resolver-supplied cart and input args; falls back to a
 * billing-only context when no cart is supplied (headless add-card / save-card).
 *
 * Target origins: the actual headless storefront origin cannot be derived server-side, so the
 * "Additional Target Origins" config remains essential for GraphQL clients. The store's base-URL
 * origin is still derived (harmless; may match), but headless domains must be configured.
 */
class GraphQL extends CaptureContext
{
    /**
     * @var ContextInterface|null
     */
    protected ?ContextInterface $graphQlContext = null;

    /**
     * @var array<string, mixed>
     */
    protected array $graphQlArgs = [];

    /**
     * @var CartInterface|null
     */
    protected ?CartInterface $quote = null;

    /**
     * GraphQL constructor.
     *
     * @param Config $config
     * @param Rest $rest
     * @param Sanitizer $sanitizer
     * @param Address $addressHelper
     * @param CaptureContextRequestFactory $requestFactory
     * @param LoggerInterface $logger
     * @param GraphQLHelper $graphQL
     */
    public function __construct(
        Config $config,
        Rest $rest,
        Sanitizer $sanitizer,
        Address $addressHelper,
        CaptureContextRequestFactory $requestFactory,
        LoggerInterface $logger,
        protected readonly GraphQLHelper $graphQL
    ) {
        parent::__construct($config, $rest, $sanitizer, $addressHelper, $requestFactory, $logger);
    }

    /**
     * Set the resolver context and input arguments for this request.
     *
     * @param ContextInterface $context
     * @param array<string, mixed> $args
     * @return $this
     */
    public function setGraphQLContext(ContextInterface $context, array $args): self
    {
        $this->graphQlContext = $context;
        $this->graphQlArgs    = $args;
        $this->quote          = null;

        return $this;
    }

    /**
     * Get the order total amount, or null when there is no cart (billing-only context).
     *
     * @return string|null
     */
    protected function getAmount(): ?string
    {
        $quote = $this->getQuote();
        if ($quote === null) {
            return null;
        }

        $total = $quote->getBaseGrandTotal();

        return $total !== null ? (string)$total : null;
    }

    /**
     * Get currency for the capture-context amount.
     *
     * @return string
     */
    protected function getCurrencyCode(): string
    {
        $quote = $this->getQuote();
        if ($quote !== null) {
            return strtoupper((string)$quote->getBaseCurrencyCode());
        }

        try {
            // The extension-attribute/store chain is not null-safe end to end; degrade to '' on a
            // missing/partial resolver context, consistent with the amount path's null fallback.
            return strtoupper(
                (string)$this->graphQlContext?->getExtensionAttributes()->getStore()->getBaseCurrencyCode()
            );
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Get the UC billTo field tree from the input billingAddress or the cart billing address.
     *
     * @return array<string, string|null>
     */
    protected function getBillTo(): array
    {
        try {
            if (!empty($this->graphQlArgs['billingAddress'])) {
                return $this->mapBillTo(
                    $this->addressHelper->buildAddressFromInput($this->graphQlArgs['billingAddress'])
                );
            }

            $quote = $this->getQuote();
            if ($quote !== null) {
                return $this->mapBillTo($quote->getBillingAddress()->getDataModel());
            }
        } catch (GraphQlAuthorizationException | GraphQlNoSuchEntityException $exception) {
            // Cart authorization/lookup failures are the caller's answer, not a missing address.
            throw $exception;
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
            $quote = $this->getQuote();
            if ($quote !== null && !empty($quote->getBillingAddress()->getEmail())) {
                return $quote->getBillingAddress()->getEmail();
            }

            return $this->graphQlArgs['guestEmail'] ?? null;
        } catch (GraphQlAuthorizationException | GraphQlNoSuchEntityException $exception) {
            // Cart authorization/lookup failures are the caller's answer, not a missing email.
            throw $exception;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Surface save-card for authenticated GraphQL customers.
     *
     * @return bool
     */
    protected function canRequestSaveCard(): bool
    {
        return (int)($this->graphQlContext?->getUserId() ?? 0) > 0;
    }

    /**
     * Get the current store ID, for config scoping.
     *
     * @return int|null
     */
    protected function getStoreId(): ?int
    {
        try {
            return (int)$this->graphQlContext?->getExtensionAttributes()->getStore()->getId();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Derive the store's base-URL origin. Headless storefront origins are not derivable here;
     * they must come from the "Additional Target Origins" config.
     *
     * @return string[]
     */
    protected function deriveTargetOrigins(): array
    {
        try {
            $origin = $this->normalizeOrigin(
                $this->graphQlContext?->getExtensionAttributes()->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_WEB, true)
            );

            return $origin !== null ? [$origin] : [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Resolve the cart for this request, or null when none was supplied.
     *
     * @return CartInterface|null
     */
    protected function getQuote(): ?CartInterface
    {
        if ($this->quote instanceof CartInterface) {
            return $this->quote;
        }

        if (empty($this->graphQlArgs['cartId']) || $this->graphQlContext === null) {
            return null;
        }

        // A supplied cartId must resolve and belong to the caller. GraphQlAuthorizationException /
        // GraphQlNoSuchEntityException are the authorization gate and MUST reach the caller: swallowing
        // them here degraded a denial into a billing-only context and still minted a capture context.
        // Null is reserved for "no cart was supplied" (the headless add-card/save-card path), above.
        $this->quote = $this->graphQL->getQuote(
            $this->graphQlContext->getUserId(),
            $this->graphQlArgs['cartId']
        );

        return $this->quote;
    }
}
