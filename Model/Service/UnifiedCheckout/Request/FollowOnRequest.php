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
 * Typed value object for the Unified Checkout follow-on request bodies (capture/refund/reversal).
 *
 * Hand-written "type safety without the SDK" (DECISION D14), the follow-on sibling of PaymentRequest.
 * One DTO covers all three follow-on shapes because they share the same field tree:
 *   - capture  POST /pts/v2/payments/{id}/captures   → clientReferenceInformation + orderInformation.amountDetails
 *   - refund   POST /pts/v2/captures/{id}/refunds     → clientReferenceInformation + orderInformation.amountDetails
 *   - reversal POST /pts/v2/payments/{id}/reversals   → clientReferenceInformation + reversalInformation.amountDetails
 *
 * The reversal flag flips amountDetails to reversalInformation per the REST contract; toArray() emits a
 * clean JSON-ready tree with null/empty leaves omitted.
 *
 * @see UC-API-REFERENCE.md §3
 */
class FollowOnRequest
{
    use FilterEmptyTrait;

    /**
     * @var string|null
     */
    private ?string $clientReferenceCode = null;

    /**
     * @var string|null
     */
    private ?string $totalAmount = null;

    /**
     * @var string|null
     */
    private ?string $currency = null;

    /**
     * When true, emit the amount under reversalInformation (auth reversal) rather than orderInformation.
     *
     * @var bool
     */
    private bool $reversal = false;

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
     * Get the follow-on amount (fixed 2-decimal string, e.g. "24.00").
     *
     * @return string|null
     */
    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    /**
     * Set the follow-on amount.
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
     * Get the currency (ISO-4217).
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * Set the currency.
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
     * Whether this body is an auth reversal (amount under reversalInformation rather than orderInformation).
     *
     * @return bool
     */
    public function isReversal(): bool
    {
        return $this->reversal;
    }

    /**
     * Set the reversal flag.
     *
     * @param bool $reversal
     * @return $this
     */
    public function setReversal(bool $reversal): self
    {
        $this->reversal = $reversal;

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
     * Build the JSON-ready request tree, omitting null/empty leaves.
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

        $amountDetails = $this->filterEmpty([
            'totalAmount' => $this->totalAmount,
            'currency' => $this->currency,
        ]);

        if (!empty($amountDetails)) {
            // Reversal carries its amount under reversalInformation; capture/refund under orderInformation.
            if ($this->reversal === true) {
                $request['reversalInformation'] = [
                    'amountDetails' => $amountDetails,
                ];
            } else {
                $request['orderInformation'] = [
                    'amountDetails' => $amountDetails,
                ];
            }
        }

        return $request;
    }
}
