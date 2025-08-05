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

namespace ParadoxLabs\CyberSource\Model\Config;

/**
 * Config Class
 */
class Config
{
    const CODE = 'paradoxlabs_cybersource';
    const SOLUTION_ID = 'DEQXVEEG';

    /**
     * Gateway URLs
     */
    const CARDINAL_LIVE = 'https://songbird.cardinalcommerce.com/edge/v1/songbird.js'; // @deprecated
    const CARDINAL_TEST = 'https://songbirdstag.cardinalcommerce.com/edge/v1/songbird.js'; // @deprecated
    const REST_LIVE = 'https://api.cybersource.com';
    const REST_TEST = 'https://apitest.cybersource.com';
    const SECUREACCEPT_LIVE = 'https://secureacceptance.cybersource.com';
    const SECUREACCEPT_TEST = 'https://testsecureacceptance.cybersource.com';
    const SOAP_LIVE = 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.224.wsdl';
    const SOAP_TEST = 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.224.wsdl';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var int|null
     */
    protected $storeId;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get a payment config value by code and store ID
     *
     * @param string $key
     * @param int|null $storeId
     * @return mixed
     */
    protected function getConfigValue($key, $storeId = null)
    {
        return trim((string)$this->scopeConfig->getValue(
            'payment/' . static::CODE . '/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId ?? $this->storeId
        ));
    }

    /**
     * Get storeId
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set storeId
     *
     * @param int|null $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * Check whether the module is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function moduleIsActive($storeId = null)
    {
        return (bool)$this->getConfigValue('active', $storeId);
    }

    /**
     * Check whether the module is in sandbox mode.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isSandboxMode($storeId = null)
    {
        return (bool)$this->getConfigValue('test', $storeId);
    }

    /**
     * Get the CyberSource Organization ID.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getOrganizationId($storeId = null)
    {
        return $this->getConfigValue('organization_id', $storeId);
    }

    /**
     * Get the CyberSource Merchant ID.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getMerchantId($storeId = null)
    {
        return $this->getConfigValue('merchant_id', $storeId);
    }

    /**
     * Get the SOAP authentication type.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getSoapAuthType($storeId = null)
    {
        return $this->getConfigValue('soap_auth_type', $storeId) ?: 'transaction_key';
    }

    /**
     * Get the SOAP transaction key.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getSoapTransactionKey($storeId = null)
    {
        $value = $this->getConfigValue('soap_transaction_key', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource Transaction Key. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the SOAP P12 cert file location.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getSoapCertificate($storeId = null)
    {
        $value = json_decode(
            (string)$this->getConfigValue('soap_cert', $storeId),
            true
        );

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing Simple Order certificate. Please check configuration.')
            );
        }

        return base64_decode($value['contents']);
    }

    /**
     * Get the SOAP P12 cert password.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getSoapCertPassword($storeId = null)
    {
        $value = $this->getConfigValue('soap_cert_password', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing Simple Order certificate password. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the REST secret key ID.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getRestSecretKeyId($storeId = null)
    {
        $value = $this->getConfigValue('rest_secret_key_id', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource REST Secret Key ID. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the REST secret key.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getRestSecretKey($storeId = null)
    {
        $value = $this->getConfigValue('rest_secret_key', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource REST Secret Key. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the REST API endpoint.
     *
     * @param string $path
     * @param int|null $storeId
     * @return string
     */
    public function getRestEndpoint($path, $storeId = null)
    {
        if ($this->isSandboxMode($storeId)) {
            return static::REST_TEST . $path;
        }

        return static::REST_LIVE . $path;
    }

    /**
     * Get the Secure Acceptance checkout profile ID.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getSecureAcceptProfileId($storeId = null)
    {
        $value = $this->getConfigValue('secureaccept_profile_id', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource Secure Acceptance Profile ID. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the Secure Acceptance checkout access key.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getSecureAcceptAccessKey($storeId = null)
    {
        $value = $this->getConfigValue('secureaccept_access_key', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource Secure Acceptance Access Key. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the Secure Acceptance checkout secret key.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getSecureAcceptSecretKey($storeId = null)
    {
        $value = $this->getConfigValue('secureaccept_secret_key', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource Secure Acceptance Secret Key. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the Secure Acceptance checkout iframe URL.
     *
     * @param string $path
     * @param int|null $storeId
     * @return string
     */
    public function getSecureAcceptEndpoint($path, $storeId = null)
    {
        if ($this->isSandboxMode($storeId)) {
            return static::SECUREACCEPT_TEST . $path;
        }

        return static::SECUREACCEPT_LIVE . $path;
    }

    /**
     * Get the SOAP WSDL URL.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSoapWsdl($storeId = null)
    {
        if ($this->isSandboxMode($storeId)) {
            return static::SOAP_TEST;
        }

        return static::SOAP_LIVE;
    }

    /**
     * Get the extension solution ID.
     *
     * @return string
     */
    public function getSolutionId()
    {
        return self::SOLUTION_ID;
    }

    /**
     * Get the extension identifier.
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->getConfigValue('client_name');
    }

    /**
     * Get the extension version.
     *
     * @return string
     */
    public function getClientVersion()
    {
        return $this->getConfigValue('client_version');
    }

