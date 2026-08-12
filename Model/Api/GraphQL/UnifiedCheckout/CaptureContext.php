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

use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\GraphQL as CaptureContextService;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use Throwable;

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
     * @param Config $config
     */
    public function __construct(
        protected readonly GraphQL $graphQL,
        protected readonly CaptureContextService $captureContext,
        protected readonly Config $config
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
     * @throws GraphQlInputException
     * @throws Throwable
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $this->graphQL->authenticate($context);

        // Guard before any config-credential or gateway work: a disabled method must fail with a
        // clean "not available" error, not a credential/config error from deeper in the stack.
        if (!$this->config->moduleIsActive($this->getStoreId($context))) {
            throw new GraphQlInputException(__('This payment method is not available.'));
        }

        if (!is_array($args) || !isset($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('Required `input` argument is missing.'));
        }

        $this->captureContext->setGraphQLContext($context, $args['input']);

        try {
            return [
                'captureContext' => $this->captureContext->generate(),
            ];
        } catch (LocalizedException $exception) {
            if ($exception instanceof ClientAware) {
                // Already a client-safe GraphQL exception (authorization/no-such-entity/input);
                // rethrow untouched so its category and message survive.
                throw $exception;
            }

            // Non-GraphQL LocalizedExceptions (e.g. StateException for missing REST credentials)
            // would otherwise be masked as "Internal server error" outside developer mode. Their
            // messages are merchant-safe configuration errors; surface them to the caller.
            throw new GraphQlInputException(__($exception->getMessage()), $exception);
        }
    }

    /**
     * Derive the store ID from the resolver context, for config scoping.
     *
     * @param ContextInterface $context
     * @return int|null
     */
    protected function getStoreId(ContextInterface $context): ?int
    {
        try {
            // The extension-attribute/store chain is not null-safe end to end; degrade to the
            // default scope on a missing/partial resolver context.
            return (int)$context->getExtensionAttributes()->getStore()->getId();
        } catch (Throwable) {
            return null;
        }
    }
}
