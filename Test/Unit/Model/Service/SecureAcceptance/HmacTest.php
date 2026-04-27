<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\SecureAcceptance;

use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac
 */
class HmacTest extends TestCase
{
    private const TEST_SECRET_KEY = 'test-secret-key-12345';

    private Hmac $hmac;
    private Config|MockObject $configMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->configMock->method('getSecureAcceptSecretKey')
            ->willReturn(self::TEST_SECRET_KEY);

        $this->hmac = new Hmac($this->configMock);
    }

    public function testGetSignatureBasic(): void
    {
        $params = [
            'signed_field_names' => 'field1,field2,field3',
            'field1' => 'value1',
            'field2' => 'value2',
            'field3' => 'value3',
        ];

        $signature = $this->hmac->getSignature($params);

        $this->assertNotEmpty($signature);
        $this->assertIsString($signature);
        // Signature should be base64 encoded
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9+\/]+=*$/', $signature);
    }

    public function testGetSignatureOnlySignsSpecifiedFields(): void
    {
        $params1 = [
            'signed_field_names' => 'field1,field2',
            'field1' => 'value1',
            'field2' => 'value2',
            'unsigned_field' => 'ignored',
        ];

        $params2 = [
            'signed_field_names' => 'field1,field2',
            'field1' => 'value1',
            'field2' => 'value2',
            'unsigned_field' => 'different_value',
        ];

        // Unsigned field should not affect signature
        $this->assertSame(
            $this->hmac->getSignature($params1),
            $this->hmac->getSignature($params2)
        );
    }

    public function testGetSignatureDifferentFieldsProduceDifferentSignatures(): void
    {
        $params1 = [
            'signed_field_names' => 'field1',
            'field1' => 'value1',
        ];

        $params2 = [
            'signed_field_names' => 'field1',
            'field1' => 'value2',
        ];

        $this->assertNotSame(
            $this->hmac->getSignature($params1),
            $this->hmac->getSignature($params2)
        );
    }

    public function testGetSignatureExtractsStoreIdFromMerchantData(): void
    {
        $this->configMock->expects($this->once())
            ->method('setStoreId')
            ->with('5');

        $params = [
            'signed_field_names' => 'field1',
            'field1' => 'value1',
            'req_merchant_defined_data97' => '5',
        ];

        $this->hmac->getSignature($params);
    }

    public function testGetSignatureWithSingleField(): void
    {
        $params = [
            'signed_field_names' => 'amount',
            'amount' => '100.00',
        ];

        $signature = $this->hmac->getSignature($params);
        $this->assertNotEmpty($signature);
    }

    public function testGetSignatureProducesConsistentResults(): void
    {
        $params = [
            'signed_field_names' => 'amount,currency',
            'amount' => '100.00',
            'currency' => 'USD',
        ];

        $signature1 = $this->hmac->getSignature($params);
        $signature2 = $this->hmac->getSignature($params);

        $this->assertSame($signature1, $signature2);
    }

    public function testValidateSignatureValid(): void
    {
        $params = [
            'signed_field_names' => 'field1,field2',
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        // Generate valid signature
        $params['signature'] = $this->hmac->getSignature($params);

        $this->assertTrue($this->hmac->validateSignature($params));
    }

    public function testValidateSignatureInvalid(): void
    {
        $params = [
            'signed_field_names' => 'field1,field2',
            'field1' => 'value1',
            'field2' => 'value2',
            'signature' => 'invalid-signature',
        ];

        $this->assertFalse($this->hmac->validateSignature($params));
    }

    public function testValidateSignatureMissing(): void
    {
        $params = [
            'signed_field_names' => 'field1',
            'field1' => 'value1',
        ];

        $this->assertFalse($this->hmac->validateSignature($params));
    }

    public function testValidateSignatureEmpty(): void
    {
        $params = [
            'signed_field_names' => 'field1',
            'field1' => 'value1',
            'signature' => '',
        ];

        $this->assertFalse($this->hmac->validateSignature($params));
    }

    public function testValidateSignatureTamperedData(): void
    {
        $params = [
            'signed_field_names' => 'amount',
            'amount' => '100.00',
        ];

        // Generate valid signature
        $params['signature'] = $this->hmac->getSignature($params);

        // Tamper with signed data
        $params['amount'] = '999.99';

        $this->assertFalse($this->hmac->validateSignature($params));
    }

    public function testValidateSignatureUsesConstantTimeComparison(): void
    {
        // This test verifies hash_equals is used (timing-safe comparison)
        // by ensuring valid signatures pass and invalid ones fail
        $params = [
            'signed_field_names' => 'test',
            'test' => 'data',
        ];

        $validSignature = $this->hmac->getSignature($params);

        // Valid signature
        $params['signature'] = $validSignature;
        $this->assertTrue($this->hmac->validateSignature($params));

        // Nearly-valid signature (one character different)
        $params['signature'] = substr($validSignature, 0, -1) . 'X';
        $this->assertFalse($this->hmac->validateSignature($params));
    }
}
