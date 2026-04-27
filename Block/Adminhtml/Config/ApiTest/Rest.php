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

namespace ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest;

use Magento\Framework\Phrase;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Method\Factory;
use Throwable;

class Rest extends AbstractTest
{
    const CREDENTIAL_KEYS
        = [
            'rest_secret_key_id',
            'rest_secret_key',
        ];

    /**
     * @param Context $context
     * @param Data $helper
     * @param StoreFactory $storeFactory
     * @param WebsiteFactory $websiteFactory
     * @param Factory $methodFactory
     * @param \ParadoxLabs\CyberSource\Model\Service\Rest $restClient
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        StoreFactory $storeFactory,
        WebsiteFactory $websiteFactory,
        Factory $methodFactory,
        protected readonly \ParadoxLabs\CyberSource\Model\Service\Rest $restClient,
        array $data = []
    ) {
        parent::__construct($context, $helper, $storeFactory, $websiteFactory, $methodFactory, $data);
    }

    /**
     * Validate the REST API keys.
     *
     * @return Phrase|string
     */
    protected function testApi()
    {
        try {
            $this->checkRequiredFields(static::CREDENTIAL_KEYS);
            $this->checkFormFactor();

            $this->testConnection();
        } catch (Throwable $e) {
            return $e->getMessage() . $this->getUserManualInstruction();
        }

        return __(
            'REST API connected successfully. (%1)',
            $this->getMethod()->getConfigData('test') ? __('SANDBOX') : __('PRODUCTION')
        );
    }

    /**
     * Validate whether the REST API creds match the expected form factor.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function checkFormFactor()
    {
        $keyId = (string)$this->getMethod()->getConfigData('rest_secret_key_id');
        if (preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/i', $keyId) === 0) {
            throw new LocalizedException(
                __('Secret Key ID is not in the expected format; please verify you\'ve entered the correct data.')
            );
        }

        if (strlen((string)$this->getMethod()->getConfigData('rest_secret_key')) < 32) {
            throw new LocalizedException(
                __('Secret Key is shorter than expected; please verify you\'ve entered the correct data.')
            );
        }
    }

    /**
     * Test the given REST API credentials.
     *
     * @return void
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function testConnection()
    {
        /**
         * NB: We do this by trying to fetch details of transaction id 1. It won't exist, but it'll throw an auth
         * failure first if invalid.
         *
         * VALID: {"message":"The requested resource does not exist"} (404 not found)
         * INVALID: {"response": {"rmsg": "Authentication Failed"}} (401 unauthorized)
         */

        try {
            $this->restClient->setStoreId($this->getStoreId());
            $this->restClient->get('/tss/v2/transactions/1');
        } catch (Throwable $exception) {
            if ($exception->getCode() === 404) {
                return;
            }

            if ($exception->getCode() === 401) {
                throw new LocalizedException(
                    __('%1: Your API credentials are invalid.', $exception->getMessage())
                );
            }

            throw $exception;
        }
    }
}
