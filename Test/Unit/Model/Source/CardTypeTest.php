<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Source;

use ParadoxLabs\CyberSource\Model\Source\CardType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Source\CardType
 */
class CardTypeTest extends TestCase
{
    private CardType $cardType;

    protected function setUp(): void
    {
        $this->cardType = new CardType();
    }

    /**
     * @dataProvider cardTypeMappingDataProvider
     */
    public function testGetType(string $code, string $expected): void
    {
        $this->assertSame($expected, $this->cardType->getType($code));
    }

    public static function cardTypeMappingDataProvider(): array
    {
        return [
            'Visa' => ['001', 'VI'],
            'Mastercard' => ['002', 'MC'],
            'American Express' => ['003', 'AE'],
            'Discover' => ['004', 'DI'],
            'Diners Club' => ['005', 'DN'],
            'JCB' => ['007', 'JCB'],
            'Maestro International' => ['042', 'MI'],
        ];
    }

    public function testGetTypeUnknownCode(): void
    {
        $this->assertSame('OT', $this->cardType->getType('999'));
    }

    public function testGetTypeEmptyCode(): void
    {
        $this->assertSame('OT', $this->cardType->getType(''));
    }

    public function testGetTypeNumericIntegerCode(): void
    {
        // Integer keys would not match string keys in array_key_exists
        $this->assertSame('OT', $this->cardType->getType(1));
    }

    public function testGetTypeNonPaddedCode(): void
    {
        // '1' should not match '001'
        $this->assertSame('OT', $this->cardType->getType('1'));
    }

    public function testCardTypeMapConstant(): void
    {
        $this->assertCount(7, CardType::CARD_TYPE_MAP);
        $this->assertArrayHasKey('001', CardType::CARD_TYPE_MAP);
        $this->assertArrayHasKey('042', CardType::CARD_TYPE_MAP);
    }
}
