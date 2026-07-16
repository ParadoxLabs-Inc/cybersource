<?php declare(strict_types=1);
/**
 * Copyright © 2015-present ParadoxLabs, Inc.
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

namespace ParadoxLabs\CyberSource\Test\ApiFunctional\GraphQl;

use Magento\Customer\Test\Fixture\Customer as CustomerFixture;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Test\Fixture\CustomerCart as CustomerCartFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\Quote\Test\Fixture\QuoteIdMask as QuoteIdMaskFixture;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Web API functional tests for the cyberSourceUnifiedCheckoutCaptureContext GraphQL query
 * (resolver: \ParadoxLabs\CyberSource\Model\Api\GraphQL\UnifiedCheckout\CaptureContext,
 * service: \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\GraphQL).
 *
 * NOTE: These tests run against a live application over HTTP, so the CyberSource REST
 * client cannot be stubbed. Every case below is chosen because it resolves before any
 * gateway HTTP call is dispatched:
 *
 *  - The public-API gate and the cart guards run in the resolver/TokenBase helper.
 *  - The GraphQL schema rejects malformed input before the resolver is reached.
 *  - Config::getRestSecretKey()/getRestSecretKeyId() throw a StateException while signing,
 *    i.e. still before the socket is opened, when REST credentials are unconfigured.
 *
 * Cases that require a real capture-context JWT (a live /up/v1/capture-contexts POST) are
 * skipped; the sandbox MID has no REST/TMS provisioning — m2-extension-cybersource#4.
 */
class UnifiedCheckoutCaptureContextTest extends GraphQlAbstract
{
    private const CONFIG_ENABLE_PUBLIC_API = 'checkout/tokenbase/enable_public_api';
    private const CONFIG_METHOD_ACTIVE = 'payment/paradoxlabs_cybersource/active';
    private const CONFIG_REST_KEY_ID = 'payment/paradoxlabs_cybersource/rest_secret_key_id';
    private const CONFIG_REST_KEY = 'payment/paradoxlabs_cybersource/rest_secret_key';

    private const CUSTOMER_EMAIL = 'cybersource.uc.customer@example.com';
    private const OTHER_CUSTOMER_EMAIL = 'cybersource.uc.other@example.com';
    private const CUSTOMER_PASSWORD = 'password';

    /**
     * @var CustomerTokenServiceInterface
     */
    private ?CustomerTokenServiceInterface $customerTokenService = null;

