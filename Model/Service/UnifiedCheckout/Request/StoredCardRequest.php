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
 * Typed value object for the Unified Checkout STORED-CARD (vault / MIT) payment request body.
 *
 * Hand-written "type safety without the SDK" (DECISION D14), the stored-card sibling of PaymentRequest.
 * The card is already vaulted, so there is NO transient token and NO actionList/TOKEN_CREATE; instead the
 * three TMS ids are sent under paymentInformation (customer / paymentInstrument / instrumentIdentifier),
 * plus the stored-credential / merchant-initiated initiator block under
 * processingInformation.authorizationOptions.initiator. toArray() emits a clean JSON-ready tree with
 * null/empty leaves omitted, while preserving boolean false (the all-important `capture` flag, where
 * false = authorize-only and true = sale).
 *
 * The merchantInitiatedTransaction sub-object is emitted only when previousTransactionId is non-empty; the
 * authorizationOptions.initiator block is emitted only when initiatorType is set.
 *
 * @see UC-API-REFERENCE.md §1.B
 * @see UC-API-REFERENCE.md §3
 */
class StoredCardRequest
{
    /**
     * @var string|null
     */
    private ?string $clientReferenceCode = null;

    /**
     * @var bool|null
     */
    private ?bool $capture = null;

    /**
     * @var string|null
     */
    private ?string $commerceIndicator = null;

    /**
     * Initiator type: 'merchant' (MIT) or 'customer' (CIT).
     *
     * @var string|null
     */
    private ?string $initiatorType = null;

    /**
     * @var bool|null
     */
    private ?bool $storedCredentialUsed = null;

    /**
     * @var string|null
     */
    private ?string $previousTransactionId = null;

    /**
     * @var string|null
     */
    private ?string $customerId = null;

    /**
     * @var string|null
     */
    private ?string $paymentInstrumentId = null;

    /**
     * @var string|null
     */
    private ?string $instrumentIdentifierId = null;

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
     * Get the commerceIndicator (e.g. 'recurring' for an MIT).
     *
     * @return string|null
     */
    public function getCommerceIndicator(): ?string
    {
        return $this->commerceIndicator;
    }

