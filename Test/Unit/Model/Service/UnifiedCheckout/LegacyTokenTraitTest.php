<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\LegacyTokenTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\LegacyTokenTrait
 */
class LegacyTokenTraitTest extends TestCase
{
    /**
     * @return array<string, array{0: string|null, 1: bool}>
     */
    public static function tokenProvider(): array
    {
        return [
            '16-digit legacy' => [
                '9504202000051486',
                true,
            ],
            '22-digit legacy' => [
                '6843290000001234567890',
                true,
            ],
            '32-hex TMS token' => [
                'F2C0A1B2C3D4E5F6A7B8C9D0E1F2A3B4',
                false,
            ],
            '32-digit' => [
                '12345678901234567890123456789012',
                false,
            ],
            '15-digit' => [
                '950420200005148',
                false,
            ],
            '16 chars not all digits' => [
                '950420200005148A',
                false,
            ],
            'empty' => [
                '',
                false,
            ],
            'null' => [
                null,
                false,
            ],
        ];
    }

    /**
     * @param string|null $tokenId
     * @param bool $expected
     * @return void
     * @dataProvider tokenProvider
     */
    #[DataProvider('tokenProvider')]
    public function testMatchesOnlySixteenOrTwentyTwoDigits(?string $tokenId, bool $expected): void
    {
        $subject = new class {
            use LegacyTokenTrait;

            /**
             * @param string|null $tokenId
             * @return bool
             */
            public function check(?string $tokenId): bool
            {
                return $this->isLegacyToken($tokenId);
            }
        };

        $this->assertSame($expected, $subject->check($tokenId));
    }
}
