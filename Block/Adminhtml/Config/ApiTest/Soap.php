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

namespace ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest;

/**
 * Soap Class
 */
class Soap extends \ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\AbstractTest
{
    const CREDENTIAL_KEYS = [
        'organization_id',
        'merchant_id',
        'soap_transaction_key',
    ];

    /**
     * Validate and test the SOAP API keys.
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function testApi()
    {
        try {
            $this->checkRequiredFields();

            /** @var \ParadoxLabs\CyberSource\Model\Gateway $gateway */
            $gateway = $this->getMethod()->gateway();
            $gateway->testConnection();
        } catch (\Exception $e) {
            if (strpos((string)$e->getMessage(), 'UsernameToken') !== false) {
                return __('Your Merchant ID or SOAP API Transaction Key is incorrect.')
                    . $this->getUserManualInstruction();
            }

            if ($e->getCode() === 101) {
                return __(
                    'Simple Order API connected successfully. (%1)',
                    $this->getMethod()->getConfigData('test') ? __('SANDBOX') : __('PRODUCTION')
                );
            }

            return $e->getMessage() . $this->getUserManualInstruction();
        }

        return '';
    }
}
