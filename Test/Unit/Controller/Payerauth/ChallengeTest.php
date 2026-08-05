<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Controller\Payerauth;

use Magento\Csp\Api\Data\PolicyInterface;
use Magento\Csp\Helper\CspNonceProvider;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Escaper;
use ParadoxLabs\CyberSource\Controller\Payerauth\Challenge;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\MessageProtocol;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Controller\Payerauth\Challenge
 */
class ChallengeTest extends TestCase
{
    private Challenge $controller;
    private Raw|MockObject $resultMock;
    private ResultFactory|MockObject $resultFactoryMock;
    private Escaper|MockObject $escaperMock;

    /**
     * @var string
     */
    private string $body = '';

    protected function setUp(): void
    {
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
        $this->escaperMock->method('escapeHtmlAttr')
            ->willReturnCallback(static fn ($value): string => (string)$value);

        $this->controller = new Challenge($this->resultFactoryMock, $this->escaperMock);
    }

    /**
     * Build a controller with nonce support present, as on Magento 2.4.7+.
     *
     * @param string $nonce
     * @return Challenge
     */
    private function controllerWithNonce(string $nonce): Challenge
    {
        $nonceProvider = $this->createMock(CspNonceProvider::class);
        $nonceProvider->method('generateNonce')->willReturn($nonce);

        return new Challenge($this->resultFactoryMock, $this->escaperMock, $nonceProvider);
    }

    public function testIsGetOnlyAndNeedsNoCsrfExemption(): void
    {
        $this->assertInstanceOf(HttpGetActionInterface::class, $this->controller);
        $this->assertNotInstanceOf(CsrfAwareActionInterface::class, $this->controller);
    }

    public function testExecuteReturnsAnHtmlRawResult(): void
    {
        $this->resultMock->expects($this->once())
            ->method('setHeader')
            ->with('Content-Type', 'text/html; charset=UTF-8');

        $this->assertSame($this->resultMock, $this->controller->execute());
    }

    public function testScriptCarriesTheCspNonceWhenThePlatformSupportsIt(): void
    {
        $this->controllerWithNonce('nonce-value-123')->execute();

        $this->assertStringContainsString('<script nonce="nonce-value-123">', $this->body);
    }

    public function testScriptOmitsTheNonceAttributeOnPlatformsWithoutNonceSupport(): void
    {
        $this->controller->execute();

        $this->assertStringContainsString('<script>', $this->body);
        $this->assertStringNotContainsString('nonce', $this->body);
    }

    public function testBodyUsesTheSharedProtocolTag(): void
    {
        $this->controller->execute();

        $this->assertStringContainsString(
            "var TAG = '" . MessageProtocol::MESSAGE_TAG . "';",
            $this->body
        );
    }

    public function testBodyAnnouncesReadyAndRelaysReturnToItsOwnOrigin(): void
    {
        $this->controller->execute();

        $this->assertStringContainsString(
            "window.parent.postMessage({source: TAG, event: 'ready'}, ORIGIN);",
            $this->body
        );
        $this->assertStringContainsString(
            "window.parent.postMessage({source: TAG, event: 'return'}, ORIGIN);",
            $this->body
        );
        $this->assertStringContainsString("var ORIGIN = window.location.origin;", $this->body);
    }

    public function testBodyOriginChecksTheChallengeMessageAndSourceChecksTheReturn(): void
    {
        $this->controller->execute();

        $this->assertStringContainsString(
            "data.event === 'challenge' && event.origin === ORIGIN",
            $this->body
        );
        $this->assertStringContainsString(
            "data.event === 'return' && frame && event.source === frame.contentWindow",
            $this->body
        );
    }

    public function testBodyPostsTheCreqToAValidatedHttpsAcsUrl(): void
    {
        $this->controller->execute();

        $this->assertStringContainsString("parsed.protocol !== 'https:'", $this->body);
        $this->assertStringContainsString("form.setAttribute('method', 'POST');", $this->body);
        $this->assertStringContainsString("form.setAttribute('action', acsUrl);", $this->body);
        $this->assertStringContainsString("form.setAttribute('target', 'pl-pa-acs');", $this->body);
        $this->assertStringContainsString("input.setAttribute('name', 'creq');", $this->body);
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
            'cavv' => ['cavv'],
        ];
    }

    public function testModifyCspKeepsAppliedPoliciesAndAddsHttpsFrameAndFormPolicies(): void
    {
        $applied = [new FetchPolicy('script-src', false, ['example.com'], [], true)];

        $result = $this->controller->modifyCsp($applied);

        $this->assertCount(3, $result);
        $this->assertSame($applied[0], $result[0]);

        foreach ($result as $policy) {
            $this->assertInstanceOf(PolicyInterface::class, $policy);
        }

        $byId = [];
        foreach ($result as $policy) {
            $byId[$policy->getId()] = $policy;
        }

        $this->assertArrayHasKey('frame-src', $byId);
        $this->assertArrayHasKey('form-action', $byId);

        foreach (['frame-src', 'form-action'] as $id) {
            /** @var FetchPolicy $policy */
            $policy = $byId[$id];

            $this->assertSame(['https'], $policy->getSchemeSources(), $id);
            $this->assertTrue($policy->isSelfAllowed(), $id);
            $this->assertFalse($policy->isNoneAllowed(), $id);
            $this->assertSame([], $policy->getHostSources(), $id);
        }
    }
}
