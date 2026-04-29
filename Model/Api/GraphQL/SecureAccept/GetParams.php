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

namespace ParadoxLabs\CyberSource\Model\Api\GraphQL\SecureAccept;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\GraphQLRequest;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;

class GetParams implements ResolverInterface
{
    /**
     * @var \ParadoxLabs\TokenBase\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\GraphQLRequest
     */
    protected $secureAcceptRequest;

    /**
     * GetParams constructor.
     *
     * @param \ParadoxLabs\TokenBase\Model\Api\GraphQL $graphQL
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\GraphQLRequest $secureAcceptRequest
     */
    public function __construct(
        GraphQL $graphQL,
        GraphQLRequest $secureAcceptRequest
    ) {
        $this->graphQL             = $graphQL;
        $this->secureAcceptRequest = $secureAcceptRequest;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|\Magento\Framework\GraphQl\Query\Resolver\Value
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $this->graphQL->authenticate($context);

        if (!is_array($args) || !isset($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('Required `input` argument is missing.'));
        }

        $this->secureAcceptRequest->setGraphQLContext($context, $args['input']);

        return [
            'iframeAction' => $this->secureAcceptRequest->getIframeUrl(),
            'iframeParams' => json_encode($this->secureAcceptRequest->getIframeParams()),
        ];
    }
}
