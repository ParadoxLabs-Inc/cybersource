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
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Service\SecureAcceptance;

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
     * Get Secure Acceptance HMAC signature for the given signed parameters.
     *
     * $params may include unsigned request params. Only fields passed in as comma-separated signed_field_names will be
     * signed, as per API spec.
     *
     * @param array $params
     * @return string
     */
    public function getSignature(array $params)
    {
        if (isset($params['req_merchant_defined_data97'])) {
            $this->config->setStoreId($params['req_merchant_defined_data97']);
        }

        $signedParams = [];
        $signedFields = explode(',', (string)$params['signed_field_names']);
        foreach ($signedFields as $key) {
            $signedParams[] = $key . '=' . $params[$key];
        }

        $hmac = hash_hmac(
            'sha256',
            implode(',', $signedParams),
            (string)$this->config->getSecureAcceptSecretKey(),
            true
        );

        return base64_encode($hmac);
    }

    /**
     * Validate the Secure Acceptance HMAC signature for the given signed parameters.
     *
     * Will return false if there is no signature, or if the signature does not validate for all signed_field_names.
     * Signing requires the secret key, so this prevents request forgery or tampering, assuming the secret key
     * is, in fact, secret.
     *
     * @param array $params
     * @return bool
     */
    public function validateSignature(array $params)
    {
        if (!empty($params['signature'])) {
            return hash_equals(
                (string)$this->getSignature($params),
                (string)$params['signature']
            );
        }

        return false;
    }
}
