<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Controller\Adminhtml\SecureAccept;

use Magento\Framework\Controller\ResultFactory;

/**
 * GetParams Class
 */
class GetParams extends \Magento\Backend\App\Action
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\BackendRequest
     */
    protected $secureAcceptRequest;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\BackendRequest $secureAcceptRequest
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\BackendRequest $secureAcceptRequest,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);

        $this->secureAcceptRequest = $secureAcceptRequest;
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
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
            $this->initCustomer();

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
     * Initialize the customer for the current scope, if relevant.
     *
     * This is how we know what customer the current request should process for. Customer view gives ID param.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
