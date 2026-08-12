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

use Magento\Framework\Exception\StateException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface as BaseContextInterface;
use ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth\Setup;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\SetupResult;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use PHPUnit\Framework\TestCase;

/**
 * @see \ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth\Setup
 */
class SetupTest extends TestCase
{
    use ResolverHarnessTrait;

    protected function setUp(): void
    {
        $this->setUpHarness();
    }

    public function testResolveReturnsSetupParametersForCustomerCart(): void
    {
        $this->contextMock->method('getUserId')->willReturn(7);

        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->expects($this->once())
            ->method('getQuote')
            ->with(7, self::CART_ID)
            ->willReturn($this->quoteMock);

        $this->managementMock->expects($this->once())
            ->method('setQuote')
            ->with($this->quoteMock)
            ->willReturnSelf();

        $this->managementMock->expects($this->once())
            ->method('setup')
            ->with('eyJ.transient.token', null)
            ->willReturn(
                (new SetupResult())->setSkipped(false)
                    ->setAccessToken('eyJ.ddc.jwt')
                    ->setDeviceDataCollectionUrl('https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect')
            );

        $result = $this->resolveWith(
            $this->makeResolver(),
            [
                'cartId' => self::CART_ID,
                'transientToken' => 'eyJ.transient.token',
            ]
        );

        $this->assertSame(
            [
                'skipped' => false,
                'accessToken' => 'eyJ.ddc.jwt',
                'deviceDataCollectionUrl' => 'https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect',
            ],
            $result
        );
    }

    public function testResolveAuthorizesGuestCartByMaskedIdAndPassesStoredCard(): void
    {
        // Guests carry no user id; possession of the masked cart id is the authorization, and the
        // shared guard refuses a masked id belonging to a customer's cart.
        $this->contextMock->method('getUserId')->willReturn(null);

        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->expects($this->once())
            ->method('getQuote')
            ->with(null, self::CART_ID)
            ->willReturn($this->quoteMock);

        $this->managementMock->expects($this->once())
            ->method('setup')
            ->with(null, 'abc123hash')
            ->willReturn((new SetupResult())->setSkipped(true));

        $result = $this->resolveWith(
            $this->makeResolver(),
            [
                'cartId' => self::CART_ID,
                'guestEmail' => 'guest@example.com',
                'cardHash' => 'abc123hash',
            ]
        );

        $this->assertSame(
            [
                'skipped' => true,
                'accessToken' => null,
                'deviceDataCollectionUrl' => null,
            ],
            $result
        );
    }

    public function testResolveRethrowsCartAuthorizationDenial(): void
    {
        // Fail CLOSED: a denial from the cart guard is the caller's answer, never downgraded.
        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->method('getQuote')
            ->willThrowException(
                new GraphQlAuthorizationException(__('The current user cannot perform operations on cart "%1"', 'x'))
            );

        $this->managementMock->expects($this->never())->method('setup');

        $this->expectException(GraphQlAuthorizationException::class);

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    public function testResolveRethrowsMissingCartLookupFailure(): void
    {
        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->method('getQuote')
            ->willThrowException(new GraphQlNoSuchEntityException(__('Could not find a cart with ID "%1"', 'x')));

        $this->managementMock->expects($this->never())->method('setup');

        $this->expectException(GraphQlNoSuchEntityException::class);

        $this->resolveWith($this->makeResolver(), ['cartId' => 'nope']);
    }

    public function testResolveDeniesWhenQueryContextCarriesNoIdentity(): void
    {
        // Fail CLOSED: no caller identity means no cart ownership check is possible.
        $this->contextMock = $this->createMock(BaseContextInterface::class);

        $this->managementMock->expects($this->never())->method('setup');

        $this->expectException(GraphQlAuthorizationException::class);

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    public function testResolveRequiresCartId(): void
    {
        $this->managementMock->expects($this->never())->method('setup');

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessageMatches('/cartId/');

        $this->resolveWith($this->makeResolver(), ['guestEmail' => 'guest@example.com']);
    }

    public function testResolveRequiresInputArgument(): void
    {
        $this->managementMock->expects($this->never())->method('setup');

        $this->expectException(GraphQlInputException::class);

        $this->resolveWith($this->makeResolver(), null);
    }

    public function testResolveFailsCleanlyWhenMethodInactive(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->configMock->expects($this->once())
            ->method('moduleIsActive')
            ->with(self::STORE_ID)
            ->willReturn(false);

        $this->managementMock->expects($this->never())->method('setup');

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessageMatches('/not (available|active|enabled)/i');

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    public function testResolveSurfacesLocalizedExceptionMessage(): void
    {
        $this->managementMock->method('setup')
            ->willThrowException(
                new StateException(__('Missing CyberSource REST Secret Key. Please check configuration.'))
            );

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Missing CyberSource REST Secret Key. Please check configuration.');

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    public function testResolveWrapsUnexpectedThrowableGenericallyAndLogs(): void
    {
        // Internals never reach the client: a coding error surfaces as the generic message only.
        $this->managementMock->method('setup')
            ->willThrowException(new \RuntimeException('SQLSTATE[42S02] table cybersource_secret missing'));

        $this->loggerMock->expects($this->once())
            ->method('log')
            ->with($this->anything(), $this->stringContains('Payer Authentication GraphQL error'));

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Payer authentication is temporarily unavailable. Please try again.');

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    /**
     * @return Setup
     */
    private function makeResolver(): Setup
    {
        return new Setup(
            $this->graphQLMock,
            $this->managementFactoryMock,
            $this->configMock,
            $this->loggerMock
        );
    }
}
