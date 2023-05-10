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
 * PayerAuth Class
 */
class PayerAuth extends \ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\AbstractTest
{
    const CREDENTIAL_KEYS = [
        'cardinal_org_unit_id',
        'cardinal_secret_key_id',
        'cardinal_secret_key',
    ];

    /**
     * Validate the Payer Authentication API keys.
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function testApi()
    {
        try {
            $this->checkRequiredFields();
            $this->checkFormFactor();

            return __(
                'Keys entered successfully. (%1)<br />'
                . '<small class="desc">* Note: We can\'t test Cardinal Cruise. If the values are not correct, trying '
                . 'to complete 3D Secure on checkout will cause errors.</small>',
                $this->getMethod()->getConfigData('test') ? __('SANDBOX') : __('PRODUCTION')
            );
        } catch (\Exception $e) {
            return $e->getMessage() . $this->getUserManualInstruction();
        }
    }

    /**
     * Validate whether the Payer Authentication API creds match the expected form factor.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkFormFactor()
    {
        // NB: Not guaranteed these will be the same as our test data. May need to adjust the rules over time.

        if (strlen((string)$this->getMethod()->getConfigData('cardinal_org_unit_id')) < 24) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Org Unit ID is shorter than expected; please verify you\'ve entered the correct data.')
            );
        }

        if (strlen((string)$this->getMethod()->getConfigData('cardinal_secret_key_id')) < 24) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('API ID is shorter than expected; please verify you\'ve entered the correct data.')
            );
        }

        $key = (string)$this->getMethod()->getConfigData('cardinal_secret_key');
        if (preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/i', $key) === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('API Key is not in the expected format; please verify you\'ve entered the correct data.')
            );
        }
    }
}
