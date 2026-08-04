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

namespace ParadoxLabs\CyberSource\Test\ApiFunctional\Rest;

use Magento\Customer\Test\Fixture\Customer as CustomerFixture;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Test\Fixture\CustomerCart as CustomerCartFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\TestFramework\Fixture\Config;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use ParadoxLabs\CyberSource\Test\ApiFunctional\Fixture\QuoteIdMask as QuoteIdMaskFixture;

/**
 * Web API functional tests for the Payer Authentication REST routes declared in etc/webapi.xml
 * (services: \ParadoxLabs\CyberSource\Api\PayerAuthManagementInterface and
 * \ParadoxLabs\CyberSource\Api\GuestPayerAuthManagementInterface).
 *
 * NOTE: These tests run against a live application over HTTP, so the CyberSource REST client
 * cannot be stubbed, and the sandbox MID has no Payer Auth provisioning available to the test
 * environment (m2-extension-cybersource#4). Every case below is therefore chosen because it
 * resolves BEFORE any /risk/v1 call is dispatched:
 *
 *  - the routing, the ACL resource (self vs anonymous) and the cart resolution,
 *  - the guest cart-id enforcement,
 *  - the disabled-method short circuit, which by definition issues no gateway call,
 *  - the input guards in Management (instrument ambiguity, unreadable token, missing setup).
 *
 * The gateway-answering behaviour -- the DDC handles, the frictionless verdict, the challenge
 * payload, the record custody across requests, and the assertion that a disabled method makes ZERO
 * calls -- is covered against a stubbed REST boundary in
 * {@see \ParadoxLabs\CyberSource\Test\Integration\Model\CyberSourcePayerAuthWebapiTest}, which is
 * the only layer where the HTTP boundary can be doubled.
 */
class PayerAuthTest extends WebapiAbstract
{
    private const MINE_PATH = '/V1/carts/mine/paradoxlabs-cybersource/payer-auth';
    private const GUEST_PATH = '/V1/guest-carts/%s/paradoxlabs-cybersource/payer-auth';

    private const CONFIG_METHOD_ACTIVE = 'payment/paradoxlabs_cybersource/active';
    private const CONFIG_PAYER_AUTH_ACTIVE = 'payment/paradoxlabs_cybersource/cardinal_active';
    private const CONFIG_REST_KEY_ID = 'payment/paradoxlabs_cybersource/rest_secret_key_id';
    private const CONFIG_REST_KEY = 'payment/paradoxlabs_cybersource/rest_secret_key';

    private const CUSTOMER_EMAIL = 'cybersource.payerauth.customer@example.com';
    private const CUSTOMER_PASSWORD = 'password';

    /**
     * @var DataFixtureStorage
     */
    private ?DataFixtureStorage $fixtures = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->_markTestAsRestOnly();

