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

use Magento\Customer\Api\Data\AddressInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Builds a Unified Checkout capture-context request and POSTs it to CyberSource, returning the JWT.
 *
 * Mirrors the Secure Acceptance request-handler tree: this abstract base assembles the request from
 * config + context-neutral logic, while subclasses (Frontend/Backend/GraphQL) source the amount,
 * currency, billing address, store ID, and payment_action from their environment.
 *
 * @see UC-API-REFERENCE.md §1
 */
abstract class CaptureContext
{
    /**
     * Capture-context REST endpoint path.
     */
    public const CAPTURE_CONTEXT_PATH = '/up/v1/capture-contexts';

    /**
     * CaptureContext constructor.
     *
     * @param Config $config
     * @param Rest $rest
     * @param Sanitizer $sanitizer
     * @param Address $addressHelper
     * @param CaptureContextRequestFactory $requestFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected readonly Config $config,
        protected readonly Rest $rest,
        protected readonly Sanitizer $sanitizer,
        protected readonly Address $addressHelper,
        protected readonly CaptureContextRequestFactory $requestFactory,
        protected readonly LoggerInterface $logger
    ) {
    }

    /**
     * Generate a capture-context JWT for the current context.
     *
     * @return string The capture-context JWT string returned by CyberSource.
     * @throws \Throwable
     */
    public function generate(): string
    {
        $storeId = $this->getStoreId();
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request = $this->buildRequest();

        return $this->rest->postRaw(self::CAPTURE_CONTEXT_PATH, $request->toArray());
    }

    /**
     * Assemble the capture-context request DTO from config and context-specific sourcing.
     *
     * @return CaptureContextRequest
     * @throws \Throwable
     */
    public function buildRequest(): CaptureContextRequest
    {
        $storeId = $this->getStoreId();
        $billTo  = $this->getBillTo();

        /** @var CaptureContextRequest $request */
        $request = $this->requestFactory->create();

        $request->setClientVersion($this->config->getUcClientVersion($storeId))
            ->setTargetOrigins($this->getTargetOrigins($storeId))
            ->setAllowedCardNetworks($this->config->getUcAllowedCardNetworks($storeId))
            ->setAllowedPaymentTypes($this->config->getUcAllowedPaymentTypes($storeId))
            ->setCountry($this->resolveCountry($billTo))
            ->setLocale($this->config->getUcLocale($storeId))
            ->setBillingType($this->config->getUcBillingType($storeId))
            // requestSaveCard is deliberately omitted: the module's own payment[save] checkbox is the
            // sole save-card consent point (it also covers wallets, which UC's checkbox never did).
            // Contact details are collected by Magento (checkout/account/admin forms) and sent with
            // the payment request; explicit false stops UC from re-collecting them (UC defaults on).
            ->setRequestEmail(false)
            ->setRequestPhone(false)
            ->setRequestShipping(false)
            ->setCompleteMandateType($this->config->getUcCompleteMandateType($storeId))
            ->setDecisionManager($this->config->isDecisionManagerEnabled($storeId))
            ->setConsumerAuthentication($this->config->is3dsEnabled($storeId));

        $amount = $this->getAmount();
        if ($amount !== null) {
            // UC expects a fixed 2-decimal string (e.g. "24.00"); Sanitizer::amount() returns a float.
            $request->setTotalAmount(number_format((float)$this->sanitizer->amount($amount), 2, '.', ''))
                ->setCurrency($this->sanitizer->alpha($this->getCurrencyCode(), 3));
        }

        $request->setBillTo($billTo);

        return $request;
    }

    /**
     * Map the configured/context billing address object to the UC billTo field tree.
     *
     * Returns an empty array (billing-only / no address) rather than throwing, so a capture context
     * can still be generated for the admin add-card / "save card" no-amount case.
     *
     * @param AddressInterface|null $address
     * @return array<string, string|null>
     */
    protected function mapBillTo(?AddressInterface $address): array
    {
        if ($address === null) {
            return [];
        }

        $street = $address->getStreet();
        $address1 = (string)($street[0] ?? '');

        // UC is JSON REST (not the SA hosted form), so free-text fields use the space-preserving
        // alphanumericPunc filter; buildingNumber/country/postal use the stricter sanitizers.
        return array_filter([
            'firstName' => $this->sanitizer->alphanumericPunc($address->getFirstname(), 60),
            'lastName' => $this->sanitizer->alphanumericPunc($address->getLastname(), 60),
            'address1' => $this->sanitizer->alphanumericPunc($address1, 60),
            'address2' => $this->sanitizer->alphanumericPunc($street[1] ?? null, 60),
            'buildingNumber' => $this->extractBuildingNumber($address1),
            'locality' => $this->sanitizer->alphanumericPunc($address->getCity(), 50),
            'administrativeArea' => $this->sanitizer->alphanumericPunc(
                strtoupper((string)$address->getRegion()->getRegionCode()),
                20
            ),
            'postalCode' => $this->sanitizer->postcode($address->getPostcode(), $address->getCountryId()),
            'country' => $this->sanitizer->alpha(strtoupper((string)$address->getCountryId()), 2),
            'email' => $this->sanitizeEmail($this->getEmail()),
            'phoneNumber' => $this->sanitizer->phone($address->getTelephone(), 15),
        ], static fn($value): bool => $value !== null && $value !== '');
    }

