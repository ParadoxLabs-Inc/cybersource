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

namespace ParadoxLabs\CyberSource\Controller\CardinalCruise;

use Magento\Framework\Controller\ResultFactory;

/**
 * GetAuthPayload Class
 */
class GetAuthPayload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor
     */
    protected $persistor;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $persistor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $persistor
    ) {
        parent::__construct($context);

        $this->persistor = $persistor;
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
            $enrollReply = $this->persistor->loadPayerAuthEnrollReply();

            $payload = [
                'authPayload'  => $this->getAuthPayload($enrollReply),
                'orderPayload' => $this->getOrderPayload($enrollReply),
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
     * @param array $enrollReply
     * @return array
     */
    protected function getAuthPayload(array $enrollReply)
    {
        return [
            'AcsUrl' => $enrollReply['acs_url'],
            'Payload' => $enrollReply['pa_req'],
        ];
    }

    /**
     * @param array $enrollReply
     * @return array
     */
    protected function getOrderPayload(array $enrollReply)
    {
        return [
            // TODO
            'OrderDetails' => [
                'TransactionId' => $enrollReply['transaction_id'],
            ],
        ];
    }
}
