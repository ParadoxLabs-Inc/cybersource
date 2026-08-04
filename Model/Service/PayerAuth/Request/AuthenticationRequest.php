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

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FilterEmptyTrait;

/**
 * Typed value object for POST /risk/v1/authentications (the enrollment check / authentication).
 *
 * Two hard requirements are enforced here rather than discovered in production:
 *
 * 1. FULL browser deviceInformation. Verified live 2026-08-04 (gate G2, finding 1): with only
 *    Accept/User-Agent, an enrolled card silently degrades to veresEnrolled U / vbv_failure, i.e.
 *    a silent 3DS bypass. Missing/empty browser data therefore fails loud.
 * 2. Exactly one card addressing shape — raw card, TMS payment-instrument id, or (new card, PAN
 *    never server-side) the Unified Checkout transient token.
 *
 * referenceId (from authentication-setups) is OPTIONAL: the sandbox runs authentications with no
 * referenceId at all, so the client's DDC timeout can proceed without it (gate G1 bonus finding).
 */
class AuthenticationRequest
{
    use FilterEmptyTrait;

    /**
     * Browser fields the request must always carry (gate G2, finding 1).
     */
    public const REQUIRED_DEVICE_FIELDS = [
        'httpAcceptBrowserValue',
        'userAgentBrowserValue',
        'ipAddress',
        'httpBrowserLanguage',
        'httpBrowserJavaEnabled',
        'httpBrowserJavaScriptEnabled',
        'httpBrowserColorDepth',
        'httpBrowserScreenHeight',
        'httpBrowserScreenWidth',
        'httpBrowserTimeDifference',
    ];

    /**
     * Card fields accepted under paymentInformation.card.
     */
    public const CARD_FIELDS = [
        'number',
        'expirationMonth',
        'expirationYear',
        'type',
    ];

    /**
     * @var string|null
     */
    private ?string $clientReferenceCode = null;

    /**
     * Setup reference id. Optional — authentications runs without it.
     *
     * @var string|null
     */
    private ?string $referenceId = null;

    /**
     * @var string|null
     */
    private ?string $returnUrl = null;

    /**
     * @var string|null
     */
    private ?string $totalAmount = null;

    /**
     * @var string|null
     */
    private ?string $currency = null;

    /**
     * Billing address fields, keyed by API field name, already sanitized by the caller.
     *
     * @var array<string, string|null>
     */
    private array $billTo = [];

    /**
     * Raw card fields (new-card path), keyed by API field name.
     *
     * @var array<string, string|null>
     */
    private array $card = [];

    /**
     * TMS payment-instrument id (stored-card path).
     *
     * @var string|null
     */
    private ?string $paymentInstrumentId = null;

    /**
     * Unified Checkout transient token (newly entered card, not yet tokenized).
     *
     * @var string|null
     */
    private ?string $transientToken = null;

    /**
     * Browser/device fields, keyed by API field name.
     *
     * @var array<string, string|null>
     */
    private array $deviceInformation = [];

    /**
     * Get the merchant client-reference code.
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
     * Get the device-data-collection reference id from authentication-setups.
     *
     * @return string|null
     */
    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    /**
     * Set the device-data-collection reference id (optional).
     *
     * @param string|null $referenceId
     * @return $this
     */
    public function setReferenceId(?string $referenceId): self
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    /**
     * Get the challenge return URL.
     *
     * @return string|null
     */
    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    /**
     * Set the challenge return URL (where the ACS posts the challenge result).
     *
     * @param string|null $returnUrl
     * @return $this
     */
    public function setReturnUrl(?string $returnUrl): self
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    /**
     * Get the authenticated amount (fixed 2-decimal string).
     *
     * @return string|null
     */
    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    /**
     * Set the authenticated amount (fixed 2-decimal string, e.g. "24.00").
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
     * Get the authenticated currency (ISO-4217).
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * Set the authenticated currency (ISO-4217).
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
     * Set the billTo address fields, keyed by API field name (sanitized by the caller).
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
     * Get the raw card fields.
     *
     * @return array<string, string|null>
     */
    public function getCard(): array
    {
        return $this->card;
    }

    /**
     * Set the raw card fields; only number/expirationMonth/expirationYear/type are kept.
     *
     * @param array<string, string|null> $card
     * @return $this
     */
    public function setCard(array $card): self
    {
        $this->card = $this->filterEmpty(
            array_intersect_key($card, array_flip(self::CARD_FIELDS))
        );

        return $this;
    }

    /**
     * Get the TMS payment-instrument id.
     *
     * @return string|null
     */
    public function getPaymentInstrumentId(): ?string
    {
        return $this->paymentInstrumentId;
    }

