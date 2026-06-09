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

namespace ParadoxLabs\CyberSource\Controller\Adminhtml\UnifiedCheckout;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Backend;
use Throwable;

/**
 * Admin (backend) Unified Checkout capture-context endpoint.
 *
 * Replaces the Secure Acceptance GetParams trio: returns the capture-context JWT for admin
 * order-create / add-card flows.
 */
class CaptureContext extends Action
{
    /**
     * CaptureContext constructor.
     *
     * @param Context $context
     * @param Backend $captureContext
     * @param Registry $registry
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        protected readonly Backend $captureContext,
        protected readonly Registry $registry,
        protected readonly CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
    }

    /**
     * Generate and return the Unified Checkout capture-context JWT.
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        /** @var Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $this->initCustomer();

            $result->setData([
                'captureContext' => $this->captureContext->generate(),
            ]);
        } catch (Throwable $exception) {
            $result->setHttpResponseCode(400);
            $result->setData([
                'message' => $exception->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * Initialize the customer for the current scope, if relevant.
     *
     * This is how we know what customer the current request should process for. Customer view gives ID param.
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function initCustomer()
    {
        if (is_numeric($this->getRequest()->getParam('id'))) {
            $customer = $this->customerRepository->getById(
                $this->getRequest()->getParam('id')
            );

            $this->registry->register('current_customer', $customer);
        }
    }
}
