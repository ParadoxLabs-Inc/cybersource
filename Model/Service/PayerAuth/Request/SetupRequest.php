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
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\LegacyTokenTrait;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FilterEmptyTrait;

/**
 * Typed value object for POST /risk/v1/authentication-setups (device data collection setup).
 *
 * The card is addressed EITHER by the Unified Checkout transient token (new card) OR by the TMS
 * payment-instrument id (stored card). Both shapes were verified 201 COMPLETED against the live
 * sandbox; the raw paymentInstrument shape is used for vault cards
 * because it matches how the payment call addresses them.
 *
 * orderInformation.billTo is REQUIRED by the endpoint: without it the call answers
 * 400 MISSING_FIELD orderInformation.billTo.administrativeArea and no referenceId is ever issued,
 * so device data collection silently never runs. No amountDetails — the setup call does not price
 * anything, and the API asked for billTo only.
 */
class SetupRequest
{
    use FilterEmptyTrait;
    use LegacyTokenTrait;

    /**
     * @var string|null
     */
    private ?string $clientReferenceCode = null;

    /**
     * Unified Checkout transient token (new-card path).
     *
     * @var string|null
     */
    private ?string $transientToken = null;

    /**
     * TMS payment-instrument id (stored-card path).
     *
     * @var string|null
     */
    private ?string $paymentInstrumentId = null;

    /**
     * Billing address fields, keyed by API field name, already sanitized by the caller.
     *
     * @var array<string, string|null>
     */
    private array $billTo = [];

    /**
     * Get the merchant client-reference code (quote id / order increment id).
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
     * Get the Unified Checkout transient token.
     *
     * @return string|null
     */
    public function getTransientToken(): ?string
    {
        return $this->transientToken;
    }

    /**
     * Set the Unified Checkout transient token (tokenInformation.transientTokenJwt).
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
     * Same caller-sanitized shape the authentications call takes, so both requests describe the
     * cardholder identically.
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
     * @throws InputException When neither or both card addressing shapes are set.
     */
    public function toArray(): array
    {
        $hasToken = $this->transientToken !== null && $this->transientToken !== '';
        $hasPi    = $this->paymentInstrumentId !== null && $this->paymentInstrumentId !== '';

        if ($hasToken === $hasPi) {
            throw new InputException(
                __(
                    'Payer Authentication setup requires exactly one of a transient token or a'
                    . ' payment instrument id.'
                )
            );
        }

        $request = [];

        $clientReferenceInformation = $this->filterEmpty(['code' => $this->clientReferenceCode]);
        if (!empty($clientReferenceInformation)) {
            $request['clientReferenceInformation'] = $clientReferenceInformation;
        }

        $billTo = $this->filterEmpty($this->billTo);
        if (!empty($billTo)) {
            $request['orderInformation'] = ['billTo' => $billTo];
        }

        if ($hasToken) {
            $request['tokenInformation'] = ['transientTokenJwt' => $this->transientToken];
        } elseif ($this->isLegacyToken($this->paymentInstrumentId)) {
            $request['paymentInformation'] = [
                'customer' => ['customerId' => $this->paymentInstrumentId],
            ];
        } else {
            $request['paymentInformation'] = [
                'paymentInstrument' => ['id' => $this->paymentInstrumentId],
            ];
        }

        return $request;
    }
}
