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
    const CARDINAL_LIVE = 'https://songbird.cardinalcommerce.com/edge/v1/songbird.js';
    const CARDINAL_TEST = 'https://songbirdstag.cardinalcommerce.com/edge/v1/songbird.js';
    const REST_LIVE = 'https://api.cybersource.com';
    const REST_TEST = 'https://apitest.cybersource.com';
    const SECUREACCEPT_LIVE = 'https://secureacceptance.cybersource.com';
    const SECUREACCEPT_TEST = 'https://testsecureacceptance.cybersource.com';
    const SOAP_LIVE = 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.161.wsdl';
    const SOAP_TEST = 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.161.wsdl';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

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
        return trim($this->scopeConfig->getValue(
            'payment/' . static::CODE . '/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ));
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
     * @return string|null
     */
    public function getFingerprintSessionId($sessionId, $storeId = null)
    {
        return $this->isFingerprintEnabled($storeId)
            ? $this->getConfigValue('merchant_id', $storeId) . $sessionId
            : null;
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
     * Get whether Payer Authentication (Cardinal Cruise API) is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isPayerAuthEnabled($storeId = null)
    {
        if ((bool)$this->getConfigValue('cardinal_active', $storeId) === false
            || empty($this->getCardinalOrgUnitId($storeId))
            || empty($this->getCardinalSecretKeyId($storeId))
            || empty($this->getCardinalSecretKey($storeId))) {
            return false;
        }

        return true;
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
     * Get the Cardinal Cruise Songbird.js library URL for the configured environment.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCardinalSongbirdUrl($storeId = null)
    {
        if ($this->isSandboxMode($storeId)) {
            return static::CARDINAL_TEST;
        }

        return static::CARDINAL_LIVE;
    }
}
