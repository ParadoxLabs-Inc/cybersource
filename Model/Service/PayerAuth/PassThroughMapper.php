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

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth;

/**
 * Maps a persisted authentication result onto the /pts/v2/payments pass-through fields, per network.
 *
 * The ONE place the per-network mapping lives (PA1-IMPLEMENTATION.md, "Per-network pass-through
 * mapping" / PAYER-AUTH-PLAN.md gate G3):
 *
 *  - Universal: `cavv`, `xid`, `eciRaw`, `paresStatus`, `paSpecificationVersion` (from the reply's
 *    `specificationVersion`), `directoryServerTransactionId`.
 *  - Mastercard/Maestro ride UCAF instead of CAVV: the same authentication value is sent as
 *    `ucafAuthenticationData` with a `ucafCollectionIndicator`, `directoryServerTransactionId` is
 *    required, and `cavv` is OMITTED.
 *  - The authentications reply's TEXT `ecommerceIndicator` (`vbv`, `vbv_attempted`, `spa`,
 *    `internet`, …) is passed through as `processingInformation.commerceIndicator`. When the reply
 *    carries none, none is sent — an indicator is never invented here.
 *
 * Nothing is derived from the top-level authentication `status`: only AUTHENTICATED / ATTEMPTED
 * results ever reach this class (see Verdict::hasLiabilityShift()), and every emitted value comes
 * from the persisted reply.
 *
 * The `ca` input holds the CAVV/UCAF value — this class must never log or echo it.
 */
class PassThroughMapper
{
    /**
     * Magento credit card type codes that authenticate over Mastercard's UCAF rails.
     *
     * MC = Mastercard, MI = Maestro (International), MD = Maestro (Domestic). G3 pins UCAF to
     * "Mastercard/Maestro"; CAVV covers Visa/Amex/Discover/Diners/JCB/CUP/Elo.
     */
    public const UCAF_CARD_TYPES = [
        'MC',
        'MI',
        'MD',
    ];

    /**
     * Universal source-field => request-field mapping (applies to every network).
     */
    private const FIELD_MAP = [
        'cavv' => 'cavv',
        'xid' => 'xid',
        'eciRaw' => 'eciRaw',
        'paresStatus' => 'paresStatus',
        'specificationVersion' => 'paSpecificationVersion',
        'directoryServerTransactionId' => 'directoryServerTransactionId',
    ];

    /**
     * paresStatus => ucafCollectionIndicator. Y (fully authenticated) = 2, A (attempted) = 1.
     */
    private const PARES_UCAF_INDICATOR = [
        'Y' => '2',
        'A' => '1',
    ];

    /**
     * Mastercard eciRaw => ucafCollectionIndicator fallback. MC ECI 02 = full auth, 01 = attempts.
     */
    private const ECI_UCAF_INDICATOR = [
        '02' => '2',
        '2' => '2',
        '01' => '1',
        '1' => '1',
    ];

    /**
     * Build the pass-through block (and commerceIndicator) for this result and card network.
     *
     * @param array<string, mixed> $ca Persisted consumerAuthenticationInformation from the reply.
     * @param string $ccType Magento credit card type code of the instrument being charged.
     * @return array{consumerAuthenticationInformation: array<string, string>, commerceIndicator: string|null}
     */
    public function map(array $ca, string $ccType): array
    {
        $block = [];

        foreach (self::FIELD_MAP as $source => $target) {
            $value = $this->scalarValue($ca[$source] ?? null);

            if ($value !== null) {
                $block[$target] = $value;
            }
        }

        if ($this->isUcafNetwork($ccType)) {
            $block = $this->applyUcaf($block, $ca);
        }

        return [
            'consumerAuthenticationInformation' => $block,
            'commerceIndicator' => $this->scalarValue($ca['ecommerceIndicator'] ?? null),
        ];
    }

    /**
     * Swap the CAVV for the UCAF pair on the Mastercard/Maestro networks.
     *
     * The authentication value itself is identical (UCAF is Mastercard's name for the AAV); what
     * changes is the field it rides in, so `cavv` is removed rather than duplicated.
     *
     * @param array<string, string> $block
     * @param array<string, mixed> $ca
     * @return array<string, string>
     */
    private function applyUcaf(array $block, array $ca): array
    {
        $authenticationData = $block['cavv'] ?? null;

        unset($block['cavv']);

        if ($authenticationData !== null) {
            $block['ucafAuthenticationData'] = $authenticationData;
        }

        $indicator = $this->ucafCollectionIndicator($ca);

        if ($indicator !== null) {
            $block['ucafCollectionIndicator'] = $indicator;
        }

        return $block;
    }

    /**
     * Derive the UCAF collection indicator: 2 = fully authenticated, 1 = attempted/merchant-only.
     *
     * paresStatus is the primary source (Y => 2, A => 1) because it is the verdict the classifier
     * itself keys on, so the indicator can never disagree with the verdict that let this result
     * through. Mastercard's raw ECI (02 = full authentication, 01 = attempts) is only a fallback for
     * a reply that omitted paresStatus. Neither present => omitted; a value is never invented.
     *
     * @param array<string, mixed> $ca
     * @return string|null
     */
    private function ucafCollectionIndicator(array $ca): ?string
    {
        $paresStatus = strtoupper((string)($this->scalarValue($ca['paresStatus'] ?? null) ?? ''));

        if (isset(self::PARES_UCAF_INDICATOR[$paresStatus])) {
            return self::PARES_UCAF_INDICATOR[$paresStatus];
        }

        $eciRaw = $this->scalarValue($ca['eciRaw'] ?? null) ?? '';

        return self::ECI_UCAF_INDICATOR[$eciRaw] ?? null;
    }

    /**
     * Whether this card network authenticates over UCAF rather than CAVV.
     *
     * @param string $ccType
     * @return bool
     */
    private function isUcafNetwork(string $ccType): bool
    {
        return in_array(strtoupper($ccType), self::UCAF_CARD_TYPES, true);
    }

    /**
     * Normalize a reply value to a non-empty string, or null when absent/empty/non-scalar.
     *
     * @param mixed $value
     * @return string|null
     */
    private function scalarValue(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string)$value);

        return $value !== '' ? $value : null;
    }
}
