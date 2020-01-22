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
    const ENDPOINT_LIVE = 'https://secureacceptance.cybersource.com';
    const ENDPOINT_TEST = 'https://testsecureacceptance.cybersource.com';

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
     * Get the Secure Acceptance checkout profile ID.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getCheckoutProfileId($storeId = null)
    {
        return $this->getConfigValue('checkout_profile_id', $storeId);
    }

    /**
     * Get the Secure Acceptance checkout access key.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getAccessKey($storeId = null)
    {
        return $this->getConfigValue('access_key', $storeId);
    }

    /**
     * Get the Secure Acceptance checkout secret key.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getSecretKey($storeId = null)
    {
        return $this->getConfigValue('secret_key', $storeId);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getSecureAcceptanceEndpoint($path)
    {
        if ($this->isSandboxMode()) {
            return static::ENDPOINT_TEST . $path;
        }

        return static::ENDPOINT_LIVE . $path;
    }

    // TODO: Add more getters, probably
}