        $this->fixtures = Bootstrap::getObjectManager()
            ->get(DataFixtureStorageManager::class)
            ->getStorage();
    }

    /**
     * The mine routes are declared with resource `self`, so an unauthenticated caller must be
     * rejected by the framework before any of our code runs.
     *
     * _security
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
    ]
    public function testMineSetupRequiresACustomerToken(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(401);

        $this->_webApiCall(
            $this->serviceInfo(self::MINE_PATH . '/setup', 'invalid-anonymous-token'),
            ['transientToken' => 'irrelevant']
        );
    }

    /**
     * Same for the other two mine routes: a self resource that only guards one verb is not a guard.
     *
     * _security
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
    ]
    public function testMineAuthenticateAndFinalizeRequireACustomerToken(): void
    {
        foreach (['/authenticate', '/finalize'] as $action) {
            $rejected = false;

            try {
                $this->_webApiCall(
                    $this->serviceInfo(self::MINE_PATH . $action, 'invalid-anonymous-token'),
                    ['browserInfo' => $this->browserInfo()]
                );
            } catch (\Throwable $exception) {
                $rejected = true;
                self::assertSame(401, $exception->getCode(), $action . ' must reject an unauthenticated caller.');
            }

            self::assertTrue($rejected, $action . ' must not be reachable without a customer token.');
        }
    }

    /**
     * A masked cart id that addresses nothing must be a clean not-found, not a 500 and certainly not
     * a silently-created attempt against some other cart.
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
    ]
    public function testGuestSetupRejectsAnUnknownCartId(): void
    {
        $caught = null;

        try {
            $this->_webApiCall(
                $this->serviceInfo(sprintf(self::GUEST_PATH, 'no_such_cart_id') . '/setup'),
                ['transientToken' => 'irrelevant']
            );
        } catch (\Throwable $exception) {
            $caught = $exception;
        }

        self::assertNotNull($caught, 'An unknown masked cart id must not be accepted.');
        self::assertGreaterThanOrEqual(400, $caught->getCode());
        self::assertLessThan(500, $caught->getCode(), 'An unknown cart is a client error, not a server fault.');
    }

    /**
     * Guest routes must fail closed against a customer-owned cart: the masked id is the only
     * credential a guest route has, and a customer's cart is not a guest cart.
     *
     * _security
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
        DataFixture(CustomerCartFixture::class, ['customer_id' => '$customer.id$'], as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testGuestSetupRejectsACustomerOwnedCart(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No active cart was found for Payer Authentication.');

        $this->_webApiCall(
            $this->serviceInfo($this->guestPath('/setup')),
            ['transientToken' => 'irrelevant']
        );
    }

    /**
     * With Payer Authentication disabled the guest route must answer `skipped` and never reach the
     * gateway. REST credentials are deliberately left blank: a request that got as far as signing
     * would fail with a missing-credentials error instead, so a clean `skipped` response is
     * positive evidence that no /risk/v1 call was attempted.
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '0'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(GuestCartFixture::class, as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testGuestSetupIsSkippedWhenPayerAuthIsDisabled(): void
    {
        $response = $this->_webApiCall(
            $this->serviceInfo($this->guestPath('/setup')),
            ['transientToken' => 'irrelevant']
        );

        self::assertTrue((bool)($response['skipped'] ?? false), 'A disabled method must report skipped.');
        self::assertEmpty($response['access_token'] ?? null, 'A skipped setup has no device-data-collection token.');
        self::assertEmpty($response['device_data_collection_url'] ?? null);
        self::assertArrayNotHasKey('reference_id', $response, 'The referenceId must never leave the server.');
    }

    /**
     * The mine route resolves the cart from the bearer token's user context, with no cart id in the
     * URL at all. Payer Auth is left disabled so the assertion is about routing and cart resolution
     * rather than about the gateway.
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '0'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(CustomerFixture::class, ['email' => self::CUSTOMER_EMAIL], as: 'customer'),
        DataFixture(CustomerCartFixture::class, ['customer_id' => '$customer.id$'], as: 'quote'),
    ]
    public function testMineSetupResolvesTheAuthenticatedCustomersCart(): void
    {
        $response = $this->_webApiCall(
            $this->serviceInfo(self::MINE_PATH . '/setup', $this->getCustomerToken()),
            ['transientToken' => 'irrelevant']
        );

        self::assertTrue((bool)($response['skipped'] ?? false));
        self::assertArrayNotHasKey('reference_id', $response);
    }

    /**
     * setup() authenticates exactly one instrument. Neither (or both) is an ambiguous request, and
     * ambiguity here would mean binding a verdict to something other than the card being charged.
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(GuestCartFixture::class, as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testGuestSetupRejectsAnAmbiguousInstrument(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('exactly one of a transient token or a stored card');

        $this->_webApiCall(
            $this->serviceInfo($this->guestPath('/setup')),
            ['transientToken' => '', 'cardHash' => '']
        );
    }

    /**
     * An unreadable transient token must be rejected with a re-enter-your-card message rather than
     * sent to CyberSource: without a readable jti there is no binding, and a record with no binding
     * is a record that could be replayed against a different card.
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(GuestCartFixture::class, as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testGuestSetupRejectsAnUnreadableTransientToken(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('The card entry could not be read.');

        $this->_webApiCall(
            $this->serviceInfo($this->guestPath('/setup')),
            ['transientToken' => 'not-a-jwt']
        );
    }

    /**
     * authenticate() without a prior setup() has no binding, so it must refuse rather than
     * authenticate an instrument nobody declared.
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(GuestCartFixture::class, as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testGuestAuthenticateWithoutSetupIsRejected(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Payer Authentication has not been set up for this cart.');

        $this->_webApiCall(
            $this->serviceInfo($this->guestPath('/authenticate')),
            ['browserInfo' => $this->browserInfo()]
        );
    }

    /**
     * finalize() exists only to complete a challenge; with no challenged record it must say so
     * rather than post an empty authentication-results call.
     */
    #[
        Config(self::CONFIG_METHOD_ACTIVE, '1'),
        Config(self::CONFIG_PAYER_AUTH_ACTIVE, '1'),
        Config(self::CONFIG_REST_KEY_ID, ''),
        Config(self::CONFIG_REST_KEY, ''),
        DataFixture(GuestCartFixture::class, as: 'quote'),
        DataFixture(QuoteIdMaskFixture::class, ['cart_id' => '$quote.id$'], as: 'quoteIdMask'),
    ]
    public function testGuestFinalizeWithoutChallengeIsRejected(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('There is no pending authentication challenge to complete.');

        $this->_webApiCall($this->serviceInfo($this->guestPath('/finalize')), []);
    }

    /**
     * Happy path: guest setup returns the DDC access token and collection URL for a real
     * authentication-setups call.
     *
     * Requires a live POST to /risk/v1/authentication-setups; the sandbox MID available to the test
     * environment has no Payer Auth provisioning — m2-extension-cybersource#4. The equivalent
     * assertions run against a stubbed boundary in CyberSourcePayerAuthWebapiTest.
     */
    public function testGuestSetupHappyPath(): void
    {
        $this->markTestSkipped(
            'Requires a live CyberSource /risk/v1/authentication-setups call; the test sandbox MID '
            . 'has no Payer Authentication provisioning. Blocked by m2-extension-cybersource#4. '
            . 'Stub-driven equivalent: Test/Integration/Model/CyberSourcePayerAuthWebapiTest.'
        );
    }

    /**
     * Happy path: guest authenticate returns a frictionless success carrying no CAVV/ECI.
     *
     * Blocked for the same reason as testGuestSetupHappyPath.
     */
    public function testGuestAuthenticateHappyPath(): void
    {
        $this->markTestSkipped(
            'Requires a live CyberSource /risk/v1/authentications call; the test sandbox MID has no '
            . 'Payer Authentication provisioning. Blocked by m2-extension-cybersource#4. '
            . 'Stub-driven equivalent: Test/Integration/Model/CyberSourcePayerAuthWebapiTest.'
        );
    }

    /**
     * Challenge path: guest authenticate returns status challenge with the ACS URL and CReq.
     *
     * Blocked for the same reason as testGuestSetupHappyPath.
     */
    public function testGuestAuthenticateChallengePath(): void
    {
        $this->markTestSkipped(
            'Requires a live CyberSource /risk/v1/authentications call against an enrolled test PAN; '
            . 'the test sandbox MID has no Payer Authentication provisioning. Blocked by '
            . 'm2-extension-cybersource#4. Stub-driven equivalent: '
            . 'Test/Integration/Model/CyberSourcePayerAuthWebapiTest.'
        );
    }

    /**
     * Build the guest route path for the fixture cart's masked id.
     *
     * @param string $action
     * @return string
     */
    private function guestPath(string $action): string
    {
        $maskedId = $this->fixtures->get('quoteIdMask')->getMaskedId();

        return sprintf(self::GUEST_PATH, $maskedId) . $action;
    }

    /**
     * Build serviceInfo for a POST to one of the Payer Auth routes.
     *
     * @param string $resourcePath
     * @param string|null $token
     * @return array
     */
    private function serviceInfo(string $resourcePath, ?string $token = null): array
    {
        $rest = [
            'resourcePath' => $resourcePath,
            'httpMethod' => Request::HTTP_METHOD_POST,
        ];

        if ($token !== null) {
            $rest['token'] = $token;
        }

        return ['rest' => $rest];
    }

    /**
     * A complete browser profile, in the snake_case shape the REST layer expects.
     *
     * @return array
     */
    private function browserInfo(): array
    {
        return [
            'language' => 'en-US',
            'java_enabled' => false,
            'java_script_enabled' => true,
            'color_depth' => 24,
            'screen_height' => 1080,
            'screen_width' => 1920,
            'time_difference' => 300,
        ];
    }

    /**
     * Get a bearer token for the fixture customer.
     *
     * @param string $email
     * @return string
     */
    private function getCustomerToken(string $email = self::CUSTOMER_EMAIL): string
    {
        return Bootstrap::getObjectManager()
            ->get(CustomerTokenServiceInterface::class)
            ->createCustomerAccessToken($email, self::CUSTOMER_PASSWORD);
    }
}
