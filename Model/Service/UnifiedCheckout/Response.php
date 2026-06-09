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

use Magento\Framework\Exception\RuntimeException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequestFactory;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use Throwable;

/**
 * Executes a Unified Checkout auth/sale via REST POST /pts/v2/payments and interprets the response.
 *
 * Consumes the transient-token JWT captured client-side (payment additional_data.transient_token),
 * runs the payment with actionList:[TOKEN_CREATE] to mint the TMS vault ids inline, and translates
 * the JSON reply into the gateway Response object the rest of the module already consumes
 * (mirroring Gateway::interpretTransaction()). Card-saving to the vault is A2; gateway wiring is A3.
 *
 * Two confirmed spike findings shape the parsing (see UC-API-REFERENCE.md §4):
 *  1. Decision Manager review (AUTHORIZED_PENDING_REVIEW) suppresses tokenInformation entirely — a
 *     successful auth can legitimately carry NO TMS ids. We treat that as success and flag it.
 *  2. TOKEN_CREATE can fail ("Requested service is forbidden" / PROCESSOR_ERROR) while the auth itself
 *     approves. We must not fail the auth for a token-service error — proceed token-less.
 *
 * @see UC-API-REFERENCE.md §2, §3, §4
 * @see \ParadoxLabs\CyberSource\Model\Gateway::interpretTransaction()
 */
class Response
{
    /**
     * Payment REST endpoint path.
     */
    public const PAYMENTS_PATH = '/pts/v2/payments';

    /**
     * processingInformation.actionList value that mints the TMS vault ids inline.
     */
    public const ACTION_TOKEN_CREATE = 'TOKEN_CREATE';

    /**
     * processingInformation.actionTokenTypes — the three TMS ids for the D5 vault mapping.
     */
    public const ACTION_TOKEN_TYPES = ['customer', 'paymentInstrument', 'instrumentIdentifier'];

    /**
     * CyberSource payment status values that represent an approved (or accepted-pending) auth.
     *
     * AUTHORIZED_PENDING_REVIEW is a Decision Manager hold — the auth is good but held for review,
     * and (per the spike) carries NO tokenInformation.
     */
    public const APPROVED_STATUSES = [
        'AUTHORIZED',
        'AUTHORIZED_PENDING_REVIEW',
        'PARTIAL_AUTHORIZED',
        'PENDING',
        'PENDING_AUTHENTICATION',
        'PENDING_REVIEW',
    ];

    /**
     * The processor responseCode that maps to a clean approval (mirrors SOAP reasonCode 100).
     */
    public const RESPONSE_CODE_APPROVED = '100';

    /**
     * errorInformation.reason value emitted when a sub-service (e.g. TOKEN_CREATE) is not provisioned.
     */
    public const REASON_PROCESSOR_ERROR = 'PROCESSOR_ERROR';

    /**
     * Response constructor.
     *
     * @param Rest $rest
     * @param Config $config
     * @param Sanitizer $sanitizer
     * @param Data $helper
     * @param CardType $cardType
     * @param ResponseFactory $responseFactory
     * @param PaymentRequestFactory $requestFactory
     */
    public function __construct(
        protected readonly Rest $rest,
        protected readonly Config $config,
        protected readonly Sanitizer $sanitizer,
        protected readonly Data $helper,
        protected readonly CardType $cardType,
        protected readonly ResponseFactory $responseFactory,
        protected readonly PaymentRequestFactory $requestFactory
    ) {
    }

    /**
     * Run a Unified Checkout auth/sale for the given payment and amount, and interpret the reply.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return GatewayResponse
     * @throws CommandException On a declined transaction (mirrors the SA/SOAP decline path).
     * @throws RuntimeException On an error/invalid response, or a missing transient token.
     * @throws Throwable
     */
    public function place(InfoInterface $payment, float $amount): GatewayResponse
    {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order   = $payment->getOrder();
        $storeId = $order->getStoreId() !== null ? (int)$order->getStoreId() : null;

        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $request  = $this->buildRequest($payment, $amount);
        $response = $this->rest->post(self::PAYMENTS_PATH, $request->toArray());

        return $this->interpretResponse($response, $payment);
    }

