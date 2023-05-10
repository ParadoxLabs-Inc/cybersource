<?php
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 * @link https://support.paradoxlabs.com
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