    /**
     * Get whether device fingerprinting is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isFingerprintEnabled($storeId = null)
    {
        if ((bool)$this->getConfigValue('fingerprint', $storeId) === false
            || empty($this->getFingerprintOrgId($storeId))) {
            return false;
        }

        return true;
    }

    /**
     * Get the CyberSource-spec fingerprint session key for the given ID, or null if fingerprinting is disabled.
     *
     * @param string $sessionId
     * @param int|null $storeId
     * @param bool $apiScope
     * @return string|null
     */
    public function getFingerprintSessionId($sessionId, $storeId = null, $apiScope = false)
    {
        if ($this->isFingerprintEnabled($storeId) !== true) {
            return null;
        }

        if ($apiScope === true) {
            return $sessionId;
        }

        return $this->getConfigValue('merchant_id', $storeId) . $sessionId;
    }

    /**
     * Get the fingerprint organization ID.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getFingerprintOrgId($storeId = null)
    {
        return $this->isSandboxMode($storeId) ? '1snn5n9w' : 'k8vif92e';
    }

    /**
     * Get the fingerprint script URL including session key, or null if fingerprinting is disabled.
     *
     * @param string $sessionId
     * @param int|null $storeId
     * @return string|null
     */
    public function getFingerprintUrl($sessionId, $storeId = null)
    {
        if ($this->isFingerprintEnabled($storeId) === false) {
            return null;
        }

        $params = [
            'org_id' => $this->getFingerprintOrgId($storeId),
            'session_id' => $this->getFingerprintSessionId($sessionId, $storeId),
        ];

        return sprintf(
            'https://%s/fp/tags.js?%s',
            $this->getConfigValue('fingerprint_domain', $storeId) ?: 'h.online-metrix.net',
            http_build_query($params)
        );
    }

    /**
     * Get whether decision manager/fraud mgmt essentials is enabled for card storage.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isCardStorageValidationEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue('validate_card_storage', $storeId);
    }

    /**
     * Get whether Payer Authentication (Cardinal Cruise API) is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isPayerAuthEnabled($storeId = null)
    {
        try {
            if ((bool)$this->getConfigValue('cardinal_active', $storeId) === false
                || empty($this->getCardinalOrgUnitId($storeId))
                || empty($this->getCardinalSecretKeyId($storeId))
                || empty($this->getCardinalSecretKey($storeId))) {
                return false;
            }

            return true;
        } catch (\Magento\Framework\Exception\StateException $exception) {
            return false;
        }
    }

    /**
     * Get whether Payer Authentication is enabled for a specific card type.
     *
     * @param string $ccType
     * @param int|null $storeId
     * @return bool
     */
    public function isPayerAuthEnabledForType(string $ccType, $storeId = null)
    {
        try {
            $enabledTypes = explode(',', (string)$this->getConfigValue('cardinal_card_types', $storeId));

            if ($this->isPayerAuthEnabled($storeId) === false
                || in_array($ccType, $enabledTypes, true) === false) {
                return false;
            }

            return true;
        } catch (\Magento\Framework\Exception\StateException $exception) {
            return false;
        }
    }

    /**
     * Get the Cardinal Cruise organization unit ID.
     *
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getCardinalOrgUnitId($storeId = null)
    {
        $value = $this->getConfigValue('cardinal_org_unit_id', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource Cardinal Cruise Org Unit ID. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the Cardinal Cruise secret key ID.
     *
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getCardinalSecretKeyId($storeId = null)
    {
        $value = $this->getConfigValue('cardinal_secret_key_id', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource Cardinal Cruise API ID. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the Cardinal Cruise secret key.
     *
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getCardinalSecretKey($storeId = null)
    {
        $value = $this->getConfigValue('cardinal_secret_key', $storeId);

        if (empty($value)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Missing CyberSource Cardinal Cruise API Key. Please check configuration.')
            );
        }

        return $value;
    }

    /**
     * Get the Cardinal Cruise Songbird JS library URL for the configured environment.
     *
     * @param int|null $storeId
     * @return string
     * @see ParadoxLabs_CyberSource::etc/config.xml:27
     */
    public function getCardinalSongbirdUrl($storeId = null)
    {
        if ($this->isSandboxMode($storeId)) {
            return $this->getConfigValue('cardinal_songbird_url_test', $storeId);
        }

        return $this->getConfigValue('cardinal_songbird_url_live', $storeId);
    }

    /**
     * Get the Cardinal Cruise Songbird JS library SRI hash for the configured environment.
     *
     * @param int|null $storeId
     * @return string
     * @see https://developer.cardinaltrusted.com/docs/songbird-js-changes
     * @see ParadoxLabs_CyberSource::etc/config.xml:28
     */
    public function getCardinalSongbirdSRIHash($storeId = null)
    {
        if ($this->isSandboxMode($storeId)) {
            return $this->getConfigValue('cardinal_songbird_sri_test', $storeId);
        }

        return $this->getConfigValue('cardinal_songbird_sri_live', $storeId);
    }
}
