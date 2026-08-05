<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Controller\Payerauth;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Escaper;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use ParadoxLabs\CyberSource\Controller\Payerauth\Callback;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\MessageProtocol;
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
    private ResultFactory|MockObject $resultFactoryMock;
    private Escaper|MockObject $escaperMock;
    private SecureHtmlRenderer|MockObject $secureRendererMock;

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

        $this->resultFactoryMock = $this->createMock(ResultFactory::class);
        $this->resultFactoryMock->method('create')
            ->with(ResultFactory::TYPE_RAW)
            ->willReturn($this->resultMock);

        $this->escaperMock = $this->createMock(Escaper::class);
        $this->escaperMock->method('escapeHtml')
            ->willReturnCallback(static fn ($value): string => (string)$value);

        $this->secureRendererMock = $this->createMock(SecureHtmlRenderer::class);
        $this->secureRendererMock->method('renderTag')
            ->willReturnCallback(
                static fn (string $tag, array $attributes, ?string $content, bool $textContent): string
                    => '<' . $tag . '>' . $content . '</' . $tag . '>'
            );

        $this->controller = new Callback(
            $this->resultFactoryMock,
            $this->escaperMock,
            $this->secureRendererMock
        );
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
            "window.parent.postMessage({source: '" . MessageProtocol::MESSAGE_TAG . "', event: 'return'}, '*');",
            $this->body
        );
        $this->assertStringContainsString('You may close this window.', $this->body);
    }

    public function testScriptTagIsRenderedThroughSecureHtmlRenderer(): void
    {
        $this->secureRendererMock->expects($this->once())
            ->method('renderTag')
            ->with('script', [], $this->stringContains('postMessage'), false);

        $this->controller->execute();
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
