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
