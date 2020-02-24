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
