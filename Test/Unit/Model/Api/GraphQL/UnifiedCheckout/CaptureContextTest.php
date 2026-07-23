<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Api\GraphQL\UnifiedCheckout;

use Magento\Framework\Exception\StateException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ParadoxLabs\CyberSource\Model\Api\GraphQL\UnifiedCheckout\CaptureContext;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\GraphQL as CaptureContextService;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Api\GraphQL\UnifiedCheckout\CaptureContext
 */
class CaptureContextTest extends TestCase
{
    private const TEST_JWT = 'eyJ.capture.context';

    private GraphQL|MockObject $graphQLMock;
    private CaptureContextService|MockObject $captureContextMock;
    private Field|MockObject $fieldMock;
    private ContextInterface|MockObject $contextMock;
    private ResolveInfo|MockObject $infoMock;

    protected function setUp(): void
    {
        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->captureContextMock = $this->createMock(CaptureContextService::class);
        $this->fieldMock = $this->createMock(Field::class);
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->infoMock = $this->createMock(ResolveInfo::class);
    }

    public function testResolveAuthenticatesPassesArgsAndReturnsJwt(): void
    {
        $input = [
            'cartId' => 'cart123',
            'guestEmail' => 'guest@example.com',
        ];

        $this->graphQLMock->expects($this->once())
            ->method('authenticate')
            ->with($this->contextMock);

        $this->captureContextMock->expects($this->once())
            ->method('setGraphQLContext')
            ->with($this->contextMock, $input);

        $this->captureContextMock->expects($this->once())
            ->method('generate')
            ->willReturn(self::TEST_JWT);

        $result = $this->makeResolver(true)->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            null,
            ['input' => $input]
        );

        $this->assertSame(['captureContext' => self::TEST_JWT], $result);
    }

    public function testResolveThrowsWhenInputMissing(): void
    {
        $this->graphQLMock->expects($this->once())
            ->method('authenticate')
            ->with($this->contextMock);

        $this->captureContextMock->expects($this->never())
            ->method('generate');

        $this->expectException(GraphQlInputException::class);

        $this->makeResolver(true)->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            null,
            []
        );
    }

    public function testResolveThrowsCleanlyWhenMethodInactive(): void
    {
        // A disabled payment method must fail with a clean "not available" error before any
        // config-credential or gateway work (never a missing-REST-credentials StateException).
        $this->captureContextMock->expects($this->never())
            ->method('generate');

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessageMatches('/not (available|active|enabled)/i');

        $this->makeResolver(false)->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            null,
            ['input' => []]
        );
    }

    public function testResolveWrapsLocalizedExceptionAsGraphQlInputException(): void
    {
        // Non-GraphQL LocalizedExceptions (e.g. StateException for missing REST credentials) are
        // masked as "Internal server error" outside developer mode unless rethrown as a
        // client-aware GraphQL exception carrying the original message.
        $this->captureContextMock->method('generate')
            ->willThrowException(
                new StateException(__('Missing CyberSource REST Secret Key. Please check configuration.'))
            );

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Missing CyberSource REST Secret Key. Please check configuration.');

        $this->makeResolver(true)->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            null,
            ['input' => []]
        );
    }

    public function testResolveRethrowsClientAwareExceptionsUnwrapped(): void
    {
        // GraphQl* exceptions are already client-safe; wrapping them would change their category
        // (an authorization denial must not surface as an input error).
        $this->captureContextMock->method('generate')
            ->willThrowException(
                new GraphQlAuthorizationException(__('The current user cannot perform operations on cart "bad"'))
            );

        $this->expectException(GraphQlAuthorizationException::class);

        $this->makeResolver(true)->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            null,
            ['input' => ['cartId' => 'bad']]
        );
    }

    /**
     * Build the resolver with the payment method active or inactive.
     *
     * @param bool $methodActive
     * @return CaptureContext
     */
    private function makeResolver(bool $methodActive): CaptureContext
    {
        $config = $this->createMock(Config::class);
        $config->method('moduleIsActive')->willReturn($methodActive);

        return new CaptureContext(
            $this->graphQLMock,
            $this->captureContextMock,
            $config
        );
    }
}
