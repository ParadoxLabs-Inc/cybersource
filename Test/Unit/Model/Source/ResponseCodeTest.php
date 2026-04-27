<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Source;

use ParadoxLabs\CyberSource\Model\Source\ResponseCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Source\ResponseCode
 */
class ResponseCodeTest extends TestCase
{
    private ResponseCode $responseCode;

    protected function setUp(): void
    {
        $this->responseCode = new ResponseCode();
    }

    /**
     * @dataProvider knownResponseCodesDataProvider
     */
    public function testGetMessageKnownCodes($code, string $expectedMessage): void
    {
        $this->assertSame($expectedMessage, $this->responseCode->getMessage($code));
    }

    public static function knownResponseCodesDataProvider(): array
    {
        return [
            'success (100)' => [100, 'Transaction successful.'],
            'success as string' => ['100', 'Transaction successful.'],
            'issuer declined (201)' => [201, 'Credit card issuer declined the request.'],
            'invalid card (231)' => [231, 'Invalid credit card number. Please confirm your billing details.'],
            'payer auth required (475)' => [
                475,
                'The entered card is enrolled in Payer Authentication. Please authenticate before continuing.',
            ],
            'missing fields (101)' => [101, 'The request is missing one or more fields.'],
            'invalid data (102)' => [102, 'One or more fields in the request contains invalid data.'],
            'duplicate (104)' => [
                104,
                'Duplicate Transaction; the reference code matches one already sent in the last 15 minutes.',
            ],
            'insufficient funds (204)' => [204, 'Transaction declined; insufficient funds.'],
            'decision manager review (480)' => [480, 'The order is marked for review by Decision Manager.'],
        ];
    }

    public function testGetMessageUnknownCode(): void
    {
        $this->assertSame('Unknown response code', $this->responseCode->getMessage(9999));
    }

    public function testGetMessageStringToIntCasting(): void
    {
        // String '100' should cast to int 100 and match
        $this->assertSame('Transaction successful.', $this->responseCode->getMessage('100'));
    }

    public function testGetMessageFloatCasting(): void
    {
        // Float 100.5 should cast to int 100 and match
        $this->assertSame('Transaction successful.', $this->responseCode->getMessage(100.5));
    }

    public function testGetMessageNegativeCode(): void
    {
        $this->assertSame('Unknown response code', $this->responseCode->getMessage(-100));
    }

    public function testGetMessageZero(): void
    {
        $this->assertSame('Unknown response code', $this->responseCode->getMessage(0));
    }

    public function testResponseCodesConstantExists(): void
    {
        $this->assertIsArray(ResponseCode::RESPONSE_CODES);
        $this->assertNotEmpty(ResponseCode::RESPONSE_CODES);
    }

    public function testResponseCodesContainsCommonCodes(): void
    {
        $commonCodes = [100, 101, 102, 200, 201, 231, 475, 480];

        foreach ($commonCodes as $code) {
            $this->assertArrayHasKey($code, ResponseCode::RESPONSE_CODES);
        }
    }
}
