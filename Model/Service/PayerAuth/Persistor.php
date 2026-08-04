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

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Payment as QuotePayment;
use Magento\Quote\Model\ResourceModel\Quote\Payment as QuotePaymentResource;
use Magento\Sales\Model\Order\Payment as OrderPayment;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;

/**
 * Server-side custody of the Payer Authentication result between authenticate and place.
 *
 * The record lives in quote-payment `additional_information['payer_auth']` as a JSON string, with
 * the shape (all keys always present):
 *
 *   reference_id        ?string  the authentication-setups referenceId (DDC correlation)
 *   auth_transaction_id ?string  consumerAuthenticationInformation.authenticationTransactionId
 *   verdict             ?string  Verdict::value; null while only setup has run
 *   ca                  array    full normalized consumerAuthenticationInformation (CAVV lives here)
 *   amount              ?string  authenticated base amount, 2dp string
 *   currency            ?string  authenticated base currency
 *   binding             ?string  transient-token `jti` (new card) or 'card:' . tokenbase_id (stored)
 *   transient_token     ?string  new-card path only: the transient token the authentication re-sends
 *                                (masked-PAN reference, never a PAN; cleared with the record)
 *   created_at          int      unix timestamp of this record's creation
 *
 * The record is authorization-bearing (it carries the liability shift), so it is deliberately bound
 * to ONE quote payment and never mirrored anywhere with a wider scope: a copy readable from a
 * different quote would be exactly the cross-quote replay the BindingValidator rules exist to stop.
 * Any binding change, or a fresh saveReferenceId(), REPLACES the whole record — a new setup
 * invalidates whatever authentication preceded it.
 *
 * `ca` holds the CAVV/XID and must NEVER be logged or handed to a client DTO; the lifecycle logging
 * here emits the verdict and a masked binding only.
 *
 * Persistence mechanics are adapted from the 3.x `CardinalCruise\Persistor`: the quote payment is
 * written through its RESOURCE directly rather than via the cart repository, because the checkout
 * place-order flow runs inside a DB transaction that rolls back the whole quote on failure. See
 * saveRecord() for the constraint that carries over from master.
 *
 * @see \ParadoxLabs\CyberSource\Model\Service\PayerAuth\BindingValidator
 */
class Persistor
{
    /**
     * Payment additional_information key holding the JSON record.
     */
    public const PERSIST_KEY = 'payer_auth';

    /**
     * Binding prefix identifying a stored (vaulted) card by its tokenbase id.
     */
    public const BINDING_CARD_PREFIX = 'card:';

    /**
     * Persistor constructor.
     *
     * @param QuotePaymentResource $paymentResource
     * @param CartRepositoryInterface $cartRepository
     * @param Json $json
     * @param Data $helper
     */
    public function __construct(
        private readonly QuotePaymentResource $paymentResource,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly Json $json,
        private readonly Data $helper
    ) {
    }

    /**
     * Build the binding value for a stored (vaulted) card.
     *
     * @param int|string $tokenbaseId
     * @return string
     */
    public function cardBinding(int|string $tokenbaseId): string
    {
        return self::BINDING_CARD_PREFIX . $tokenbaseId;
    }

    /**
     * Seed (or replace) the record at setup time with the authentication-setups referenceId.
     *
     * Always writes a NEW record: running setup again means a new authentication attempt, so any
     * prior verdict must not survive it.
     *
     * @param InfoInterface $payment
     * @param string $referenceId
     * @param string $binding Transient-token `jti`, or self::cardBinding() for a stored card.
     * @param string|null $transientToken New-card path only: the token the authentication must
     *                                    re-send, since the PAN never reaches the server and an
     *                                    uncharged card has no payment-instrument id yet.
     * @return void
     */
    public function saveReferenceId(
        InfoInterface $payment,
        string $referenceId,
        string $binding,
        ?string $transientToken = null
    ): void {
        $this->saveRecord(
            $payment,
            [
                'reference_id' => $referenceId,
                'auth_transaction_id' => null,
                'verdict' => null,
                'ca' => [],
                'amount' => null,
                'currency' => null,
                'binding' => $binding,
                'transient_token' => $transientToken,
                'created_at' => $this->now(),
            ]
        );

        $this->helper->log(
            Config::CODE,
            'Payer Authentication: payer_auth record seeded at setup, binding=' . $this->maskBinding($binding)
        );
    }

    /**
     * Store an authentication (or finalized challenge) outcome against the payment.
     *
     * Preserves the setup referenceId when the binding is unchanged; a different binding replaces
     * the record outright (the prior setup belonged to a different instrument).
     *
     * @param InfoInterface $payment
     * @param AuthenticationResult $result
     * @param string $amount Authenticated base amount, 2dp string.
     * @param string $currency Authenticated base currency code.
     * @param string $binding Transient-token `jti`, or self::cardBinding() for a stored card.
     * @return void
     */
    public function saveResult(
        InfoInterface $payment,
        AuthenticationResult $result,
        string $amount,
        string $currency,
        string $binding,
    ): void {
        $existing = $this->load($payment);
        $referenceId = null;
        $transientToken = null;

        if ($existing !== null && isset($existing['binding']) && $existing['binding'] === $binding) {
            $referenceId = isset($existing['reference_id']) && $existing['reference_id'] !== null
                ? (string)$existing['reference_id']
                : null;
            $transientToken = isset($existing['transient_token']) && $existing['transient_token'] !== null
                ? (string)$existing['transient_token']
                : null;
        }

        $verdict = $result->getVerdict();

        $this->saveRecord(
            $payment,
            [
                'reference_id' => $referenceId,
                'auth_transaction_id' => $result->authenticationTransactionId(),
                'verdict' => $verdict->value,
                'ca' => $result->getConsumerAuthenticationInformation(),
                'amount' => $amount,
                'currency' => $currency,
                'binding' => $binding,
                'transient_token' => $transientToken,
                'created_at' => $this->now(),
            ]
        );

        $this->helper->log(
            Config::CODE,
            'Payer Authentication: payer_auth record saved, verdict=' . $verdict->value
            . ', binding=' . $this->maskBinding($binding)
        );
    }

