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

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request;

/**
 * Typed value object for the Unified Checkout capture-context request body.
 *
 * Hand-written "type safety without the SDK" (DECISION D14). Mirrors the fields we actually send
 * under GenerateUnifiedCheckoutCaptureContextRequest; toArray() emits a clean JSON-ready tree with
 * null/empty leaves omitted so the request body is minimal.
 *
 * @see UC-API-REFERENCE.md §1
 */
class CaptureContextRequest
{
    /**
     * @var string|null
     */
    private ?string $clientVersion = null;

    /**
     * @var string[]
     */
    private array $targetOrigins = [];

    /**
     * @var string[]
     */
    private array $allowedCardNetworks = [];

    /**
     * @var string[]
     */
    private array $allowedPaymentTypes = [];

    /**
     * @var string|null
     */
    private ?string $country = null;

    /**
     * @var string|null
     */
    private ?string $locale = null;

    /**
     * @var string|null
     */
    private ?string $billingType = null;

    /**
     * @var bool|null
     */
    private ?bool $requestSaveCard = null;

    /**
     * @var string|null
     */
    private ?string $completeMandateType = null;

    /**
     * @var bool|null
     */
    private ?bool $decisionManager = null;

    /**
     * @var bool|null
     */
    private ?bool $consumerAuthentication = null;

    /**
     * @var string|null
     */
    private ?string $totalAmount = null;

    /**
     * @var string|null
     */
    private ?string $currency = null;

    /**
     * Billing address fields, keyed by API field name (firstName, lastName, address1, …).
     *
     * @var array<string, string|null>
     */
    private array $billTo = [];

    /**
     * Get the UC.js client version selector.
     *
     * @return string|null
     */
    public function getClientVersion(): ?string
    {
        return $this->clientVersion;
    }

    /**
     * Set the UC.js client version selector.
     *
     * @param string|null $clientVersion
     * @return $this
     */
    public function setClientVersion(?string $clientVersion): self
    {
        $this->clientVersion = $clientVersion;

        return $this;
    }

    /**
     * Get the allowed target origins (exact HTTPS origins).
     *
     * @return string[]
     */
    public function getTargetOrigins(): array
    {
        return $this->targetOrigins;
    }

    /**
     * Set the allowed target origins.
     *
     * @param string[] $targetOrigins
     * @return $this
     */
    public function setTargetOrigins(array $targetOrigins): self
    {
        $this->targetOrigins = array_values(array_filter($targetOrigins));

        return $this;
    }

    /**
     * Get the allowed card networks.
     *
     * @return string[]
     */
    public function getAllowedCardNetworks(): array
    {
        return $this->allowedCardNetworks;
    }

    /**
     * Set the allowed card networks.
     *
     * @param string[] $allowedCardNetworks
     * @return $this
     */
    public function setAllowedCardNetworks(array $allowedCardNetworks): self
    {
        $this->allowedCardNetworks = array_values(array_filter($allowedCardNetworks));

        return $this;
    }

    /**
     * Get the allowed payment types.
     *
     * @return string[]
     */
    public function getAllowedPaymentTypes(): array
    {
        return $this->allowedPaymentTypes;
    }

    /**
     * Set the allowed payment types.
     *
     * @param string[] $allowedPaymentTypes
     * @return $this
     */
    public function setAllowedPaymentTypes(array $allowedPaymentTypes): self
    {
        $this->allowedPaymentTypes = array_values(array_filter($allowedPaymentTypes));

        return $this;
    }

    /**
     * Get the ISO-2 country code.
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Set the ISO-2 country code.
     *
     * @param string|null $country
     * @return $this
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the locale (e.g. en_US).
     *
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Set the locale.
     *
     * @param string|null $locale
     * @return $this
     */
    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the captureMandate billingType (FULL|PARTIAL|NONE).
     *
     * @return string|null
     */
    public function getBillingType(): ?string
    {
        return $this->billingType;
    }

    /**
     * Set the captureMandate billingType.
     *
     * @param string|null $billingType
     * @return $this
     */
    public function setBillingType(?string $billingType): self
    {
        $this->billingType = $billingType;

        return $this;
    }

    /**
     * Get whether to request the save-card checkbox.
     *
     * @return bool|null
     */
    public function getRequestSaveCard(): ?bool
    {
        return $this->requestSaveCard;
    }

