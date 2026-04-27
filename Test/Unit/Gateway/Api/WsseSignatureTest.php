<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Gateway\Api;

use DOMDocument;
use DOMElement;
use ParadoxLabs\CyberSource\Gateway\Api\WsseSignature;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Gateway\Api\WsseSignature
 */
class WsseSignatureTest extends TestCase
{
    private WsseSignature $wsseSignature;

    protected function setUp(): void
    {
        $this->wsseSignature = new WsseSignature();
    }

    public function testNamespaceConstants(): void
    {
        $this->assertSame(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3',
            WsseSignature::WSS_509
        );
        $this->assertSame(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary',
            WsseSignature::WSS_ENC
        );
        $this->assertSame(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
            WsseSignature::WSSE_NS
        );
        $this->assertSame(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd',
            WsseSignature::WSU_NS
        );
        $this->assertSame(
            'http://schemas.xmlsoap.org/soap/envelope/',
            WsseSignature::SOAP_NS
        );
        $this->assertSame(
            'http://www.w3.org/2000/09/xmldsig#',
            WsseSignature::DS_NS
        );
        $this->assertSame(
            'http://www.w3.org/2001/10/xml-exc-c14n#',
            WsseSignature::CANONIC
        );
        $this->assertSame(
            'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
            WsseSignature::SIG_ALG
        );
        $this->assertSame(
            'http://www.w3.org/2001/04/xmlenc#sha256',
            WsseSignature::DIG_ALG
        );
    }

    public function testCanonicalizeNodeSimple(): void
    {
        $doc = new DOMDocument('1.0', 'utf-8');
        $element = $doc->createElement('test', 'content');
        $doc->appendChild($element);

        $result = $this->wsseSignature->canonicalizeNode($element);

        $this->assertIsString($result);
        $this->assertStringContainsString('test', $result);
        $this->assertStringContainsString('content', $result);
    }

    public function testCanonicalizeNodeWithAttributes(): void
    {
        $doc = new DOMDocument('1.0', 'utf-8');
        $element = $doc->createElement('test');
        $element->setAttribute('attr1', 'value1');
        $element->setAttribute('attr2', 'value2');
        $doc->appendChild($element);

        $result = $this->wsseSignature->canonicalizeNode($element);

        $this->assertStringContainsString('attr1', $result);
        $this->assertStringContainsString('value1', $result);
    }

    public function testCanonicalizeNodeWithChildren(): void
    {
        $doc = new DOMDocument('1.0', 'utf-8');
        $parent = $doc->createElement('parent');
        $child = $doc->createElement('child', 'childcontent');
        $parent->appendChild($child);
        $doc->appendChild($parent);

        $result = $this->wsseSignature->canonicalizeNode($parent);

        $this->assertStringContainsString('parent', $result);
        $this->assertStringContainsString('child', $result);
        $this->assertStringContainsString('childcontent', $result);
    }

    public function testBuildSignedInfoStructure(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <SOAP-ENV:Body wsu:Id="Body">
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, ['Body']);

        $this->assertInstanceOf(DOMElement::class, $result);
        $this->assertSame('ds:SignedInfo', $result->nodeName);
    }

    public function testBuildSignedInfoContainsCanonicalizationMethod(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <SOAP-ENV:Body wsu:Id="TestId">
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, ['TestId']);

        $signedInfoXml = $doc->saveXML($result);
        $this->assertStringContainsString('CanonicalizationMethod', $signedInfoXml);
        $this->assertStringContainsString(WsseSignature::CANONIC, $signedInfoXml);
    }

    public function testBuildSignedInfoContainsSignatureMethod(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <SOAP-ENV:Body wsu:Id="TestId">
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, ['TestId']);

        $signedInfoXml = $doc->saveXML($result);
        $this->assertStringContainsString('SignatureMethod', $signedInfoXml);
        $this->assertStringContainsString(WsseSignature::SIG_ALG, $signedInfoXml);
    }

    public function testBuildSignedInfoCreatesReferenceForId(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <SOAP-ENV:Body wsu:Id="BodyId">
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, ['BodyId']);

        $signedInfoXml = $doc->saveXML($result);
        $this->assertStringContainsString('ds:Reference', $signedInfoXml);
        $this->assertStringContainsString('#BodyId', $signedInfoXml);
    }

    public function testBuildSignedInfoContainsDigestValue(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <SOAP-ENV:Body wsu:Id="TestId">
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, ['TestId']);

        $signedInfoXml = $doc->saveXML($result);
        $this->assertStringContainsString('DigestMethod', $signedInfoXml);
        $this->assertStringContainsString('DigestValue', $signedInfoXml);
        $this->assertStringContainsString(WsseSignature::DIG_ALG, $signedInfoXml);
    }

    public function testBuildSignedInfoSkipsNonexistentIds(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <SOAP-ENV:Body wsu:Id="ExistingId">
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, ['NonExistentId', 'ExistingId']);

        $signedInfoXml = $doc->saveXML($result);
        // Should only have reference for ExistingId
        $this->assertStringContainsString('#ExistingId', $signedInfoXml);
        $this->assertStringNotContainsString('#NonExistentId', $signedInfoXml);
    }

    public function testBuildSignedInfoMultipleIds(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                               xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                <SOAP-ENV:Header wsu:Id="HeaderId">
                    <security>token</security>
                </SOAP-ENV:Header>
                <SOAP-ENV:Body wsu:Id="BodyId">
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, ['HeaderId', 'BodyId']);

        $signedInfoXml = $doc->saveXML($result);
        $this->assertStringContainsString('#HeaderId', $signedInfoXml);
        $this->assertStringContainsString('#BodyId', $signedInfoXml);
    }

    public function testGetPrivateKeyInitiallyNull(): void
    {
        $result = $this->wsseSignature->getPrivateKey();

        $this->assertNull($result);
    }

    public function testBuildSignedInfoEmptyIds(): void
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                <SOAP-ENV:Body>
                    <content>test</content>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $result = $this->wsseSignature->buildSignedInfo($doc, []);

        // Should still create SignedInfo structure, just without references
        $this->assertInstanceOf(DOMElement::class, $result);
        $this->assertSame('ds:SignedInfo', $result->nodeName);
    }
}
