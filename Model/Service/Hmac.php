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

namespace ParadoxLabs\CyberSource\Model\Service;

/**
 * Hmac Class
 */
class Hmac
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
     * Get CyberSource HMAC signature for the given signed parameters.
     *
     * $params may include unsigned request params. Only fields passed in as comma-separated signed_field_names will be
     * signed, as per API spec.
     *
     * @param array $params
     * @return string
     */
    public function getSignature(array $params)
    {
        $signedParams = [];
        $signedFields = explode(',', $params['signed_field_names']);
        foreach ($signedFields as $key) {
            $signedParams[] = $key . '=' . $params[$key];
        }

        $hmac = hash_hmac(
            'sha256',
            implode(',', $signedParams),
            $this->config->getSecretKey(),
            true
        );

        return base64_encode($hmac);
    }

    /**
     * Validate the CyberSource HMAC signature for the given signed parameters.
     *
     * @param array $params
     * @return bool
     */
    public function validateSignature(array $params)
    {
        if (!empty($params['signature'])) {
            return hash_equals(
                $this->getSignature($params),
                $params['signature']
            );
        }

        return false;
    }
}
