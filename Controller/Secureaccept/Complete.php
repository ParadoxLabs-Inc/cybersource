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

namespace ParadoxLabs\CyberSource\Controller\Secureaccept;

/**
 * Completed Class
 */
class Complete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Hmac
     */
    protected $hmac;

    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac,
        \ParadoxLabs\CyberSource\Helper\Data $helper,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        parent::__construct($context);

        // Bypass form key validation for this incoming POST request.
        $this->getRequest()->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');

        $this->hmac = $hmac;
        $this->helper = $helper;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        // TODO: Move this stuff into a block ?.
        $post = $this->getRequest()->getPostValue();

        $this->helper->log('cybersource', json_encode($post), true);
        $this->helper->log(
            'cybersource',
            'HMAC validation: '.($this->hmac->validateSignature($post) ? 'true' : 'false'),
            true
        );

        /** @var \Magento\Framework\View\Result\Page $result */
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        return $result;
    }
}
