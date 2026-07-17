<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Block\Adminhtml\Config\ApiTest;

use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\AbstractTest;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Method;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Method\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\AbstractTest
 */
class AbstractTestTest extends TestCase
{
    private AbstractTest $block;
    private Factory|MockObject $methodFactoryMock;
    private Method|MockObject $methodMock;
    private RequestInterface|MockObject $requestMock;
    private StoreFactory|MockObject $storeFactoryMock;

    /**
     * @var array<string, mixed>
     */
    private array $methodConfig = [];

    protected function setUp(): void
    {
        // Magento\Backend\Block\Template's constructor pulls its json/directory helpers straight off
        // the global ObjectManager, so the block cannot be built without one primed.
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('get')->willReturnCallback(
            fn(string $type) => match ($type) {
                JsonHelper::class => $this->createMock(JsonHelper::class),
                DirectoryHelper::class => $this->createMock(DirectoryHelper::class),
                default => null,
            }
        );
        ObjectManager::setInstance($objectManager);

        $this->requestMock = $this->createMock(RequestInterface::class);

        $contextMock = $this->createMock(Context::class);
        $contextMock->method('getRequest')->willReturn($this->requestMock);

        $this->storeFactoryMock = $this->createMock(StoreFactory::class);

        $this->methodMock = $this->createMock(Method::class);
        $this->methodMock->method('getConfigData')
            ->willReturnCallback(fn($key) => $this->methodConfig[$key] ?? null);

        $this->methodFactoryMock = $this->createMock(Factory::class);
        $this->methodFactoryMock->method('getMethodInstance')->willReturn($this->methodMock);

        $this->block = new class (
            $contextMock,
            $this->createMock(Data::class),
            $this->storeFactoryMock,
            $this->createMock(WebsiteFactory::class),
            $this->methodFactoryMock,
            []
        ) extends AbstractTest {
            /**
             * @var mixed
             */
            public $apiResult = 'stub result';

            protected function testApi()
            {
                return $this->apiResult;
            }
        };
    }

    /**
     * Invoke a protected method on the block.
     */
    private function invoke(string $name, array $args = []): mixed
    {
        $method = new ReflectionMethod($this->block, $name);

        return $method->invoke($this->block, ...$args);
    }

    /**
     * Incomplete credentials must be reported as "complete and save", not fired at the API.
     */
    public function testCheckRequiredFieldsThrowsWhenAFieldIsEmpty(): void
    {
        $this->methodConfig = ['key_id' => 'abc', 'key' => ''];

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Please complete all of these settings and save to test.');

        $this->invoke('checkRequiredFields', [['key_id', 'key']]);
    }

    public function testCheckRequiredFieldsThrowsWhenAFieldIsMissingEntirely(): void
    {
        $this->methodConfig = ['key_id' => 'abc'];

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Please complete all of these settings and save to test.');

        $this->invoke('checkRequiredFields', [['key_id', 'key']]);
    }

    /**
     * Non-ASCII in a decrypted credential means the encryption key changed or the row is corrupt.
     * That is a distinct, actionable failure and must not be reported as "incomplete".
     */
    public function testCheckRequiredFieldsThrowsCorruptionMessageOnNonAsciiValue(): void
    {
        $this->methodConfig = ['key_id' => 'abc', 'key' => "d\xC3\xA9f\xC3\xA9ctive"];

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Please re-enter your API keys. They may be corrupted.');

        $this->invoke('checkRequiredFields', [['key_id', 'key']]);
    }

    public function testCheckRequiredFieldsPassesForCompleteAsciiCredentials(): void
    {
        $this->methodConfig = [
            'key_id' => '08c94330-f618-42a3-b09d-e1e43be5efda',
            'key' => 'yBJxy6LjM2TmcPGu+GaJrHtkke5m+z+YVhY6DEfM3jU=',
        ];

        $this->expectNotToPerformAssertions();

        $this->invoke('checkRequiredFields', [['key_id', 'key']]);
    }

