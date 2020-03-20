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
        $session->setSessionId($context->getRequest()->getParam('req_merchant_defined_data99'));
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