    /**
     * Assemble the /pts/v2/payments request DTO from the order/payment and the re-validated amount.
     *
     * The capture flag is derived from the SERVER-SIDE payment_action (never anything client-supplied):
     * payment_action=authorize_capture -> capture=true (sale); otherwise capture=false (authorize-only).
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @return PaymentRequest
     * @throws RuntimeException When no transient token is present on the payment.
     */
    public function buildRequest(InfoInterface $payment, float $amount): PaymentRequest
    {
        /** @var Payment $payment */
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $transientToken = $this->getTransientToken($payment);
        if ($transientToken === null || $transientToken === '') {
            throw new RuntimeException(
                __('Missing Unified Checkout payment token. Please re-enter your payment information.')
            );
        }

        /** @var PaymentRequest $request */
        $request = $this->requestFactory->create();

        $request->setTransientTokenJwt($transientToken)
            ->setClientReferenceCode((string)$order->getIncrementId())
            ->setActionList([self::ACTION_TOKEN_CREATE])
            ->setActionTokenTypes(self::ACTION_TOKEN_TYPES)
            ->setCapture($this->isCapture((int)$order->getStoreId()))
            ->setTotalAmount(number_format((float)$this->sanitizer->amount($amount), 2, '.', ''))
            ->setCurrency($this->sanitizer->alpha((string)$order->getBaseCurrencyCode(), 3))
            ->setBillTo($this->getBillTo($order));

        return $request;
    }

    /**
     * Translate the /pts/v2/payments JSON reply into a gateway Response object.
     *
     * Mirrors Gateway::interpretTransaction(): sets transaction_id / response_code /
     * response_reason_code / response_reason_text / auth_code, plus the ccAuthReply.* keys that
     * Method::storeTransactionStatuses() reads for AVS/CVV/approval. Throws CommandException on a
     * decline and RuntimeException on an error, so Method handles it identically to the SOAP path.
     *
     * @param array<string, mixed> $response
     * @param InfoInterface|null $payment
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     */
    public function interpretResponse(array $response, ?InfoInterface $payment = null): GatewayResponse
    {
        $status            = (string)($response['status'] ?? '');
        $processorResponse = (string)($response['processorInformation']['approvalCode'] ?? '');
        $responseCode      = (string)($response['processorInformation']['responseCode'] ?? '');
        $errorReason       = (string)($response['errorInformation']['reason'] ?? '');
        $errorMessage      = (string)($response['errorInformation']['message'] ?? '');

        $isApproved   = in_array($status, self::APPROVED_STATUSES, true);
        $isUnderReview = $this->isUnderReview($status);

        // Flatten the raw reply so Method::storeTransactionStatuses() can read ccAuthReply.* keys, and
        // expose the same processorInformation tree for downstream (A2) consumption.
        $data = $response;

        $data['transaction_id']       = $response['id'] ?? null;
        $data['response_code']        = $responseCode !== '' ? $responseCode : $status;
        $data['response_reason_code'] = $responseCode !== '' ? $responseCode : $status;
        $data['response_reason_text'] = $errorMessage !== '' ? $errorMessage : $status;
        $data['auth_code']            = $processorResponse;

        // Map the processorInformation tree to the SOAP-style ccAuthReply.* keys the module reads.
        // Method::storeTransactionStatuses() reads these via $response->getData('ccAuthReply.avsCode'),
        // i.e. literal dotted keys (not nested), so store them flat to match that contract.
        $avsCode = $response['processorInformation']['avs']['code'] ?? null;
        $cvCode  = $response['processorInformation']['cardVerification']['resultCode'] ?? null;

        if ($processorResponse !== '') {
            $data['ccAuthReply.authorizationCode'] = $processorResponse;
        }
        if ($avsCode !== null && $avsCode !== '') {
            $data['ccAuthReply.avsCode'] = $avsCode;
        }
        if ($cvCode !== null && $cvCode !== '') {
            $data['ccAuthReply.cvCode'] = $cvCode;
        }

        $this->extractTokenInformation($response, $data, $isApproved);
        $this->extractCardMetadata($response, $data);

        /** @var GatewayResponse $gatewayResponse */
        $gatewayResponse = $this->responseFactory->create(['data' => $data]);

        // A successful auth that CyberSource held for Decision Manager review is still approved.
        $gatewayResponse->setIsFraud($isUnderReview);

        if ($isApproved) {
            $gatewayResponse->setIsError(false);

            // TOKEN_CREATE may fail ("Requested service is forbidden") while the auth approves; the
            // auth stands and we proceed token-less (spike isolation finding).
            if ($errorReason === self::REASON_PROCESSOR_ERROR && empty($data['token_information'])) {
                $this->helper->log(
                    Config::CODE,
                    sprintf(
                        'Unified Checkout auth approved (%s) but TOKEN_CREATE failed: %s. Proceeding token-less.',
                        $status,
                        $errorMessage
                    )
                );
                $gatewayResponse->setData('uc_token_missing', true);
            }

            return $gatewayResponse;
        }

        return $this->throwForFailure($gatewayResponse, $status, $responseCode, $errorMessage, $payment);
    }

