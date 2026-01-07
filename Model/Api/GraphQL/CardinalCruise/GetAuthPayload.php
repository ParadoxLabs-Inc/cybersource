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
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Api\GraphQL\CardinalCruise;

/**
 * GetAuthPayload Class
 */
class GetAuthPayload implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    /**
     * @var \ParadoxLabs\TokenBase\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor
     */
    protected $persistor;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator
     */
    protected $jsonWebTokenGenerator;

    /**
     * GetParams constructor.
     *
     * @param \ParadoxLabs\TokenBase\Model\Api\GraphQL $graphQL
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $persistor
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator $jsonWebTokenGenerator
     */
    public function __construct(
        \ParadoxLabs\TokenBase\Model\Api\GraphQL $graphQL,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $persistor,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator $jsonWebTokenGenerator
    ) {
        $this->graphQL = $graphQL;
        $this->quoteRepository = $quoteRepository;
        $this->persistor = $persistor;
        $this->jsonWebTokenGenerator = $jsonWebTokenGenerator;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return mixed|\Magento\Framework\GraphQl\Query\Resolver\Value
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $this->graphQL->authenticate($context);

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->graphQL->getQuote($context->getUserId(), $args['input']['cartId'] ?? '');
        if (empty($quote->getBillingAddress()->getEmail()) && !empty($args['input']['guestEmail'])) {
            $quote->getBillingAddress()->setEmail($args['input']['guestEmail']);
        }

        $enrollReply = $this->persistor->loadPayerAuthEnrollReply($quote->getPayment());

        return [
            'authPayload'  => json_encode($this->getAuthPayload($enrollReply)),
            'orderPayload' => json_encode($this->getOrderPayload($enrollReply, $quote)),
            'JWT' => $this->jsonWebTokenGenerator->getJwt($quote),
        ];
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
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return array
     */
    protected function getOrderPayload(array $enrollReply, \Magento\Quote\Api\Data\CartInterface $quote)
    {
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
