<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Gateway\Api;

use ParadoxLabs\CyberSource\Gateway\Api\WsseHeader;
use PHPUnit\Framework\TestCase;
use SoapHeader;
use SoapVar;

/**
 * @covers \ParadoxLabs\CyberSource\Gateway\Api\WsseHeader
 */
class WsseHeaderTest extends TestCase
{
    private const OASIS_WSSE_NS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    public function testExtendsSOAPHeader(): void
    {
        $header = new WsseHeader('', '', null, false, null, 'user', 'pass');

        $this->assertInstanceOf(SoapHeader::class, $header);
    }

    public function testEmptyNamespaceDefaultsToOasis(): void
    {
        $header = new WsseHeader('', '', null, false, null, 'user', 'pass');

        $this->assertSame(self::OASIS_WSSE_NS, $header->namespace);
    }

    public function testHeaderNameIsSecurity(): void
    {
        $header = new WsseHeader('', '', null, false, null, 'user', 'pass');

        $this->assertSame('Security', $header->name);
    }

    public function testHeaderMustUnderstandIsTrue(): void
    {
        $header = new WsseHeader('', '', null, false, null, 'user', 'pass');

        $this->assertTrue($header->mustUnderstand);
    }

    public function testDataContainsSoapVar(): void
    {
        $header = new WsseHeader('', '', null, false, null, 'testuser', 'testpass');

        $this->assertInstanceOf(SoapVar::class, $header->data);
    }

    public function testCustomNamespacePreserved(): void
    {
        $customNs = 'http://custom.namespace.com';
        $header = new WsseHeader($customNs, '', null, false, null, 'user', 'pass');

        $this->assertSame($customNs, $header->namespace);
    }

    public function testSecurityHeaderStructure(): void
    {
        $header = new WsseHeader('', '', null, false, null, 'testuser', 'testpass');

        // Data should be a SoapVar containing nested structure
        $this->assertInstanceOf(SoapVar::class, $header->data);

        // The enc_type should be SOAP_ENC_OBJECT (300)
        $this->assertSame(SOAP_ENC_OBJECT, $header->data->enc_type);
    }

    public function testWithDifferentCredentials(): void
    {
        $header1 = new WsseHeader('', '', null, false, null, 'user1', 'pass1');
        $header2 = new WsseHeader('', '', null, false, null, 'user2', 'pass2');

        // Both should be valid WSSE headers
        $this->assertInstanceOf(WsseHeader::class, $header1);
        $this->assertInstanceOf(WsseHeader::class, $header2);

        // Both should have Security as name
        $this->assertSame('Security', $header1->name);
        $this->assertSame('Security', $header2->name);
    }

    public function testNullCredentialsHandled(): void
    {
        // Should not throw with null credentials
        $header = new WsseHeader('', '', null, false, null, null, null);

        $this->assertInstanceOf(WsseHeader::class, $header);
    }

    public function testEmptyStringCredentialsHandled(): void
    {
        $header = new WsseHeader('', '', null, false, null, '', '');

        $this->assertInstanceOf(WsseHeader::class, $header);
    }
}
