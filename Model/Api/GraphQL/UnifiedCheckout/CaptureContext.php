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

namespace ParadoxLabs\CyberSource\Model\Api\GraphQL\UnifiedCheckout;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\GraphQL as CaptureContextService;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;

/**
 * GraphQL (headless storefront) Unified Checkout capture-context resolver.
 *
 * Replaces the Secure Acceptance GetParams resolver: authenticates the request, sources the cart
 * and input args via the C1 GraphQL service, and returns the capture-context JWT.
 */
class CaptureContext implements ResolverInterface
{
    /**
     * CaptureContext constructor.
     *
     * @param GraphQL $graphQL
     * @param CaptureContextService $captureContext
     */
    public function __construct(
        protected readonly GraphQL $graphQL,
        protected readonly CaptureContextService $captureContext
    ) {
    }

    /**
     * Generate and return the Unified Checkout capture-context JWT.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array<string, string>
     * @throws \Throwable
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

        $this->captureContext->setGraphQLContext($context, $args['input']);

        return [
            'captureContext' => $this->captureContext->generate(),
        ];
    }
}
