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
     * Wired into clientReferenceInformation.partner.solutionId on all REST request DTOs (Iter 7 T4).
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
        // Fallback agrees with the config.xml default: checkout already collects billing.
        return strtoupper($this->getConfigValue('uc_billing_type', $storeId) ?: 'NONE');
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
     * Get whether Payer Authentication (3D Secure) is enabled.
     *
     * Unlike 3.x, this is the flag alone: Payer Auth runs on the CyberSource merchant account via
     * the normal REST keys, so there are no Cardinal portal credentials left to validate.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isPayerAuthEnabled($storeId = null): bool
    {
        return (bool)$this->getConfigValue('cardinal_active', $storeId);
    }

    /**
     * Get whether Payer Authentication must have run before an order may be placed.
     *
     * The storefront clients always authenticate when Payer Auth is on, so this only governs
     * REST/GraphQL callers that skip the payer-auth calls.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isPayerAuthRequired($storeId = null): bool
    {
        return (bool)$this->getConfigValue('payer_auth_required', $storeId);
    }

    /**
     * Get whether Payer Authentication is enabled for a specific card type.
     *
     * @param string $ccType
     * @param int|null $storeId
     * @return bool
     */
    public function isPayerAuthEnabledForType(string $ccType, $storeId = null): bool
    {
        if ($this->isPayerAuthEnabled($storeId) === false) {
            return false;
        }

        $enabledTypes = explode(',', (string)$this->getConfigValue('cardinal_card_types', $storeId));

        return in_array($ccType, $enabledTypes, true);
    }

    /**
     * Get the additional origins permitted as payer-auth challenge return targets.
     *
     * Headless/GraphQL storefronts run on their own origin, so their return URL is not on the
     * store's host. This is the merchant's allowlist for those: one origin per line, normalized
     * only to trimmed lowercase strings here — shape validation belongs to the consumer.
     *
     * @param int|null $storeId
     * @return string[]
     */
    public function getPayerAuthReturnOrigins($storeId = null): array
    {
        $value = (string)$this->getConfigValue('payer_auth_return_origins', $storeId);

        if (trim($value) === '') {
            return [];
        }

        $origins = array_map(
            static fn($origin): string => strtolower(trim((string)$origin)),
            preg_split('/[\r\n]+/', $value) ?: []
        );

        return array_values(array_filter($origins, static fn(string $origin): bool => $origin !== ''));
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
     * Whether Decision Manager should also screen the $0 card-storage authorization.
     *
     * 3.x parity: card storage is not screened unless the merchant opts in, because screening every
     * add-card raises transaction fees. See Response::buildZeroDollarRequest().
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isCardStorageValidationEnabled($storeId = null): bool
    {
        return (bool)$this->getConfigValue('validate_card_storage', $storeId);
    }

    /**
     * Whether to place the order automatically after Unified Checkout new-card entry.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isUcAutoPlaceOrderEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue('uc_auto_place_order', $storeId);
    }

    /**
     * Whether to keep Unified Checkout's own review screen after card entry, at customer checkout.
     *
     * Off by default: it repeats details the customer just typed, and Magento's own review step
     * follows it either way. It is the only place UC applies the buttonType label, though, so a
     * merchant who wants the last click before an auto-placed order to read something other than
     * the card form's fixed "Continue" turns this on.
     *
     * Typed, unlike its older neighbours: buildRequest() feeds this straight into the request tree,
     * where a null would be filtered out of the payload instead of sent as an explicit false.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isUcReviewStepEnabled(?int $storeId = null): bool
    {
        return (bool)$this->getConfigValue('uc_review_step', $storeId);
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

        return array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)$value) ?: [])));
    }
}