    /**
     * An empty required-field list is a no-op, not an error.
     */
    public function testCheckRequiredFieldsWithNoFieldsPasses(): void
    {
        $this->expectNotToPerformAssertions();

        $this->invoke('checkRequiredFields', [[]]);
    }

    /**
     * The base class declares no credential keys; concrete tests must opt in.
     */
    public function testBaseCredentialKeysAreEmpty(): void
    {
        $this->assertSame([], AbstractTest::CREDENTIAL_KEYS);
    }

    /**
     * The method instance must be scoped to the config scope being edited, or the admin tests the
     * default scope's keys while looking at a website/store override.
     */
    public function testGetMethodResolvesTheCyberSourceMethodScopedToTheCurrentStore(): void
    {
        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(7);
        $this->storeFactoryMock->method('create')->willReturn($store);

        $this->requestMock->method('getParam')
            ->willReturnCallback(static fn($key) => $key === 'store' ? '7' : '');

        $this->methodFactoryMock->expects($this->once())
            ->method('getMethodInstance')
            ->with(Config::CODE)
            ->willReturn($this->methodMock);

        $this->methodMock->expects($this->once())
            ->method('setStore')
            ->with(7);

        $this->assertSame($this->methodMock, $this->invoke('getMethod'));
    }

    /**
     * getMethod() is called repeatedly across a single test run; it must resolve and re-scope the
     * method exactly once.
     */
    public function testGetMethodIsMemoized(): void
    {
        $this->requestMock->method('getParam')->willReturn('');

        $this->methodFactoryMock->expects($this->once())
            ->method('getMethodInstance')
            ->willReturn($this->methodMock);

        $this->methodMock->expects($this->once())->method('setStore');

        $first = $this->invoke('getMethod');
        $second = $this->invoke('getMethod');

        $this->assertSame($first, $second);
    }

    public function testGetMethodUsesDefaultScopeWhenNoStoreOrWebsiteParam(): void
    {
        $this->requestMock->method('getParam')->willReturn('');

        $this->methodMock->expects($this->once())
            ->method('setStore')
            ->with(0);

        $this->invoke('getMethod');
    }

    /**
     * Every failure message links to the user manual; the link must point at the CyberSource
     * manual and open in a new tab so the admin doesn't lose unsaved config.
     */
    public function testGetUserManualInstructionLinksToTheCyberSourceManual(): void
    {
        $instruction = (string)$this->invoke('getUserManualInstruction');

        $this->assertStringContainsString(AbstractTest::USER_MANUAL_URL, $instruction);
        $this->assertStringContainsString('ParadoxLabs-CyberSource-M2-user-manual.pdf', $instruction);
        $this->assertStringContainsString('target="_blank"', $instruction);
        $this->assertStringContainsString('User Manual', $instruction);
    }

    /**
     * The URL is a `static::` reference so subclasses can retarget it; the base must supply the
     * CyberSource default.
     */
    public function testUserManualUrlIsHttps(): void
    {
        $this->assertStringStartsWith('https://', AbstractTest::USER_MANUAL_URL);
    }

    /**
     * A result string containing "success" renders green; anything else renders red. This is the
     * contract the concrete testApi() implementations write their messages against.
     */
    public function testElementHtmlRendersSuccessResultInGreen(): void
    {
        $this->block->apiResult = 'REST API connected successfully. (SANDBOX)';

        $html = $this->invoke('_getElementHtml', [
            $this->createMock(\Magento\Framework\Data\Form\Element\AbstractElement::class),
        ]);

        $this->assertStringContainsString('color:#0a0', $html);
        $this->assertStringContainsString('connected successfully', $html);
    }

    public function testElementHtmlRendersFailureResultInRed(): void
    {
        $this->block->apiResult = 'Your API credentials are invalid.';

        $html = $this->invoke('_getElementHtml', [
            $this->createMock(\Magento\Framework\Data\Form\Element\AbstractElement::class),
        ]);

        $this->assertStringContainsString('color:#D40707', $html);
    }
}
