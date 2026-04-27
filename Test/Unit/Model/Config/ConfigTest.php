<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
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
     * @dataProvider secureAcceptEndpointDataProvider
     */
    public function testGetSecureAcceptEndpoint(bool $isSandbox, string $path, string $expected): void
    {
        $this->setupSandboxMode($isSandbox);

        $this->assertSame($expected, $this->config->getSecureAcceptEndpoint($path));
    }

    public static function secureAcceptEndpointDataProvider(): array
    {
        return [
            'sandbox with path' => [
                true,
                '/embedded/token/create',
                'https://testsecureacceptance.cybersource.com/embedded/token/create',
            ],
            'live with path' => [
                false,
                '/embedded/token/create',
                'https://secureacceptance.cybersource.com/embedded/token/create',
            ],
        ];
    }

    /**
     * @dataProvider soapWsdlDataProvider
     */
    public function testGetSoapWsdl(bool $isSandbox, string $expected): void
    {
        $this->setupSandboxMode($isSandbox);

        $this->assertSame($expected, $this->config->getSoapWsdl());
    }

    public static function soapWsdlDataProvider(): array
    {
        return [
            'sandbox' => [
                true,
                'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.224.wsdl',
            ],
            'live' => [
                false,
                'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.224.wsdl',
            ],
        ];
    }

    /**
     * @dataProvider fingerprintOrgIdDataProvider
     */
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

    public function testIsPayerAuthEnabledReturnsTrueWhenAllKeysPresent(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/cardinal_org_unit_id', ScopeInterface::SCOPE_STORE, null, 'org123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key_id', ScopeInterface::SCOPE_STORE, null, 'keyid123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key', ScopeInterface::SCOPE_STORE, null, 'secret123'],
            ]);

        $this->assertTrue($this->config->isPayerAuthEnabled());
    }

    public function testIsPayerAuthEnabledReturnsFalseWhenDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '0'],
            ]);

        $this->assertFalse($this->config->isPayerAuthEnabled());
    }

    public function testIsPayerAuthEnabledReturnsFalseWhenOrgUnitIdMissing(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/cardinal_org_unit_id', ScopeInterface::SCOPE_STORE, null, ''],
            ]);

        $this->assertFalse($this->config->isPayerAuthEnabled());
    }

    public function testIsPayerAuthEnabledReturnsFalseWhenSecretKeyIdMissing(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/cardinal_org_unit_id', ScopeInterface::SCOPE_STORE, null, 'org123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key_id', ScopeInterface::SCOPE_STORE, null, ''],
            ]);

        $this->assertFalse($this->config->isPayerAuthEnabled());
    }

    public function testIsPayerAuthEnabledReturnsFalseWhenSecretKeyMissing(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/cardinal_org_unit_id', ScopeInterface::SCOPE_STORE, null, 'org123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key_id', ScopeInterface::SCOPE_STORE, null, 'keyid123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key', ScopeInterface::SCOPE_STORE, null, ''],
            ]);

        $this->assertFalse($this->config->isPayerAuthEnabled());
    }

    public function testIsPayerAuthEnabledForTypeReturnsTrueWhenTypeInList(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/cardinal_org_unit_id', ScopeInterface::SCOPE_STORE, null, 'org123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key_id', ScopeInterface::SCOPE_STORE, null, 'keyid123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key', ScopeInterface::SCOPE_STORE, null, 'secret123'],
                ['payment/paradoxlabs_cybersource/cardinal_card_types', ScopeInterface::SCOPE_STORE, null, 'VI,MC,AE'],
            ]);

        $this->assertTrue($this->config->isPayerAuthEnabledForType('VI'));
        $this->assertTrue($this->config->isPayerAuthEnabledForType('MC'));
        $this->assertTrue($this->config->isPayerAuthEnabledForType('AE'));
    }

    public function testIsPayerAuthEnabledForTypeReturnsFalseWhenTypeNotInList(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '1'],
                ['payment/paradoxlabs_cybersource/cardinal_org_unit_id', ScopeInterface::SCOPE_STORE, null, 'org123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key_id', ScopeInterface::SCOPE_STORE, null, 'keyid123'],
                ['payment/paradoxlabs_cybersource/cardinal_secret_key', ScopeInterface::SCOPE_STORE, null, 'secret123'],
                ['payment/paradoxlabs_cybersource/cardinal_card_types', ScopeInterface::SCOPE_STORE, null, 'VI,MC'],
            ]);

        $this->assertFalse($this->config->isPayerAuthEnabledForType('DI'));
    }

    public function testIsPayerAuthEnabledForTypeReturnsFalseWhenPayerAuthDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, '0'],
                ['payment/paradoxlabs_cybersource/cardinal_card_types', ScopeInterface::SCOPE_STORE, null, 'VI,MC'],
            ]);

        $this->assertFalse($this->config->isPayerAuthEnabledForType('VI'));
    }

    private function setupSandboxMode(bool $isSandbox): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($isSandbox ? '1' : '0');
    }
}
