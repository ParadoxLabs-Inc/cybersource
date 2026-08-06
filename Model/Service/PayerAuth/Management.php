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

use Magento\Authorization\Model\UserContextInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\HTTP\PhpEnvironment\Request as HttpRequest;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterfaceFactory;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterfaceFactory;
use ParadoxLabs\CyberSource\Api\PayerAuthManagementInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\TransientTokenReader;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\CyberSource\Helper\Data;

/**
 * Payer Authentication orchestration: the one place quote, config, card and the T2 services meet.
 *
 * Every transport (REST, GraphQL, Luma controllers) goes through this class, so every rule below
 * holds for all of them:
 *
 * - setup() is MANDATORY, even though the authentications call tolerates a missing referenceId.
 *   It is where the attempt is bound to an instrument, and the binding — not the client's later
 *   claims — decides what is authenticated. Without a record there is nothing to authenticate.
 * - The client never names an amount, a currency, a card id, a User-Agent, an Accept header or an
 *   IP. Those come from the quote and the live HTTP request. The client supplies only the browser
 *   profile it alone can see, and a card reference (transient token / card hash) that is resolved
 *   server-side against this cart's customer.
 * - Stored cards are addressed by hash and must belong to the cart's customer, be active, and be a
 *   CyberSource card. Anything else reads as "not found" — no distinction is surfaced, so the
 *   endpoint cannot be used to probe for other customers' card hashes.
 * - Nothing authorization-bearing crosses back: the returned DTOs carry a status and, on a
 *   challenge, the ACS handles. The CAVV/verdict live only in the Persistor record.
 *
 * @see \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Management implements PayerAuthManagementInterface
{
    /**
     * Frontend route the ACS challenge posts its result back to.
     *
     * Must stay in lockstep with \ParadoxLabs\CyberSource\Controller\Payerauth\Callback and the
     * pdl_cybs front name in etc/frontend/routes.xml; a mismatch is a silent challenge dead-end.
     */
    public const RETURN_ROUTE = 'pdl_cybs/payerauth/callback';

    /**
     * Explicitly-set quote (guest wrapper / GraphQL), bypassing session resolution.
     *
     * @var Quote|null
     */
    private ?Quote $quote = null;

    /**
     * Management constructor.
     *
     * @param Config $config
     * @param Setup $setupService
     * @param Authenticate $authenticateService
     * @param Results $resultsService
     * @param SetupRequestFactory $setupRequestFactory
     * @param AuthenticationRequestFactory $authenticationRequestFactory
     * @param ResultsRequestFactory $resultsRequestFactory
     * @param Persistor $persistor
     * @param TransientTokenReader $tokenReader
     * @param CardRepositoryInterface $cardRepository
     * @param CartRepositoryInterface $cartRepository
     * @param CheckoutSession $checkoutSession
     * @param UserContextInterface $userContext
     * @param StoreManagerInterface $storeManager
     * @param RemoteAddress $remoteAddress
     * @param RequestInterface $request
     * @param Sanitizer $sanitizer
     * @param PayerAuthSetupResultInterfaceFactory $setupResultFactory
     * @param PayerAuthResultInterfaceFactory $resultFactory
     * @param Data $helper
     */
    public function __construct(
        private readonly Config $config,
        private readonly Setup $setupService,
        private readonly Authenticate $authenticateService,
        private readonly Results $resultsService,
        private readonly SetupRequestFactory $setupRequestFactory,
        private readonly AuthenticationRequestFactory $authenticationRequestFactory,
        private readonly ResultsRequestFactory $resultsRequestFactory,
        private readonly Persistor $persistor,
        private readonly TransientTokenReader $tokenReader,
        private readonly CardRepositoryInterface $cardRepository,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CheckoutSession $checkoutSession,
        private readonly UserContextInterface $userContext,
        private readonly StoreManagerInterface $storeManager,
        private readonly RemoteAddress $remoteAddress,
        private readonly RequestInterface $request,
        private readonly Sanitizer $sanitizer,
        private readonly PayerAuthSetupResultInterfaceFactory $setupResultFactory,
        private readonly PayerAuthResultInterfaceFactory $resultFactory,
        private readonly Data $helper
    ) {
    }

    /**
     * Operate on a caller-resolved quote instead of the session/user-context one.
     *
     * Internal seam for the guest wrapper (masked cart id) and the GraphQL resolvers (cart from the
     * hardened authz path); NOT part of the service contract.
     *
     * @param CartInterface $quote
     * @return $this
     * @throws InputException When the cart is not a usable quote.
     */
    public function setQuote(CartInterface $quote): self
    {
        $this->quote = $this->toQuote($quote);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setup(?string $transientToken = null, ?string $cardHash = null): PayerAuthSetupResultInterface
    {
        try {
            return $this->runSetup($transientToken, $cardHash);
        } catch (LocalizedException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $this->unavailable('setup', $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function authenticate(
        PayerAuthBrowserInfoInterface $browserInfo,
        ?string $returnUrl = null
    ): PayerAuthResultInterface {
        try {
            return $this->runAuthenticate($browserInfo, $returnUrl, false);
        } catch (LocalizedException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $this->unavailable('authenticate', $exception);
        }
    }

    /**
     * Authenticate with a return URL the caller has already validated.
     *
     * The GraphQL surface serves headless storefronts whose origin is NOT the store base URL, so
     * the same-host rule authenticate() enforces cannot apply there. What that surface enforces is
     * SHAPE plus ORIGIN: the URL must be absolute, https, carry a plain host and no userinfo
     * component, and its origin must be the store's secure base-URL origin or one listed in the
     * merchant's payment/paradoxlabs_cybersource/payer_auth_return_origins config
     * (Config::getPayerAuthReturnOrigins()). An empty allowlist means same-store-origin only.
     * This method is deliberately absent from the service contract so no REST/webapi caller can
     * reach it.
     *
     * @param PayerAuthBrowserInfoInterface $browserInfo
     * @param string $returnUrl Absolute URL, already validated by the caller.
     * @return PayerAuthResultInterface
     * @throws InputException
     * @throws LocalizedException
     */
    public function authenticateWithValidatedReturnUrl(
        PayerAuthBrowserInfoInterface $browserInfo,
        string $returnUrl
    ): PayerAuthResultInterface {
        try {
            return $this->runAuthenticate($browserInfo, $returnUrl, true);
        } catch (LocalizedException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $this->unavailable('authenticate', $exception);
        }
    }

    /**
     * @inheritDoc
     */
    public function finalize(): PayerAuthResultInterface
    {
        try {
            return $this->runFinalize();
        } catch (LocalizedException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw $this->unavailable('finalize', $exception);
        }
    }

    /**
     * Start an attempt: resolve the instrument, run authentication-setups, seed the record.
     *
     * A setup DECLINE is not a sale failure: the record is seeded without a reference id and the
     * attempt continues DDC-less (see the catch below for the probed reason).
     *
     * @param string|null $transientToken
     * @param string|null $cardHash
     * @return PayerAuthSetupResultInterface
     * @throws InputException
     * @throws LocalizedException
     */
    private function runSetup(?string $transientToken, ?string $cardHash): PayerAuthSetupResultInterface
    {
        $quote   = $this->getQuote();
        $storeId = (int)$quote->getStoreId();

        if ($this->config->isPayerAuthEnabled($storeId) === false) {
            return $this->skippedSetup($quote);
        }

        $hasToken = $transientToken !== null && $transientToken !== '';
        $hasHash  = $cardHash !== null && $cardHash !== '';

        if ($hasToken === $hasHash) {
            throw new InputException(
                __('Payer Authentication requires exactly one of a transient token or a stored card.')
            );
        }

        /** @var SetupRequest $request */
        $request = $this->setupRequestFactory->create();
        $request->setClientReferenceCode($this->clientReferenceCode($quote));

        $storedToken = null;

        if ($hasToken) {
            $binding = $this->tokenReader->readJti((string)$transientToken);

            if ($binding === null) {
                throw new InputException(__('The card entry could not be read. Please re-enter your card.'));
            }

            $ccType      = $this->tokenReader->read((string)$transientToken)['cc_type'] ?? null;
            $storedToken = (string)$transientToken;
            $request->setTransientToken($storedToken);
        } else {
            $card                = $this->loadCardByHash((string)$cardHash, $quote);
            $paymentInstrumentId = (string)$card->getPaymentId();

            // Legacy/unreconciled vault card with no TMS instrument (uc_token_missing): there is
            // nothing to authenticate against, and the payment itself will fail loudly later.
            if ($paymentInstrumentId === '') {
                return $this->skippedSetup($quote);
            }

            $binding = $this->persistor->cardBinding((int)$card->getId());
            $ccType  = $this->cardType($card);
            $request->setPaymentInstrumentId($paymentInstrumentId);
        }

        if ($this->isTypeExcluded($ccType, $storeId)) {
            return $this->skippedSetup($quote);
        }

        try {
            $reply = $this->setupService->execute($request, $storeId);
        } catch (RuntimeException $exception) {
            // A declined authentication-setups degrades to no-DDC instead of failing the sale.
            // CyberSource rejects the call outright for Unified Checkout (gda) transient tokens
            // (probed 2026-08-06: every tokenInformation spelling 400s INVALID_REQUEST), while
            // the enrollment check accepts the same token directly and runs fine with no
            // referenceId. DDC is best-effort by contract, so the record is seeded with an empty
            // reference id and the client gets no collector handles (runDdc resolves 'skipped').
            // The call itself is kept so DDC resumes unaided if CyberSource starts accepting them.
            $this->helper->log(
                Config::CODE,
                sprintf(
                    'Payer Authentication setup declined (HTTP %d);'
                        . ' continuing without device data collection.',
                    (int)$exception->getCode()
                )
            );

            $this->persistor->saveReferenceId($quote->getPayment(), '', $binding, $storedToken);

            return $this->setupResultFactory->create()->setSkipped(false);
        }

        $auth = $this->replyAuthenticationInformation($reply);

        // The reference id is the DDC correlation handle and is optional (authentications runs
        // without it); the record is seeded either way, because the BINDING is what setup exists
        // for and authenticate() refuses to run without it.
        $this->persistor->saveReferenceId(
            $quote->getPayment(),
            (string)($auth['referenceId'] ?? ''),
            $binding,
            $storedToken
        );

        return $this->setupResultFactory->create()
            ->setSkipped(false)
            ->setAccessToken($this->stringOrNull($auth['accessToken'] ?? null))
            ->setDeviceDataCollectionUrl($this->stringOrNull($auth['deviceDataCollectionUrl'] ?? null));
    }

    /**
     * Run the authentication for the attempt seeded by setup().
     *
     * @param PayerAuthBrowserInfoInterface $browserInfo
     * @param string|null $returnUrl
     * @param bool $returnUrlIsValidated Whether the caller already validated an absolute return URL.
     * @return PayerAuthResultInterface
     * @throws InputException
     * @throws LocalizedException
     */
    private function runAuthenticate(
        PayerAuthBrowserInfoInterface $browserInfo,
        ?string $returnUrl,
        bool $returnUrlIsValidated
    ): PayerAuthResultInterface {
        $quote   = $this->getQuote();
        $storeId = (int)$quote->getStoreId();

        if ($this->config->isPayerAuthEnabled($storeId) === false) {
            return $this->skippedResult($quote);
        }

        $payment = $quote->getPayment();
        $record  = $this->persistor->load($payment) ?? [];
        $binding = (string)($record['binding'] ?? '');

        if ($binding === '') {
            throw new InputException(
                __('Payer Authentication has not been set up for this cart. Run setup first.')
            );
        }

        /** @var AuthenticationRequest $request */
        $request = $this->authenticationRequestFactory->create();
        $ccType  = $this->applyInstrument($request, $record, $binding, $quote);

        if ($this->isTypeExcluded($ccType, $storeId)) {
            return $this->skippedResult($quote);
        }

        $amount   = $this->baseAmount($quote);
        $currency = strtoupper((string)$quote->getBaseCurrencyCode());

        $request->setClientReferenceCode($this->clientReferenceCode($quote))
            ->setReferenceId($this->stringOrNull($record['reference_id'] ?? null))
            ->setReturnUrl(
                $returnUrlIsValidated && $returnUrl !== null
                    ? $returnUrl
                    : $this->resolveReturnUrl($returnUrl, $storeId)
            )
            ->setTotalAmount($amount)
            ->setCurrency($currency)
            ->setBillTo($this->buildBillTo($quote))
            ->setDeviceInformation($this->buildDeviceInformation($browserInfo));

        $result = $this->authenticateService->execute($request, $storeId);

        $this->persistor->saveResult($payment, $result, $amount, $currency, $binding);

        return $this->mapResult($result);
    }

    /**
     * Finalize a challenged authentication with authentication-results.
     *
     * @return PayerAuthResultInterface
     * @throws InputException
     * @throws LocalizedException
     */
    private function runFinalize(): PayerAuthResultInterface
    {
        $quote   = $this->getQuote();
        $storeId = (int)$quote->getStoreId();
        $payment = $quote->getPayment();
        $record  = $this->persistor->load($payment) ?? [];

        $transactionId = (string)($record['auth_transaction_id'] ?? '');
        $amount        = (string)($record['amount'] ?? '');
        $currency      = (string)($record['currency'] ?? '');
        $binding       = (string)($record['binding'] ?? '');

        if ($transactionId === ''
            || ($record['verdict'] ?? null) !== Verdict::CHALLENGE->value
            || $amount === ''
            || $currency === ''
            || $binding === '') {
            throw new InputException(__('There is no pending authentication challenge to complete.'));
        }

        $request = $this->resultsRequestFactory->create();
        $request->setClientReferenceCode($this->clientReferenceCode($quote))
            ->setAuthenticationTransactionId($transactionId);

        $result = $this->resultsService->execute($request, $storeId);

        $this->persistor->saveResult($payment, $result, $amount, $currency, $binding);

        return $this->mapResult($result);
    }

    /**
     * Address the instrument the record is bound to, and report its card type.
     *
     * Stored cards are re-loaded and re-checked against the cart's customer on every call: the
     * binding is a card id, and an id alone is not authorization.
     *
     * @param AuthenticationRequest $request
     * @param array<string, mixed> $record
     * @param string $binding
     * @param Quote $quote
     * @return string|null Magento card type code, when known.
     * @throws InputException
     * @throws LocalizedException
     */
    private function applyInstrument(
        AuthenticationRequest $request,
        array $record,
        string $binding,
        Quote $quote
    ): ?string {
        if (str_starts_with($binding, Persistor::BINDING_CARD_PREFIX) === false) {
            $token = (string)($record['transient_token'] ?? '');

            if ($token === '') {
                throw new InputException(
                    __('Payer Authentication has not been set up for this cart. Run setup first.')
                );
            }

            $request->setTransientToken($token);

            return $this->tokenReader->read($token)['cc_type'] ?? null;
        }

        $cardId = (int)substr($binding, strlen(Persistor::BINDING_CARD_PREFIX));
        $card   = $this->loadCardById($cardId, $quote);

        $paymentInstrumentId = (string)$card->getPaymentId();

        if ($paymentInstrumentId === '') {
            throw new InputException(__('The requested card could not be found.'));
        }

        $request->setPaymentInstrumentId($paymentInstrumentId);

        return $this->cardType($card);
    }

    /**
     * Build the deviceInformation tree: client browser profile + server-derived request facts.
     *
     * User-Agent, Accept and IP are read from the live HTTP request, never from client input — a
     * client that could set them could describe a browser that was never there.
     *
     * @param PayerAuthBrowserInfoInterface $browserInfo
     * @return array<string, string|null>
     */
    private function buildDeviceInformation(PayerAuthBrowserInfoInterface $browserInfo): array
    {
        return [
            'httpAcceptBrowserValue' => $this->requestHeader('Accept'),
            // Same Accept header under the name the DS actually consumes (EMV browserAcceptHeader).
            // Without it the AReq fails DS validation (error 201) and every enrollment comes back
            // veresEnrolled U — no challenge ever raised (probed 2026-08-06; DDC had been masking
            // this by supplying the device data out of band).
            'httpAcceptContent' => $this->requestHeader('Accept'),
            'userAgentBrowserValue' => $this->requestHeader('User-Agent'),
            'ipAddress' => $this->stringOrNull($this->remoteAddress->getRemoteAddress()),
            'httpBrowserLanguage' => $this->sanitizer->alphanumericPunc($browserInfo->getLanguage(), 20),
            'httpBrowserJavaEnabled' => $this->boolString($browserInfo->getJavaEnabled()),
            'httpBrowserJavaScriptEnabled' => $this->boolString($browserInfo->getJavaScriptEnabled()),
            'httpBrowserColorDepth' => $this->intString($browserInfo->getColorDepth()),
            'httpBrowserScreenHeight' => $this->intString($browserInfo->getScreenHeight()),
            'httpBrowserScreenWidth' => $this->intString($browserInfo->getScreenWidth()),
            'httpBrowserTimeDifference' => $this->intString($browserInfo->getTimeDifference()),
        ];
    }

    /**
     * Read a header off the live HTTP request, or null when unavailable.
     *
     * @param string $name
     * @return string|null
     */
    private function requestHeader(string $name): ?string
    {
        if (!$this->request instanceof HttpRequest) {
            return null;
        }

        $value = $this->request->getHeader($name);

        return is_scalar($value) && (string)$value !== '' ? (string)$value : null;
    }

    /**
     * Map the quote billing address to the authentications billTo tree.
     *
     * Mirrors the Unified Checkout capture-context mapping (same sanitizers, same field names);
     * buildingNumber is omitted because authentications does not take it.
     *
     * @param Quote $quote
     * @return array<string, string|null>
     */
    private function buildBillTo(Quote $quote): array
    {
        $address = $quote->getBillingAddress();

        if ($address === null) {
            return [];
        }

        $street = (array)$address->getStreet();

        return array_filter(
            [
                'firstName' => $this->sanitizer->alphanumericPunc($address->getFirstname(), 60),
                'lastName' => $this->sanitizer->alphanumericPunc($address->getLastname(), 60),
                'address1' => $this->sanitizer->alphanumericPunc($street[0] ?? null, 60),
                'address2' => $this->sanitizer->alphanumericPunc($street[1] ?? null, 60),
                'locality' => $this->sanitizer->alphanumericPunc($address->getCity(), 50),
                'administrativeArea' => $this->sanitizer->alphanumericPunc(
                    strtoupper((string)$address->getRegionCode()),
                    20
                ),
                'postalCode' => $this->sanitizer->postcode($address->getPostcode(), $address->getCountryId()),
                'country' => $this->sanitizer->alpha(strtoupper((string)$address->getCountryId()), 2),
                'email' => $this->sanitizeEmail($address->getEmail() ?: $quote->getCustomerEmail()),
                'phoneNumber' => $this->sanitizer->phone($address->getTelephone(), 15),
            ],
            static fn($value): bool => $value !== null && $value !== ''
        );
    }

    /**
     * Sanitize the billing email, tolerating an invalid/missing value.
     *
     * @param string|null $email
     * @return string|null
     */
    private function sanitizeEmail(?string $email): ?string
    {
        if ($email === null || $email === '') {
            return null;
        }

        try {
            return $this->sanitizer->email($email);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * Resolve the challenge return URL: the module's own route, or a validated caller URL.
     *
     * A caller-supplied URL must be absolute HTTPS on the store's own host. Anything else would let
     * a client aim the issuer's challenge POST — which carries the authentication result — at a
     * host of its choosing.
     *
     * @param string|null $returnUrl
     * @param int $storeId
     * @return string
     * @throws InputException
     * @throws LocalizedException
     */
    private function resolveReturnUrl(?string $returnUrl, int $storeId): string
    {
        $baseUrl = (string)$this->storeManager->getStore($storeId)
            ->getBaseUrl(UrlInterface::URL_TYPE_LINK, true);

        if ($returnUrl === null || $returnUrl === '') {
            return rtrim($baseUrl, '/') . '/' . self::RETURN_ROUTE;
        }

        // phpcs:disable Magento2.Functions.DiscouragedFunction -- validating a URL, not fetching it.
        $parts    = parse_url($returnUrl);
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);
        // phpcs:enable Magento2.Functions.DiscouragedFunction

        if (!is_array($parts)
            || ($parts['scheme'] ?? '') !== 'https'
            || !is_string($baseHost)
            || !isset($parts['host'])
            || strcasecmp((string)$parts['host'], $baseHost) !== 0) {
            throw new InputException(
                __('The return URL must be a secure URL on this store.')
            );
        }

        return $returnUrl;
    }

    /**
     * Load a stored card by hash, scoped to the cart's customer.
     *
     * @param string $cardHash
     * @param Quote $quote
     * @return CardInterface
     * @throws InputException
     */
    private function loadCardByHash(string $cardHash, Quote $quote): CardInterface
    {
        $this->assertCustomerCart($quote);

        try {
            $card = $this->cardRepository->getByHash($cardHash);
        } catch (\Throwable $exception) {
            throw $this->cardNotFound();
        }

        return $this->assertCardUsable($card, $quote);
    }

    /**
     * Load a stored card by id, scoped to the cart's customer.
     *
     * @param int $cardId
     * @param Quote $quote
     * @return CardInterface
     * @throws InputException
     */
    private function loadCardById(int $cardId, Quote $quote): CardInterface
    {
        $this->assertCustomerCart($quote);

        try {
            $card = $this->cardRepository->getById((string)$cardId);
        } catch (\Throwable $exception) {
            throw $this->cardNotFound();
        }

        return $this->assertCardUsable($card, $quote);
    }

    /**
     * Assert the cart belongs to a customer (guest carts have no stored cards).
     *
     * @param Quote $quote
     * @return void
     * @throws InputException
     */
    private function assertCustomerCart(Quote $quote): void
    {
        if ((int)$quote->getCustomerId() <= 0) {
            throw new InputException(__('Stored cards are not available for guest checkout.'));
        }
    }

    /**
     * Assert the card belongs to this cart's customer, is active, and is a CyberSource card.
     *
     * Every failure reads as the same "not found": a wrong-owner hash must not be distinguishable
     * from a nonexistent one.
     *
     * @param CardInterface $card
     * @param Quote $quote
     * @return CardInterface
     * @throws InputException
     */
    private function assertCardUsable(CardInterface $card, Quote $quote): CardInterface
    {
        if ((int)$card->getCustomerId() !== (int)$quote->getCustomerId()
            || (int)$card->getActive() === 0
            || (string)$card->getMethod() !== Config::CODE) {
            throw $this->cardNotFound();
        }

        return $card;
    }

    /**
     * Resolve the quote this request operates on.
     *
     * @return Quote
     * @throws InputException
     * @throws LocalizedException
     */
    private function getQuote(): Quote
    {
        if ($this->quote !== null) {
            return $this->quote;
        }

        $quoteId = (int)$this->checkoutSession->getQuoteId();

        if ($quoteId > 0) {
            $this->quote = $this->toQuote($this->cartRepository->getActive($quoteId));

            return $this->quote;
        }

        $customerId = (int)$this->userContext->getUserId();

        if ($customerId > 0 && $this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
            $this->quote = $this->toQuote($this->cartRepository->getActiveForCustomer($customerId));

            return $this->quote;
        }

        throw new InputException(__('No active cart was found for Payer Authentication.'));
    }

    /**
     * Narrow a cart to the quote model this class needs (totals, payment, customer id).
     *
     * @param CartInterface $cart
     * @return Quote
     * @throws InputException
     */
    private function toQuote(CartInterface $cart): Quote
    {
        if (!$cart instanceof Quote) {
            throw new InputException(__('No active cart was found for Payer Authentication.'));
        }

        return $cart;
    }

    /**
     * Map a classified authentication outcome to the client-facing DTO.
     *
     * UNAVAILABLE reports as success on purpose: the order proceeds without a liability shift, and
     * the distinction is the merchant's business, not the browser's.
     *
     * @param AuthenticationResult $result
     * @return PayerAuthResultInterface
     */
    private function mapResult(AuthenticationResult $result): PayerAuthResultInterface
    {
        $dto = $this->resultFactory->create();

        return match ($result->getVerdict()) {
            Verdict::FAILED => $dto->setStatus(PayerAuthResultInterface::STATUS_FAILED),
            Verdict::CHALLENGE => $dto->setStatus(PayerAuthResultInterface::STATUS_CHALLENGE)
                ->setAcsUrl($result->acsUrl())
                ->setPareq($result->pareq())
                ->setStepUpUrl($result->stepUpUrl())
                ->setAccessToken($result->accessToken()),
            default => $dto->setStatus(PayerAuthResultInterface::STATUS_SUCCESS),
        };
    }

    /**
     * Build a "Payer Authentication did not run" setup result, clearing any record first.
     *
     * @param Quote $quote
     * @return PayerAuthSetupResultInterface
     */
    private function skippedSetup(Quote $quote): PayerAuthSetupResultInterface
    {
        $this->clearRecord($quote);

        return $this->setupResultFactory->create()->setSkipped(true);
    }

    /**
     * Build a "Payer Authentication did not run" outcome, clearing any record first.
     *
     * @param Quote $quote
     * @return PayerAuthResultInterface
     */
    private function skippedResult(Quote $quote): PayerAuthResultInterface
    {
        $this->clearRecord($quote);

        return $this->resultFactory->create()->setStatus(PayerAuthResultInterface::STATUS_SKIPPED);
    }

    /**
     * Drop any persisted record for a cart Payer Authentication does not apply to.
     *
     * Every skip path is a config decision that THIS charge needs no 3DS. A record left from an
     * earlier attempt would otherwise be picked up by the BindingValidator at place time and
     * hard-block a cart that is not supposed to be authenticated at all; the validator never
     * discards, so the clear has to happen here.
     *
     * @param Quote $quote
     * @return void
     */
    private function clearRecord(Quote $quote): void
    {
        $payment = $quote->getPayment();

        if ($payment !== null) {
            $this->persistor->clear($payment);
        }
    }

    /**
     * Whether a known card type is excluded from Payer Authentication by config.
     *
     * An unknown type (wallet token, unmapped brand) is NOT excluded: the enrollment check itself
     * is the authority there.
     *
     * @param string|null $ccType
     * @param int $storeId
     * @return bool
     */
    private function isTypeExcluded(?string $ccType, int $storeId): bool
    {
        return $ccType !== null
            && $ccType !== ''
            && $this->config->isPayerAuthEnabledForType($ccType, $storeId) === false;
    }

    /**
     * Get the stored card's Magento card type code, when known.
     *
     * @param CardInterface $card
     * @return string|null
     */
    private function cardType(CardInterface $card): ?string
    {
        return $this->stringOrNull($card->getAdditional('cc_type'));
    }

    /**
     * Get the merchant reference code for this cart.
     *
     * @param Quote $quote
     * @return string
     */
    private function clientReferenceCode(Quote $quote): string
    {
        return (string)$quote->getId();
    }

    /**
     * Get the quote's base grand total as a fixed 2-decimal string.
     *
     * This is the amount PINNED into the record as "what was authenticated". It is the CEILING the
     * cardholder saw and approved; the charge that reaches the gateway can legitimately be lower
     * (store credit, gift cards, partial payments), which is why the BindingValidator's amount rule
     * runs charge <= authenticated.
     *
     * @param Quote $quote
     * @return string
     */
    private function baseAmount(Quote $quote): string
    {
        return number_format((float)$quote->getBaseGrandTotal(), 2, '.', '');
    }

    /**
     * Get the consumerAuthenticationInformation branch of a reply, or [].
     *
     * @param array<string, mixed> $reply
     * @return array<string, mixed>
     */
    private function replyAuthenticationInformation(array $reply): array
    {
        $auth = $reply['consumerAuthenticationInformation'] ?? null;

        return is_array($auth) ? $auth : [];
    }

    /**
     * Normalize a value to a non-empty string, or null.
     *
     * @param mixed $value
     * @return string|null
     */
    private function stringOrNull(mixed $value): ?string
    {
        return is_scalar($value) && (string)$value !== '' ? (string)$value : null;
    }

    /**
     * Render a browser boolean for the API (string, because empty values are rejected upstream).
     *
     * @param bool|null $value
     * @return string|null
     */
    private function boolString(?bool $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value ? 'true' : 'false';
    }

    /**
     * Render a browser integer for the API.
     *
     * @param int|null $value
     * @return string|null
     */
    private function intString(?int $value): ?string
    {
        return $value !== null ? (string)$value : null;
    }

    /**
     * Log an unexpected failure and return a generic error.
     *
     * Internals never reach the client: a transport failure, a malformed reply or a coding error
     * all surface as the same message, so the endpoint cannot be used to map the gateway.
     *
     * @param string $step
     * @param \Throwable $exception
     * @return LocalizedException
     */
    private function unavailable(string $step, \Throwable $exception): LocalizedException
    {
        $this->helper->log(
            Config::CODE,
            sprintf(
                'Payer Authentication %s failed: %s',
                $step,
                $exception->getMessage()
            )
        );

        return new LocalizedException(
            __('Payer authentication is temporarily unavailable. Please try again.')
        );
    }

    /**
     * Build the uniform card-not-found failure.
     *
     * @return InputException
     */
    private function cardNotFound(): InputException
    {
        return new InputException(__('The requested card could not be found.'));
    }
}
