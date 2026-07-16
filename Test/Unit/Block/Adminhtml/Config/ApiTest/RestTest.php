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
use ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\Rest;
use ParadoxLabs\CyberSource\Model\Method;
use ParadoxLabs\CyberSource\Model\Service\Rest as RestClient;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Method\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\Rest
 */
class RestTest extends TestCase
{
    private const VALID_KEY_ID = '08c94330-f618-42a3-b09d-e1e43be5efda';
    private const VALID_KEY = 'yBJxy6LjM2TmcPGu+GaJrHtkke5m+z+YVhY6DEfM3jUA';

    private Rest $block;
    private RestClient|MockObject $restClientMock;
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
        $this->requestMock->method('getParam')->willReturn('');

        $contextMock = $this->createMock(Context::class);
        $contextMock->method('getRequest')->willReturn($this->requestMock);

        $this->storeFactoryMock = $this->createMock(StoreFactory::class);
        $this->restClientMock = $this->createMock(RestClient::class);

        $this->methodMock = $this->createMock(Method::class);
        $this->methodMock->method('getConfigData')
            ->willReturnCallback(fn($key) => $this->methodConfig[$key] ?? null);

        $methodFactory = $this->createMock(Factory::class);
        $methodFactory->method('getMethodInstance')->willReturn($this->methodMock);