    /**
     * Set the TMS payment-instrument id (paymentInformation.paymentInstrument.id).
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
     * Get the Unified Checkout transient token.
     *
     * @return string|null
     */
    public function getTransientToken(): ?string
    {
        return $this->transientToken;
    }

    /**
     * Set the Unified Checkout transient token (tokenInformation.transientToken).
     *
     * The new-card path has no other way to address the card: the PAN never reaches the server, and
     * a card that has not been charged yet has no TMS payment-instrument id. The setups call takes
     * this exact shape (gate G1, 201-verified), and authentications belongs to the same API family;
     * it is nonetheless the one shape in this DTO not yet proven live on THIS endpoint.
     *
     * @param string|null $transientToken
     * @return $this
     */
    public function setTransientToken(?string $transientToken): self
    {
        $this->transientToken = $transientToken;

        return $this;
    }

    /**
     * Get the browser/device fields.
     *
     * @return array<string, string|null>
     */
    public function getDeviceInformation(): array
    {
        return $this->deviceInformation;
    }

    /**
     * Set the browser/device fields, keyed by API field name.
     *
     * @param array<string, string|null> $deviceInformation
     * @return $this
     */
    public function setDeviceInformation(array $deviceInformation): self
    {
        $this->deviceInformation = $deviceInformation;

        return $this;
    }

    /**
     * Build the JSON-ready request tree, omitting null/empty leaves.
     *
     * @return array<string, mixed>
     * @throws InputException On a missing return URL, amount/currency, card addressing, or any
     *                        missing browser field.
     */
    public function toArray(): array
    {
        $this->validate();

        $request = [];

        $clientReferenceInformation = $this->filterEmpty(['code' => $this->clientReferenceCode]);
        if (!empty($clientReferenceInformation)) {
            $request['clientReferenceInformation'] = $clientReferenceInformation;
        }

        $request['consumerAuthenticationInformation'] = $this->filterEmpty([
            'referenceId' => $this->referenceId,
            'returnUrl' => $this->returnUrl,
        ]);

        $orderInformation = [
            'amountDetails' => [
                'totalAmount' => $this->totalAmount,
                'currency' => $this->currency,
            ],
        ];

        $billTo = $this->filterEmpty($this->billTo);
        if (!empty($billTo)) {
            $orderInformation['billTo'] = $billTo;
        }

        $request['orderInformation']  = $orderInformation;
        $request['deviceInformation'] = $this->buildDeviceInformation();

        if ($this->hasTransientToken()) {
            $request['tokenInformation'] = ['transientToken' => $this->transientToken];
        } else {
            $request['paymentInformation'] = !empty($this->card)
                ? ['card' => $this->card]
                : ['paymentInstrument' => ['id' => $this->paymentInstrumentId]];
        }

        return $request;
    }

    /**
     * Assert everything the authentications call needs is present.
     *
     * @return void
     * @throws InputException
     */
    private function validate(): void
    {
        if ($this->returnUrl === null || $this->returnUrl === '') {
            throw new InputException(__('Payer Authentication requires a return URL.'));
        }

        if ($this->totalAmount === null || $this->totalAmount === ''
            || $this->currency === null || $this->currency === '') {
            throw new InputException(__('Payer Authentication requires an amount and currency.'));
        }

        $shapes = (int)!empty($this->card)
            + (int)($this->paymentInstrumentId !== null && $this->paymentInstrumentId !== '')
            + (int)$this->hasTransientToken();

        if ($shapes !== 1) {
            throw new InputException(
                __(
                    'Payer Authentication requires exactly one of card details, a payment'
                    . ' instrument id, or a transient token.'
                )
            );
        }

        $this->validateDeviceInformation();
    }

    /**
     * Assert the full browser device profile is present (gate G2, finding 1: thin data = silent bypass).
     *
     * @return void
     * @throws InputException
     */
    private function validateDeviceInformation(): void
    {
        $missing = [];

        foreach (self::REQUIRED_DEVICE_FIELDS as $field) {
            $value = $this->deviceInformation[$field] ?? null;

            if ($value === null || (string)$value === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new InputException(
                __(
                    'Payer Authentication requires complete browser information. Missing: %1',
                    implode(', ', $missing)
                )
            );
        }
    }

    /**
     * Whether a transient token is set.
     *
     * @return bool
     */
    private function hasTransientToken(): bool
    {
        return $this->transientToken !== null && $this->transientToken !== '';
    }

    /**
     * Build the deviceInformation branch: the required browser fields plus any extra caller fields.
     *
     * @return array<string, mixed>
     */
    private function buildDeviceInformation(): array
    {
        return $this->filterEmpty($this->deviceInformation);
    }
}
