<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Chad Bender <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Block\Adminhtml\Config;

/**
 * ApiTest Class
 */
class ApiTest extends \ParadoxLabs\TokenBase\Block\Adminhtml\Config\ApiTest
{
    /**
     * @var string
     */
    protected $code = 'paradoxlabs_cybersource';

    /**
     * Test the API connection and report common errors.
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function testApi()
    {
        /** @var \ParadoxLabs\CyberSource\Model\Method $method */
        $method = $this->methodFactory->getMethodInstance($this->code);
        $method->setStore($this->getStoreId());

        $apiSettings = [
            'merchant_id',
            'soap_transaction_key',
            'secureaccept_profile_id',
            'secureaccept_access_key',
            'secureaccept_secret_key',
            'rest_secret_key_id',
            'rest_secret_key',
        ];

        // Don't test unless all details are entered and look valid.
        foreach ($apiSettings as $key) {
            if (empty($method->getConfigData($key))) {
                return __('Please enter all API credentials and save to test.'
                    . ' See the user manual for a walkthrough of setup and configuration.');
            }

            // Verify no non-ASCII characters -- suggests changed encryption key/corrupted data.
            if ($this->containsInvalidCharacters($method->getConfigData($key))) {
                return __('Please re-enter your API credentials. They may be corrupted.');
            }
        }

        /**
         * Unfortunately, we can only validate the SOAP connection (merchant_id, transaction_key). Better than nothing.
         */
        try {
            /** @var \ParadoxLabs\CyberSource\Model\Gateway $gateway */
            $gateway = $method->gateway();
            $gateway->testConnection();
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'UsernameToken') !== false) {
                return __('Your Merchant ID or SOAP API Transaction Key is incorrect.');
            }

            if ($e->getCode() === 101) {
                return __('CyberSource SOAP API connected successfully.') . ($method->getConfigData('test')
                        ? __(' (SANDBOX)')
                        : __(' (PRODUCTION)'));
            }

            return $e->getMessage();
        }
    }
}
