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
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator
     */
    protected $jsonWebTokenGenerator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $persistor
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator $jsonWebTokenGenerator
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $persistor,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator $jsonWebTokenGenerator,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);

        $this->persistor = $persistor;
        $this->jsonWebTokenGenerator = $jsonWebTokenGenerator;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
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
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->checkoutSession->getQuote();

            $enrollReply = $this->persistor->loadPayerAuthEnrollReply($quote->getPayment());

            $payload = [
                'authPayload'  => $this->getAuthPayload($enrollReply),
                'orderPayload' => $this->getOrderPayload($enrollReply),
                'JWT' => $this->jsonWebTokenGenerator->getJwt($this->checkoutSession->getQuote()),
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
            'AcsUrl' => $enrollReply['acsURL'],
            'Payload' => $enrollReply['paReq'],
        ];
    }

    /**
     * @param array $enrollReply
     * @return array
     */
    protected function getOrderPayload(array $enrollReply)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        if (empty($quote->getReservedOrderId())) {
            $quote->reserveOrderId();
            $this->quoteRepository->save($quote);
        }

        return [
            'OrderDetails' => [
                'TransactionId' => $enrollReply['authenticationTransactionID'],
                'OrderNumber' => $quote->getReservedOrderId(),
            ],
        ];
    }
}
