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
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth\Finalize;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\Result;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use PHPUnit\Framework\TestCase;

/**
 * @see \ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth\Finalize
 */
class FinalizeTest extends TestCase
{
    use ResolverHarnessTrait;

    protected function setUp(): void
    {
        $this->setUpHarness();
    }

    public function testResolveReturnsFinalOutcomeForCustomerCart(): void
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
            ->method('finalize')
            ->willReturn((new Result())->setStatus(PayerAuthResultInterface::STATUS_SUCCESS));

        $result = $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);

        $this->assertSame(
            ['status' => 'success', 'acsUrl' => null, 'pareq' => null],
            $result
        );
    }

    public function testResolveAuthorizesGuestCartByMaskedId(): void
    {
        $this->contextMock->method('getUserId')->willReturn(null);

        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->expects($this->once())
            ->method('getQuote')
            ->with(null, self::CART_ID)
            ->willReturn($this->quoteMock);

        $this->managementMock->expects($this->once())
            ->method('finalize')
            ->willReturn((new Result())->setStatus(PayerAuthResultInterface::STATUS_FAILED));

        $result = $this->resolveWith(
            $this->makeResolver(),
            ['cartId' => self::CART_ID, 'guestEmail' => 'guest@example.com']
        );

        $this->assertSame(
            ['status' => 'failed', 'acsUrl' => null, 'pareq' => null],
            $result
        );
    }

    public function testResolveRethrowsCartAuthorizationDenial(): void
    {
        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->method('getQuote')
            ->willThrowException(
                new GraphQlAuthorizationException(__('The current user cannot perform operations on cart "%1"', 'x'))
            );

        $this->managementMock->expects($this->never())->method('finalize');

        $this->expectException(GraphQlAuthorizationException::class);

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    public function testResolveRequiresCartId(): void
    {
        $this->managementMock->expects($this->never())->method('finalize');

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessageMatches('/cartId/');

        $this->resolveWith($this->makeResolver(), ['guestEmail' => 'guest@example.com']);
    }

    public function testResolveWrapsUnexpectedThrowableGenericallyAndLogs(): void
    {
        $this->managementMock->method('finalize')
            ->willThrowException(new \RuntimeException('json_decode(): unexpected token in /var/www/app/etc/env.php'));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('CyberSource Payer Authentication GraphQL error'));

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Payer authentication is temporarily unavailable. Please try again.');

        $this->resolveWith($this->makeResolver(), ['cartId' => self::CART_ID]);
    }

    /**
     * @return Finalize
     */
    private function makeResolver(): Finalize
    {
        return new Finalize(
            $this->graphQLMock,
            $this->managementFactoryMock,
            $this->configMock,
            $this->loggerMock
        );
    }
}
