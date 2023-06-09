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

namespace ParadoxLabs\CyberSource\Block\Form;

/**
 * Credit card input form on checkout
 */
class Cc extends \ParadoxLabs\TokenBase\Block\Form\Cc
{
    /**
     * @var string
     */
    protected $_template = 'ParadoxLabs_CyberSource::form/cc.phtml';

    /**
     * @var string
     */
    protected $brandingImage = 'ParadoxLabs_CyberSource::images/logo.webp';

    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession *Proxy
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     * @param \ParadoxLabs\TokenBase\Model\Method\Factory $tokenbaseMethodFactory
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \ParadoxLabs\TokenBase\Model\Method\Factory $tokenbaseMethodFactory,
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $paymentConfig,
            $helper,
            $customerSession,
            $checkoutSession,
            $tokenbaseMethodFactory,
            $data
        );

        $this->config = $config;
    }

    /**
     * Get the Decision Manager fingerprint script URL, including session key.
     *
     * @return string|null
     */
    public function getFingerprintUrl()
    {
        return $this->config->getFingerprintUrl(
            $this->checkoutSession->getQuoteId()
        );
    }
}