    /**
     * Whether the configured payment_action is a sale (auth+capture) rather than authorize-only.
     *
     * Sourced strictly from server-side config — never from client input.
     *
     * @param int $storeId
     * @return bool
     */
    protected function isCapture(int $storeId): bool
    {
        return $this->config->getUcCompleteMandateType($storeId) === 'CAPTURE';
    }

    /**
     * Whether the given status is a Decision Manager / pending-review hold (no token returned inline).
     *
     * @param string $status
     * @return bool
     */
    protected function isUnderReview(string $status): bool
    {
        return str_contains($status, 'REVIEW') || str_contains($status, 'PENDING');
    }

    /**
     * Read the transient-token JWT from the payment's additional information.
     *
     * @param InfoInterface $payment
     * @return string|null
     */
    protected function getTransientToken(InfoInterface $payment): ?string
    {
        $token = $payment->getAdditionalInformation('transient_token');

        return $token !== null && $token !== '' ? (string)$token : null;
    }

    /**
     * Defensively extract the three TMS ids from tokenInformation into the result, when present.
     *
     * On AUTHORIZED_PENDING_REVIEW (or any approval without tokenInformation) the ids are absent; we
     * record that no token was returned so A2 can decide whether to defer/reconcile tokenization,
     * rather than crashing on missing keys.
     *
     * @param array<string, mixed> $response
     * @param array<string, mixed> $data
     * @param bool $isApproved
     * @return void
     */
    protected function extractTokenInformation(array $response, array &$data, bool $isApproved): void
    {
        $tokenInformation = $response['tokenInformation'] ?? null;

        $customerId             = $tokenInformation['customer']['id'] ?? null;
        $paymentInstrumentId    = $tokenInformation['paymentInstrument']['id'] ?? null;
        $instrumentIdentifierId = $tokenInformation['instrumentIdentifier']['id'] ?? null;

        $tokens = array_filter([
            'customer' => $customerId,
            'paymentInstrument' => $paymentInstrumentId,
            'instrumentIdentifier' => $instrumentIdentifierId,
        ], static fn($value): bool => $value !== null && $value !== '');

        if (!empty($tokens)) {
            $data['token_information'] = $tokens;
            $data['uc_token_missing']  = false;

            return;
        }

        // No TMS ids returned — valid on DM review, or when TMS isn't provisioned. Flag, don't fail.
        $data['uc_token_missing'] = true;

        if ($isApproved) {
            $this->helper->log(
                Config::CODE,
                sprintf(
                    'Unified Checkout auth approved (%s) with no tokenInformation; no TMS ids returned.',
                    (string)($response['status'] ?? '')
                )
            );
        }
    }

