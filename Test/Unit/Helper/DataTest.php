<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use ParadoxLabs\CyberSource\Helper\Data;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Helper\Data
 */
class DataTest extends TestCase
{
    private Data $helper;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        // Create minimal mocks for parent class dependencies
        $context = $this->createMock(Context::class);

        // Use ObjectManager to create the helper with mocked dependencies
        $this->helper = $objectManager->getObject(
            Data::class,
            [
                'context' => $context,
            ]
        );
    }

    /**
     * @dataProvider avsCodesDataProvider
     */
    public function testTranslateAvs(string $code, string $expectedDescription): void
    {
        $result = $this->helper->translateAvs($code);
        $resultString = (string)$result;

        $this->assertStringContainsString($code, $resultString);
        $this->assertStringContainsString($expectedDescription, $resultString);
    }

    public static function avsCodesDataProvider(): array
    {
        return [
            'A - Street matches' => ['A', 'Street matches; postcode does not'],
            'M - Match' => ['M', 'Match'],
            'N - No match' => ['N', 'Street and postcode do not match card'],
            'Y - Match' => ['Y', 'Match'],
            'D - Match' => ['D', 'Match'],
            'X - Match' => ['X', 'Match'],
            'Z - Postcode matches' => ['Z', 'Postcode matches; street does not'],
            'U - Unavailable' => ['U', 'AVS unavailable'],
            '1 - Not supported' => ['1', 'AVS not supported'],
        ];
    }

    public function testTranslateAvsUnknownCode(): void
    {
        $unknownCode = 'UNKNOWN';
        $result = $this->helper->translateAvs($unknownCode);

        $this->assertSame($unknownCode, $result);
    }

    public function testTranslateAvsEmptyCode(): void
    {
        $result = $this->helper->translateAvs('');

        $this->assertSame('', $result);
    }

    /**
     * @dataProvider cvnCodesDataProvider
     */
    public function testTranslateCvn(string $code, string $expectedDescription): void
    {
        $result = $this->helper->translateCvn($code);
        $resultString = (string)$result;

        $this->assertStringContainsString($code, $resultString);
        $this->assertStringContainsString($expectedDescription, $resultString);
    }

    public static function cvnCodesDataProvider(): array
    {
        return [
            'M - Match' => ['M', 'Match'],
            'N - No match' => ['N', 'No match'],
            'P - Not processed' => ['P', 'Not processed for an unspecified reason'],
            'S - No CVN provided' => ['S', 'No CVN provided'],
            'U - Not supported' => ['U', 'Not supported by the issuing bank'],
            'D - Suspicious' => ['D', 'Transaction considered suspicious by the issuing bank'],
            'I - Failed validation' => ['I', 'Failed data validation'],
        ];
    }

    public function testTranslateCvnUnknownCode(): void
    {
        $unknownCode = 'UNKNOWN';
        $result = $this->helper->translateCvn($unknownCode);

        $this->assertSame($unknownCode, $result);
    }

    public function testTranslateCvnEmptyCode(): void
    {
        $result = $this->helper->translateCvn('');

        $this->assertSame('', $result);
    }

    /**
     * @dataProvider riskFactorCodesDataProvider
     */
    public function testTranslateRiskFactor(string $code, string $expectedDescription): void
    {
        $result = $this->helper->translateRiskFactor($code);
        $resultString = (string)$result;

        $this->assertStringContainsString($code, $resultString);
        $this->assertStringContainsString($expectedDescription, $resultString);
    }

    public static function riskFactorCodesDataProvider(): array
    {
        return [
            'A - Address changes' => ['A', 'Excessive address changes'],
            'F - Negative list' => ['F', 'Negative list or negative history'],
            'V - Velocity' => ['V', 'Velocity'],
            'G - Geolocation' => ['G', 'Geolocation inconsistencies'],
            'E - Positive list' => ['E', 'The customer is on your positive list'],
            'B - BIN risk' => ['B', 'Card BIN or authorization risk'],
            'R - Risky order' => ['R', 'Risky order; multiple high-risk correlations'],
        ];
    }

    public function testTranslateRiskFactorUnknownCode(): void
    {
        $unknownCode = 'UNKNOWN';
        $result = $this->helper->translateRiskFactor($unknownCode);

        $this->assertSame($unknownCode, $result);
    }

    public function testTranslateRiskFactorEmptyCode(): void
    {
        $result = $this->helper->translateRiskFactor('');

        $this->assertSame('', $result);
    }

    public function testAvsResponseCodesConstant(): void
    {
        $this->assertIsArray(Data::AVS_RESPONSE_CODES);
        $this->assertNotEmpty(Data::AVS_RESPONSE_CODES);
        $this->assertArrayHasKey('A', Data::AVS_RESPONSE_CODES);
        $this->assertArrayHasKey('M', Data::AVS_RESPONSE_CODES);
    }

    public function testCvnResponseCodesConstant(): void
    {
        $this->assertIsArray(Data::CVN_RESPONSE_CODES);
        $this->assertNotEmpty(Data::CVN_RESPONSE_CODES);
        $this->assertArrayHasKey('M', Data::CVN_RESPONSE_CODES);
        $this->assertArrayHasKey('N', Data::CVN_RESPONSE_CODES);
    }

    public function testRiskFactorCodesConstant(): void
    {
        $this->assertIsArray(Data::RISK_FACTOR_CODES);
        $this->assertNotEmpty(Data::RISK_FACTOR_CODES);
        $this->assertArrayHasKey('A', Data::RISK_FACTOR_CODES);
        $this->assertArrayHasKey('V', Data::RISK_FACTOR_CODES);
    }
}
