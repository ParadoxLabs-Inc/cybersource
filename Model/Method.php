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
     * Initialize/return the API gateway class.
     *
     * @api
     *
     * @return \ParadoxLabs\TokenBase\Api\GatewayInterface
     */
    public function gateway()
    {
        // TODO: This
        if ($this->gateway->isInitialized() !== true) {
            $this->gateway->init([
                'login' => $this->getConfigData('login'),
                'password' => $this->getConfigData('trans_key'),
                'test_mode' => $this->getConfigData('test'),
            ]);
        }

        return $this->gateway;
    }
}
