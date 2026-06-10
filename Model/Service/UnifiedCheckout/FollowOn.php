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

use Exception;
use Magento\Framework\Exception\RuntimeException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FollowOnRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FollowOnRequestFactory;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use Throwable;

/**
 * Executes Unified Checkout follow-on operations (capture/refund/auth-reversal/TMS delete) over REST.
 *
 * The REST sibling of the SOAP follow-on bodies in Gateway. Each operation references the stored
 * transaction id (the SOAP requestID == REST `id`, per decision D4 — REST and SOAP share the same
 * transaction-id space, so a pre-3.0.0 SOAP-era order captures/refunds via these REST paths using its
 * existing stored id). Bodies follow UC-API-REFERENCE.md §3:
 *   - capture   POST /pts/v2/payments/{id}/captures
 *   - refund    POST /pts/v2/captures/{id}/refunds   (linked) / /pts/v2/payments/{id}/refunds (unlinked)
 *   - reversal  POST /pts/v2/payments/{id}/reversals (auth reversal / void)
 *   - delete    DELETE /tms/v2/payment-instruments/{id} (+ /tms/v2/customers/{id})
 *
 * CODE-SPACE RECONCILIATION (the A1/A2 correctness point): the SOAP path threw exceptions whose code was
 * the SOAP reasonCode, and Gateway::capture()/refund() key their recapture/unlinked-credit retry on
 * 102/242 (capture: txn not found / not valid for follow-on) and 241 (refund: not valid for follow-on).
 * REST replies do NOT carry those codes. FAIL-SAFE rule (A3 review): only a genuinely-not-found / not-
 * re-presentable id condition maps to the 242/241 retry codes — specifically (1) an HTTP 404 against the
 * follow-on path, and (2) a 2xx body whose errorInformation.reason is EXACTLY 'NOT_FOUND'. Everything else
 * is surfaced as-is. In particular, a body that carries a real processorInformation.responseCode (a genuine
 * auth/capture decline) is ALWAYS thrown as a plain decline (CommandException with its own code / 0), NEVER
 * as 242/241 — because the retry codes drive a fresh bundled auth+capture (capture) or an unlinked credit
 * (refund), and mapping a real decline to them would re-charge / re-credit silently. Processor reasons such
 * as PROCESSOR_ERROR / INVALID_REQUEST are NOT not-found conditions (UC-API-REFERENCE.md §4 shows
 * PROCESSOR_ERROR on an APPROVED auth) and are deliberately excluded; matching is by exact reason equality,
 * never substring.
 *
 * VERIFY (UC-API-REFERENCE.md §4/§5): the exact REST error reason/status emitted for an expired or
 * already-consumed follow-on target is NOT live-confirmed (sandbox MID lacks TMS provisioning). The
 * NOT_FOUND reason / 404 status mapping is SDK/reference-derived and must be confirmed against a boarded MID
 * before relying on the auto-recapture path in production.
 *
 * @see \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response  (the auth/sale sibling, A1)
 * @see \ParadoxLabs\CyberSource\Model\Gateway::capture()  (the retry logic this feeds)
 */
class FollowOn
{
    /**
     * Capture path template. {id} = stored auth transaction id.
     */
    public const CAPTURE_PATH = '/pts/v2/payments/%s/captures';

    /**
     * Linked refund path template. {id} = stored capture transaction id.
     */
    public const REFUND_LINKED_PATH = '/pts/v2/captures/%s/refunds';

    /**
     * Unlinked refund path template. {id} = stored payment transaction id.
     */
    public const REFUND_UNLINKED_PATH = '/pts/v2/payments/%s/refunds';

    /**
     * Auth reversal (void) path template. {id} = stored auth transaction id.
     */
    public const REVERSAL_PATH = '/pts/v2/payments/%s/reversals';

