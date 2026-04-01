<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\Sanitizer
 */
class SanitizerTest extends TestCase
{
    private Sanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new Sanitizer();
    }

    /**
     * @dataProvider lengthDataProvider
     */
    public function testLength(string $input, int $maxLength, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->length($input, $maxLength));
    }

    public static function lengthDataProvider(): array
    {
        return [
            'truncation at boundary' => ['abcdefghij', 5, 'abcde'],
            'empty string' => ['', 10, ''],
            'exact length' => ['hello', 5, 'hello'],
            'shorter than max' => ['hi', 10, 'hi'],
            'unicode truncation' => ['héllo wörld', 5, 'héll'], // substr works on bytes, not chars
        ];
    }

    /**
     * @dataProvider alphaDataProvider
     */
    public function testAlpha(string $input, int $maxLength, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->alpha($input, $maxLength));
    }

    public static function alphaDataProvider(): array
    {
        return [
            'strips numbers' => ['abc123def', 100, 'abcdef'],
            'strips special chars' => ['a!b@c#d$', 100, 'abcd'],
            'preserves case' => ['AbCdEf', 100, 'AbCdEf'],
            'empty result' => ['12345!@#$%', 100, ''],
            'truncates after cleaning' => ['abcdefghij', 5, 'abcde'],
            'strips spaces' => ['hello world', 100, 'helloworld'],
        ];
    }

    /**
     * @dataProvider alphanumericDataProvider
     */
    public function testAlphanumeric(string $input, int $maxLength, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->alphanumeric($input, $maxLength));
    }

    public static function alphanumericDataProvider(): array
    {
        return [
            'strips special chars' => ['abc!@#123', 100, 'abc123'],
            'preserves alphanumeric' => ['Test123', 100, 'Test123'],
            'strips spaces' => ['test 123', 100, 'test123'],
            'empty result' => ['!@#$%^&*()', 100, ''],
            'truncates result' => ['abcdefghij123', 5, 'abcde'],
        ];
    }

    /**
     * @dataProvider alphanumericPuncDataProvider
     */
    public function testAlphanumericPunc(string $input, int $maxLength, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->alphanumericPunc($input, $maxLength));
    }

    public static function alphanumericPuncDataProvider(): array
    {
        return [
            'allows alphanumeric' => ['Test123', 100, 'Test123'],
            'allows spaces' => ['hello world', 100, 'hello world'],
            'allows quotes' => ['"test\'s"', 100, '"test\'s"'],
            'allows punctuation' => ['test!@#$%&()', 100, 'test!@#$%&()'], // @ is allowed
            'allows math operators' => ['1+2=3', 100, '1+2=3'],
            'allows special chars' => ['test.com/path?q=a', 100, 'test.com/path?q=a'],
            'strips brackets' => ['test[0]', 100, 'test0'],
            'strips backslash' => ['path\\file', 100, 'pathfile'],
            'truncates result' => ['abcdefghij', 5, 'abcde'],
        ];
    }

    /**
     * @dataProvider asciiAlphanumericPuncDataProvider
     */
    public function testAsciiAlphanumericPunc(string $input, int $maxLength, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->asciiAlphanumericPunc($input, $maxLength));
    }

    public static function asciiAlphanumericPuncDataProvider(): array
    {
        return [
            'allows alphanumeric' => ['Test123', 100, 'Test123'],
            'allows limited punctuation' => ["test!&'()+,-./:@", 100, "test!&'()+,-./:@"],
            'strips spaces' => ['hello world', 100, 'helloworld'],
            'strips hash' => ['test#tag', 100, 'testtag'],
            'strips equals' => ['a=b', 100, 'ab'],
            'strips question' => ['test?q=a', 100, 'testqa'],
            'truncates result' => ['abcdefghij', 5, 'abcde'],
        ];
    }

    /**
     * @dataProvider amountDataProvider
     */
    public function testAmount($input, float $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->amount($input));
    }

    public static function amountDataProvider(): array
    {
        return [
            'integer' => [100, 100.0],
            'float' => [99.99, 99.99],
            'string float' => ['123.45', 123.45],
            'negative stripped' => [-50.00, 50.0],
            'negative string stripped' => ['-25.50', 25.5],
            'currency symbol stripped' => ['$100.00', 100.0],
            'comma stripped' => ['1,234.56', 1234.56],
            'zero' => [0, 0.0],
            'empty string' => ['', 0.0],
            'decimal only' => ['.99', 0.99],
        ];
    }

    /**
     * @dataProvider isoDateDataProvider
     */
    public function testIsoDate(string $input, string $expectedPattern): void
    {
        $result = $this->sanitizer->isoDate($input);
        $this->assertMatchesRegularExpression($expectedPattern, $result);
    }

    public static function isoDateDataProvider(): array
    {
        return [
            'standard date' => ['2024-01-15', '/^2024-01-15T\d{2}:\d{2}:\d{2}Z$/'],
            'datetime' => ['2024-06-20 14:30:00', '/^2024-06-20T14:30:00Z$/'],
            'relative date' => ['now', '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/'],
        ];
    }

    public function testEmailValid(): void
    {
        $this->assertSame('test@example.com', $this->sanitizer->email('test@example.com'));
    }

    public function testEmailEmpty(): void
    {
        $this->assertSame('', $this->sanitizer->email(''));
    }

    public function testEmailNullAllowed(): void
    {
        $this->assertNull($this->sanitizer->email(null));
    }

    public function testEmailInvalidThrowsException(): void
    {
        $this->expectException(InputException::class);
        $this->sanitizer->email('invalid-email');
    }

    public function testEmailMissingAtThrowsException(): void
    {
        $this->expectException(InputException::class);
        $this->sanitizer->email('testexample.com');
    }

    /**
     * @dataProvider ipAddressDataProvider
     */
    public function testIpAddress(string $input, ?string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->ipAddress($input));
    }

    public static function ipAddressDataProvider(): array
    {
        return [
            'valid ipv4' => ['192.168.1.1', '192.168.1.1'],
            'valid localhost' => ['127.0.0.1', '127.0.0.1'],
            'valid public' => ['8.8.8.8', '8.8.8.8'],
            'invalid format' => ['256.256.256.256', null],
            'invalid string' => ['not-an-ip', null],
            'empty string' => ['', null],
            'ipv6 loopback' => ['::1', '::1'],
            'ipv6 full' => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
            'ipv6 compressed' => ['2001:db8::1', '2001:db8::1'],
            'ipv6 mapped ipv4' => ['::ffff:192.168.1.1', '::ffff:192.168.1.1'],
        ];
    }

    /**
     * @dataProvider numericDataProvider
     */
    public function testNumeric($input, int $maxLength, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->numeric($input, $maxLength));
    }

    public static function numericDataProvider(): array
    {
        return [
            'digits only' => ['12345', 100, '12345'],
            'strips letters' => ['abc123def', 100, '123'],
            'strips special' => ['1-2-3', 100, '123'],
            'preserves leading zeros' => ['00123', 100, '00123'],
            'truncates result' => ['1234567890', 5, '12345'],
            'empty result' => ['abcdef', 100, ''],
            'float to digits' => [123.45, 100, '12345'],
        ];
    }

    /**
     * @dataProvider phoneDataProvider
     */
    public function testPhone(string $input, int $maxLength, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->phone($input, $maxLength));
    }

    public static function phoneDataProvider(): array
    {
        return [
            'valid 10 digit' => ['1234567890', 20, '1234567890'],
            'valid with formatting' => ['(123) 456-7890', 20, '(123) 456-7890'],
            'valid with extension' => ['123-456-7890 x123', 20, '123-456-7890 x123'],
            'valid international' => ['+1-555-123-4567', 20, '+1-555-123-4567'],
            'too short returns empty' => ['123456789', 20, ''],
            'exactly 10 chars' => ['1234567890', 20, '1234567890'],
            'strips invalid chars' => ['123!@$456%^7890', 20, '1234567890'], // # is allowed in phone
            'truncates long number' => ['12345678901234567890', 15, '123456789012345'],
        ];
    }

    /**
     * @dataProvider postcodeUsDataProvider
     */
    public function testPostcodeUs(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->postcode($input, 'US'));
    }

    public static function postcodeUsDataProvider(): array
    {
        return [
            '5 digit' => ['12345', '12345'],
            '5+4 format' => ['123456789', '12345-6789'],
            '5+4 with dash' => ['12345-6789', '12345-6789'],
            'short padded' => ['123', '00123'],
            'short 4 digit padded' => ['1234', '01234'],
            'strips letters' => ['1234A', '01234'],
            'strips spaces' => ['123 45', '12345'],
        ];
    }

    /**
     * @dataProvider postcodeCanadaDataProvider
     */
    public function testPostcodeCanada(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->postcode($input, 'CA'));
    }

    public static function postcodeCanadaDataProvider(): array
    {
        return [
            'formatted' => ['K1A 0B1', 'K1A 0B1'],
            'unformatted' => ['K1A0B1', 'K1A 0B1'],
            'lowercase' => ['k1a0b1', 'k1a 0b1'],
            'with extra chars' => ['K1A-0B1', 'K1A 0B1'],
        ];
    }

    /**
     * @dataProvider postcodeOtherDataProvider
     */
    public function testPostcodeOther(string $input, string $country, string $expected): void
    {
        $this->assertSame($expected, $this->sanitizer->postcode($input, $country));
    }

    public static function postcodeOtherDataProvider(): array
    {
        return [
            'uk postcode' => ['SW1A 1AA', 'GB', 'SW1A 1AA'],
            'german postcode' => ['10115', 'DE', '10115'],
            'australian postcode' => ['2000', 'AU', '2000'],
            'truncated to 10' => ['12345678901234', 'FR', '1234567890'],
        ];
    }

    public function testUrlValid(): void
    {
        $this->assertSame(
            'https://example.com/path',
            $this->sanitizer->url('https://example.com/path')
        );
    }

    public function testUrlTruncated(): void
    {
        $longUrl = 'https://example.com/' . str_repeat('a', 300);
        $result = $this->sanitizer->url($longUrl);

        $this->assertSame(255, strlen($result));
    }

    public function testUrlInvalidThrowsException(): void
    {
        $this->expectException(InputException::class);
        $this->sanitizer->url('not-a-url');
    }

    public function testUrlMissingProtocolThrowsException(): void
    {
        $this->expectException(InputException::class);
        $this->sanitizer->url('example.com/path');
    }
}
