<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <support@paradoxlabs.com>
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

            $avs = $info->getData('cc_avs_status') ?: $info->getAdditionalInformation('ccAuthReply.avsCode');
            if (!empty($avs)) {
                $data[(string)__('AVS Response')] = $this->helper->translateAvs($avs);
            }

            $cvn = $info->getData('cc_cid_status') ?: $info->getAdditionalInformation('ccAuthReply.cvCode');
            if (!empty($cvn)) {
                $data[(string)__('CVN Response')] = $this->helper->translateCvn($cvn);
            }

            if (!empty($info->getAdditionalInformation('afsReply.afsResult'))) {
                $data[(string)__('Fraud Risk Score')] = __(
                    '%1/100',
                    $info->getAdditionalInformation('afsReply.afsResult')
                );
            }

            if (!empty($info->getAdditionalInformation('afsReply.afsFactorCode'))) {
                $riskFactors = explode('^', (string)$info->getAdditionalInformation('afsReply.afsFactorCode'));
                foreach ($riskFactors as $code) {
                    $data[(string)__('Risk Factor')] = $this->helper->translateRiskFactor($code);
                }
            }
        }

        $transport->setData(array_merge($transport->getData(), $data));

        return $transport;
    }
}
