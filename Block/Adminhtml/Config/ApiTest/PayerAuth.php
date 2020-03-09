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

        if (strlen($this->getMethod()->getConfigData('cardinal_org_unit_id')) < 24) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Org Unit ID is shorter than expected; please verify you\'ve entered the correct data.')
            );
        }

        if (strlen($this->getMethod()->getConfigData('cardinal_secret_key_id')) < 24) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('API ID is shorter than expected; please verify you\'ve entered the correct data.')
            );
        }

        $key = $this->getMethod()->getConfigData('cardinal_secret_key');
        if (preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/i', $key) === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('API Key is not in the expected format; please verify you\'ve entered the correct data.')
            );
        }
    }
}
