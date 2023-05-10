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

namespace ParadoxLabs\CyberSource\Controller\SecureAccept;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

/**
 * GetParams Class
 */
class GetParams extends Action implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\FrontendRequest
     */
    protected $secureAcceptRequest;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKey;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\FrontendRequest $secureAcceptRequest
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKey
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\FrontendRequest $secureAcceptRequest,
        \Magento\Framework\Data\Form\FormKey\Validator $formKey
    ) {
        parent::__construct($context);

        $this->secureAcceptRequest = $secureAcceptRequest;
        $this->formKey = $formKey;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $payload = [
                'iframeAction' => $this->secureAcceptRequest->getIframeUrl(),
                'iframeParams' => $this->secureAcceptRequest->getIframeParams(),
            ];

            $result->setData($payload);
        } catch (\Exception $exception) {
            $result->setHttpResponseCode(400);
            $result->setData([
                'message' => $exception->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\Request\InvalidRequestException|null
     */
    public function createCsrfValidationException(
        \Magento\Framework\App\RequestInterface $request
    ): ?\Magento\Framework\App\Request\InvalidRequestException {
        $message = __('Invalid Form Key. Please refresh the page.');

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setHttpResponseCode(403);
        $result->setData([
            'message' => $message,
        ]);

        return new \Magento\Framework\App\Request\InvalidRequestException(
            $result,
            [$message]
        );
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return $this->formKey->validate($request);
    }
}