    /**
     * Capture void path template. {id} = stored capture transaction id.
     *
     * Used to void an already-settled (captured) transaction before it clears, where reversing the
     * underlying auth id is no longer valid. Confirmed against SDK VoidApi::voidCapture
     * (POST /pts/v2/captures/{id}/voids, VoidCaptureRequest carries clientReferenceInformation only).
     */
    public const CAPTURE_VOID_PATH = '/pts/v2/captures/%s/voids';

    /**
     * TMS payment-instrument delete path template. {id} = stored paymentId.
     */
    public const TMS_PAYMENT_INSTRUMENT_PATH = '/tms/v2/payment-instruments/%s';

    /**
     * TMS customer delete path template. {id} = stored profileId.
     */
    public const TMS_CUSTOMER_PATH = '/tms/v2/customers/%s';

    /**
     * The processor responseCode that maps to a clean approval (mirrors SOAP reasonCode 100).
     */
    public const RESPONSE_CODE_APPROVED = '100';

    /**
     * REST statuses that represent a successful follow-on (capture/refund/reversal accepted).
     */
    public const APPROVED_STATUSES = [
        'AUTHORIZED',
        'PARTIAL_AUTHORIZED',
        'PENDING',
        'TRANSMITTED',
        'VOIDED',
        'REVERSED',
        'COMPLETED',
    ];

    /**
     * SOAP reasonCode equivalent for "capture target not found / not valid for follow-on".
     *
     * Gateway::capture() recapture logic keys on 102/242; we re-throw 242 so it fires.
     */
    public const SOAP_CODE_CAPTURE_NOT_FOLLOWABLE = 242;

    /**
     * SOAP reasonCode equivalent for "refund target not valid for follow-on" (drives unlinked credit).
     */
    public const SOAP_CODE_REFUND_NOT_FOLLOWABLE = 241;

    /**
     * The single errorInformation.reason value that means "follow-on target id is unusable / not found".
     *
     * FAIL-SAFE (A3 review): this is intentionally NARROW. It maps ONLY the genuinely not-found /
     * not-re-presentable id condition to the SOAP 242/241 retry codes. It must NOT include processor
     * decisions such as PROCESSOR_ERROR or INVALID_REQUEST: those are real declines/errors (see
     * UC-API-REFERENCE.md §4 — PROCESSOR_ERROR fires on an APPROVED auth where TMS is unprovisioned, i.e.
     * NOT a not-found condition). Mapping them to 242/241 would trigger a silent re-charge / re-credit.
     * Matched by EXACT reason equality, never substring — an ambiguous condition is surfaced, not retried.
     */
    public const FOLLOWON_NOT_FOUND_REASON = 'NOT_FOUND';

    /**
     * FollowOn constructor.
     *
     * @param Rest $rest
     * @param Config $config
     * @param Sanitizer $sanitizer
     * @param Data $helper
     * @param ResponseFactory $responseFactory
     * @param FollowOnRequestFactory $requestFactory
     */
    public function __construct(
        protected readonly Rest $rest,
        protected readonly Config $config,
        protected readonly Sanitizer $sanitizer,
        protected readonly Data $helper,
        protected readonly ResponseFactory $responseFactory,
        protected readonly FollowOnRequestFactory $requestFactory
    ) {
    }

    /**
     * Capture (settle) $amount against the stored auth transaction id, over REST.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param string $transactionId Stored auth transaction id (SOAP requestID == REST id).
     * @return GatewayResponse
     * @throws CommandException On a decline / follow-on-not-found (carrying the SOAP-equivalent code).
     * @throws RuntimeException On an error/invalid response.
     * @throws Throwable
     */
    public function capture(InfoInterface $payment, float $amount, string $transactionId): GatewayResponse
    {
        $this->scopeFromPayment($payment);

        $request = $this->buildRequest($payment, $amount);
        $path    = sprintf(self::CAPTURE_PATH, rawurlencode($transactionId));

        return $this->send($path, $request, $payment, self::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE);
    }

