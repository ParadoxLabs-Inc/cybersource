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

use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;

/**
 * Maps the Unified Checkout TMS token result onto a TokenBase vault CardInterface (decision D5).
 *
 * The auth-execution service ({@see Response}) returns a gateway Response that, when TOKEN_CREATE
 * succeeded, carries token_information (the TMS ids) and card_information (cc_* metadata). This
 * mapper applies the D5 vault mapping, mirroring the field writes of the Secure Acceptance card-save
 * path so existing stored cards remain format-compatible (decision D7 continuity):
 *
 *   paymentId                       <- TMS paymentInstrument id (the MIT key; preserves payment_id semantics)
 *   additional[instrument_identifier] <- TMS instrumentIdentifier id (card fingerprint)
 *   additional[cc_type|cc_last4|cc_bin|cc_exp_month|cc_exp_year] <- card metadata
 *
 * Token-less reality (see UC-API-REFERENCE.md §4): a successful auth can carry NO TMS ids when the
 * transaction is held for Decision Manager review (AUTHORIZED_PENDING_REVIEW) or when TMS is not yet
 * provisioned (TOKEN_CREATE forbidden). In that case the gateway Response has uc_token_missing=true and
 * no token_information. We MUST NOT write empty/null ids over the card (that would corrupt the vault and
 * break MIT read-back in A4). We leave the card un-tokenized, flag the condition on the card so it can be
 * reconciled later, log it at info level, and do not throw.
 *
 * @see \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response::interpretResponse()
 */
class CardBuilder
{
    use StringNormalizationTrait;

    /**
     * Card data flag set when a Unified Checkout auth approved but returned no TMS token.
     *
     * Surfaced so an un-tokenized card can be identified and reconciled later (no MIT key was minted).
     */
    public const CARD_FLAG_TOKEN_MISSING = 'uc_token_missing';

    /**
     * CardBuilder constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        protected readonly Data $helper
    ) {
    }

    /**
     * Apply the D5 TMS-token mapping from a UC gateway Response onto the given vault card.
     *
     * Card metadata (cc_*) is written whenever present, independent of tokenization. The TMS ids are
     * written only when token_information is present and uc_token_missing is not set — never empty ids.
     *
     * @param CardInterface $card
     * @param GatewayResponse $response
     * @return CardInterface
     */
    public function applyTokenToCard(CardInterface $card, GatewayResponse $response): CardInterface
    {
        // Card metadata is safe to write regardless of tokenization outcome.
        $this->applyCardMetadata($card, (array)$response->getData('card_information'));

        $tokenInformation = $response->getData('token_information');
        $tokenMissing     = (bool)$response->getData('uc_token_missing');

        if ($tokenMissing === true || !is_array($tokenInformation) || $tokenInformation === []) {
            // No TMS ids returned. Do NOT overwrite ids with empties. Flag for later reconciliation.
            // The flag lives in `additional` (persisted, interface-typed) so it survives the card save.
            $card->setAdditional(self::CARD_FLAG_TOKEN_MISSING, '1');

            $this->helper->log(
                Config::CODE,
                'Unified Checkout: approved auth returned no TMS token; card left un-tokenized'
                . ' (uc_token_missing). Card will need reconciliation before reuse.'
            );

            return $card;
        }

        $this->applyTokenIds($card, $tokenInformation);

        // Clear any stale flag from a prior token-less attempt now that we have real ids.
        $this->clearTokenMissingFlag($card);

        return $card;
    }

    /**
     * Remove the uc_token_missing flag from the card's additional data, for real.
     *
     * Card::setAdditional($key, null) is a NO-OP for a string key (the real setter only writes when
     * $value !== null, replaces wholesale when $key is an array, or merges a CardAdditionalInterface).
     * To actually DELETE the key we read the full additional array, unset the flag, and pass the array
     * back — the array form sets `$this->additional = $key` (full replace), which drops the key.
     *
     * @see \ParadoxLabs\TokenBase\Model\Card::setAdditional()
     * @param CardInterface $card
     * @return void
     */
    protected function clearTokenMissingFlag(CardInterface $card): void
    {
        $additional = $card->getAdditional();
        if (!is_array($additional) || !array_key_exists(self::CARD_FLAG_TOKEN_MISSING, $additional)) {
            return;
        }

        unset($additional[self::CARD_FLAG_TOKEN_MISSING]);

        // Array form => full replace of additional (Card::setAdditional), so the flag is dropped.
        $card->setAdditional($additional);
    }

    /**
     * Write the TMS ids onto the card per the D5 mapping, skipping any individual id that is absent.
     *
     * No profileId: cards are standalone TMS payment instruments — no customer token is requested or
     * stored (see Response::ACTION_TOKEN_TYPES).
     *
     * @param CardInterface $card
     * @param array<string, mixed> $tokenInformation
     * @return void
     */
    protected function applyTokenIds(CardInterface $card, array $tokenInformation): void
    {
        $paymentInstrumentId    = $this->stringOrNull($tokenInformation['paymentInstrument'] ?? null);
        $instrumentIdentifierId = $this->stringOrNull($tokenInformation['instrumentIdentifier'] ?? null);

        // paymentId <- TMS paymentInstrument (the MIT key).
        if ($paymentInstrumentId !== null) {
            $card->setPaymentId($paymentInstrumentId);
        }

        // As in the legacy Secure Acceptance handling: instrument_identifier doubles as the non-reversible fingerprint.
        if ($instrumentIdentifierId !== null) {
            $card->setAdditional('instrument_identifier', $instrumentIdentifierId);
            $card->setAdditional('fingerprint', $instrumentIdentifierId);
        }
    }

    /**
     * Write the cc_* card metadata onto the card, mirroring the legacy Secure Acceptance field writes.
     *
     * @param CardInterface $card
     * @param array<string, mixed> $cardInformation
     * @return void
     */
    protected function applyCardMetadata(CardInterface $card, array $cardInformation): void
    {
        if ($cardInformation === []) {
            return;
        }

        $type      = $this->stringOrNull($cardInformation['cc_type'] ?? null);
        $last4     = $this->stringOrNull($cardInformation['cc_last4'] ?? null);
        $bin       = $this->stringOrNull($cardInformation['cc_bin'] ?? null);
        $expMonth  = $this->stringOrNull($cardInformation['cc_exp_month'] ?? null);
        $expYear   = $this->stringOrNull($cardInformation['cc_exp_year'] ?? null);

        if ($type !== null) {
            $card->setAdditional('cc_type', $type);
        }

        if ($last4 !== null) {
            $card->setAdditional('cc_last4', $last4);
        }

        if ($bin !== null) {
            $card->setAdditional('cc_bin', $bin);
        }

        if ($expMonth !== null) {
            $card->setAdditional('cc_exp_month', $expMonth);
        }

        if ($expYear !== null) {
            $card->setAdditional('cc_exp_year', $expYear);
        }

        // Maintain the stored-card expiry the rest of TokenBase reads (mirrors SA setExpires()).
        if ($expYear !== null && $expMonth !== null) {
            $day = date('t', (int)strtotime($expYear . '-' . $expMonth));
            $card->setExpires(sprintf('%s-%s-%s 23:59:59', $expYear, $expMonth, $day));
        }
    }
}
