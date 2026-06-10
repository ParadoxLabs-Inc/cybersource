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
 * Typed value object for the Unified Checkout payment (auth/sale) request body.
 *
 * Hand-written "type safety without the SDK" (DECISION D14). Mirrors the fields we actually send
 * under CreatePaymentRequest for POST /pts/v2/payments; toArray() emits a clean JSON-ready tree
 * with null/empty leaves omitted, while preserving boolean false (the all-important `capture` flag,
 * where false = authorize-only and true = sale).
 *
 * @see UC-API-REFERENCE.md §3
 */
class PaymentRequest
{
    /**
     * @var string|null
     */
    private ?string $transientTokenJwt = null;

    /**
     * @var string|null
     */
    private ?string $clientReferenceCode = null;

    /**
     * @var string[]
     */
    private array $actionList = [];

    /**
     * @var string[]
     */
    private array $actionTokenTypes = [];

    /**
     * @var bool|null
     */
    private ?bool $capture = null;

    /**
     * Per-transaction Decision Manager toggle. Null = account default; false = suppress DM on this call.
     *
     * @var bool|null
     */
    private ?bool $enableDecisionManager = null;

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
     * CyberSource partner solution ID (clientReferenceInformation.partner.solutionId).
     *
     * @var string|null
     */
    private ?string $solutionId = null;

    /**
     * Extension identifier (clientReferenceInformation.applicationName).
     *
     * @var string|null
     */
    private ?string $applicationName = null;

    /**
     * Extension version (clientReferenceInformation.applicationVersion).
     *
     * @var string|null
     */
    private ?string $applicationVersion = null;

    /**
     * Get the transient-token JWT that represents the captured card.
     *
     * @return string|null
     */
    public function getTransientTokenJwt(): ?string
    {
        return $this->transientTokenJwt;
    }

    /**
     * Set the transient-token JWT that represents the captured card.
     *
     * @param string|null $transientTokenJwt
     * @return $this
     */
    public function setTransientTokenJwt(?string $transientTokenJwt): self
    {
        $this->transientTokenJwt = $transientTokenJwt;

        return $this;
    }

    /**
     * Get the merchant client-reference code (order increment id / origin).
     *
     * @return string|null
     */
    public function getClientReferenceCode(): ?string
    {
        return $this->clientReferenceCode;
    }

    /**
     * Set the merchant client-reference code.
     *
     * @param string|null $clientReferenceCode
     * @return $this
     */
    public function setClientReferenceCode(?string $clientReferenceCode): self
    {
        $this->clientReferenceCode = $clientReferenceCode;

        return $this;
    }

    /**
     * Get the processingInformation.actionList (e.g. ['TOKEN_CREATE']).
     *
     * @return string[]
     */
    public function getActionList(): array
    {
        return $this->actionList;
    }

    /**
     * Set the processingInformation.actionList.
     *
     * @param string[] $actionList
     * @return $this
     */
    public function setActionList(array $actionList): self
    {
        $this->actionList = array_values(array_filter($actionList));

        return $this;
    }

    /**
     * Get the processingInformation.actionTokenTypes (customer/paymentInstrument/instrumentIdentifier).
     *
     * @return string[]
     */
    public function getActionTokenTypes(): array
    {
        return $this->actionTokenTypes;
    }

    /**
     * Set the processingInformation.actionTokenTypes.
     *
     * @param string[] $actionTokenTypes
     * @return $this
     */
    public function setActionTokenTypes(array $actionTokenTypes): self
    {
        $this->actionTokenTypes = array_values(array_filter($actionTokenTypes));

        return $this;
    }

    /**
     * Get the capture flag (false = authorize-only, true = sale).
     *
     * @return bool|null
     */
    public function getCapture(): ?bool
    {
        return $this->capture;
    }

    /**
     * Set the capture flag.
     *
     * @param bool|null $capture
     * @return $this
     */
    public function setCapture(?bool $capture): self
    {
        $this->capture = $capture;

        return $this;
    }

    /**
     * Get the per-transaction Decision Manager toggle (null = account default, false = suppress).
     *
     * @return bool|null
     */
    public function getEnableDecisionManager(): ?bool
    {
        return $this->enableDecisionManager;
    }

    /**
     * Set the per-transaction Decision Manager toggle.
     *
     * @param bool|null $enableDecisionManager
     * @return $this
     */
    public function setEnableDecisionManager(?bool $enableDecisionManager): self
    {
        $this->enableDecisionManager = $enableDecisionManager;

        return $this;
    }

    /**
     * Get the order total amount (fixed 2-decimal string, e.g. "24.00").
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
     * Get the partner solution ID.
     *
     * @return string|null
     */
    public function getSolutionId(): ?string
    {
        return $this->solutionId;
    }

    /**
     * Set the partner solution ID (clientReferenceInformation.partner.solutionId).
     *
     * @param string|null $solutionId
     * @return $this
     */
    public function setSolutionId(?string $solutionId): self
    {
        $this->solutionId = $solutionId;

        return $this;
    }

    /**
     * Get the application name.
     *
     * @return string|null
     */
    public function getApplicationName(): ?string
    {
        return $this->applicationName;
    }

    /**
     * Set the application name (clientReferenceInformation.applicationName).
     *
     * @param string|null $applicationName
     * @return $this
     */
    public function setApplicationName(?string $applicationName): self
    {
        $this->applicationName = $applicationName;

        return $this;
    }

    /**
     * Get the application version.
     *
     * @return string|null
     */
    public function getApplicationVersion(): ?string
    {
        return $this->applicationVersion;
    }

    /**
     * Set the application version (clientReferenceInformation.applicationVersion).
     *
     * @param string|null $applicationVersion
     * @return $this
     */
    public function setApplicationVersion(?string $applicationVersion): self
    {
        $this->applicationVersion = $applicationVersion;

        return $this;
    }

    /**
     * Build the JSON-ready request tree, omitting null/empty leaves while preserving boolean false.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $request = [];

        $clientReferenceInformation = $this->filterEmpty([
            'code' => $this->clientReferenceCode,
            'applicationName' => $this->applicationName,
            'applicationVersion' => $this->applicationVersion,
        ]);
        $partner = $this->filterEmpty(['solutionId' => $this->solutionId]);
        if (!empty($partner)) {
            $clientReferenceInformation['partner'] = $partner;
        }

        if (!empty($clientReferenceInformation)) {
            $request['clientReferenceInformation'] = $clientReferenceInformation;
        }

        $processingInformation = $this->filterEmpty([
            'actionList' => $this->actionList,
            'actionTokenTypes' => $this->actionTokenTypes,
            'capture' => $this->capture,
            'enableDecisionManager' => $this->enableDecisionManager,
        ]);
        if (!empty($processingInformation)) {
            $request['processingInformation'] = $processingInformation;
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

        $tokenInformation = $this->filterEmpty([
            'transientTokenJwt' => $this->transientTokenJwt,
        ]);
        if (!empty($tokenInformation)) {
            $request['tokenInformation'] = $tokenInformation;
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
