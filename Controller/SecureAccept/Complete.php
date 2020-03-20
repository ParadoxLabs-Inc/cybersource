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

namespace ParadoxLabs\CyberSource\Controller\SecureAccept;

/**
 * Completed Class
 */
class Complete extends \Magento\Framework\App\Action\Action
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        // Initialize session with ID from the CyberSource payload, to prevent SameSite cookie issues.
        if ($context->getRequest()->getParam('req_merchant_defined_data99')) {
            session_commit();
            session_id($context->getRequest()->getParam('req_merchant_defined_data99'));
        }

        parent::__construct($context);

        // Bypass form key validation for this incoming POST request.
        $this->getRequest()->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
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
}
