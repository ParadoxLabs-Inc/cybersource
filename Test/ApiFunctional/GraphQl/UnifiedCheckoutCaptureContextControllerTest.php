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

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\TestFramework\TestCase\HttpClient\CurlClient;
use Throwable;

/**
 * Web API functional tests for the Unified Checkout capture-context controllers:
 *
 *  - \ParadoxLabs\CyberSource\Controller\UnifiedCheckout\CaptureContext (storefront)
 *  - \ParadoxLabs\CyberSource\Controller\Adminhtml\UnifiedCheckout\CaptureContext (admin)
 *
 * These issue real HTTP requests against the running application, so the CyberSource REST
 * client cannot be stubbed. Only the request-admission posture (HTTP verb, CSRF form key,
 * admin authentication) is asserted here — all of it resolves before the controller body,
 * and therefore before any gateway call. Capture-context minting itself is covered by the
 * skipped happy-path cases in UnifiedCheckoutCaptureContextTest.
 *
 * Extends GraphQlAbstract only for the shared web-API test harness bootstrapping; these
 * cases do not issue GraphQL queries. The class lives in the GraphQl directory because that
 * is the directory the ParadoxLabs web API suite (dev/tests/api-functional/phpunit_graphql.xml)
 * collects.
 */
class UnifiedCheckoutCaptureContextControllerTest extends GraphQlAbstract
{
    private const FRONTEND_PATH = 'pdl_cybs/unifiedCheckout/captureContext';
    private const ADMIN_PATH = 'pdl_cybs/unifiedCheckout/captureContext';

    /**
     * @var CurlClient
     */
    private ?CurlClient $curlClient = null;

    protected function setUp(): void
    {
        $this->curlClient = Bootstrap::getObjectManager()->get(CurlClient::class);
    }

    /**
     * The storefront endpoint is CsrfAwareActionInterface with form-key validation: a POST
     * without a valid form key must be refused with HTTP 403 and the localized message, and
     * must not return a capture context.
     */
    public function testFrontendCaptureContextRequiresFormKey(): void
    {
        $response = $this->postExpectingError($this->getFrontendUrl(), 'form_key=not-a-valid-form-key');

        self::assertSame(403, $response['status'], 'Storefront capture context must reject a bad form key.');
        self::assertStringContainsString('Invalid Form Key', $response['body']);
        self::assertStringNotContainsString('captureContext', $response['body']);
    }

    /**
     * A POST with no form key at all must be refused the same way.
     */
    public function testFrontendCaptureContextRejectsRequestWithoutFormKey(): void
    {
        $response = $this->postExpectingError($this->getFrontendUrl(), '');

        self::assertSame(403, $response['status'], 'Storefront capture context must reject a missing form key.');
        self::assertStringNotContainsString('captureContext', $response['body']);
    }

    /**
     * The storefront endpoint is HttpPostActionInterface; a GET must not be dispatched.
     */
    public function testFrontendCaptureContextRejectsGetRequest(): void
    {
        try {
            $result = $this->curlClient->getWithFullResponse($this->getFrontendUrl());
            $status = (int)$result['meta']['http_code'];
            $body   = (string)$result['body'];
        } catch (Throwable $exception) {
            $status = (int)$exception->getCode();
            $body   = $exception->getMessage();
        }

        self::assertGreaterThanOrEqual(
            400,
            $status,
            'Storefront capture context is POST-only; a GET must not be dispatched.'
        );
        self::assertStringNotContainsString('captureContext', $body);
    }

