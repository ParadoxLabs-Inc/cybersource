<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Config\Config
 */
class ConfigTest extends TestCase
{
    private Config $config;
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->config = new Config($this->scopeConfigMock);
    }

    public function testSetStoreIdAndGetStoreId(): void
    {
        $this->assertNull($this->config->getStoreId());

        $result = $this->config->setStoreId(5);

        $this->assertSame($this->config, $result);
        $this->assertSame(5, $this->config->getStoreId());
    }

    public function testGetSolutionId(): void
    {
        $this->assertSame('DEQXVEEG', $this->config->getSolutionId());
    }

    /**
     * @dataProvider restEndpointDataProvider
     */
    #[DataProvider('restEndpointDataProvider')]
    public function testGetRestEndpoint(bool $isSandbox, string $path, string $expected): void
    {
        $this->setupSandboxMode($isSandbox);

        $this->assertSame($expected, $this->config->getRestEndpoint($path));
    }

    public static function restEndpointDataProvider(): array
    {
        return [
            'sandbox with path' => [
                true,
                '/pts/v2/payments',
                'https://apitest.cybersource.com/pts/v2/payments',
            ],
            'live with path' => [
                false,
                '/pts/v2/payments',
                'https://api.cybersource.com/pts/v2/payments',
            ],
            'sandbox empty path' => [
                true,
                '',
                'https://apitest.cybersource.com',
            ],
            'live empty path' => [
                false,
                '',
                'https://api.cybersource.com',
            ],
        ];
    }

    /**
     * @dataProvider fingerprintOrgIdDataProvider
     */
    #[DataProvider('fingerprintOrgIdDataProvider')]
    public function testGetFingerprintOrgId(bool $isSandbox, string $expected): void
    {
        $this->setupSandboxMode($isSandbox);

        $this->assertSame($expected, $this->config->getFingerprintOrgId());
    }

    public static function fingerprintOrgIdDataProvider(): array
    {
        return [
            'sandbox' => [true, '1snn5n9w'],
            'live' => [false, 'k8vif92e'],
        ];
    }

    public function testIsFingerprintEnabledReturnsTrueWhenFlagAndOrgIdPresent(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
            ]);

        $this->assertTrue($this->config->isFingerprintEnabled());
    }

    public function testIsFingerprintEnabledReturnsFalseWhenFlagDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '0'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
            ]);

        $this->assertFalse($this->config->isFingerprintEnabled());
    }

    public function testGetFingerprintSessionIdReturnsNullWhenDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '0'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
            ]);

        $this->assertNull($this->config->getFingerprintSessionId('session123'));
    }

    public function testGetFingerprintSessionIdReturnsRawWhenApiScope(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
            ]);

        $this->assertSame('session123', $this->config->getFingerprintSessionId('session123', null, true));
    }

    public function testGetFingerprintSessionIdReturnsPrefixedWhenNotApiScope(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/merchant_id', ScopeInterface::SCOPE_STORE, null, 'merchant123'],
            ]);

        $this->assertSame(
            'merchant123session456',
            $this->config->getFingerprintSessionId('session456', null, false)
        );
    }

    public function testGetFingerprintUrlReturnsNullWhenDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '0'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
            ]);

        $this->assertNull($this->config->getFingerprintUrl('session123'));
    }

    public function testGetFingerprintUrlBuildsCorrectUrl(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/merchant_id', ScopeInterface::SCOPE_STORE, null, 'merchant123'],
                ['payment/paradoxlabs_cybersource/fingerprint_domain', ScopeInterface::SCOPE_STORE, null, ''],
            ]);

        $result = $this->config->getFingerprintUrl('session456');

        $this->assertStringStartsWith('https://h.online-metrix.net/fp/tags.js?', $result);
        $this->assertStringContainsString('org_id=1snn5n9w', $result);
        $this->assertStringContainsString('session_id=merchant123session456', $result);
    }

    public function testGetFingerprintUrlUsesCustomDomain(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/fingerprint', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/merchant_id', ScopeInterface::SCOPE_STORE, null, 'merchant123'],
                ['payment/paradoxlabs_cybersource/fingerprint_domain', ScopeInterface::SCOPE_STORE, null, 'custom.domain.com'],
            ]);

        $result = $this->config->getFingerprintUrl('session456');

        $this->assertStringStartsWith('https://custom.domain.com/fp/tags.js?', $result);
    }

    /**
     * D10: completeMandate.type must follow Magento payment_action semantics — authorize_capture
     * is a sale (CAPTURE); everything else authorizes only (AUTH). Every consumer suite mocks
     * Config, so this mapping is the single point where an inversion would force-capture at
     * checkout for authorize-mode merchants; it must be pinned against the real class.
     *
     * @dataProvider completeMandateTypeDataProvider
     */
    #[DataProvider('completeMandateTypeDataProvider')]
    public function testGetUcCompleteMandateTypeMapsPaymentAction(
        string $paymentAction,
        string $expected
    ): void {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/payment_action', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($paymentAction);

        $this->assertSame($expected, $this->config->getUcCompleteMandateType());
    }

    public static function completeMandateTypeDataProvider(): array
    {
        return [
            'authorize maps to AUTH' => ['authorize', 'AUTH'],
            'authorize_capture maps to CAPTURE' => ['authorize_capture', 'CAPTURE'],
            'unset payment_action defaults to AUTH' => ['', 'AUTH'],
        ];
    }

    private function setupSandboxMode(bool $isSandbox): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($isSandbox ? '1' : '0');
    }
}
