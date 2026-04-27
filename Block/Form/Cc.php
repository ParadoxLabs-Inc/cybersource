<?php declare(strict_types=1);
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
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Block\Form;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Method\Factory;

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
     * @param Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param Data $helper
     * @param \Magento\Customer\Model\Session $customerSession *Proxy
     * @param Session $checkoutSession *Proxy
     * @param Factory $tokenbaseMethodFactory
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        Session $checkoutSession,
        Factory $tokenbaseMethodFactory,
        protected readonly Config $config,
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
