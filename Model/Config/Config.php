<?php declare(strict_types=1);
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

namespace ParadoxLabs\CyberSource\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const CODE = 'paradoxlabs_cybersource';
    const SOLUTION_ID = 'DEQXVEEG';

    /**
     * Gateway URLs
     */
    const REST_LIVE = 'https://api.cybersource.com';
    const REST_TEST = 'https://apitest.cybersource.com';

    /**
     * @var int|null
     */
    protected $storeId;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(protected readonly ScopeConfigInterface $scopeConfig)
    {
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
        return trim(
            (string)$this->scopeConfig->getValue(
                'payment/' . static::CODE . '/' . $key,
                ScopeInterface::SCOPE_STORE,
                $storeId ?? $this->storeId
            )
        );
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
     * Get the REST secret key ID.
     *
     * @param int|null $storeId
     * @return mixed
     * @throws StateException
     */
    public function getRestSecretKeyId($storeId = null)
    {
        $value = $this->getConfigValue('rest_secret_key_id', $storeId);

        if (empty($value)) {
            throw new StateException(
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
     * @throws StateException
     */
    public function getRestSecretKey($storeId = null)
    {
        $value = $this->getConfigValue('rest_secret_key', $storeId);

        if (empty($value)) {
            throw new StateException(
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
     * Get the extension solution ID.
     *
     * NB: With the SOAP gateway removed, getSolutionId/getClientName/getClientVersion have no runtime
     * consumer. Kept (with their client_name/client_version config) as candidates for the REST
     * clientReferenceInformation.partner solutionId/applicationName/applicationVersion fields (Iter 7).
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
     * Get the Unified Checkout UC.js client version selector.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUcClientVersion($storeId = null)
    {
        return $this->getConfigValue('uc_client_version', $storeId) ?: '0.34';
    }

    /**
     * Get the Unified Checkout allowed target origins (exact HTTPS origins).
     *
     * @param int|null $storeId
     * @return string[]
     */
    public function getUcTargetOrigins($storeId = null)
    {
        return $this->explodeConfigList('uc_target_origins', $storeId);
    }

    /**
     * Get the Unified Checkout allowed card networks.
     *
     * @param int|null $storeId
     * @return string[]
     */
    public function getUcAllowedCardNetworks($storeId = null)
    {
        return $this->explodeConfigList('uc_allowed_card_networks', $storeId);
    }

    /**
     * Get the Unified Checkout allowed payment types (e.g. PANENTRY, APPLEPAY).
     *
     * @param int|null $storeId
     * @return string[]
     */
    public function getUcAllowedPaymentTypes($storeId = null)
    {
        return $this->explodeConfigList('uc_allowed_payment_types', $storeId);
    }

    /**
     * Get the Unified Checkout captureMandate billingType (FULL|PARTIAL|NONE).
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUcBillingType($storeId = null)
    {
        return strtoupper($this->getConfigValue('uc_billing_type', $storeId) ?: 'FULL');
    }

    /**
     * Get the Unified Checkout ISO-2 country, falling back to store general country.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUcCountry($storeId = null)
    {
        $value = $this->getConfigValue('uc_country', $storeId);

        if (empty($value)) {
            $value = trim((string)$this->scopeConfig->getValue(
                'general/country/default',
                ScopeInterface::SCOPE_STORE,
                $storeId ?? $this->storeId
            ));
        }

        return strtoupper($value ?: 'US');
    }

    /**
     * Get the Unified Checkout locale, falling back to the store general locale.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUcLocale($storeId = null)
    {
        $value = $this->getConfigValue('uc_locale', $storeId);

        if (empty($value)) {
            $value = trim((string)$this->scopeConfig->getValue(
                'general/locale/code',
                ScopeInterface::SCOPE_STORE,
                $storeId ?? $this->storeId
            ));
        }

        return $value ?: 'en_US';
    }

    /**
     * Whether 3DS (consumerAuthentication) is enabled for Unified Checkout.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function is3dsEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue('uc_3ds', $storeId);
    }

    /**
     * Whether Decision Manager is enabled for Unified Checkout.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDecisionManagerEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue('uc_decision_manager', $storeId);
    }

    /**
     * Map the Magento payment_action to the UC completeMandate type.
     *
     * authorize -> AUTH, authorize_capture -> CAPTURE; anything else defaults to AUTH.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUcCompleteMandateType($storeId = null)
    {
        return $this->getConfigValue('payment_action', $storeId) === 'authorize_capture'
            ? 'CAPTURE'
            : 'AUTH';
    }

    /**
     * Read a comma-delimited config value into a trimmed, non-empty string list.
     *
     * @param string $key
     * @param int|null $storeId
     * @return string[]
     */
    protected function explodeConfigList($key, $storeId = null)
    {
        $value = $this->getConfigValue($key, $storeId);

        if ($value === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
}