    /**
     * Refund $amount against the stored capture/payment transaction id, over REST.
     *
     * Uses the linked-refund path (refund off the capture id); if the gateway's auto-fallback retry
     * downgrades to an unlinked credit it re-enters with the unlinked path via refundUnlinked().
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param string $transactionId Stored capture/payment transaction id.
     * @return GatewayResponse
     * @throws CommandException On a decline / follow-on-not-found (carrying the SOAP-equivalent code).
     * @throws RuntimeException On an error/invalid response.
     * @throws Throwable
     */
    public function refund(InfoInterface $payment, float $amount, string $transactionId): GatewayResponse
    {
        $this->scopeFromPayment($payment);

        $request = $this->buildRequest($payment, $amount);
        $path    = sprintf(self::REFUND_LINKED_PATH, rawurlencode($transactionId));

        return $this->send($path, $request, $payment, self::SOAP_CODE_REFUND_NOT_FOLLOWABLE);
    }

    /**
     * Refund $amount as an UNLINKED credit against the stored payment id (the SOAP 241 fallback path).
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param string $transactionId Stored payment transaction id.
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     * @throws Throwable
     */
    public function refundUnlinked(InfoInterface $payment, float $amount, string $transactionId): GatewayResponse
    {
        $this->scopeFromPayment($payment);

        $request = $this->buildRequest($payment, $amount);
        $path    = sprintf(self::REFUND_UNLINKED_PATH, rawurlencode($transactionId));

        return $this->send($path, $request, $payment, self::SOAP_CODE_REFUND_NOT_FOLLOWABLE);
    }

    /**
     * Void (auth reversal) the stored auth transaction id, over REST.
     *
     * This is the UNCAPTURED-auth path only: it reverses the named auth. For an already-settled
     * (captured) order, reversing the auth id is rejected by the processor — Gateway::void() must
     * route that case to voidCapture() instead.
     *
     * @param InfoInterface $payment
     * @param float $amount Amount to reverse (typically the auth/due amount).
     * @param string $transactionId Stored auth transaction id.
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     * @throws Throwable
     */
    public function void(InfoInterface $payment, float $amount, string $transactionId): GatewayResponse
    {
        $this->scopeFromPayment($payment);

        $request = $this->buildRequest($payment, $amount)
            ->setReversal(true);
        $path    = sprintf(self::REVERSAL_PATH, rawurlencode($transactionId));

        return $this->send($path, $request, $payment, self::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE);
    }

    /**
     * Void an already-settled CAPTURE over REST (POST /pts/v2/captures/{id}/voids).
     *
     * Once an order is captured/settled, the auth reversal path is invalid; the capture itself must be
     * voided (before it clears) against the capture id. A capture void is full — no amount is sent
     * (confirmed against SDK VoidApi::voidCapture / VoidCaptureRequest, which carries only
     * clientReferenceInformation).
     *
     * @param InfoInterface $payment
     * @param string $transactionId Stored capture transaction id.
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     * @throws Throwable
     */
    public function voidCapture(InfoInterface $payment, string $transactionId): GatewayResponse
    {
        $this->scopeFromPayment($payment);

        /** @var Payment $payment */
        $order = $payment->getOrder();

        // A capture void is full; send no amount (clientReferenceInformation only).
        /** @var FollowOnRequest $request */
        $request = $this->requestFactory->create();
        $request->setClientReferenceCode((string)$order->getIncrementId())
            ->setSolutionId($this->config->getSolutionId())
            ->setApplicationName($this->config->getClientName())
            ->setApplicationVersion($this->config->getClientVersion());

        $path = sprintf(self::CAPTURE_VOID_PATH, rawurlencode($transactionId));

        return $this->send($path, $request, $payment, self::SOAP_CODE_REFUND_NOT_FOLLOWABLE);
    }

