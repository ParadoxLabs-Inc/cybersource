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
 * AbstractTest Class
 */
abstract class AbstractTest extends \ParadoxLabs\TokenBase\Block\Adminhtml\Config\ApiTest
{
    const CREDENTIAL_KEYS = [];
    const USER_MANUAL_URL = 'https://store.paradoxlabs.com/media/wysiwyg/ParadoxLabs-CyberSource-M2-user-manual.pdf';

    /**
     * @var \ParadoxLabs\CyberSource\Model\Method
     */
    protected $method;

    /**
     * Validate required creds fields for the tested config section.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkRequiredFields()
    {
        $method = $this->getMethod();

        // Don't test unless all details are entered and look valid.
        foreach (static::CREDENTIAL_KEYS as $key) {
            if (empty($method->getConfigData($key))) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please complete all of these settings and save to test.')
                );
            }

            // Verify no non-ASCII characters -- suggests changed encryption key/corrupted data.
            if ($this->containsInvalidCharacters($method->getConfigData($key))) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please re-enter your API keys. They may be corrupted.')
                );
            }
        }
    }

    /**
     * Get the payment method model, with the correct scope.
     *
     * @return \ParadoxLabs\CyberSource\Model\Method
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getMethod()
    {
        if ($this->method !== null) {
            return $this->method;
        }

        /** @var \ParadoxLabs\CyberSource\Model\Method $method */
        $this->method = $this->methodFactory->getMethodInstance(
            \ParadoxLabs\CyberSource\Model\Config\Config::CODE
        );
        $this->method->setStore($this->getStoreId());

        return $this->method;
    }

    /**
     * Get the user manual instruction string and link.
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getUserManualInstruction()
    {
        return __(
            '<br /><small class="desc">See the <a href="%1" target="_blank">User Manual</a> for instructions and '
            . 'additional information.</small>',
            static::USER_MANUAL_URL
        );
    }
}