    /**
     * Read the persisted record, if any.
     *
     * @param InfoInterface $payment
     * @return array<string, mixed>|null
     */
    public function load(InfoInterface $payment): ?array
    {
        $raw = $payment->getAdditionalInformation(self::PERSIST_KEY);

        if (is_array($raw)) {
            return $raw;
        }

        if (!is_string($raw) || $raw === '') {
            return null;
        }

        try {
            $decoded = $this->json->unserialize($raw);
        } catch (\Throwable $exception) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Drop the record (one-shot consumption after a successful place, or a validation discard).
     *
     * @param InfoInterface $payment
     * @return void
     */
    public function clear(InfoInterface $payment): void
    {
        if ($this->load($payment) === null) {
            return;
        }

        $this->saveRecord($payment, null);

        $this->helper->log(Config::CODE, 'Payer Authentication: payer_auth record cleared');
    }

    /**
     * Write (or remove, on null) the record and persist it rollback-safely.
     *
     * The record is applied to the payment object handed in — so the caller's in-memory state is
     * always correct — and to the quote payment behind it, which is saved through its resource
     * model directly. Direct resource save is what master used: the cart repository would re-save
     * the entire quote (recollecting totals mid-checkout), and normal quote saves made during
     * place-order are wiped by the checkout DB transaction rollback.
     *
     * Constraint carried over from master: a write made INSIDE the place-order transaction shares
     * its connection and is therefore still subject to that rollback. Master papered over this with
     * a checkout-session copy; PA-1 deliberately does not (see the class docblock: a session-scoped
     * copy is readable from another quote in the same session, which is a replay hole). It does not
     * need to — every mutation in the PA-1 flow runs in its own setup/authenticate/finalize request,
     * outside any order transaction. The one exception is T7's post-place one-shot clear, which is
     * safe in the same direction: if the place rolls back, the record correctly survives for retry.
     *
     * When no quote payment can be resolved (e.g. a detached order payment whose quote is gone) the
     * change stays in memory only.
     *
     * @param InfoInterface $payment
     * @param array<string, mixed>|null $record
     * @return void
     */
    private function saveRecord(InfoInterface $payment, ?array $record): void
    {
        $this->apply($payment, $record);

        $quotePayment = $this->resolveQuotePayment($payment);

        if ($quotePayment === null) {
            return;
        }

        if ($quotePayment !== $payment) {
            $this->apply($quotePayment, $record);
        }

        if ((int)$quotePayment->getId() > 0) {
            $this->paymentResource->save($quotePayment);
        }
    }

    /**
     * Set or unset the serialized record on a payment object.
     *
     * @param InfoInterface $payment
     * @param array<string, mixed>|null $record
     * @return void
     */
    private function apply(InfoInterface $payment, ?array $record): void
    {
        if ($record === null) {
            $payment->unsAdditionalInformation(self::PERSIST_KEY);

            return;
        }

        $payment->setAdditionalInformation(self::PERSIST_KEY, $this->json->serialize($record));
    }

    /**
     * Resolve the quote payment the record belongs to, if there is one.
     *
     * @param InfoInterface $payment
     * @return QuotePayment|null
     */
    private function resolveQuotePayment(InfoInterface $payment): ?QuotePayment
    {
        if ($payment instanceof QuotePayment) {
            return $payment;
        }

        if (!$payment instanceof OrderPayment) {
            return null;
        }

        $order = $payment->getOrder();
        $quoteId = $order !== null ? (int)$order->getQuoteId() : 0;

        if ($quoteId <= 0) {
            return null;
        }

        try {
            $quotePayment = $this->cartRepository->get($quoteId)->getPayment();
        } catch (\Throwable $exception) {
            return null;
        }

        return $quotePayment instanceof QuotePayment ? $quotePayment : null;
    }

    /**
     * Current unix timestamp.
     *
     * Plain time() is deliberate: created_at only drives a 15-minute freshness tolerance, it is not
     * a security nonce and does not need a monotonic or injectable clock.
     *
     * @return int
     */
    private function now(): int
    {
        return time();
    }

    /**
     * Mask a binding for logging (a transient-token jti identifies a card entry attempt).
     *
     * @param string|null $binding
     * @return string
     */
    private function maskBinding(?string $binding): string
    {
        if ($binding === null || $binding === '') {
            return 'none';
        }

        if (str_starts_with($binding, self::BINDING_CARD_PREFIX)) {
            return $binding;
        }

        return strlen($binding) > 8
            ? substr($binding, 0, 4) . '***' . substr($binding, -4)
            : '***';
    }
}
