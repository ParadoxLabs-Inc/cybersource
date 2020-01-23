<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model\Source;

/**
 * CardType Class
 */
class CardType
{
    const CARD_TYPE_MAP = [
        '001' => 'VI',
        '002' => 'MC',
        '003' => 'AE',
        '004' => 'DI',
        '005' => 'DN',
        '007' => 'JCB',
        '042' => 'MI',
    ];

    /**
     * Get Magento card type for the given CyberSource type code.
     *
     * @param $code
     * @return mixed|string
     */
    public function getType($code)
    {
        return array_key_exists($code, static::CARD_TYPE_MAP)
            ? static::CARD_TYPE_MAP[$code]
            : 'OT';
    }
}
