<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\TransientTokenReader;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\TransientTokenReader
 */
class TransientTokenReaderTest extends TestCase
{
    private TransientTokenReader $reader;

    protected function setUp(): void
    {
        $this->reader = new TransientTokenReader(new CardType());
    }

    /**
     * Base64url-encode a JWT segment.
     *
     * @param array<string, mixed> $data
     * @return string
     */
    private static function encodeSegment(array $data): string
    {
        return rtrim(strtr(base64_encode((string)json_encode($data)), '+/', '-_'), '=');
    }

    /**
     * Build a JWT with the given payload (header/signature are irrelevant to the reader).
     *
     * @param array<string, mixed> $payload
     * @return string
     */
    private static function buildJwt(array $payload): string
    {
        return self::encodeSegment(['alg' => 'RS256', 'kid' => 'zz']) . '.'
            . self::encodeSegment($payload) . '.c2lnbmF0dXJl';
    }

    /**
     * Payload matching a REAL browser-minted gda-0.10.0 PANENTRY transient token: scalar card fields
     * wrapped as {"value": ...}, the number as {maskedValue, bin}, absent fields as empty arrays.
     *
     * @param array<string, mixed> $cardOverride
     * @return array<string, mixed>
     */
    private static function realShapedPayload(array $cardOverride = []): array
    {
        return [
            'metadata' => [
                'sequenceNumber' => '1',
                'cardholderAuthenticationStatus' => false,
                'paymentType' => 'PANENTRY',
            ],
            'iss' => 'Flex/07',
            'exp' => 1781018658,
            'type' => 'gda-0.10.0',
            'iat' => 1781017758,
            'jti' => '1E1ADDLZ49ILFQYHW348HBT4FNAD23A6U4RBN2CXBSOHJ5UZRC6O6A28302251CD',
            'content' => [
                'orderInformation' => [
                    'billTo' => [
                        'country' => [],
                        'lastName' => [],
                        'firstName' => [],
                    ],
                    'amountDetails' => [
                        'totalAmount' => [],
                        'currency' => [],
                    ],
                ],
                'paymentInformation' => [
                    'card' => $cardOverride + [
                        'expirationYear' => ['value' => '2029'],
                        'number' => [
                            'maskedValue' => 'XXXXXXXXXXXX1111',
                            'bin' => '411111',
                        ],
                        'securityCode' => [],
                        'expirationMonth' => ['value' => '09'],
                        'typeSelectionIndicator' => ['value' => '1'],
                        'type' => ['value' => '001'],
                    ],
                ],
            ],
        ];
    }

    public function testReadsFullCardMetadataFromRealShapedToken(): void
    {
        $result = $this->reader->read(self::buildJwt(self::realShapedPayload()));

        $this->assertSame(
            [
                'cc_type' => 'VI',
                'cc_last4' => '1111',
                'cc_bin' => '411111',
                'cc_exp_month' => '09',
                'cc_exp_year' => '2029',
            ],
            $result
        );
    }

    public function testMapsCyberSourceTypeCodesThroughCardTypeService(): void
    {
        $payload = self::realShapedPayload(['type' => ['value' => '002']]);

        $this->assertSame('MC', $this->reader->read(self::buildJwt($payload))['cc_type']);
    }

    public function testUnknownTypeCodeYieldsOtherNotEmpty(): void
    {
        $payload = self::realShapedPayload(['type' => ['value' => '999']]);

        $this->assertSame('OT', $this->reader->read(self::buildJwt($payload))['cc_type']);
    }

    public function testToleratesBareScalarFieldsInsteadOfValueWrappers(): void
    {
        // Defensive: accept unwrapped scalars in case the gda payload wrapping ever changes.
        $payload = self::realShapedPayload([
            'expirationYear' => '2030',
            'expirationMonth' => '01',
            'type' => '004',
        ]);

        $result = $this->reader->read(self::buildJwt($payload));

        $this->assertSame('DI', $result['cc_type']);
        $this->assertSame('01', $result['cc_exp_month']);
        $this->assertSame('2030', $result['cc_exp_year']);
    }

    public function testWalletPayloadWithoutCardFieldsDegradesGracefully(): void
    {
        // Wallet tokens (APPLEPAY/GOOGLEPAY) may omit some or all card fields; absent fields
        // serialize as empty arrays in the real payload shape. Partial data returns partial keys.
        $payload = self::realShapedPayload([
            'expirationYear' => [],
            'expirationMonth' => [],
            'number' => [],
            'type' => ['value' => '001'],
        ]);
        $payload['metadata']['paymentType'] = 'APPLEPAY';

        $this->assertSame(['cc_type' => 'VI'], $this->reader->read(self::buildJwt($payload)));
    }

    public function testWalletPayloadWithNoCardSubtreeReturnsEmpty(): void
    {
        $payload = self::realShapedPayload();
        $payload['metadata']['paymentType'] = 'GOOGLEPAY';
        unset($payload['content']['paymentInformation']['card']);

        $this->assertSame([], $this->reader->read(self::buildJwt($payload)));
    }

    /**
     * @dataProvider malformedTokenProvider
     */
    public function testMalformedInputReturnsEmptyArrayAndNeverThrows(string $jwt): void
    {
        $this->assertSame([], $this->reader->read($jwt));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function malformedTokenProvider(): array
    {
        return [
            'empty string' => [''],
            'not a jwt at all' => ['garbage'],
            'single segment' => [self::encodeSegment(['a' => 1])],
            'empty payload segment' => ['aGVhZGVy..c2ln'],
            'payload not base64' => ['header.!!!not-base64!!!.sig'],
            'payload not json' => ['header.' . rtrim(strtr(base64_encode('not json'), '+/', '-_'), '=') . '.sig'],
            'payload json scalar' => ['header.' . rtrim(strtr(base64_encode('"str"'), '+/', '-_'), '=') . '.sig'],
            'payload missing content' => [self::buildJwt(['iss' => 'Flex/07'])],
            'card is not an array' => [self::buildJwt([
                'content' => [
                    'paymentInformation' => [
                        'card' => 'bogus',
                    ],
                ],
            ])],
            'card fields all empty arrays' => [self::buildJwt([
                'content' => [
                    'paymentInformation' => [
                        'card' => [
                            'number' => [],
                            'type' => [],
                            'expirationMonth' => [],
                            'expirationYear' => [],
                        ],
                    ],
                ],
            ])],
            'masked value with no digits' => [self::buildJwt([
                'content' => [
                    'paymentInformation' => [
                        'card' => [
                            'number' => ['maskedValue' => 'XXXXXXXXXXXXXXXX'],
                        ],
                    ],
                ],
            ])],
        ];
    }
}
