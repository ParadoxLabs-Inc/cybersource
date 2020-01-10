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

namespace ParadoxLabs\CyberSource\Block\Info;

/**
 * Credit card info block
 */
class Cc extends \ParadoxLabs\TokenBase\Block\Info\Cc
{
    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $helper;

    /**
     * Prepare credit card related payment info
     *
     * @param \Magento\Framework\DataObject|array $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport  = parent::_prepareSpecificInformation($transport);
        $data       = [];

        if ($this->getIsSecureMode() === false) {
            /** @var \Magento\Sales\Model\Order\Payment\Info $info */
            $info = $this->getInfo();

            // TODO: Update display info
            if ($info->getData('cc_avs_status')) {
                $avs = $info->getData('cc_avs_status');
            } else {
                $avs = $info->getAdditionalInformation('avs_result_code');
            }

            if ($info->getData('cc_cid_status')) {
                $ccv = $info->getData('cc_cid_status');
            } else {
                $ccv = $info->getAdditionalInformation('card_code_response_code');
            }

            if (!empty($avs)) {
                $data[(string)__('AVS Response')]   = $this->helper->translateAvs($avs);
            }

            if (!empty($ccv)) {
                $data[(string)__('CCV Response')]   = $this->helper->translateCcv($ccv);
            }
        }

        $transport->setData(array_merge($transport->getData(), $data));

        return $transport;
    }
}
