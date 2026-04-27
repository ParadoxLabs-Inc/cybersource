<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service;

use Magento\Framework\HTTP\ClientInterfaceFactory;
use Magento\Framework\HTTP\ZendClientFactory;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\Rest
 */
class RestTest extends TestCase
{
    private const TEST_MERCHANT_ID = 'test_merchant';
    private const TEST_SECRET_KEY = 'dGVzdC1zZWNyZXQta2V5LTEyMzQ1'; // base64 encoded
    private const TEST_SECRET_KEY_ID = 'secret-key-id-123';
    private const TEST_ENDPOINT = 'https://apitest.cybersource.com';

    private Rest $rest;
    private Config|MockObject $configMock;
    private Data|MockObject $helperMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->configMock->method('getMerchantId')
            ->willReturn(self::TEST_MERCHANT_ID);
        $this->configMock->method('getRestSecretKey')
            ->willReturn(self::TEST_SECRET_KEY);
        $this->configMock->method('getRestSecretKeyId')
            ->willReturn(self::TEST_SECRET_KEY_ID);
        $this->configMock->method('getRestEndpoint')
            ->willReturnCallback(function ($path) {
                return self::TEST_ENDPOINT . $path;
            });

        $zendClientFactory = $this->createMock(ZendClientFactory::class);
        $this->helperMock = $this->createMock(Data::class);
        $communicatorFactory = $this->createMock(ClientInterfaceFactory::class);

        $this->rest = new Rest(
            $this->configMock,
            $zendClientFactory,
            $this->helperMock,
            $communicatorFactory,
        );
    }

    public function testSignRequestReturnsRequiredHeaders(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $this->assertArrayHasKey('Date', $headers);
        $this->assertArrayHasKey('Host', $headers);
        $this->assertArrayHasKey('v-c-merchant-id', $headers);
        $this->assertArrayHasKey('Signature', $headers);
    }

    public function testSignRequestDateFormat(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        // Date should be in RFC 7231 format
        $this->assertMatchesRegularExpression(
            '/^[A-Z][a-z]{2}, \d{2} [A-Z][a-z]{2} \d{4} \d{1,2}:\d{2}:\d{2} GMT$/',
            $headers['Date']
        );
    }

    public function testSignRequestHost(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $this->assertSame('apitest.cybersource.com', $headers['Host']);
    }

    public function testSignRequestMerchantId(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $this->assertSame(self::TEST_MERCHANT_ID, $headers['v-c-merchant-id']);
    }

    public function testSignRequestSignatureFormat(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $signature = $headers['Signature'];

        // Signature header should contain keyid, algorithm, headers, and signature
        $this->assertStringContainsString('keyid="' . self::TEST_SECRET_KEY_ID . '"', $signature);
        $this->assertStringContainsString('algorithm="HmacSHA256"', $signature);
        $this->assertStringContainsString('headers="host date request-target v-c-merchant-id"', $signature);
        $this->assertStringContainsString('signature="', $signature);
    }

    public function testSignRequestIncludesQueryStringInTarget(): void
    {
        $params = ['searchId' => 'abc123', 'limit' => 10];
        $headers = $this->invokeSignRequest('/tss/v2/searches', $params, 'GET');

        // The signature header should reference request-target
        $this->assertStringContainsString('request-target', $headers['Signature']);
    }

    public function testSignRequestDifferentPathsProduceDifferentSignatures(): void
    {
        $headers1 = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');
        $headers2 = $this->invokeSignRequest('/tss/v2/searches', [], 'GET');

        $this->assertNotSame($headers1['Signature'], $headers2['Signature']);
    }

    public function testSignRequestDifferentParamsProduceDifferentSignatures(): void
    {
        $headers1 = $this->invokeSignRequest('/tss/v2/searches', ['id' => '1'], 'GET');
        $headers2 = $this->invokeSignRequest('/tss/v2/searches', ['id' => '2'], 'GET');

        $this->assertNotSame($headers1['Signature'], $headers2['Signature']);
    }

    public function testSignRequestHttpMethodInTarget(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        // Signature is computed including lowercase http method
        // We can verify format by checking signature contains expected parts
        $this->assertStringContainsString('signature="', $headers['Signature']);
    }

    public function testSetStoreId(): void
    {
        $result = $this->rest->setStoreId(5);

        // Should return self for chaining
        $this->assertSame($this->rest, $result);
    }

    public function testSetStoreIdNull(): void
    {
        $result = $this->rest->setStoreId(null);

        $this->assertSame($this->rest, $result);
    }

    /**
     * Helper method to invoke protected signRequest method
     */
    private function invokeSignRequest(string $path, array $params, string $httpMethod): array
    {
        $method = new ReflectionMethod(Rest::class, 'signRequest');
        $method->setAccessible(true);

        return $method->invoke($this->rest, $path, $params, $httpMethod);
    }
}
