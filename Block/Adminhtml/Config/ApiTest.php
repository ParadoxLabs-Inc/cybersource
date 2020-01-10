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

namespace ParadoxLabs\FirstData\Block\Adminhtml\Config;

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
     * Does not validate Method Token or API Secret other than making sure it is not empty
     * Validating those fields requires creating a test payment: potential future improvement for this
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function testApi()
    {
        /** @var \ParadoxLabs\FirstData\Model\Method $method */
        $method = $this->methodFactory->getMethodInstance($this->code);
        $method->setStore($this->getStoreId());

        // TODO: All of this

        // Don't bother if details aren't entered.
        if ($method->getConfigData('login') == '' || $method->getConfigData('trans_key') == ''
            || $method->getConfigData('api_secret') == '' || $method->getConfigData('js_security_key') == '') {
            return 'Enter API credentials and save to test.';
        }

        // Verify no invalid characters -- suggests changed encryption key/corrupted data.
        if ($this->containsInvalidCharacters($method->getConfigData('login'))
            || $this->containsInvalidCharacters($method->getConfigData('trans_key'))
            || $this->containsInvalidCharacters($method->getConfigData('api_secret'))
            || $this->containsInvalidCharacters($method->getConfigData('js_security_key'))) {
            return __('Please re-enter your API credentials. They may be corrupted.');
        }

        /** @var \ParadoxLabs\FirstData\Model\Gateway $gateway */
        $gateway = $method->gateway();

        try {
            // Run the test call -- simple tokenize credit card request. It won't tokenize, that's okay.
            $gateway->setParameter('cardholder_name', 'Test');
            $gateway->setParameter('credit_card_type', 'visa');
            $gateway->setParameter('card_number', '4100000000000000');
            $gateway->setParameter('cvv', '123');
            $gateway->setParameter('exp_date', '0199');
            //Passing in true to make sure nothing is logged for API Test
            $gateway->tokenizeCreditCard(true);

            return __('First Data connected successfully.');
        } catch (\Exception $e) {
            /*
             * Since we are forcing the tokenization to fail if this error is being returned,
             * the authentication has passed
             */
            if (strpos($e->getMessage(), 'invalid_card_number') !== false) {
                return __('First Data connected successfully.');
            }

            return __($e->getMessage());
        }
    }
}
