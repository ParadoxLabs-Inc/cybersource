<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Gateway\Api;

/**
 * WsseHeader Class
 *
 * PHP SoapClient doesn't implement WSSE, so we have to roll our own. Based on solution from Chris at StackExchange.
 *
 * @see https://stackoverflow.com/a/6677930/2336164
 */
class WsseHeader extends \SoapHeader
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
        $auth = new \stdClass();
        $auth->Username = new \SoapVar($username, \XSD_STRING, null, $namespace, null, $namespace);
        $auth->Password = new \SoapVar($password, \XSD_STRING, null, $namespace, null, $namespace);

        $usernameToken = new \stdClass();
        $usernameToken->UsernameToken = new \SoapVar(
            $auth,
            \SOAP_ENC_OBJECT,
            null,
            $namespace,
            'UsernameToken',
            $namespace
        );

        return new \SoapVar(
            new \SoapVar($usernameToken, \SOAP_ENC_OBJECT, null, $namespace, 'UsernameToken', $namespace),
            \SOAP_ENC_OBJECT,
            null,
            $namespace,
            'Security',
            $namespace
        );
    }
}