    /**
     * Delete a stored card's TMS token(s): the payment-instrument, and the customer profile when present.
     *
     * Mirrors the SOAP paySubscriptionDelete: the paymentInstrument id (card paymentId) is the primary
     * token to remove. When a customer profile id (card profileId) is also stored we delete that too, so
     * a fully orphaned customer container does not linger in TMS. Customer deletion failure is tolerated
     * (the instrument is the record that matters); a payment-instrument failure propagates.
     *
     * @param string $paymentInstrumentId Card paymentId (TMS paymentInstrument id).
     * @param string|null $customerId Card profileId (TMS customer id), when present.
     * @param int|null $storeId
     * @return GatewayResponse
     * @throws Exception When the payment-instrument delete fails.
     */
    public function deleteCard(
        string $paymentInstrumentId,
        ?string $customerId = null,
        ?int $storeId = null
    ): GatewayResponse {
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $this->rest->delete(sprintf(self::TMS_PAYMENT_INSTRUMENT_PATH, rawurlencode($paymentInstrumentId)));

        if ($customerId !== null && $customerId !== '') {
            try {
                $this->rest->delete(sprintf(self::TMS_CUSTOMER_PATH, rawurlencode($customerId)));
            } catch (Throwable $exception) {
                // The instrument is gone; a lingering empty customer container is non-fatal. Log and move on.
                $this->helper->log(
                    Config::CODE,
                    sprintf('Unified Checkout: TMS customer delete failed (non-fatal): %s', $exception->getMessage())
                );
            }
        }

        /** @var GatewayResponse $response */
        $response = $this->responseFactory->create(['data' => ['is_approved' => true]]);

        return $response;
    }

    /**
     * Assemble a follow-on request DTO (clientReferenceInformation + amountDetails) from the payment.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return FollowOnRequest
     */
    public function buildRequest(InfoInterface $payment, float $amount): FollowOnRequest
    {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        /** @var FollowOnRequest $request */
        $request = $this->requestFactory->create();

        $request->setClientReferenceCode((string)$order->getIncrementId())
            ->setTotalAmount(number_format((float)$this->sanitizer->amount($amount), 2, '.', ''))
            ->setCurrency($this->sanitizer->alpha((string)$order->getBaseCurrencyCode(), 3))
            ->setSolutionId($this->config->getSolutionId())
            ->setApplicationName($this->config->getClientName())
            ->setApplicationVersion($this->config->getClientVersion());

        return $request;
    }

    /**
     * POST the follow-on request and interpret the reply, mapping follow-on-not-found to a SOAP code.
     *
     * @param string $path
     * @param FollowOnRequest $request
     * @param InfoInterface $payment
     * @param int $notFollowableSoapCode SOAP reasonCode to surface when the target is unusable (242/241).
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     * @throws Throwable
     */
    protected function send(
        string $path,
        FollowOnRequest $request,
        InfoInterface $payment,
        int $notFollowableSoapCode
    ): GatewayResponse {
        try {
            $response = $this->rest->post($path, $request->toArray());
        } catch (Exception $exception) {
            // A non-2xx (e.g. 404 for a missing/expired follow-on target) arrives as an Exception whose
            // code is the HTTP status. Translate the "follow-on target unusable" case into the SOAP code
            // the gateway retry logic expects; re-throw everything else as-is.
            throw $this->normalizeFollowOnException($exception, $notFollowableSoapCode);
        }

        return $this->interpretResponse($response, $payment, $notFollowableSoapCode);
    }

