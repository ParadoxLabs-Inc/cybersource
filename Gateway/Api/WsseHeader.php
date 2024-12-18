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

use SoapHeader;
use SoapVar;
use stdClass;
use const SOAP_ENC_OBJECT;
use const XSD_STRING;

/**
 * WsseHeader Class
 *
 * PHP SoapClient doesn't implement WSSE, so we have to roll our own. Based on solution from Chris at StackExchange.
 *
 * @see https://stackoverflow.com/a/6677930/2336164
 */
class WsseHeader extends SoapHeader
{
    /**
     * SoapHeader constructor
     *
     * @link https://php.net/manual/en/soapheader.soapheader.php
     * @param string $namespace <p>
     * The namespace of the SOAP header element.
     * </p>
     * @param string $name <p>
     * The name of the SoapHeader object.
     * </p>
     * @param mixed $data [optional] <p>
     * A SOAP header's content. It can be a PHP value or a
     * <b>SoapVar</b> object.
     * </p>
     * @param bool $mustunderstand [optional]
     * @param string $actor [optional] <p>
     * Value of the actor attribute of the SOAP header
     * element.
     * </p>
     * @param string $username
     * @param string $password
     */
    public function __construct(
        $namespace,
        $name,
        $data = null,
        $mustunderstand = false,
        $actor = null,
        $username = null,
        $password = null
    ) {
        if (empty($namespace)) {
            $namespace = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        }

        parent::__construct(
            $namespace,
            'Security',
            $this->getSecurityHeader($namespace, $username, $password),
            true
        );
    }

    /**
     * Create a WSSE-compatible security header object with the given user and password.
     *
     * @param string $namespace
     * @param string $username
     * @param string $password
     * @return \SoapVar
     */
    protected function getSecurityHeader($namespace, $username, $password)
    {
        $auth           = new stdClass();
        $auth->Username = new SoapVar($username, XSD_STRING, null, $namespace, null, $namespace);
        $auth->Password = new SoapVar($password, XSD_STRING, null, $namespace, null, $namespace);

        $usernameToken                = new stdClass();
        $usernameToken->UsernameToken = new SoapVar(
            $auth,
            SOAP_ENC_OBJECT,
            null,
            $namespace,
            'UsernameToken',
            $namespace
        );

        return new SoapVar(
            new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, $namespace, 'UsernameToken', $namespace),
            SOAP_ENC_OBJECT,
            null,
            $namespace,
            'Security',
            $namespace
        );
    }
}
