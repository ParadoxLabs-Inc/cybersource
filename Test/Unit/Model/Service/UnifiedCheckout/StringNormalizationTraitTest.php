<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\StringNormalizationTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * The host classes (CardBuilder, Response) cover the null and populated-string paths only. The
 * empty-string -> null collapse is the behavior that distinguishes this from a plain (string) cast
 * and is what stops empty ids reaching the vault/API, so it is pinned directly here.
 *
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\StringNormalizationTrait
 */
class StringNormalizationTraitTest extends TestCase
{
    private object $subject;

    protected function setUp(): void
    {
        $this->subject = new class {
            use StringNormalizationTrait;
        };
    }

    /**
     * Invoke the protected trait method on the anonymous host.
     */
    private function stringOrNull(mixed $value): ?string
    {
        $method = new ReflectionMethod($this->subject, 'stringOrNull');
        $method->setAccessible(true);

        return $method->invoke($this->subject, $value);
    }

    /**
     * @dataProvider stringOrNullDataProvider
     */
    #[DataProvider('stringOrNullDataProvider')]
    public function testStringOrNull(mixed $value, ?string $expected): void
    {
        $this->assertSame($expected, $this->stringOrNull($value));
    }

    public static function stringOrNullDataProvider(): array
    {
        return [
            // Null passes straight through so callers can omit the key entirely.
            'null stays null' => [null, null],

            // Empty string collapses to null: an empty TMS id must be omitted, never written as ''.
            'empty string collapses to null' => ['', null],

            // Populated strings pass through untouched -- no trimming, no case changes.
            'populated string passes through' => ['PI-CARD-1', 'PI-CARD-1'],
            'whitespace-only string is preserved as non-empty' => [' ', ' '],
            'string with surrounding whitespace is not trimmed' => [' PI-1 ', ' PI-1 '],

            // Scalars from JSON payloads are coerced to their string form.
            'int coerced to string' => [1234, '1234'],
            'zero int coerced to "0", not dropped' => [0, '0'],
            'float coerced to string' => [12.5, '12.5'],
            'string zero preserved' => ['0', '0'],

            // Booleans: true casts to '1'; false casts to '' and therefore collapses to null.
            'true coerced to "1"' => [true, '1'],
            'false collapses to null via empty-string cast' => [false, null],
        ];
    }

    /**
     * Objects implementing __toString are normalized via their string form (Phrase, for example).
     */
    public function testStringableObjectIsCoercedToItsStringForm(): void
    {
        $stringable = new class {
            public function __toString(): string
            {
                return 'CUST-1';
            }
        };

        $this->assertSame('CUST-1', $this->stringOrNull($stringable));
    }

    /**
     * A Stringable yielding '' must collapse to null exactly like a literal empty string.
     */
    public function testStringableObjectYieldingEmptyStringCollapsesToNull(): void
    {
        $stringable = new class {
            public function __toString(): string
            {
                return '';
            }
        };

        $this->assertNull($this->stringOrNull($stringable));
    }
}
