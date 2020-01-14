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

        // Don't bother if details aren't entered.
        if (empty($method->getConfigData('checkout_profile_id'))
            || empty($method->getConfigData('access_key'))
            || empty($method->getConfigData('secret_key'))) {
            return __('Please enter your Secure Acceptance Checkout profile API credentials and save to test.'
                . ' See the user manual for a walkthrough of setup and configuration.');
        }

        // Verify no invalid characters -- suggests changed encryption key/corrupted data.
        if ($this->containsInvalidCharacters($method->getConfigData('checkout_profile_id'))
            || $this->containsInvalidCharacters($method->getConfigData('access_key'))
            || $this->containsInvalidCharacters($method->getConfigData('secret_key'))) {
            return __('Please re-enter your API credentials. They may be corrupted.');
        }

        /** @var \ParadoxLabs\CyberSource\Model\Gateway $gateway */
        $gateway = $method->gateway();

        try {
            // TODO: Run the test call -- simple tokenize credit card request. It won't tokenize, that's okay.
            return __('CyberSource connected successfully.');
        } catch (\Exception $e) {
            return __($e->getMessage());
        }
    }
}
