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

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Api\GraphQL\PayerAuth;

use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterfaceFactory;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth\Authenticate;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\BrowserInfo;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\Result;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use PHPUnit\Framework\TestCase;

/**
 * @see \ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth\Authenticate
 */
class AuthenticateTest extends TestCase
{
    use ResolverHarnessTrait;

    /**
     * Schema-shaped browserInfo input; every field is non-null in the schema.
     */
    private const BROWSER_INFO = [
        'language' => 'en-US',
        'javaEnabled' => false,
        'javaScriptEnabled' => true,
        'colorDepth' => 24,
        'screenHeight' => 1080,
        'screenWidth' => 1920,
        'timeDifference' => 300,
    ];

    protected function setUp(): void
    {
        $this->setUpHarness();
    }

    public function testResolveAuthenticatesWithStoreDefaultReturnUrlWhenNoneGiven(): void
    {
        $this->contextMock->method('getUserId')->willReturn(7);

        /** @var PayerAuthBrowserInfoInterface|null $captured */
        $captured = null;

        $this->managementMock->expects($this->once())
            ->method('authenticate')
            ->with(
                $this->callback(
                    static function (PayerAuthBrowserInfoInterface $browserInfo) use (&$captured): bool {
                        $captured = $browserInfo;

                        return true;
                    }
                ),
                null
            )
            ->willReturn((new Result())->setStatus(PayerAuthResultInterface::STATUS_SUCCESS));

        $this->managementMock->expects($this->never())->method('authenticateWithValidatedReturnUrl');

        $result = $this->resolveWith(
            $this->makeResolver(),
            [
                'cartId' => self::CART_ID,
                'browserInfo' => self::BROWSER_INFO,
            ]
        );

        $this->assertSame(
            ['status' => 'success', 'acsUrl' => null, 'pareq' => null],
            $result
        );
        $this->assertSame('en-US', $captured?->getLanguage());
        $this->assertFalse($captured?->getJavaEnabled());
        $this->assertTrue($captured?->getJavaScriptEnabled());
        $this->assertSame(24, $captured?->getColorDepth());
        $this->assertSame(1080, $captured?->getScreenHeight());
        $this->assertSame(1920, $captured?->getScreenWidth());
        $this->assertSame(300, $captured?->getTimeDifference());
    }

    public function testResolveAcceptsForeignHeadlessReturnUrlAndReturnsChallenge(): void
    {
        // A headless storefront runs on its own origin, so a foreign host is legitimate here.
        $this->managementMock->expects($this->once())
            ->method('authenticateWithValidatedReturnUrl')
            ->with($this->isInstanceOf(PayerAuthBrowserInfoInterface::class), 'https://pwa.example.net/checkout/3ds')
            ->willReturn(
                (new Result())->setStatus(PayerAuthResultInterface::STATUS_CHALLENGE)
                    ->setAcsUrl('https://acs.example.com/challenge')
                    ->setPareq('eyJ.pareq')
            );

        $result = $this->resolveWith(
            $this->makeResolver(),
            [
                'cartId' => self::CART_ID,
                'guestEmail' => 'guest@example.com',
                'returnUrl' => 'https://pwa.example.net/checkout/3ds',
                'browserInfo' => self::BROWSER_INFO,
            ]
        );

        $this->assertSame(
            [
                'status' => 'challenge',
                'acsUrl' => 'https://acs.example.com/challenge',
                'pareq' => 'eyJ.pareq',
            ],
            $result
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidReturnUrlProvider(): array
    {
        return [
            'userinfo spoof' => ['https://store.example.com@evil.example/return'],
            'userinfo with password' => ['https://user:pass@evil.example/return'],
            'plaintext http' => ['http://pwa.example.net/checkout/3ds'],
            'relative path' => ['/checkout/3ds'],
            'javascript scheme' => ['javascript:alert(1)'],
        ];
    }

    /**
     * @dataProvider invalidReturnUrlProvider
     * @param string $returnUrl
     */
    public function testResolveRejectsUnsafeReturnUrls(string $returnUrl): void
    {
        $this->managementMock->expects($this->never())->method('authenticateWithValidatedReturnUrl');
        $this->managementMock->expects($this->never())->method('authenticate');

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessageMatches('/return URL/i');

        $this->resolveWith(
            $this->makeResolver(),
            [
                'cartId' => self::CART_ID,
                'returnUrl' => $returnUrl,
                'browserInfo' => self::BROWSER_INFO,
            ]
        );
    }

    public function testResolveRequiresBrowserInfo(): void
    {
        $this->managementMock->expects($this->never())->method('authenticate');

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessageMatches('/browserInfo/');

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    public function testResolveRequiresEveryBrowserField(): void
    {
        $browserInfo = self::BROWSER_INFO;
        unset($browserInfo['colorDepth']);

        $this->managementMock->expects($this->never())->method('authenticate');

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessageMatches('/colorDepth/');

        $this->resolveWith(
            $this->makeResolver(),
            ['cartId' => self::CART_ID, 'browserInfo' => $browserInfo]
        );
    }

    public function testResolveRethrowsCartAuthorizationDenial(): void
    {
        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->method('getQuote')
            ->willThrowException(
                new GraphQlAuthorizationException(__('The current user cannot perform operations on cart "%1"', 'x'))
            );

        $this->managementMock->expects($this->never())->method('authenticate');

        $this->expectException(GraphQlAuthorizationException::class);

        $this->resolveWith(
            $this->makeResolver(),
            ['cartId' => self::CART_ID, 'browserInfo' => self::BROWSER_INFO]
        );
    }

    public function testResolveWrapsUnexpectedThrowableGenericallyAndLogs(): void
    {
        $this->managementMock->method('authenticate')
            ->willThrowException(new \RuntimeException('cURL error 7: failed to connect to api.cybersource.com'));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('CyberSource Payer Authentication GraphQL error'));

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Payer authentication is temporarily unavailable. Please try again.');

        $this->resolveWith(
            $this->makeResolver(),
            ['cartId' => self::CART_ID, 'browserInfo' => self::BROWSER_INFO]
        );
    }

    /**
     * @return Authenticate
     */
    private function makeResolver(): Authenticate
    {
        $browserInfoFactory = $this->createMock(PayerAuthBrowserInfoInterfaceFactory::class);
        $browserInfoFactory->method('create')->willReturnCallback(static fn(): BrowserInfo => new BrowserInfo());

        return new Authenticate(
            $this->graphQLMock,
            $this->managementFactoryMock,
            $this->configMock,
            $this->loggerMock,
            $browserInfoFactory
        );
    }
}
