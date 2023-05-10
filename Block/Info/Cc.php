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
