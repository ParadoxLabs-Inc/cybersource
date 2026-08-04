<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Controller\Payerauth;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use ParadoxLabs\CyberSource\Controller\Payerauth\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Controller\Payerauth\Callback
 */
class CallbackTest extends TestCase
{
    private Callback $controller;
    private Raw|MockObject $resultMock;
    private RequestInterface|MockObject $requestMock;

    /**
     * @var string
     */
    private string $body = '';

    protected function setUp(): void
    {
        $this->requestMock = $this->createMock(RequestInterface::class);

        $this->resultMock = $this->createMock(Raw::class);
        $this->resultMock->method('setHeader')->willReturnSelf();
        $this->resultMock->method('setContents')
            ->willReturnCallback(function ($contents) {
                $this->body = (string)$contents;

                return $this->resultMock;
            });

        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory->method('create')
            ->with(ResultFactory::TYPE_RAW)
            ->willReturn($this->resultMock);

        $this->controller = new Callback($resultFactory);
    }

    public function testAcceptsBothPostAndGetDelivery(): void
    {
        $this->assertInstanceOf(HttpPostActionInterface::class, $this->controller);
        $this->assertInstanceOf(HttpGetActionInterface::class, $this->controller);
        $this->assertInstanceOf(CsrfAwareActionInterface::class, $this->controller);
    }

    public function testIsExemptFromFormKeyValidation(): void
    {
        $this->assertTrue($this->controller->validateForCsrf($this->requestMock));
        $this->assertNull($this->controller->createCsrfValidationException($this->requestMock));
    }

    public function testExecuteReturnsAnHtmlRawResult(): void
    {
        $this->resultMock->expects($this->once())
            ->method('setHeader')
            ->with('Content-Type', 'text/html; charset=UTF-8');

        $this->assertSame($this->resultMock, $this->controller->execute());
    }

    public function testBodyPostsTheReturnEventToTheParent(): void
    {
        $this->controller->execute();

        $this->assertStringContainsString(
            "window.parent.postMessage({source: 'pl-cybersource-payerauth', event: 'return'}, '*');",
            $this->body
        );
        $this->assertStringContainsString('You may close this window.', $this->body);
    }

    public function testMessagePayloadCarriesNoKeysBesidesSourceAndEvent(): void
    {
        $this->controller->execute();

        preg_match_all('/postMessage\((\{[^}]*})/', $this->body, $matches);

        $this->assertCount(1, $matches[1], 'Exactly one postMessage call is expected.');

        preg_match_all('/(\w+):/', $matches[1][0], $keys);

        $this->assertSame(['source', 'event'], $keys[1]);
    }

    /**
     * @dataProvider forbiddenMarkerProvider
     */
    public function testBodyLeaksNoSessionOrCustomerData(string $marker): void
    {
        $this->controller->execute();

        $this->assertStringNotContainsStringIgnoringCase($marker, $this->body);
    }

    /**
     * @return array<string, string[]>
     */
    public static function forbiddenMarkerProvider(): array
    {
        return [
            'form key' => ['form_key'],
            'session' => ['session'],
            'customer' => ['customer'],
            'quote' => ['quote'],
            'email' => ['@example'],
            'transient token' => ['transientToken'],
            'creq echo' => ['creq'],
            'cres echo' => ['cres'],
            'cavv' => ['cavv'],
        ];
    }
}
