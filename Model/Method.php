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

namespace ParadoxLabs\CyberSource\Model;

/**
 * Payment method
 */
class Method extends \ParadoxLabs\TokenBase\Model\AbstractMethod
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Gateway
     */
    protected $gateway;

    /**
     * Initialize/return the API gateway class.
     *
     * @api
     *
     * @return \ParadoxLabs\CyberSource\Model\Gateway
     */
    public function gateway()
    {
        if ($this->gateway->isInitialized() !== true) {
            $this->gateway->init([
                'checkout_profile_id' => $this->getConfigData('checkout_profile_id'),
                'access_key' => $this->getConfigData('access_key'),
                'secret_key' => $this->getConfigData('secret_key'),
                'test_mode' => $this->getConfigData('test'),
            ]);
        }

        return $this->gateway;
    }
}
