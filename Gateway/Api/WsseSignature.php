<?php
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Gateway\Api;

/**
 * WsseSignature Class
 *
 * Based on CyberSource PHPSoapToolkit
 *
 * @see https://github.com/CyberSource/cybersource-soap-toolkit/blob/master/PHPSoapToolkit/SecurityUtility.php
 */
class WsseSignature
{
    public const WSS_509 = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3';
    public const WSS_ENC = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';
    public const WSSE_NS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    public const WSU_NS  = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
    public const SOAP_NS = 'http://schemas.xmlsoap.org/soap/envelope/';
    public const DS_NS   = 'http://www.w3.org/2000/09/xmldsig#';
    public const CANONIC = 'http://www.w3.org/2001/10/xml-exc-c14n#';
    public const SIG_ALG = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
    public const DIG_ALG = 'http://www.w3.org/2001/04/xmlenc#sha256';

    /**
     * @var \OpenSSLAsymmetricKey|resource|false
     */
    protected $privateKey;

    /**
     * @param $xmlDom
     * @param $certificate
     * @param $keyPass
     * @return mixed
     */
    public function generateSecurityToken($xmlDom, $certificate, $keyPass)
    {
        // PKCS12 certificate file
        openssl_pkcs12_read($certificate, $certs, $keyPass);
        $this->privateKey = openssl_pkey_get_private($certs['pkey']);
        $pubcert = explode("\n", $certs['cert']);
        array_shift($pubcert);

        while (!trim(array_pop($pubcert))) { /* Empty while loop */ }
        // array_filter($pubcert);

        array_walk($pubcert, 'trim');
        $pubcert = implode('', $pubcert);
        unset($certs);

        // add public key reference to the token
        $tokenElement = $xmlDom->createElementNS(self::WSSE_NS, 'wsse:BinarySecurityToken', $pubcert);
        $tokenElement->setAttribute('ValueType', self::WSS_509);
        $tokenElement->setAttribute('EncodingType', self::WSS_ENC);
        $tokenElement->setAttributeNS(self::WSU_NS, 'wsu:Id', 'X509Token');

        return $tokenElement;
    }

    /**
     * @return \OpenSSLAsymmetricKey|resource|false|null
     */
    public function getPrivateKey()
    {
        return $this->privateKey ?? null;
    }

    /**
     * Canonicalize \DOMNode instance and return result as string
     *
     * @param \DOMNode $domNode
     * @return string
     */
    public function canonicalizeNode($domNode)
    {
        $domDocument = new \DOMDocument('1.0', 'utf-8');
        $domDocument->appendChild($domDocument->importNode($domNode, true));
        return $this->canonicalizeXML($domDocument->saveXML($domDocument->documentElement));
    }

    /**
     * XML canonicalization
     *
     * @param string $data
     * @return string
     */
    protected function canonicalizeXML($data)
    {
        $fname = tempnam(sys_get_temp_dir(), 'temporaryBinarySecurityToken');
        $f = fopen($fname, 'w+');
        fwrite($f, $data);
        fclose($f);

        $tempFile = new \DOMDocument('1.0', 'utf-8');
        $tempFile->load($fname);
        unlink($fname);

        return $tempFile->C14N(true, true);
    }

    /**
     * Prepares SignedInfo \DOMElement with required data
     *
     * @param \DOMDocument $domDocument
     * @param array $ids
     * @return \DOMNode
     */
    public function buildSignedInfo($domDocument, $ids)
    {
        $domXPath = new \DOMXPath($domDocument);
        $domXPath->registerNamespace('SOAP-ENV', self::SOAP_NS);
        $domXPath->registerNamespace('wsu', self::WSU_NS);
        $domXPath->registerNamespace('wsse', self::WSSE_NS);
        $domXPath->registerNamespace('ds', self::DS_NS);

        $signedInfo = $domDocument->createElementNS(self::DS_NS, 'ds:SignedInfo');

        // Canonicalization algorithm
        $method = $signedInfo->appendChild($domDocument->createElementNS(self::DS_NS, 'ds:CanonicalizationMethod'));
        $method->setAttribute('Algorithm', self::CANONIC);

        // Signature algorithm
        $method = $signedInfo->appendChild($domDocument->createElementNS(self::DS_NS, 'ds:SignatureMethod'));
        $method->setAttribute('Algorithm', self::SIG_ALG);

        foreach ($ids as $id)
        {
            // find a node and canonicalize it
            $nodes = $domXPath->query("//*[(@wsu:Id='$id')]");
            if ($nodes->length == 0) { continue; }

            $canonicalized = $this->canonicalizeNode($nodes->item(0));

            // Create Reference Element
            $referenceElement = $signedInfo->appendChild($domDocument->createElementNS(self::DS_NS, 'ds:Reference'));
            $referenceElement->setAttribute('URI', '#' . $id);

            // Create Transform Element
            $transforms = $referenceElement->appendChild($domDocument->createElementNS(self::DS_NS, 'ds:Transforms'));
            $transformElement = $transforms->appendChild($domDocument->createElementNS(self::DS_NS, 'ds:Transform'));

            // Mark node as Canonicalized
            $transformElement->setAttribute('Algorithm', self::CANONIC);

            // Add a SHA256 digest
            $digestValue = hash('sha256', $canonicalized, true);
            $method = $referenceElement->appendChild($domDocument->createElementNS(self::DS_NS, 'ds:DigestMethod'));
            $method->setAttribute('Algorithm', self::DIG_ALG);
            $referenceElement->appendChild(
                $domDocument->createElementNS(self::DS_NS, 'ds:DigestValue', base64_encode($digestValue))
            );
        }

        return $signedInfo;
    }
}
