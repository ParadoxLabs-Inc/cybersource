<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Api\GraphQL\UnifiedCheckout;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ParadoxLabs\CyberSource\Model\Api\GraphQL\UnifiedCheckout\CaptureContext;
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

    private CaptureContext $resolver;
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

        $this->resolver = new CaptureContext(
            $this->graphQLMock,
            $this->captureContextMock
        );
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

        $result = $this->resolver->resolve(
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

        $this->resolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            null,
            []
        );
    }
}