    /**
     * Translate a Rest transport Exception into the right gateway exception, preserving retry semantics.
     *
     * @param Exception $exception
     * @param int $notFollowableSoapCode
     * @return Throwable
     */
    protected function normalizeFollowOnException(Exception $exception, int $notFollowableSoapCode): Throwable
    {
        $status = (int)$exception->getCode();

        // FAIL-SAFE: only an HTTP 404 maps to the not-followable SOAP code here. A 404 against a follow-on
        // path means the referenced transaction id is unknown to the processor — the REST analog of SOAP
        // "transaction not found" (102/242), an id/state problem, so the gateway recapture / unlinked-credit
        // fallback may fire. We intentionally do NOT substring-match the raw transport message for reason
        // tokens (it cannot distinguish a genuine processor decline from a not-found id) — anything that is
        // not a clean 404 is surfaced as-is rather than silently re-charged/re-credited.
        if ($status === 404) {
            return new CommandException(
                __('Transaction Failed: %1', __($exception->getMessage())),
                null,
                $notFollowableSoapCode
            );
        }

        return $exception;
    }

    /**
     * Translate a follow-on JSON reply into a gateway Response, mirroring Response::interpretResponse().
     *
     * @param array<string, mixed> $response
     * @param InfoInterface|null $payment
     * @param int $notFollowableSoapCode
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     */
    public function interpretResponse(
        array $response,
        ?InfoInterface $payment,
        int $notFollowableSoapCode
    ): GatewayResponse {
        $status       = (string)($response['status'] ?? '');
        $responseCode = (string)($response['processorInformation']['responseCode'] ?? '');
        $errorReason  = (string)($response['errorInformation']['reason'] ?? '');
        $errorMessage = (string)($response['errorInformation']['message'] ?? '');

        $isApproved = $responseCode === self::RESPONSE_CODE_APPROVED
            || ($responseCode === '' && in_array($status, self::APPROVED_STATUSES, true));

        $data = $response;
        $data['transaction_id']       = $response['id'] ?? null;
        $data['response_code']        = $responseCode !== '' ? $responseCode : $status;
        $data['response_reason_code'] = $responseCode !== '' ? $responseCode : $status;
        $data['response_reason_text'] = $errorMessage !== '' ? $errorMessage : $status;

        /** @var GatewayResponse $gatewayResponse */
        $gatewayResponse = $this->responseFactory->create(['data' => $data]);

        if ($isApproved) {
            $gatewayResponse->setIsError(false);

            return $gatewayResponse;
        }

        $gatewayResponse->setIsError(true);

        $reason  = $errorMessage !== '' ? $errorMessage : $status;
        $message = __('Transaction Failed: %1', __($reason));

        if ($payment !== null) {
            $this->helper->log(Config::CODE, sprintf('%s (%s)', (string)$message, $status));
        }

        $code = ctype_digit($responseCode) ? (int)$responseCode : 0;

        // FAIL-SAFE: a real processor decision (the body carries a processorInformation.responseCode) is
        // ALWAYS surfaced as a plain decline, NEVER mapped to the 242/241 retry codes. The retry codes are
        // reserved for "the follow-on target id is unusable", which is an id/state problem — not a
        // processor decision. Mapping a genuine decline to 242/241 would re-charge / re-credit silently.
        if ($responseCode !== '') {
            throw new CommandException($message, null, $code);
        }

        // No processor responseCode: an id/state-level failure. Map ONLY the exact NOT_FOUND reason (the
        // 2xx-but-unusable analog of a 404) onto the SOAP code so the gateway recapture / unlinked-credit
        // fallback fires. Exact equality, never substring; anything ambiguous is surfaced, not retried.
        if ($errorReason === self::FOLLOWON_NOT_FOUND_REASON) {
            throw new CommandException($message, null, $notFollowableSoapCode);
        }

        if ($status === 'DECLINED') {
            throw new CommandException($message, null, $code);
        }

        throw new RuntimeException($message, null, $code);
    }

    /**
     * Set config/rest scope from the payment's order store.
     *
     * @param InfoInterface $payment
     * @return void
     */
    protected function scopeFromPayment(InfoInterface $payment): void
    {
        /** @var Payment $payment */
        $order   = $payment->getOrder();
        $storeId = $order->getStoreId() !== null ? (int)$order->getStoreId() : null;

        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);
    }
}
