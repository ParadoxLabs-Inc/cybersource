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

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

/**
 * JsonWebTokenEncoder Class
 *
 * Implements JSON Web Token (JWT) signing and validation for the CardinalCruise API, for Payer Authentication.
 * @see https://cardinaldocs.atlassian.net/wiki/spaces/CC/pages/196850/JWT+Creation
 * @see https://jwt.io/introduction/
 *
 * Implementation heavily based on the jweety Encoder by Vaibhav Pandey <contact@vaibhavpandey.com>, MIT license.
 * @see https://github.com/vaibhavpandeyvpz/jweety
 * @license https://github.com/vaibhavpandeyvpz/jweety/blob/1.0/LICENSE
 */
class JsonWebTokenEncoder
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * Hmac constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Unpack and validate the given JWT string, and return it if all good
     *
     * @param string $token
     * @param boolean $validateClaims
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    public function unpack($token, $validateClaims = true)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \Magento\Framework\Exception\InputException(__('Invalid or malformed JWT supplied.'));
        }

        $signature = $this->sign($parts[0] . '.' . $parts[1]);
        if (hash_equals($signature, (string)$this->decode($parts[2]))) {
            $claims = json_decode(
                $this->decode($parts[1]),
                JSON_OBJECT_AS_ARRAY
            );

            if ($validateClaims) {
                $this->validateClaims($claims);
            }

            return $claims;
        }

        throw new \Magento\Framework\Exception\InputException(__('JWT signature verification failed.'));
    }

    /**
     * Turn the given claims (params) array into an encoded JWT string
     *
     * @param array $claims
     * @param string $alg
     * @param string $typ
     * @return string
     * @throws \InvalidArgumentException
     */
    public function pack($claims, $alg = 'HS256', $typ = 'JWT')
    {
        $parts = [
            'header' => $this->encode(
                json_encode([
                    'alg' => $alg,
                    'typ' => $typ,
                ])
            ),
            'payload' => $this->encode(json_encode($claims)),
        ];

        $parts['signature'] = $this->encode(
            $this->sign(implode('.', $parts))
        );

        return implode('.', $parts);
    }

    /**
     * Generate signature for the given JWT header and payload package
     *
     * @param string $payload
     * @return string
     */
    protected function sign($payload)
    {
        return hash_hmac(
            'SHA256', // NB: Hardcoding SHA-256 (HS256) algorithm
            $payload,
            $this->config->getCardinalSecretKey(),
            true
        );
    }

    /**
     * Validate the JWT claims (expiration, etc.)
     *
     * @param array $claims
     * @throws \Magento\Framework\Exception\InputException
     */
    public function validateClaims($claims)
    {
        if (isset($claims['exp']) && (time() >= $claims['exp'])) {
            throw new \Magento\Framework\Exception\InputException(
                __("JWT expired at '%1'.", date(\DATE_ATOM, $claims['exp']))
            );
        }

        if (isset($claims['iat']) && (time() < $claims['iat'])) {
            throw new \Magento\Framework\Exception\InputException(
                __('JWT was issued in the future (%1).', date(\DATE_ATOM, $claims['iat']))
            );
        }

        if (isset($claims['nbf']) && (time() < $claims['nbf'])) {
            throw new \Magento\Framework\Exception\InputException(
                __("JWT cannot be used before '%1'.", date(\DATE_ATOM, $claims['nbf']))
            );
        }
    }

    /**
     * Decode the given JWT part string.
     *
     * @param string $part
     * @return string
     */
    protected function decode($part)
    {
        // Pad with = to get to the correct length for base64 decoding
        $remainder = strlen($part) % 4;
        if ($remainder) {
            $padding = 4 - $remainder;
            $part    .= str_repeat('=', $padding);
        }

        // Swap -_ (url-friendly) to +/
        $part = strtr($part, '-_', '+/');

        // Decode
        return base64_decode($part);
    }

    /**
     * Encode the given part string for JWT.
     *
     * @param string $part
     * @return string
     */
    public function encode($part)
    {
        // Encode
        $part = base64_encode($part);

        // Make it URL-friendly per spec
        $part = strtr($part, '+/', '-_');
        return trim($part, '=');
    }
}
