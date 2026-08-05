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

namespace ParadoxLabs\CyberSource\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Drop the config rows for the integrations 4.0.0 removed: SOAP, Secure Acceptance, CardinalCommerce.
 *
 * These are dead settings after the Unified Checkout migration, and three of them (the SOAP transaction
 * key and certificate password, the Secure Acceptance secret key, the Cardinal secret key) are encrypted
 * credentials with nothing left to authenticate — leaving them in core_config_data keeps live secrets on
 * disk for an integration the module can no longer speak. The rows are deleted in every scope, which is
 * also what makes the patch idempotent: a re-run simply matches nothing.
 *
 * Deliberately NOT purged: `cardinal_active` and `cardinal_card_types`. Both are still live settings in
 * 4.0.0 — Payer Authentication now runs natively on the CyberSource merchant account — and deleting them
 * would silently turn 3DS off for every merchant who had it on.
 */
class PurgeLegacyConfig implements DataPatchInterface
{
    /**
     * Config paths removed in 4.0.0, exactly as they appeared in 3.x etc/adminhtml/system.xml.
     */
    private const REMOVED_PATHS = [
        // SOAP gateway (replaced by the REST API).
        'payment/paradoxlabs_cybersource/soap_auth_type',
        'payment/paradoxlabs_cybersource/soap_transaction_key',
        'payment/paradoxlabs_cybersource/soap_cert',
        'payment/paradoxlabs_cybersource/soap_cert_password',
        // Secure Acceptance hosted profile (replaced by Unified Checkout).
        'payment/paradoxlabs_cybersource/secureaccept_profile_id',
        'payment/paradoxlabs_cybersource/secureaccept_access_key',
        'payment/paradoxlabs_cybersource/secureaccept_secret_key',
        // CardinalCommerce portal credentials (Payer Auth now runs on the CyberSource account).
        'payment/paradoxlabs_cybersource/cardinal_org_unit_id',
        'payment/paradoxlabs_cybersource/cardinal_secret_key_id',
        'payment/paradoxlabs_cybersource/cardinal_secret_key',
        // Songbird.js library pins (the library is no longer loaded at all).
        'payment/paradoxlabs_cybersource/cardinal_songbird_url_live',
        'payment/paradoxlabs_cybersource/cardinal_songbird_url_test',
        'payment/paradoxlabs_cybersource/cardinal_songbird_sri_live',
        'payment/paradoxlabs_cybersource/cardinal_songbird_sri_test',
    ];

    /**
     * PurgeLegacyConfig constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Config $configResource
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Config $configResource,
    ) {
    }

    /**
     * Run patch
     *
     * @return $this
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->configResource->getConnection();

        $connection->delete(
            $this->configResource->getTable('core_config_data'),
            [$connection->quoteInto('path IN (?)', self::REMOVED_PATHS)]
        );

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * Get the config paths this patch removes.
     *
     * @return string[]
     */
    public static function getRemovedPaths(): array
    {
        return self::REMOVED_PATHS;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }
}
