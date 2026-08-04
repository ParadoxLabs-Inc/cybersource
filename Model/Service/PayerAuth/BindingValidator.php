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

use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;

/**
 * Decides whether a persisted Payer Authentication result may be used for THIS charge.
 *
 * The record is only worth anything if it authenticated the same money, on the same instrument,
 * recently. Every failure mode discards the record, so a customer is never permanently blocked by
 * a stale one — they simply have to re-verify.
 *
 * Rules (PA1-IMPLEMENTATION.md, "Shared design contracts"):
 *  - no record            => null. NOT an error: Payer Auth may be off, or a REST integrator may
 *                            simply not have run it.
 *  - FAILED               => discard + CommandException. A failed authentication must never be
 *                            bypassable by re-submitting place.
 *  - CHALLENGE            => the customer abandoned the step-up. Discard + CommandException: an
 *                            unfinished challenge must not place, and must not block permanently.
 *  - amount / currency / binding mismatch, or age > 900s
 *                         => discard. If the record carried a liability shift, CommandException
 *                            (something of value was lost, and placing now would silently drop the
 *                            shift); if it was UNAVAILABLE, return null — nothing was lost.
 *  - usable AUTHENTICATED / ATTEMPTED / UNAVAILABLE
 *                         => return the verdict + `ca`. The record is NOT cleared here: the
 *                            one-shot clear happens after a successful place (T7).
 *
 * @see \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor
 */
class BindingValidator
{
    /**
     * Maximum age of a usable authentication result, in seconds.
     */
    public const MAX_AGE_SECONDS = 900;

    /**
     * BindingValidator constructor.
     *
     * @param Persistor $persistor
     * @param Data $helper
     */
    public function __construct(
        private readonly Persistor $persistor,
        private readonly Data $helper
    ) {
    }

    /**
     * Resolve the usable authentication result for the charge about to be made, if any.
     *
     * @param InfoInterface $payment
     * @param string $orderBaseAmount2dp Base grand total being charged, 2dp string.
     * @param string $orderCurrency Base currency code being charged.
     * @param string $currentBinding Binding of the instrument being charged (see Persistor).
     * @return array{verdict: Verdict, ca: array<string, mixed>}|null
     * @throws CommandException On a failed, abandoned, or stale-but-valuable authentication.
     */
    public function resolve(
        InfoInterface $payment,
        string $orderBaseAmount2dp,
        string $orderCurrency,
        string $currentBinding,
    ): ?array {
        $record = $this->persistor->load($payment);

        if ($record === null) {
            return null;
        }

        $verdict = is_string($record['verdict'] ?? null) ? Verdict::tryFrom($record['verdict']) : null;

        if ($verdict === Verdict::FAILED) {
            $this->discard($payment, 'authentication_failed', $verdict);

            throw new CommandException(
                __('Your payment could not be verified. Please re-enter your payment information and try again.')
            );
        }

        // A setup-only (or unrecognized) record has no verdict to consume: drop it, place unauthenticated.
        if ($verdict === null) {
            $this->discard($payment, 'no_verdict', null);

            return null;
        }

        $mismatch = $this->findMismatch($record, $orderBaseAmount2dp, $orderCurrency, $currentBinding);

        if ($verdict === Verdict::CHALLENGE || $mismatch !== null) {
            $this->discard($payment, $mismatch ?? 'challenge_incomplete', $verdict);

            if ($verdict === Verdict::CHALLENGE || $verdict->hasLiabilityShift()) {
                throw new CommandException(
                    __('Your payment verification is no longer valid. Please verify your payment again.')
                );
            }

            return null;
        }

        return [
            'verdict' => $verdict,
            'ca' => is_array($record['ca'] ?? null) ? $record['ca'] : [],
        ];
    }

    /**
     * Identify the first reason this record does not cover the charge, if any.
     *
     * Amount is compared as an exact 2dp string: the caller normalizes both sides, and any drift at
     * all (up or down) invalidates the authentication.
     *
     * @param array<string, mixed> $record
     * @param string $orderBaseAmount2dp
     * @param string $orderCurrency
     * @param string $currentBinding
     * @return string|null Mismatch reason, or null when the record covers the charge.
     */
    private function findMismatch(
        array $record,
        string $orderBaseAmount2dp,
        string $orderCurrency,
        string $currentBinding,
    ): ?string {
        if (!is_string($record['amount'] ?? null) || $record['amount'] !== $orderBaseAmount2dp) {
            return 'amount_mismatch';
        }

        $currency = is_string($record['currency'] ?? null) ? $record['currency'] : '';

        if (strtoupper($currency) !== strtoupper($orderCurrency) || $currency === '') {
            return 'currency_mismatch';
        }

        if (!is_string($record['binding'] ?? null)
            || $record['binding'] === ''
            || $record['binding'] !== $currentBinding) {
            return 'binding_mismatch';
        }

        $createdAt = isset($record['created_at']) && is_numeric($record['created_at'])
            ? (int)$record['created_at']
            : 0;

        if ($createdAt <= 0 || (time() - $createdAt) > self::MAX_AGE_SECONDS) {
            return 'expired';
        }

        return null;
    }

    /**
     * Discard an unusable record, logging the reason without any of its values.
     *
     * @param InfoInterface $payment
     * @param string $reason
     * @param Verdict|null $verdict
     * @return void
     */
    private function discard(InfoInterface $payment, string $reason, ?Verdict $verdict): void
    {
        $this->helper->log(
            Config::CODE,
            'Payer Authentication: discarding payer_auth record, reason=' . $reason
            . ', verdict=' . ($verdict !== null ? $verdict->value : 'none')
        );

        $this->persistor->clear($payment);
    }
}
