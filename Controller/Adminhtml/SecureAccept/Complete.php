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

namespace ParadoxLabs\CyberSource\Controller\Adminhtml\SecureAccept;

/**
 * Completed Class
 */
class Complete extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        // Initialize session with ID from the CyberSource payload, to prevent SameSite cookie issues.
        $session = $context->getSession();
        $session->writeClose();
        $session->setSessionId($context->getRequest()->getParam('req_merchant_defined_data98'));
        $session->start();

        $authSession = $context->getAuth()->getAuthStorage();
        if ($authSession instanceof \Magento\Backend\Model\Auth\Session) {
            $authSession->writeClose();
            $authSession->setSessionId($session->getSessionId());
            $authSession->start();
        }

        parent::__construct($context);

        // Bypass form key validation for this incoming POST request.
        $this->getRequest()->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->getRequest()->setParam('form_key', $formKey->getFormKey());
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $result */
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        return $result;
    }

    /**
     * Check url keys.
     *
     * @return bool
     * @see \Magento\Backend\App\Request\BackendValidator for default request validation.
     */
    public function _processUrlKeys()
    {
        return true;
    }
}