    /**
     * Sanitize the billing email, tolerating an invalid/missing value (Sanitizer::email() may throw).
     *
     * @param string|null $email
     * @return string|null
     */
    protected function sanitizeEmail(?string $email): ?string
    {
        if ($email === null || $email === '') {
            return null;
        }

        try {
            return $this->sanitizer->email($email);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Normalize billing-input keys to the flat snake_case keys buildAddressFromInput() expects.
     *
     * Frontend/Backend billing input may arrive with camelCase keys (countryId/regionId/regionCode);
     * GraphQL CustomerAddressInput arrives with country_code and a nested region object
     * (region/region_code/region_id). The address helper reads flat snake_case. Existing flat
     * snake_case keys are never overwritten.
     *
     * @param array<string, mixed> $billing
     * @return array<string, mixed>
     */
    protected function normalizeBillingInputKeys(array $billing): array
    {
        if (isset($billing['region']) && is_array($billing['region'])) {
            $region = $billing['region'];

            $billing['region_id']   ??= $region['region_id'] ?? null;
            $billing['region_code'] ??= $region['region_code'] ?? null;
            // The customer_address form has no region_code attribute; a 2-letter code in 'region'
            // is how buildAddressFromInput() resolves the region, so prefer the code over the name.
            $billing['region'] = $region['region_code'] ?? $region['region'] ?? null;
        }

        $billing['country_id']  ??= $billing['countryId'] ?? $billing['country_code'] ?? null;
        $billing['region_id']   ??= $billing['regionId'] ?? null;
        $billing['region_code'] ??= $billing['regionCode'] ?? null;

        return $billing;
    }

    /**
     * Extract the leading numeric building number from a street line (the spike's 404-clearing fix).
     *
     * UC requires a separate buildingNumber when billingType=FULL; CyberSource 404s the capture
     * context without it. We derive it from the leading digits of address1.
     *
     * @param string $address1
     * @return string|null
     */
    protected function extractBuildingNumber(string $address1): ?string
    {
        if (preg_match('/^\s*([0-9]+[A-Za-z]?)/', $address1, $matches) === 1) {
            return $this->sanitizer->asciiAlphanumericPunc($matches[1], 15);
        }

        return null;
    }

    /**
     * Resolve the ISO-2 country for the capture context (billing address, else config default).
     *
     * @param array<string, string|null> $billTo
     * @return string|null
     */
    protected function resolveCountry(array $billTo): ?string
    {
        if (!empty($billTo['country'])) {
            return $billTo['country'];
        }

        return $this->config->getUcCountry($this->getStoreId());
    }

    /**
     * Build the full targetOrigins list: context-derived origins first, then configured extras.
     *
     * CyberSource requires exact scheme+host(+port) origins for everywhere UC.js mounts (the UC
     * iframes enforce frame-ancestors = targetOrigins); wildcards are not supported. The derived
     * origin covers the current context's host, while the "Additional Target Origins" config adds
     * extras such as headless storefront domains. Config extras are normalized to browser-origin
     * form too (lowercase host, default port stripped, no path); entries that cannot be parsed to
     * a scheme+host origin are dropped. Duplicates are removed after normalization, derived-first.
     *
     * @param int|null $storeId
     * @return string[]
     */
    protected function getTargetOrigins(?int $storeId): array
    {
        $configExtras = $this->config->getUcTargetOrigins($storeId);
        $normalizedExtras = [];

        foreach ($configExtras as $raw) {
            $normalized = $this->normalizeOrigin($raw);

            if ($normalized === null) {
                $this->logger->info(
                    'CyberSource UC: dropping invalid targetOrigins entry (must include scheme): ' . $raw
                );

                continue;
            }

            $normalizedExtras[] = $normalized;
        }

        $origins = array_merge($this->deriveTargetOrigins(), $normalizedExtras);

        return array_values(array_unique(array_filter($origins)));
    }

    /**
     * Normalize a URL to origin form: scheme + lowercase host + explicit port only if non-default.
     *
     * No path, no trailing slash. Returns null when the URL lacks a scheme or host.
     *
     * @param string|null $url
     * @return string|null
     */
    protected function normalizeOrigin(?string $url): ?string
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction -- parsing trusted config/base URLs.
        $parts = $url !== null && $url !== '' ? parse_url($url) : false;

        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);
        $origin = $scheme . '://' . strtolower($parts['host']);

        // Default ports are implied by the scheme; appending them would mismatch browser origins.
        $defaultPorts = [
            'http' => 80,
            'https' => 443,
        ];
        if (isset($parts['port']) && $parts['port'] !== ($defaultPorts[$scheme] ?? null)) {
            $origin .= ':' . $parts['port'];
        }

        return $origin;
    }

    /**
     * Get the order total amount for the capture context, or null for a billing-only context.
     *
     * @return string|null
     */
    abstract protected function getAmount(): ?string;

    /**
     * Get the currency code for the capture context amount.
     *
     * @return string
     */
    abstract protected function getCurrencyCode(): string;

    /**
     * Get the UC billTo field tree for the capture context.
     *
     * @return array<string, string|null>
     */
    abstract protected function getBillTo(): array;

    /**
     * Get the customer email for the capture context billing address.
     *
     * @return string|null
     */
    abstract protected function getEmail(): ?string;

    /**
     * Get the current store ID, for config scoping.
     *
     * @return int|null
     */
    abstract protected function getStoreId(): ?int;

    /**
     * Derive the origin(s) where UC.js will mount for this context, in normalized origin form.
     *
     * @return string[]
     */
    abstract protected function deriveTargetOrigins(): array;
}
