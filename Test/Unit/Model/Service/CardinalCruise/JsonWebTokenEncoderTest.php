<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\CardinalCruise;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder
 */
class JsonWebTokenEncoderTest extends TestCase
{
    private const TEST_SECRET_KEY = 'cardinal-test-secret-key-12345';

    private JsonWebTokenEncoder $encoder;
    private Config|MockObject $configMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->configMock->method('getCardinalSecretKey')
            ->willReturn(self::TEST_SECRET_KEY);

        $this->encoder = new JsonWebTokenEncoder($this->configMock);
    }

    public function testPackCreatesValidJwtStructure(): void
    {
        $claims = ['test' => 'data', 'iat' => time()];

        $jwt = $this->encoder->pack($claims);

        // JWT should have three parts separated by dots
        $parts = explode('.', $jwt);
        $this->assertCount(3, $parts);
    }

    public function testPackHeaderContainsAlgorithmAndType(): void
    {
        $claims = ['test' => 'data'];

        $jwt = $this->encoder->pack($claims);
        $parts = explode('.', $jwt);

        $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);

        $this->assertSame('HS256', $header['alg']);
        $this->assertSame('JWT', $header['typ']);
    }

    public function testPackPayloadContainsClaims(): void
    {
        $claims = [
            'test' => 'data',
            'number' => 123,
            'nested' => ['key' => 'value'],
        ];

        $jwt = $this->encoder->pack($claims);
        $parts = explode('.', $jwt);

        // Decode payload
        $payload = $parts[1];
        $payload = strtr($payload, '-_', '+/');
        $remainder = strlen($payload) % 4;
        if ($remainder) {
            $payload .= str_repeat('=', 4 - $remainder);
        }
        $decoded = json_decode(base64_decode($payload), true);

        $this->assertSame('data', $decoded['test']);
        $this->assertSame(123, $decoded['number']);
        $this->assertSame(['key' => 'value'], $decoded['nested']);
    }

    public function testPackWithCustomAlgAndTyp(): void
    {
        $claims = ['test' => 'data'];

        $jwt = $this->encoder->pack($claims, 'HS384', 'CustomType');
        $parts = explode('.', $jwt);

        $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);

        $this->assertSame('HS384', $header['alg']);
        $this->assertSame('CustomType', $header['typ']);
    }

    public function testUnpackValidToken(): void
    {
        $originalClaims = [
            'test' => 'data',
            'iat' => time() - 60,
            'exp' => time() + 3600,
        ];

        $jwt = $this->encoder->pack($originalClaims);
        $decoded = $this->encoder->unpack($jwt);

        $this->assertSame($originalClaims['test'], $decoded['test']);
        $this->assertSame($originalClaims['iat'], $decoded['iat']);
        $this->assertSame($originalClaims['exp'], $decoded['exp']);
    }

    public function testUnpackWithoutClaimValidation(): void
    {
        $claims = [
            'test' => 'data',
            'exp' => time() - 3600, // Expired
        ];

        $jwt = $this->encoder->pack($claims);

        // Should succeed without claim validation
        $decoded = $this->encoder->unpack($jwt, false);

        $this->assertSame('data', $decoded['test']);
    }

    public function testUnpackInvalidTokenStructure(): void
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Invalid or malformed JWT');

        $this->encoder->unpack('invalid-token');
    }

    public function testUnpackTooFewParts(): void
    {
        $this->expectException(InputException::class);

        $this->encoder->unpack('only.two');
    }

    public function testUnpackTooManyParts(): void
    {
        $this->expectException(InputException::class);

        $this->encoder->unpack('one.two.three.four');
    }

    public function testUnpackInvalidSignature(): void
    {
        $claims = ['test' => 'data'];
        $jwt = $this->encoder->pack($claims);

        // Tamper with signature
        $parts = explode('.', $jwt);
        $parts[2] = 'tampered-signature';
        $tamperedJwt = implode('.', $parts);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('signature verification failed');

        $this->encoder->unpack($tamperedJwt);
    }

    public function testEncodeBase64UrlSafe(): void
    {
        // Test string that produces + and / in base64
        $input = chr(251) . chr(255) . chr(254);

        $encoded = $this->encoder->encode($input);

        // Should not contain + or / (not URL-safe)
        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);
        // Should not end with = (stripped)
        $this->assertStringEndsNotWith('=', $encoded);
    }

    public function testEncodeStripsPadding(): void
    {
        // 'a' in base64 is 'YQ==' - two padding characters
        $encoded = $this->encoder->encode('a');

        $this->assertStringEndsNotWith('=', $encoded);
        $this->assertSame('YQ', $encoded);
    }

    public function testEncodeDecodeRoundTrip(): void
    {
        $testStrings = [
            'simple',
            'with spaces',
            'special: +/= chars',
            '{"json":"data"}',
            str_repeat('x', 1000),
        ];

        foreach ($testStrings as $input) {
            $encoded = $this->encoder->encode($input);

            // Manually decode to verify
            $decoded = strtr($encoded, '-_', '+/');
            $remainder = strlen($decoded) % 4;
            if ($remainder) {
                $decoded .= str_repeat('=', 4 - $remainder);
            }
            $decoded = base64_decode($decoded);

            $this->assertSame($input, $decoded, "Round-trip failed for: $input");
        }
    }

    public function testValidateClaimsExpiredToken(): void
    {
        $claims = ['exp' => time() - 3600];

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('JWT expired');

        $this->encoder->validateClaims($claims);
    }

    public function testValidateClaimsFutureIat(): void
    {
        $claims = ['iat' => time() + 3600];

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('issued in the future');

        $this->encoder->validateClaims($claims);
    }

    public function testValidateClaimsNotBeforeInFuture(): void
    {
        $claims = ['nbf' => time() + 3600];

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('cannot be used before');

        $this->encoder->validateClaims($claims);
    }

    public function testValidateClaimsMissingClaimsOk(): void
    {
        // No exp, iat, or nbf - should pass
        $claims = ['test' => 'data'];

        // Should not throw
        $this->encoder->validateClaims($claims);
        $this->assertTrue(true);
    }

    public function testValidateClaimsValidExpiration(): void
    {
        $claims = ['exp' => time() + 3600];

        // Should not throw
        $this->encoder->validateClaims($claims);
        $this->assertTrue(true);
    }

    public function testValidateClaimsValidIat(): void
    {
        $claims = ['iat' => time() - 60];

        // Should not throw
        $this->encoder->validateClaims($claims);
        $this->assertTrue(true);
    }

    public function testValidateClaimsValidNbf(): void
    {
        $claims = ['nbf' => time() - 60];

        // Should not throw
        $this->encoder->validateClaims($claims);
        $this->assertTrue(true);
    }

    public function testPackUnpackRoundTrip(): void
    {
        $originalClaims = [
            'sub' => 'user123',
            'data' => ['key' => 'value'],
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jwt = $this->encoder->pack($originalClaims);
        $decoded = $this->encoder->unpack($jwt);

        $this->assertEquals($originalClaims, $decoded);
    }
}
