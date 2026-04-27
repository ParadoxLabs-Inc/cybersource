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

namespace ParadoxLabs\CyberSource\Controller\CardinalCruise;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor;
use Throwable;

class GetAuthPayload extends Action
{
    /**
     * @param Context $context
     * @param Persistor $persistor
     * @param JsonWebTokenGenerator $jsonWebTokenGenerator
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        protected readonly Persistor $persistor,
        protected readonly JsonWebTokenGenerator $jsonWebTokenGenerator,
        protected readonly Session $checkoutSession,
        protected readonly CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        /** @var Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            /** @var Quote $quote */
            $quote = $this->checkoutSession->getQuote();

            $enrollReply = $this->persistor->loadPayerAuthEnrollReply($quote->getPayment());

            $payload = [
                'authPayload' => $this->getAuthPayload($enrollReply),
                'orderPayload' => $this->getOrderPayload($enrollReply),
                'JWT' => $this->jsonWebTokenGenerator->getJwt($this->checkoutSession->getQuote()),
            ];

            $result->setData($payload);
        } catch (Throwable $exception) {
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
        /** @var Quote $quote */
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