        $this->block = new Rest(
            $contextMock,
            $this->createMock(Data::class),
            $this->storeFactoryMock,
            $this->createMock(WebsiteFactory::class),
            $methodFactory,
            $this->restClientMock,
            [],
        );
    }

    /**
     * Populate valid-looking credentials.
     */
    private function primeValidCredentials(bool $sandbox = true): void
    {
        $this->methodConfig = [
            'rest_secret_key_id' => self::VALID_KEY_ID,
            'rest_secret_key' => self::VALID_KEY,
            'test' => $sandbox ? '1' : '0',
        ];
    }

    /**
     * Invoke a protected method on the block.
     */
    private function invoke(string $name, array $args = []): mixed
    {
        $method = new ReflectionMethod(Rest::class, $name);
        $method->setAccessible(true);

        return $method->invoke($this->block, ...$args);
    }

    private function testApi(): string
    {
        return (string)$this->invoke('testApi');
    }

    /**
     * The credentials probe hits a known-nonexistent transaction: a 404 proves the keys
     * authenticated, and is therefore SUCCESS, not failure.
     */
    public function testApiReportsSuccessWhenProbeReturns404(): void
    {
        $this->primeValidCredentials();

        $this->restClientMock->expects($this->once())
            ->method('get')
            ->with('/tss/v2/transactions/1')
            ->willThrowException(new \Exception('The requested resource does not exist', 404));

        $this->assertStringContainsString('connected successfully', $this->testApi());
    }

    /**
     * The success string must contain the literal 'success' -- the parent's _getElementHtml()
     * colors the result green by substring match on it.
     */
    public function testApiSuccessMessageContainsSuccessKeywordForGreenRendering(): void
    {
        $this->primeValidCredentials();
        $this->restClientMock->method('get')->willThrowException(new \Exception('nope', 404));

        $this->assertStringContainsString('success', $this->testApi());
    }

    public function testApiReportsSandboxWhenTestModeEnabled(): void
    {
        $this->primeValidCredentials(true);
        $this->restClientMock->method('get')->willThrowException(new \Exception('nope', 404));

        $this->assertStringContainsString('SANDBOX', $this->testApi());
    }

    public function testApiReportsProductionWhenTestModeDisabled(): void
    {
        $this->primeValidCredentials(false);
        $this->restClientMock->method('get')->willThrowException(new \Exception('nope', 404));

        $this->assertStringContainsString('PRODUCTION', $this->testApi());
    }

    /**
     * A 401 is the definitive "bad keys" signal and must be reported as such.
     */
    public function testApiReportsInvalidCredentialsOn401(): void
    {
        $this->primeValidCredentials();

        $this->restClientMock->method('get')
            ->willThrowException(new \Exception('Authentication Failed', 401));

        $result = $this->testApi();

        $this->assertStringContainsString('Your API credentials are invalid.', $result);
        $this->assertStringNotContainsString('connected successfully', $result);
    }

    /**
     * Any other transport/server error must surface to the admin, not be reported as success.
     */
    public function testApiSurfacesUnexpectedErrors(): void
    {
        $this->primeValidCredentials();

        $this->restClientMock->method('get')
            ->willThrowException(new \Exception('Connection timed out', 500));

        $result = $this->testApi();

        $this->assertStringContainsString('Connection timed out', $result);
        $this->assertStringNotContainsString('connected successfully', $result);
    }

    /**
     * Every failure path must carry the user-manual pointer.
     */
    public function testApiFailureIncludesUserManualInstruction(): void
    {
        $this->primeValidCredentials();
        $this->restClientMock->method('get')
            ->willThrowException(new \Exception('Authentication Failed', 401));

        $this->assertStringContainsString('User Manual', $this->testApi());
    }

    /**
     * The probe must be scoped to the config scope being edited, so a website-level key override is
     * what actually gets tested.
     */
    public function testApiScopesRestClientToTheCurrentStore(): void
    {
        $this->primeValidCredentials();

        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(3);
        $this->storeFactoryMock->method('create')->willReturn($store);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')
            ->willReturnCallback(static fn($key) => $key === 'store' ? '3' : '');

        $context = $this->createMock(Context::class);
        $context->method('getRequest')->willReturn($request);

        $methodFactory = $this->createMock(Factory::class);
        $methodFactory->method('getMethodInstance')->willReturn($this->methodMock);

        $block = new Rest(
            $context,
            $this->createMock(Data::class),
            $this->storeFactoryMock,
            $this->createMock(WebsiteFactory::class),
            $methodFactory,
            $this->restClientMock,
            [],
        );

        $this->restClientMock->expects($this->once())
            ->method('setStoreId')
            ->with(3);
        $this->restClientMock->method('get')->willThrowException(new \Exception('nope', 404));

        $method = new ReflectionMethod(Rest::class, 'testApi');
        $method->setAccessible(true);
        $method->invoke($block);
    }

    /**
     * Blank credentials must be caught locally -- no pointless API call.
     */
    public function testApiRejectsEmptyCredentialsWithoutCallingTheApi(): void
    {
        $this->methodConfig = ['rest_secret_key_id' => '', 'rest_secret_key' => ''];

        $this->restClientMock->expects($this->never())->method('get');

        $this->assertStringContainsString('Please complete all of these settings', $this->testApi());
    }

    /**
     * The Secret Key ID is a UUID. Anything else is a paste error (commonly the merchant id or the
     * key itself) and must be caught before burning an API call.
     */
    public function testCheckFormFactorRejectsNonUuidKeyId(): void
    {
        $this->methodConfig = [
            'rest_secret_key_id' => 'not-a-uuid',
            'rest_secret_key' => self::VALID_KEY,
        ];

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Secret Key ID is not in the expected format');

        $this->invoke('checkFormFactor');
    }

    /**
     * The Secret Key is a base64 blob well over 32 chars; a short value is a paste error.
     */
    public function testCheckFormFactorRejectsShortSecretKey(): void
    {
        $this->methodConfig = [
            'rest_secret_key_id' => self::VALID_KEY_ID,
            'rest_secret_key' => 'tooshort',
        ];

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Secret Key is shorter than expected');

        $this->invoke('checkFormFactor');
    }

    public function testCheckFormFactorAcceptsWellFormedCredentials(): void
    {
        $this->primeValidCredentials();

        $this->expectNotToPerformAssertions();

        $this->invoke('checkFormFactor');
    }

    /**
     * A 32-char key is exactly at the boundary and must be accepted.
     */
    public function testCheckFormFactorAcceptsSecretKeyAtMinimumLength(): void
    {
        $this->methodConfig = [
            'rest_secret_key_id' => self::VALID_KEY_ID,
            'rest_secret_key' => str_repeat('a', 32),
        ];

        $this->expectNotToPerformAssertions();

        $this->invoke('checkFormFactor');
    }

    public function testCheckFormFactorAcceptsUppercaseUuidKeyId(): void
    {
        $this->methodConfig = [
            'rest_secret_key_id' => strtoupper(self::VALID_KEY_ID),
            'rest_secret_key' => self::VALID_KEY,
        ];

        $this->expectNotToPerformAssertions();

        $this->invoke('checkFormFactor');
    }

    /**
     * Form-factor failures must be reported to the admin, and must not reach the API.
     */
    public function testApiReportsFormFactorFailureWithoutCallingTheApi(): void
    {
        $this->methodConfig = [
            'rest_secret_key_id' => 'my_merchant_id',
            'rest_secret_key' => self::VALID_KEY,
        ];

        $this->restClientMock->expects($this->never())->method('get');

        $this->assertStringContainsString('Secret Key ID is not in the expected format', $this->testApi());
    }

    /**
     * Corrupt (non-ASCII) decrypted credentials get the re-enter message, not a form-factor or API
     * error.
     */
    public function testApiReportsCorruptCredentials(): void
    {
        $this->methodConfig = [
            'rest_secret_key_id' => self::VALID_KEY_ID,
            'rest_secret_key' => "yBJxy6LjM2TmcPGu\xC3\xA9GaJrHtkke5m+z+YVhY6DEfM3jU=",
        ];

        $this->restClientMock->expects($this->never())->method('get');

        $this->assertStringContainsString('may be corrupted', $this->testApi());
    }

    public function testCredentialKeysAreTheRestSecretKeyPair(): void
    {
        $this->assertSame(
            ['rest_secret_key_id', 'rest_secret_key'],
            Rest::CREDENTIAL_KEYS
        );
    }
}
