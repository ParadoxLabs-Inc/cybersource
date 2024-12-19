<?php declare(strict_types=1);
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

namespace ParadoxLabs\CyberSource\Block\Adminhtml\Config;

class P12Certificate extends \Magento\Config\Block\System\Config\Form\Field\File
{
    public function getValue()
    {
        $value = parent::getValue();

        if (!empty($value)) {
            $value = json_decode($value, true);
            return sprintf(
                '%s (%s)',
                $value['name'],
                date('Y-m-d', strtotime($value['uploaded']))
            );
        }

        return $value;
    }
}