    /**
     * Set the commerceIndicator.
     *
     * @param string|null $commerceIndicator
     * @return $this
     */
    public function setCommerceIndicator(?string $commerceIndicator): self
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
    }

    /**
     * Get the initiator type ('merchant' = MIT, 'customer' = CIT).
     *
     * @return string|null
     */
    public function getInitiatorType(): ?string
    {
        return $this->initiatorType;
    }

    /**
     * Set the initiator type.
     *
     * @param string|null $initiatorType
     * @return $this
     */
    public function setInitiatorType(?string $initiatorType): self
    {
        $this->initiatorType = $initiatorType;

        return $this;
    }

    /**
     * Get the storedCredentialUsed flag.
     *
     * @return bool|null
     */
    public function getStoredCredentialUsed(): ?bool
    {
        return $this->storedCredentialUsed;
    }

    /**
     * Set the storedCredentialUsed flag.
     *
     * @param bool|null $storedCredentialUsed
     * @return $this
     */
    public function setStoredCredentialUsed(?bool $storedCredentialUsed): self
    {
        $this->storedCredentialUsed = $storedCredentialUsed;

        return $this;
    }

    /**
     * Get the prior transaction id for the merchantInitiatedTransaction reference (MIT only).
     *
     * @return string|null
     */
    public function getPreviousTransactionId(): ?string
    {
        return $this->previousTransactionId;
    }

    /**
     * Set the prior transaction id for the merchantInitiatedTransaction reference.
     *
     * @param string|null $previousTransactionId
     * @return $this
     */
    public function setPreviousTransactionId(?string $previousTransactionId): self
    {
        $this->previousTransactionId = $previousTransactionId;

        return $this;
    }

    /**
     * Get the TMS customer id (paymentInformation.customer.id).
     *
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * Set the TMS customer id.
     *
     * @param string|null $customerId
     * @return $this
     */
    public function setCustomerId(?string $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get the TMS paymentInstrument id (paymentInformation.paymentInstrument.id).
     *
     * @return string|null
     */
    public function getPaymentInstrumentId(): ?string
    {
        return $this->paymentInstrumentId;
    }

    /**
     * Set the TMS paymentInstrument id.
     *
     * @param string|null $paymentInstrumentId
     * @return $this
     */
    public function setPaymentInstrumentId(?string $paymentInstrumentId): self
    {
        $this->paymentInstrumentId = $paymentInstrumentId;

        return $this;
    }

    /**
     * Get the TMS instrumentIdentifier id (paymentInformation.instrumentIdentifier.id).
     *
     * @return string|null
     */
    public function getInstrumentIdentifierId(): ?string
    {
        return $this->instrumentIdentifierId;
    }

    /**
     * Set the TMS instrumentIdentifier id.
     *
     * @param string|null $instrumentIdentifierId
     * @return $this
     */
    public function setInstrumentIdentifierId(?string $instrumentIdentifierId): self
    {
        $this->instrumentIdentifierId = $instrumentIdentifierId;

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
     * Build the JSON-ready stored-card request tree, omitting null/empty leaves but preserving boolean false.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $request = [];

        $clientReferenceInformation = $this->filterEmpty([
            'code' => $this->clientReferenceCode,
        ]);
        if (!empty($clientReferenceInformation)) {
            $request['clientReferenceInformation'] = $clientReferenceInformation;
        }

        $processingInformation = $this->buildProcessingInformation();
        if (!empty($processingInformation)) {
            $request['processingInformation'] = $processingInformation;
        }

        $paymentInformation = $this->buildPaymentInformation();
        if (!empty($paymentInformation)) {
            $request['paymentInformation'] = $paymentInformation;
        }

        $orderInformation = $this->buildOrderInformation();
        if (!empty($orderInformation)) {
            $request['orderInformation'] = $orderInformation;
        }

        return $request;
    }

    /**
     * Assemble processingInformation: capture, commerceIndicator, and the initiator/stored-credential block.
     *
     * @return array<string, mixed>
     */
    private function buildProcessingInformation(): array
    {
        $processingInformation = $this->filterEmpty([
            'capture' => $this->capture,
            'commerceIndicator' => $this->commerceIndicator,
        ]);

        // The initiator block is emitted only when an initiatorType is set (CIT or MIT).
        if ($this->initiatorType !== null && $this->initiatorType !== '') {
            $initiator = $this->filterEmpty([
                'type' => $this->initiatorType,
                'storedCredentialUsed' => $this->storedCredentialUsed,
            ]);

            // The merchantInitiatedTransaction sub-object rides along only with a reachable prior txn id.
            if ($this->previousTransactionId !== null && $this->previousTransactionId !== '') {
                $initiator['merchantInitiatedTransaction'] = [
                    'previousTransactionId' => $this->previousTransactionId,
                ];
            }

            $processingInformation['authorizationOptions'] = [
                'initiator' => $initiator,
            ];
        }

        return $processingInformation;
    }

    /**
     * Assemble paymentInformation from the three TMS ids (paymentInstrument required; the rest optional).
     *
     * @return array<string, mixed>
     */
    private function buildPaymentInformation(): array
    {
        $paymentInformation = [];

        if ($this->customerId !== null && $this->customerId !== '') {
            $paymentInformation['customer'] = ['id' => $this->customerId];
        }

        if ($this->paymentInstrumentId !== null && $this->paymentInstrumentId !== '') {
            $paymentInformation['paymentInstrument'] = ['id' => $this->paymentInstrumentId];
        }

        if ($this->instrumentIdentifierId !== null && $this->instrumentIdentifierId !== '') {
            $paymentInformation['instrumentIdentifier'] = ['id' => $this->instrumentIdentifierId];
        }

        return $paymentInformation;
    }

    /**
     * Assemble orderInformation (amountDetails + billTo).
     *
     * @return array<string, mixed>
     */
    private function buildOrderInformation(): array
    {
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

        return $orderInformation;
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
