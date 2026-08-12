<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Controller\Adminhtml\UnifiedCheckout;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use ParadoxLabs\CyberSource\Controller\Adminhtml\UnifiedCheckout\CaptureContext;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Backend;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ParadoxLabs\CyberSource\Controller\Adminhtml\UnifiedCheckout\CaptureContext
 */
class CaptureContextTest extends TestCase
{
    private const TEST_JWT = 'eyJ.capture.context';

    private CaptureContext $controller;
    private Backend|MockObject $captureContextMock;
    private Registry|MockObject $registryMock;
    private CustomerRepositoryInterface|MockObject $customerRepositoryMock;
    private RequestInterface|MockObject $requestMock;
    private Json|MockObject $resultMock;

    protected function setUp(): void
    {
        $this->captureContextMock = $this->createMock(Backend::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);

        $this->resultMock = $this->createMock(Json::class);
        $this->resultMock->method('setData')->willReturnSelf();
        $this->resultMock->method('setHttpResponseCode')->willReturnSelf();

        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($this->resultMock);

        $context = $this->createMock(Context::class);
        $context->method('getResultFactory')->willReturn($resultFactory);
        $context->method('getRequest')->willReturn($this->requestMock);

        $this->controller = new CaptureContext(
            $context,
            $this->captureContextMock,
            $this->registryMock,
            $this->customerRepositoryMock
        );
    }

    public function testExecuteReturnsCaptureContextJwt(): void
    {
        $this->requestMock->method('getParam')->with('id')->willReturn(null);

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

    public function testExecuteRegistersCustomerWhenIdPresent(): void
    {
        $this->requestMock->method('getParam')->with('id')->willReturn('42');

        $customer = $this->createMock(CustomerInterface::class);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with('42')
            ->willReturn($customer);

        $this->registryMock->expects($this->once())
            ->method('register')
            ->with('current_customer', $customer);

        $this->captureContextMock->method('generate')->willReturn(self::TEST_JWT);

        $this->resultMock->expects($this->once())
            ->method('setData')
            ->with(['captureContext' => self::TEST_JWT]);

        $this->controller->execute();
    }

    public function testExecuteSkipsCustomerRegistrationForNonNumericId(): void
    {
        // initCustomer guards on is_numeric: a non-numeric id must not hit the repository/registry.
        $this->requestMock->method('getParam')->with('id')->willReturn('abc');

        $this->customerRepositoryMock->expects($this->never())->method('getById');
        $this->registryMock->expects($this->never())->method('register');

        $this->captureContextMock->method('generate')->willReturn(self::TEST_JWT);

        $this->resultMock->expects($this->once())
            ->method('setData')
            ->with(['captureContext' => self::TEST_JWT]);

        $this->assertSame($this->resultMock, $this->controller->execute());
    }

    public function testExecuteReturnsErrorJsonOnFailure(): void
    {
        $this->requestMock->method('getParam')->with('id')->willReturn(null);

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
}
