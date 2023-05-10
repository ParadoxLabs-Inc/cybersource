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

namespace ParadoxLabs\CyberSource\Block\Secureaccept;

/**
 * Complete Class
 */
class Complete extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Response
     */
    protected $responseHandler;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Response $responseHandler
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Response $responseHandler,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->responseHandler = $responseHandler;
    }

    /**
     * Get/create stored card record based on the input request data from Secure Acceptance.
     *
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface
     */
    public function getCard()
    {
        $post = $this->getRequest()->getPostValue();

        return $this->responseHandler->saveCard($post);
    }
}
