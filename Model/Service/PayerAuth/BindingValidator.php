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
 * This class is a PURE GATE. It never writes and never clears: a validator that discarded the record
 * on its way to throwing would make every block one-shot — re-submitting Place Order would find no
 * record, resolve to null, and place the order unauthenticated. That is a complete 3DS bypass, and it
 * is why resolve() is read-only. All state transitions happen in the Persistor, driven by the flows:
 *
 *  - authenticate() / finalize()  => saveResult() REPLACES the result (and sets/discharges obligation)
 *  - setup()                      => saveReferenceId() re-seeds, PRESERVING any obligation
 *  - Payer Auth off / skipped     => Management clears the record outright
 *  - approved place               => Response clears the consumed record (the one-shot)
 *
 * Rules, in order (PA1-IMPLEMENTATION.md, "Shared design contracts"):
 *  1. no record            => null. NOT an error: Payer Auth may be off, or a REST integrator may
 *                             simply not have run it.
 *  2. FAILED               => CommandException. The record STAYS, so a re-submitted place is blocked
 *                             the same way — a failed authentication is never bypassable by retrying.
 *  3. obligation set       => CommandException unless the record now carries a USABLE liability shift
 *                             covering this charge. An abandoned challenge, or a failure followed by a
 *                             fresh setup(), keeps blocking until an authentication actually succeeds.
 *  4. no verdict, no obligation
 *                          => null. A setup-only record has nothing to consume; it will be replaced by
 *                             the next authenticate() or cleared by the post-place one-shot.
 *  5. CHALLENGE            => CommandException. An unfinished step-up must not place. (Rule 3 covers
 *                             this too; the branch is explicit for clarity.)
 *  6. AUTHENTICATED / ATTEMPTED / UNAVAILABLE
 *                          => match rules: currency equal (case-insensitive), binding equal, age
 *                             <= 900s, and the charge amount <= the authenticated amount. Charging
 *                             MORE than was authenticated is the attack (authenticate $1, place $500)
 *                             and demands re-verification; charging LESS is legal — store credit, gift
 *                             cards and partial-payment modules reduce the gateway charge below the
 *                             quote grand total, and EMV practice accepts charge <= authenticated.
 *  7. match failure        => CommandException when the record carried a liability shift (something of
 *                             value would be silently dropped); null on UNAVAILABLE (nothing of value
 *                             was there). Either way the record is left alone.
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
     * The substring of the re-verify refusal that the client's isReverifyFailure() auto-retry keys
     * on (the webapi fault carries no machine-readable code). Drift silently ends that retry, so
     * BindingValidatorTest pins this marker and the exact sentence; the sentence stays an inline
     * literal in reverify() for the i18n phrase collector.
     */
    public const REVERIFY_MARKER = 'verify your payment again';

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
     * Read-only: no branch of this method mutates the persisted record.
     *
     * @param InfoInterface $payment
     * @param string $orderBaseAmount2dp Base amount being charged, 2dp string.
     * @param string $orderCurrency Base currency code being charged.
     * @param string $currentBinding Binding of the instrument being charged (see Persistor).
     * @return array{verdict: Verdict, ca: array<string, mixed>}|null
     * @throws CommandException On a failed, obligated, abandoned, or stale-but-valuable authentication.
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
            $this->refuse('authentication_failed', $verdict);

            throw new CommandException(
                __('Your payment could not be verified. Please re-enter your payment information and try again.')
            );
        }

        if ($verdict === Verdict::CHALLENGE) {
            $this->refuse('challenge_incomplete', $verdict);

            throw $this->reverify();
        }

        $mismatch = $verdict !== null
            ? $this->findMismatch($record, $orderBaseAmount2dp, $orderCurrency, $currentBinding)
            : 'no_verdict';

        // A pending obligation (a prior failure or an abandoned challenge) is only discharged by an
        // authentication that actually succeeded AND covers this charge. Anything less keeps blocking.
        if ($this->obligation($record) !== null
            && ($verdict === null || $verdict->hasLiabilityShift() === false || $mismatch !== null)) {
            $this->refuse('obligation_' . $this->obligation($record), $verdict);

            throw $this->reverify();
        }

        if ($verdict === null) {
            return null;
        }

        if ($mismatch !== null) {
            $this->refuse($mismatch, $verdict);

            if ($verdict->hasLiabilityShift()) {
                throw $this->reverify();
            }

            return null;
        }

        return [
            'verdict' => $verdict,
            'ca' => is_array($record['ca'] ?? null) ? $record['ca'] : [],
        ];
    }

    /**
     * Read the record's outstanding obligation, if any.
     *
     * @param array<string, mixed> $record
     * @return string|null
     */
    private function obligation(array $record): ?string
    {
        $obligation = $record['obligation'] ?? null;

        return in_array($obligation, [Persistor::OBLIGATION_FAILED, Persistor::OBLIGATION_CHALLENGE], true)
            ? (string)$obligation
            : null;
    }

    /**
     * Identify the first reason this record does not cover the charge, if any.
     *
     * The amount rule is DIRECTIONAL: the charge must be <= the authenticated amount. Both sides are
     * canonical 2dp strings, so they are compared as integer cents; anything unparseable fails closed.
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
        $authenticated = is_string($record['amount'] ?? null) ? $this->toCents($record['amount']) : null;
        $charged       = $this->toCents($orderBaseAmount2dp);

        if ($authenticated === null || $charged === null || $charged > $authenticated) {
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
     * Convert a canonical 2dp money string to integer cents, or null when it is not one.
     *
     * Fail-closed by design: an amount this method cannot read is treated as a mismatch, never as a
     * match. Both sides of the comparison are produced by number_format($x, 2, '.', ''), so a value
     * that does not have that shape did not come from the money path.
     *
     * @param string $amount
     * @return int|null
     */
    private function toCents(string $amount): ?int
    {
        if (preg_match('/^-?\d+\.\d{2}$/', $amount) !== 1) {
            return null;
        }

        return (int)str_replace('.', '', $amount);
    }

    /**
     * Build the "verify again" refusal shown for every recoverable block.
     *
     * The wording must keep containing self::REVERIFY_MARKER or the checkout client's one-shot
     * re-verify stops firing.
     *
     * @return CommandException
     */
    private function reverify(): CommandException
    {
        return new CommandException(
            __('Your payment verification is no longer valid. Please verify your payment again.')
        );
    }

    /**
     * Log a refusal, carrying the reason and verdict only — never any value from the record.
     *
     * @param string $reason
     * @param Verdict|null $verdict
     * @return void
     */
    private function refuse(string $reason, ?Verdict $verdict): void
    {
        $this->helper->log(
            Config::CODE,
            'Payer Authentication: refusing payer_auth record, reason=' . $reason
            . ', verdict=' . ($verdict !== null ? $verdict->value : 'none')
        );
    }
}
