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

use ParadoxLabs\CyberSource\Model\Source\CardType;
use Throwable;

/**
 * Decodes display card metadata (type/last4/bin/expiry) from a Unified Checkout transient-token JWT.
 *
 * WHY: the /pts/v2/payments reply for a transient-token auth returns ONLY paymentInformation.card.type —
 * no suffix/bin/expiry — so Response::extractCardMetadata() alone yields just cc_type, leaving the vault
 * card and sales_order_payment cc_* fields empty. The transient token's payload DOES carry the masked
 * number, bin, and expiration (UC Transient Token Format), so we mine those server-side as a seed that
 * the gateway reply can override.
 *
 * NO SIGNATURE VERIFICATION — deliberate: this reader only mines DISPLAY metadata (masked digits, bin,
 * expiry, brand) from a token that is then submitted to CyberSource for the authoritative authorization.
 * A forged token cannot move money or vault a card — CyberSource rejects it at /pts/v2/payments — so the
 * worst a tampered payload can do is mislabel its own order's display fields. Verifying the signature
 * would add a JWKS fetch on the payment path for no security gain.
 *
 * Payload shape (from a real browser-minted gda-0.10.0 PANENTRY token): scalar card fields are wrapped
 * as {"value": "..."} objects, the number is {"maskedValue": "XXXXXXXXXXXX1111", "bin": "411111"}, and
 * ABSENT fields serialize as empty arrays ([]). Wallet tokens (metadata.paymentType APPLEPAY/GOOGLEPAY)
 * may omit some or all card fields. Every accessor here therefore tolerates wrapped objects, bare
 * scalars, empty arrays, and missing keys; any malformed input yields [] rather than an exception.
 */
class TransientTokenReader
{
    /**
     * TransientTokenReader constructor.
     *
     * @param CardType $cardType
     */
    public function __construct(
        protected readonly CardType $cardType
    ) {
    }

    /**
     * Extract normalized card metadata from the transient-token JWT payload.
     *
     * Returns the same key set Response::extractCardMetadata() emits (cc_type, cc_last4, cc_bin,
     * cc_exp_month, cc_exp_year), filtered to the fields actually present in the token. Returns []
     * on any malformed or metadata-less input; never throws.
     *
     * @param string $transientTokenJwt
     * @return array<string, string>
     */
    public function read(string $transientTokenJwt): array
    {
        try {
            $card = $this->getCardClaims($transientTokenJwt);
            if ($card === []) {
                return [];
            }

            $number = is_array($card['number'] ?? null) ? $card['number'] : [];

            $typeCode = $this->scalarValue($card['type'] ?? null);
            $type     = $typeCode !== null ? $this->cardType->getType($typeCode) : null;

            return array_filter([
                'cc_type' => $type,
                'cc_last4' => $this->extractLast4($this->scalarValue($number['maskedValue'] ?? null)),
                'cc_bin' => $this->scalarValue($number['bin'] ?? null),
                'cc_exp_month' => $this->scalarValue($card['expirationMonth'] ?? null),
                'cc_exp_year' => $this->scalarValue($card['expirationYear'] ?? null),
            ], static fn($value): bool => $value !== null && $value !== '');
        } catch (Throwable $error) {

            return [];
        }
    }

    /**
     * Base64url-decode the JWT payload segment and return content.paymentInformation.card, or [].
     *
     * @param string $transientTokenJwt
     * @return array<string, mixed>
     */
    protected function getCardClaims(string $transientTokenJwt): array
    {
        $segments = explode('.', $transientTokenJwt);
        if (count($segments) < 2 || $segments[1] === '') {
            return [];
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction -- decoding a JWT segment, not hiding code.
        $json = base64_decode(strtr($segments[1], '-_', '+/'), false);
        if ($json === false || $json === '') {
            return [];
        }

        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            return [];
        }

        $card = $payload['content']['paymentInformation']['card'] ?? null;

        return is_array($card) ? $card : [];
    }

    /**
     * Normalize a token claim field to its scalar string value, or null when absent/malformed.
     *
     * Real payloads wrap scalars as {"value": "..."} objects and serialize absent fields as empty
     * arrays; be permissive and also accept a bare scalar in case the wrapping ever changes.
     *
     * @param mixed $field
     * @return string|null
     */
    protected function scalarValue(mixed $field): ?string
    {
        if (is_array($field)) {
            $field = $field['value'] ?? null;
        }

        if (is_scalar($field) && (string)$field !== '') {
            return (string)$field;
        }

        return null;
    }

    /**
     * Pull the trailing digits (up to 4) off the masked card number, or null when none.
     *
     * @param string|null $maskedValue
     * @return string|null
     */
    protected function extractLast4(?string $maskedValue): ?string
    {
        if ($maskedValue === null || !preg_match('/(\d{1,4})$/', $maskedValue, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