    /**
     * @var DataFixtureStorage
     */
    private ?DataFixtureStorage $fixtures = null;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->customerTokenService = $objectManager->get(CustomerTokenServiceInterface::class);
        $this->fixtures = $objectManager->get(DataFixtureStorageManager::class)->getStorage();
    }

    /**
     * The TokenBase public-API gate must reject the query outright when disabled.
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '0'),
    ]
    public function testQueryIsRejectedWhenPublicApiIsDisabled(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The TokenbaseCard API is not enabled.');

        $this->graphQlQuery($this->getQuery('any_cart_id'));
    }

    /**
     * An unknown cartId must produce a GraphQL "no such entity" error, not a 500 and not a
     * silently-degraded billing-only capture context.
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
    ]
    public function testUnknownCartIdIsRejected(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Could not find a cart with ID "no_such_cart_id"');

        $this->graphQlQuery(
            $this->getQuery('no_such_cart_id'),
            [],
            '',
            $this->getHeaderMap()
        );
    }

    /**
     * An inactive (already converted to order) cart must be rejected rather than used.
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
        DataFixture(CustomerCartFixture::class, ['customer_id' => '$customer.id$'], as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testInactiveCartIsRejected(): void
    {
        $this->deactivateQuote((int)$this->fixtures->get('quote')->getId());
        $maskedQuoteId = $this->fixtures->get('quoteIdMask')->getMaskedId();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The cart isn\'t active.');

        $this->graphQlQuery(
            $this->getQuery($maskedQuoteId),
            [],
            '',
            $this->getHeaderMap()
        );
    }

    /**
     * A customer must NOT be able to mint a capture context against another customer's cart.
     * The capture context carries that cart's amount/currency/billTo, so minting one for a
     * foreign cart is both an authorization break and a data disclosure vector.
     *
     * _security
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
        DataFixture(CustomerFixture::class, ['email' => self::OTHER_CUSTOMER_EMAIL], as: 'otherCustomer'),
        DataFixture(CustomerCartFixture::class, ['customer_id' => '$customer.id$'], as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testCustomerCannotMintCaptureContextForAnotherCustomersCart(): void
    {
        $maskedQuoteId = $this->fixtures->get('quoteIdMask')->getMaskedId();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            sprintf('The current user cannot perform operations on cart "%s"', $maskedQuoteId)
        );

        $this->graphQlQuery(
            $this->getQuery($maskedQuoteId),
            [],
            '',
            $this->getHeaderMap(self::OTHER_CUSTOMER_EMAIL)
        );
    }

    /**
     * A guest (unauthenticated) request must NOT be able to mint a capture context against a
     * registered customer's cart.
     *
     * _security
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
        DataFixture(CustomerCartFixture::class, ['customer_id' => '$customer.id$'], as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testGuestCannotMintCaptureContextForCustomerCart(): void
    {
        $maskedQuoteId = $this->fixtures->get('quoteIdMask')->getMaskedId();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            sprintf('The current user cannot perform operations on cart "%s"', $maskedQuoteId)
        );

        $this->graphQlQuery($this->getQuery($maskedQuoteId));
    }

    /**
     * A logged-in customer must NOT be able to mint a capture context against a guest cart.
     *
     * _security
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
        DataFixture(GuestCartFixture::class, as: 'guestQuote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$guestQuote.id$'], as: 'quoteIdMask'),
    ]
    public function testCustomerCannotMintCaptureContextForGuestCart(): void
    {
        $maskedQuoteId = $this->fixtures->get('quoteIdMask')->getMaskedId();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            sprintf('The current user cannot perform operations on cart "%s"', $maskedQuoteId)
        );

        $this->graphQlQuery(
            $this->getQuery($maskedQuoteId),
            [],
            '',
            $this->getHeaderMap()
        );
    }

    /**
     * The schema declares input as non-null; omitting it must be a schema error.
     */
    public function testMissingInputArgumentIsRejectedBySchema(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('argument "input" of type "TokenBaseCyberSourceUnifiedCheckoutInput!" is required');

        $query = <<<QUERY
{
  cyberSourceUnifiedCheckoutCaptureContext {
    captureContext
  }
}
QUERY;

        $this->graphQlQuery($query);
    }

    /**
     * billingAddress is a CustomerAddressInput; country_code is a CountryCodeEnum, so an
     * unknown country must be rejected by schema validation before the resolver runs.
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
    ]
    public function testInvalidBillingAddressCountryIsRejectedBySchema(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CountryCodeEnum');

        $query = <<<QUERY
{
  cyberSourceUnifiedCheckoutCaptureContext(
    input: {
      billingAddress: {
        firstname: "Jane"
        lastname: "Doe"
        street: ["123 Main St"]
        city: "Los Angeles"
        postcode: "90001"
        telephone: "5551234567"
        country_code: NOT_A_COUNTRY
      }
    }
  ) {
    captureContext
  }
}
QUERY;

        $this->graphQlQuery($query, [], '', $this->getHeaderMap());
    }

    /**
     * A billingAddress with an unparseable structure must produce a GraphQL input error rather
     * than being silently discarded, which would mint a capture context whose billTo does not
     * match what the caller asked for.
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
    ]
    public function testUnknownBillingAddressFieldIsRejectedBySchema(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('countryId');

        $query = <<<QUERY
{
  cyberSourceUnifiedCheckoutCaptureContext(
    input: {
      billingAddress: {
        firstname: "Jane"
        countryId: "US"
      }
    }
  ) {
    captureContext
  }
}
QUERY;

        $this->graphQlQuery($query, [], '', $this->getHeaderMap());
    }

    /**
     * With the payment method disabled, the query must fail with a clean error that says the
     * method is unavailable — before any config-credential or gateway work is attempted.
     *
     * REST credentials are deliberately left blank here so the test stays offline. Consequently
     * a failure whose message is about missing REST credentials proves the method-active guard
     * did not run, which is exactly the condition this test exists to catch.
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '0'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
    ]
    public function testMethodDisabledIsRejectedCleanly(): void
    {
        $message = $this->getQueryErrorMessage($this->getQuery(null), $this->getHeaderMap());

        self::assertNotNull($message, 'A disabled payment method must not mint a capture context.');
        self::assertMatchesRegularExpression(
            '/not (available|active|enabled)/i',
            $message,
            'A disabled payment method must fail with a clean "method unavailable" error.'
        );
    }

    /**
     * With the method enabled but REST credentials unconfigured, the caller must get the clean
     * localized configuration error, and no capture context. Config::getRestSecretKey() throws a
     * StateException while signing, so this never reaches the network.
     *
     * The resolver must convert that into a GraphQL error carrying the message. It currently
     * surfaces as a bare "Internal server error" (the real message only reaches `debugMessage`,
     * and only in developer mode), which is what this test holds the line against.
     */
    #[
        Config(self::CONFIG_ENABLE_PUBLIC_API, '1'),
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
    ]
    public function testUnconfiguredRestCredentialsProduceCleanError(): void
    {
        $message = $this->getQueryErrorMessage($this->getQuery(null), $this->getHeaderMap());

        self::assertNotNull($message, 'Unconfigured REST credentials must not mint a capture context.');
        self::assertStringContainsString(
            'Missing CyberSource REST Secret Key',
            $message,
            'The caller must receive the localized configuration error.'
        );
    }

    /**
     * Happy path: a customer mints a capture-context JWT for their own cart.
     *
     * Requires a live POST to /up/v1/capture-contexts. The sandbox MID has no REST/Unified
     * Checkout provisioning, so this cannot run — see m2-extension-cybersource#4 (TMS/REST
     * provisioning blocker). Unskip once the sandbox MID is provisioned and credentials are
     * available to the test environment.
     */
    public function testCustomerCaptureContextForOwnCartHappyPath(): void
    {
        $this->markTestSkipped(
            'Requires a live CyberSource /up/v1/capture-contexts call; the sandbox MID has no '
            . 'REST/Unified Checkout provisioning. Blocked by m2-extension-cybersource#4.'
        );
    }

    /**
     * Happy path: a guest mints a capture-context JWT for their own guest cart, both with and
     * without the guestEmail input (email is optional for UC — requestEmail is false and Magento
     * collects contact details itself).
     *
     * Blocked for the same reason as testCustomerCaptureContextForOwnCartHappyPath.
     */
    public function testGuestCaptureContextForOwnGuestCartHappyPath(): void
    {
        $this->markTestSkipped(
            'Requires a live CyberSource /up/v1/capture-contexts call; the sandbox MID has no '
            . 'REST/Unified Checkout provisioning. Blocked by m2-extension-cybersource#4.'
        );
    }

    /**
     * Happy path: billing-only capture context with no cartId (headless add-card / save-card),
     * sourcing billTo from the billingAddress input.
     *
     * Blocked for the same reason as testCustomerCaptureContextForOwnCartHappyPath.
     */
    public function testBillingOnlyCaptureContextWithoutCartHappyPath(): void
    {
        $this->markTestSkipped(
            'Requires a live CyberSource /up/v1/capture-contexts call; the sandbox MID has no '
            . 'REST/Unified Checkout provisioning. Blocked by m2-extension-cybersource#4.'
        );
    }

    /**
     * Build the GraphQL query, optionally for the given masked cart ID.
     *
     * @param string|null $maskedQuoteId
     * @return string
     */
    private function getQuery(?string $maskedQuoteId): string
    {
        $input = $maskedQuoteId !== null ? sprintf('cartId: "%s"', $maskedQuoteId) : '';

        return <<<QUERY
{
  cyberSourceUnifiedCheckoutCaptureContext(
    input: {
      {$input}
    }
  ) {
    captureContext
  }
}
QUERY;
    }

    /**
     * Run the query and return the caller-visible top-level GraphQL error message, or null when
     * the query succeeded.
     *
     * GraphQlAbstract wraps failures in an exception whose message embeds a var_export() of the
     * entire response, including the developer-mode `debugMessage`. Asserting against that raw
     * string would let a test pass on a message the API consumer never sees, so the top-level
     * `errors[].message` values are extracted and returned on their own.
     *
     * @param string $query
     * @param array $headers
     * @return string|null
     */
    private function getQueryErrorMessage(string $query, array $headers = []): ?string
    {
        try {
            $this->graphQlQuery($query, [], '', $headers);
        } catch (\Throwable $exception) {
            $message = $exception->getMessage();

            // "GraphQL response contains errors: <messages>\n<var_export of response>"
            $prefix = 'GraphQL response contains errors:';
            if (str_starts_with($message, $prefix)) {
                $message = trim(strtok(substr($message, strlen($prefix)), "\n") ?: '');
            }

            return $message;
        }

        return null;
    }

    /**
     * Get an authorization header for the given fixture customer.
     *
     * @param string $email
     * @return array
     */
    private function getHeaderMap(string $email = self::CUSTOMER_EMAIL): array
    {
        $token = $this->customerTokenService->createCustomerAccessToken($email, self::CUSTOMER_PASSWORD);

        return [
            'Authorization' => 'Bearer ' . $token,
        ];
    }

    /**
     * Mark the given quote inactive, as order placement does.
     *
     * @param int $quoteId
     * @return void
     */
    private function deactivateQuote(int $quoteId): void
    {
        $cartRepository = Bootstrap::getObjectManager()->get(CartRepositoryInterface::class);

        $quote = $cartRepository->get($quoteId);
        $quote->setIsActive(false);

        $cartRepository->save($quote);
    }
}
