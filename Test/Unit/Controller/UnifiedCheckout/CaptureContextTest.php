<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Controller\UnifiedCheckout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Data\Form\FormKey\Validator;
use ParadoxLabs\CyberSource\Controller\UnifiedCheckout\CaptureContext;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Frontend;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ParadoxLabs\CyberSource\Controller\UnifiedCheckout\CaptureContext
 */
class CaptureContextTest extends TestCase
{
    private const TEST_JWT = 'eyJ.capture.context';

    private CaptureContext $controller;
    private Frontend|MockObject $captureContextMock;
    private Validator|MockObject $formKeyMock;
    private ResultFactory|MockObject $resultFactoryMock;
    private Json|MockObject $resultMock;

    protected function setUp(): void
    {
        $this->captureContextMock = $this->createMock(Frontend::class);
        $this->formKeyMock = $this->createMock(Validator::class);

        $this->resultMock = $this->createMock(Json::class);
        $this->resultMock->method('setData')->willReturnSelf();
        $this->resultMock->method('setHttpResponseCode')->willReturnSelf();

        $this->resultFactoryMock = $this->createMock(ResultFactory::class);
        $this->resultFactoryMock->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($this->resultMock);

        $context = $this->createMock(Context::class);
        $context->method('getResultFactory')->willReturn($this->resultFactoryMock);

        $this->controller = new CaptureContext(
            $context,
            $this->captureContextMock,
            $this->formKeyMock
        );
    }

    public function testExecuteReturnsCaptureContextJwt(): void
    {
        $this->captureContextMock->expects($this->once())
            ->method('generate')
            ->willReturn(self::TEST_JWT);

        $this->resultMock->expects($this->once())
            ->method('setData')
            ->with(['captureContext' => self::TEST_JWT]);

        $this->resultMock->expects($this->never())
            ->method('setHttpResponseCode');

        $this->assertSame($this->resultMock, $this->controller->execute());
    }

    public function testExecuteReturnsErrorJsonOnFailure(): void
    {
        $this->captureContextMock->method('generate')
            ->willThrowException(new RuntimeException('boom'));

        $this->resultMock->expects($this->once())
            ->method('setHttpResponseCode')
            ->with(400);

        $this->resultMock->expects($this->once())
            ->method('setData')
            ->with(['message' => 'boom']);

        $this->assertSame($this->resultMock, $this->controller->execute());
    }

    public function testValidateForCsrfDelegatesToFormKey(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $this->formKeyMock->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn(true);

        $this->assertTrue($this->controller->validateForCsrf($request));
    }

    public function testCreateCsrfValidationExceptionReturns403JsonResult(): void
    {
        $request = $this->createMock(RequestInterface::class);

        // CSRF failure must surface a 403 JSON result carrying an error message.
        $this->resultMock->expects($this->once())
            ->method('setHttpResponseCode')
            ->with(403);
        $this->resultMock->expects($this->once())
            ->method('setData')
            ->with($this->callback(static fn(array $data): bool => isset($data['message'])));

        $exception = $this->controller->createCsrfValidationException($request);

        $this->assertInstanceOf(InvalidRequestException::class, $exception);
    }
}