    /**
     * Set whether to request the save-card checkbox.
     *
     * @param bool|null $requestSaveCard
     * @return $this
     */
    public function setRequestSaveCard(?bool $requestSaveCard): self
    {
        $this->requestSaveCard = $requestSaveCard;

        return $this;
    }

    /**
     * Get the completeMandate type (AUTH|CAPTURE|PREFER_AUTH).
     *
     * @return string|null
     */
    public function getCompleteMandateType(): ?string
    {
        return $this->completeMandateType;
    }

    /**
     * Set the completeMandate type.
     *
     * @param string|null $completeMandateType
     * @return $this
     */
    public function setCompleteMandateType(?string $completeMandateType): self
    {
        $this->completeMandateType = $completeMandateType;

        return $this;
    }

    /**
     * Get the decisionManager toggle.
     *
     * @return bool|null
     */
    public function getDecisionManager(): ?bool
    {
        return $this->decisionManager;
    }

    /**
     * Set the decisionManager toggle.
     *
     * @param bool|null $decisionManager
     * @return $this
     */
    public function setDecisionManager(?bool $decisionManager): self
    {
        $this->decisionManager = $decisionManager;

        return $this;
    }

    /**
     * Get the consumerAuthentication (3DS) toggle.
     *
     * @return bool|null
     */
    public function getConsumerAuthentication(): ?bool
    {
        return $this->consumerAuthentication;
    }

    /**
     * Set the consumerAuthentication (3DS) toggle.
     *
     * @param bool|null $consumerAuthentication
     * @return $this
     */
    public function setConsumerAuthentication(?bool $consumerAuthentication): self
    {
        $this->consumerAuthentication = $consumerAuthentication;

        return $this;
    }

    /**
     * Get the order total amount (decimal string).
     *
     * @return string|null
     */
    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    /**
     * Set the order total amount.
     *
     * @param string|null $totalAmount
     * @return $this
     */
    public function setTotalAmount(?string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get the order currency (ISO-4217).
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * Set the order currency.
     *
     * @param string|null $currency
     * @return $this
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the billTo address fields.
     *
     * @return array<string, string|null>
     */
    public function getBillTo(): array
    {
        return $this->billTo;
    }

    /**
     * Set the billTo address fields, keyed by API field name.
     *
     * @param array<string, string|null> $billTo
     * @return $this
     */
    public function setBillTo(array $billTo): self
    {
        $this->billTo = $billTo;

        return $this;
    }

    /**
     * Build the JSON-ready request tree, omitting null/empty leaves.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $request = $this->filterEmpty([
            'clientVersion' => $this->clientVersion,
            'targetOrigins' => $this->targetOrigins,
            'allowedCardNetworks' => $this->allowedCardNetworks,
            'allowedPaymentTypes' => $this->allowedPaymentTypes,
            'country' => $this->country,
            'locale' => $this->locale,
        ]);

        $captureMandate = $this->filterEmpty([
            'billingType' => $this->billingType,
            'requestSaveCard' => $this->requestSaveCard,
        ]);
        if (!empty($captureMandate)) {
            $request['captureMandate'] = $captureMandate;
        }

        $completeMandate = $this->filterEmpty([
            'type' => $this->completeMandateType,
            'decisionManager' => $this->decisionManager,
            'consumerAuthentication' => $this->consumerAuthentication,
        ]);
        if (!empty($completeMandate)) {
            $request['completeMandate'] = $completeMandate;
        }

        $orderInformation = [];

        $amountDetails = $this->filterEmpty([
            'totalAmount' => $this->totalAmount,
            'currency' => $this->currency,
        ]);
        if (!empty($amountDetails)) {
            $orderInformation['amountDetails'] = $amountDetails;
        }

        $billTo = $this->filterEmpty($this->billTo);
        if (!empty($billTo)) {
            $orderInformation['billTo'] = $billTo;
        }

        if (!empty($orderInformation)) {
            $request['orderInformation'] = $orderInformation;
        }

        return $request;
    }

    /**
     * Drop null/empty-string/empty-array leaves while preserving boolean false values.
     *
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private function filterEmpty(array $values): array
    {
        return array_filter(
            $values,
            static fn($value): bool => $value !== null && $value !== '' && $value !== []
        );
    }
}
