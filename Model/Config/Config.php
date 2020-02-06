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
     * @return mixed
     */
    protected function getConfigValue($key, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'payment/' . static::CODE . '/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
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
     */
    public function getSoapTransactionKey($storeId = null)
    {
        return $this->getConfigValue('soap_transaction_key', $storeId);
    }

    /**
     * Get the Secure Acceptance checkout profile ID.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getSecureAcceptProfileId($storeId = null)
    {
        return $this->getConfigValue('secureaccept_profile_id', $storeId);
    }

    /**
     * Get the Secure Acceptance checkout access key.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getSecureAcceptAccessKey($storeId = null)
    {
        return $this->getConfigValue('secureaccept_access_key', $storeId);
    }

    /**
     * Get the Secure Acceptance checkout secret key.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getSecureAcceptSecretKey($storeId = null)
    {
        return $this->getConfigValue('secureaccept_secret_key', $storeId);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getSecureAcceptEndpoint($path)
    {
        if ($this->isSandboxMode()) {
            return static::SECUREACCEPT_TEST . $path;
        }

        return static::SECUREACCEPT_LIVE . $path;
    }

    /**
     * @return string
     */
    public function getSoapWsdl()
    {
        if ($this->isSandboxMode()) {
            return static::SOAP_TEST;
        }

        return static::SOAP_LIVE;
    }

    // TODO: Add more getters, probably
}
