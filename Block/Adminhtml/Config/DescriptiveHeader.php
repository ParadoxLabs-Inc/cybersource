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

namespace ParadoxLabs\CyberSource\Block\Adminhtml\Config;

/**
 * DescriptiveHeader Class
 */
class DescriptiveHeader extends \Magento\Config\Block\System\Config\Form\Field\Heading
{
    /**
     * Render settings subheader with comment description.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return sprintf(
            '<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5">'
                . '<h4 id="%s">%s</h4>'
                . '<p class="comment" style="padding-left:2.8rem">%s</p>'
            . '</td></tr>',
            $element->getHtmlId(),
            $element->getHtmlId(),
            $element->getLabel(),
            $element->getComment()
        );
    }
}