    /**
     * The admin endpoint must require an authenticated admin session. An unauthenticated
     * request must be redirected to the admin login and must not mint a capture context.
     *
     * _security
     */
    public function testAdminCaptureContextRequiresAuthentication(): void
    {
        try {
            $result = $this->curlClient->postWithFullResponse($this->getAdminUrl(), '', [], true);
            $status = (int)$result['meta']['http_code'];
            $header = (string)$result['header'];
            $body   = (string)$result['body'];
        } catch (Throwable $exception) {
            $status = (int)$exception->getCode();
            $header = '';
            $body   = $exception->getMessage();
        }

        self::assertNotSame(
            200,
            $status,
            'Admin capture context must not answer an unauthenticated request.'
        );
        self::assertStringNotContainsString(
            'captureContext',
            $body,
            'Admin capture context must not be minted for an unauthenticated request.'
        );

        if ($status >= 300 && $status < 400) {
            self::assertMatchesRegularExpression(
                '/Location:.*admin/i',
                $header,
                'An unauthenticated admin request must be redirected to the admin login.'
            );
        }
    }

    /**
     * The admin endpoint must not be reachable through the storefront router: the controller
     * lives under Controller/Adminhtml and is registered on the admin route only.
     *
     * _security
     */
    public function testAdminCaptureContextIsNotReachableViaFrontendRoute(): void
    {
        try {
            $result = $this->curlClient->postWithFullResponse(
                rtrim(TESTS_BASE_URL, '/') . '/' . self::FRONTEND_PATH . '/id/1',
                ''
            );
            $status = (int)$result['meta']['http_code'];
            $body   = (string)$result['body'];
        } catch (Throwable $exception) {
            $status = (int)$exception->getCode();
            $body   = $exception->getMessage();
        }

        self::assertNotSame(200, $status);
        self::assertStringNotContainsString('captureContext', $body);
    }

    /**
     * Happy path: an authenticated admin mints a capture context for the order-create /
     * add-card flow.
     *
     * Requires a live POST to /up/v1/capture-contexts. The sandbox MID has no REST/Unified
     * Checkout provisioning, so this cannot run — see m2-extension-cybersource#4.
     */
    public function testAdminCaptureContextHappyPath(): void
    {
        $this->markTestSkipped(
            'Requires an authenticated admin session plus a live CyberSource '
            . '/up/v1/capture-contexts call; the sandbox MID has no REST/Unified Checkout '
            . 'provisioning. Blocked by m2-extension-cybersource#4.'
        );
    }

    /**
     * Happy path: the storefront endpoint mints a capture context for a checkout session with
     * a valid form key.
     *
     * Blocked for the same reason as testAdminCaptureContextHappyPath.
     */
    public function testFrontendCaptureContextHappyPath(): void
    {
        $this->markTestSkipped(
            'Requires a storefront session with a valid form key plus a live CyberSource '
            . '/up/v1/capture-contexts call; the sandbox MID has no REST/Unified Checkout '
            . 'provisioning. Blocked by m2-extension-cybersource#4.'
        );
    }

    /**
     * POST expecting a >=400 response. CurlClient::invokeApi() throws on those, carrying the
     * body as the message and the status as the code.
     *
     * @param string $url
     * @param string $data
     * @return array{status: int, body: string}
     */
    private function postExpectingError(string $url, string $data): array
    {
        try {
            $result = $this->curlClient->postWithFullResponse($url, $data);

            return [
                'status' => (int)$result['meta']['http_code'],
                'body' => (string)$result['body'],
            ];
        } catch (Throwable $exception) {
            return [
                'status' => (int)$exception->getCode(),
                'body' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Get the storefront capture-context URL.
     *
     * @return string
     */
    private function getFrontendUrl(): string
    {
        return rtrim(TESTS_BASE_URL, '/') . '/' . self::FRONTEND_PATH;
    }

    /**
     * Get the admin capture-context URL, using the installation's configured admin front name.
     *
     * @return string
     */
    private function getAdminUrl(): string
    {
        $frontName = Bootstrap::getObjectManager()->get(FrontNameResolver::class)->getFrontName();

        return rtrim(TESTS_BASE_URL, '/') . '/' . $frontName . '/' . self::ADMIN_PATH;
    }
}
