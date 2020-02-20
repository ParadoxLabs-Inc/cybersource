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

namespace ParadoxLabs\CyberSource\Controller\Adminhtml\Secureaccept;

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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\BackendRequest $secureAcceptRequest
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\BackendRequest $secureAcceptRequest
    ) {
        parent::__construct($context);

        $this->secureAcceptRequest = $secureAcceptRequest;
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
}