    /**
     * Defensively extract card metadata (last4/type/bin/exp) from paymentInformation.card, when present.
     *
     * @param array<string, mixed> $response
     * @param array<string, mixed> $data
     * @return void
     */
    protected function extractCardMetadata(array $response, array &$data): void
    {
        $paymentInformation = $response['paymentInformation'] ?? null;
        if (empty($paymentInformation) || !is_array($paymentInformation)) {
            return;
        }

        $card = $paymentInformation['card'] ?? [];
        if (!is_array($card)) {
            $card = [];
        }

        $type = isset($card['type']) ? $this->cardType->getType((string)$card['type']) : null;

        // bin can live at card.bin, card.prefix (the 6-digit lead), or paymentInformation.bin.
        $bin = $card['bin'] ?? $card['prefix'] ?? $paymentInformation['bin'] ?? null;

        $data['card_information'] = array_filter([
            'cc_type' => $type,
            'cc_last4' => $card['suffix'] ?? null,
            'cc_bin' => $bin,
            'cc_exp_month' => $card['expirationMonth'] ?? null,
            'cc_exp_year' => $card['expirationYear'] ?? null,
        ], static fn($value): bool => $value !== null && $value !== '');
    }

    /**
     * Build the failure message, log it, and throw the matching exception type.
     *
     * Mirrors Gateway::interpretTransaction(): a declined transaction throws CommandException (so
     * Method's recapture/decline handling is identical), everything else throws RuntimeException.
     *
     * @param GatewayResponse $response
     * @param string $status
     * @param string $responseCode
     * @param string $errorMessage
     * @param InfoInterface|null $payment
     * @return GatewayResponse
     * @throws CommandException
     * @throws RuntimeException
     */
    protected function throwForFailure(
        GatewayResponse $response,
        string $status,
        string $responseCode,
        string $errorMessage,
        ?InfoInterface $payment
    ): GatewayResponse {
        $response->setIsError(true);

        $reason  = $errorMessage !== '' ? $errorMessage : $status;
        $message = __('Transaction Failed: %1', __($reason));

        if ($payment !== null) {
            $this->helper->log(
                Config::CODE,
                sprintf('%s (%s)', (string)$message, $status)
            );
        }

        $code = ctype_digit($responseCode) ? (int)$responseCode : 0;

        if ($status === 'DECLINED') {
            throw new CommandException($message, null, $code);
        }

        throw new RuntimeException($message, null, $code);
    }

    /**
     * Map the order billing address to the /pts/v2/payments billTo field tree.
     *
     * @param OrderInterface $order
     * @return array<string, string|null>
     */
    protected function getBillTo(OrderInterface $order): array
    {
        $billingAddress = $order->getBillingAddress();
        if (!$billingAddress instanceof OrderAddressInterface) {
            return [];
        }

        $street   = $billingAddress->getStreet();
        $address1 = (string)($street[0] ?? '');

        return array_filter([
            'firstName' => $this->sanitizer->alphanumericPunc($billingAddress->getFirstname(), 60),
            'lastName' => $this->sanitizer->alphanumericPunc($billingAddress->getLastname(), 60),
            'address1' => $this->sanitizer->alphanumericPunc($address1, 60),
            'address2' => $this->sanitizer->alphanumericPunc($street[1] ?? null, 60),
            'locality' => $this->sanitizer->alphanumericPunc($billingAddress->getCity(), 50),
            'administrativeArea' => $this->sanitizer->alphanumericPunc(
                strtoupper((string)$billingAddress->getRegionCode()),
                20
            ),
            'postalCode' => $this->sanitizer->postcode(
                $billingAddress->getPostcode(),
                (string)$billingAddress->getCountryId()
            ),
            'country' => $this->sanitizer->alpha(strtoupper((string)$billingAddress->getCountryId()), 2),
            'email' => $this->getEmail($billingAddress),
            'phoneNumber' => $this->sanitizer->phone($billingAddress->getTelephone(), 15),
        ], static fn($value): bool => $value !== null && $value !== '');
    }

    /**
     * Sanitize the billing email, tolerating an invalid/missing value (Sanitizer::email() may throw).
     *
     * @param OrderAddressInterface $billingAddress
     * @return string|null
     */
    protected function getEmail(OrderAddressInterface $billingAddress): ?string
    {
        $email = $billingAddress->getEmail();
        if ($email === null || $email === '') {
            return null;
        }

        try {
            return $this->sanitizer->email((string)$email);
        } catch (Throwable) {
            return null;
        }
    }
}
