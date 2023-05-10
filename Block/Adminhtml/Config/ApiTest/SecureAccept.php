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
 * SecureAccept Class
 */
class SecureAccept extends \ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\AbstractTest
{
    const CREDENTIAL_KEYS = [
        'secureaccept_profile_id',
        'secureaccept_access_key',
        'secureaccept_secret_key',
    ];

    /**
     * Validate the Secure Acceptance API keys.
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
                . '<small class="desc">* Note: We can\'t test Secure Acceptance. After configuring, please verify '
                . 'the credit card form loads on checkout.</small>',
                $this->getMethod()->getConfigData('test') ? __('SANDBOX') : __('PRODUCTION')
            );
        } catch (\Exception $e) {
            return $e->getMessage() . $this->getUserManualInstruction();
        }
    }

    /**
     * Validate whether the Secure Acceptance API creds match the expected form factor.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkFormFactor()
    {
        $profileId = (string)$this->getMethod()->getConfigData('secureaccept_profile_id');
        if (preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/i', $profileId) === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Profile ID is not in the expected format; please verify you\'ve entered the correct data.')
            );
        }

        if (strlen((string)$this->getMethod()->getConfigData('secureaccept_access_key')) !== 32) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Access Key is not the expected length; please verify you\'ve entered the correct data.')
            );
        }

        if (strlen((string)$this->getMethod()->getConfigData('secureaccept_secret_key')) < 256) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Secret Key is shorter than expected; please verify you\'ve entered the correct data.')
            );
        }
    }
}
