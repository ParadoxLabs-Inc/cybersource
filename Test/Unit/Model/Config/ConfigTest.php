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
     * @dataProvider ucAutoPlaceOrderDataProvider
     */
    #[DataProvider('ucAutoPlaceOrderDataProvider')]
    public function testIsUcAutoPlaceOrderEnabled(?string $configValue, bool $expected): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/uc_auto_place_order', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($configValue);

        $this->assertSame($expected, $this->config->isUcAutoPlaceOrderEnabled());
    }

    public static function ucAutoPlaceOrderDataProvider(): array
    {
        return [
            'enabled' => ['1', true],
            'disabled' => ['0', false],
            'unset' => [null, false],
        ];
    }

    /**
     * @dataProvider ucReviewStepDataProvider
     */
    #[DataProvider('ucReviewStepDataProvider')]
    public function testIsUcReviewStepEnabled(?string $configValue, bool $expected): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/uc_review_step', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($configValue);

        $this->assertSame($expected, $this->config->isUcReviewStepEnabled());
    }

    public static function ucReviewStepDataProvider(): array
    {
        return [
            'enabled' => ['1', true],
            'disabled' => ['0', false],
            // Unset must read as off: the field defaults to 0 and an absent value is not consent.
            'unset' => [null, false],
        ];
    }

    /**
     * The uc_billing_type fallback must agree with the config.xml default (NONE): an empty stored
     * value and an unconfigured install have to produce the same drop-in.
     */
    public function testGetUcBillingTypeDefaultsToNone(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/uc_billing_type', ScopeInterface::SCOPE_STORE, null)
            ->willReturn(null);

        $this->assertSame('NONE', $this->config->getUcBillingType());
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

    /**
     * @dataProvider payerAuthEnabledDataProvider
     */
    #[DataProvider('payerAuthEnabledDataProvider')]
    public function testIsPayerAuthEnabled(?string $flag, bool $expected): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($flag);

        $this->assertSame($expected, $this->config->isPayerAuthEnabled());
    }

    public static function payerAuthEnabledDataProvider(): array
    {
        return [
            'enabled' => ['1', true],
            'disabled' => ['0', false],
            'unset' => [null, false],
        ];
    }

    /**
     * @dataProvider payerAuthEnabledDataProvider
     */
    #[DataProvider('payerAuthEnabledDataProvider')]
    public function testIsPayerAuthRequired(?string $flag, bool $expected): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/payer_auth_required', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($flag);

        $this->assertSame($expected, $this->config->isPayerAuthRequired());
    }

    /**
     * @dataProvider payerAuthEnabledForTypeDataProvider
     */
    #[DataProvider('payerAuthEnabledForTypeDataProvider')]
    public function testIsPayerAuthEnabledForType(
        string $flag,
        ?string $cardTypes,
        string $ccType,
        bool $expected
    ): void {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                ['payment/paradoxlabs_cybersource/cardinal_active', ScopeInterface::SCOPE_STORE, null, $flag],
                ['payment/paradoxlabs_cybersource/cardinal_card_types', ScopeInterface::SCOPE_STORE, null, $cardTypes],
            ]);

        $this->assertSame($expected, $this->config->isPayerAuthEnabledForType($ccType));
    }

    public static function payerAuthEnabledForTypeDataProvider(): array
    {
        return [
            'type in list' => ['1', 'AE,VI,MC', 'VI', true],
            'first type in list' => ['1', 'AE,VI,MC', 'AE', true],
            'type not in list' => ['1', 'AE,VI,MC', 'DI', false],
            'feature off' => ['0', 'AE,VI,MC', 'VI', false],
            'empty config value' => ['1', '', 'VI', false],
            'unset config value' => ['1', null, 'VI', false],
        ];
    }

    /**
     * @dataProvider payerAuthReturnOriginsDataProvider
     * @param string|null $value
     * @param string[] $expected
     */
    public function testGetPayerAuthReturnOrigins(?string $value, array $expected): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/payer_auth_return_origins', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($value);

        $this->assertSame($expected, $this->config->getPayerAuthReturnOrigins());
    }

    /**
     * @return array<string, array{0: string|null, 1: string[]}>
     */
    public static function payerAuthReturnOriginsDataProvider(): array
    {
        return [
            'unset' => [null, []],
            'empty' => ['', []],
            'whitespace only' => ["  \n \n", []],
            'single' => ['https://pwa.example.net', ['https://pwa.example.net']],
            'multi-line with blanks and whitespace' => [
                "https://pwa.example.net\n\n  https://app.example.net:8443  \n",
                ['https://pwa.example.net', 'https://app.example.net:8443'],
            ],
            'windows line endings' => [
                "https://a.example.net\r\nhttps://b.example.net",
                ['https://a.example.net', 'https://b.example.net'],
            ],
            'lowercased' => ['HTTPS://PWA.Example.NET', ['https://pwa.example.net']],
        ];
    }

    private function setupSandboxMode(bool $isSandbox): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('payment/paradoxlabs_cybersource/test', ScopeInterface::SCOPE_STORE, null)
            ->willReturn($isSandbox ? '1' : '0');
    }
}
