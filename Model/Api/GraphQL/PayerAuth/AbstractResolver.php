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

namespace ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth;

use GraphQL\Error\ClientAware;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface as QueryContextInterface;
use Magento\Quote\Api\Data\CartInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ManagementFactory;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Shared behavior for the headless (GraphQL) Payer Authentication mutations.
 *
 * Every mutation runs the same gate before it touches the gateway: the public API must be enabled,
 * the supplied cart must resolve AND belong to the caller (the hardened authz path shared with the
 * capture-context resolver — it fails CLOSED, authorization denials reach the client untouched),
 * and the payment method must be active in that cart's store.
 *
 * The Payer Authentication service resolves its quote from the checkout session or the user context,
 * neither of which applies to GraphQL; the cart authorized here is bound to a per-request Management
 * instance via its setQuote() seam, exactly as the guest REST wrapper does.
 */
abstract class AbstractResolver implements ResolverInterface
{
    /**
     * AbstractResolver constructor.
     *
     * @param GraphQL $graphQL
     * @param ManagementFactory $managementFactory
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected readonly GraphQL $graphQL,
        protected readonly ManagementFactory $managementFactory,
        protected readonly Config $config,
        protected readonly LoggerInterface $logger
    ) {
    }

    /**
     * Authorize the request and run the mutation against the caller's cart.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array<string, mixed>
     * @throws GraphQlInputException
     * @throws GraphQlAuthorizationException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $this->graphQL->authenticate($context);

        $input = $this->getInput($args);

        try {
            $quote = $this->getQuote($context, $input);

            // Guard before any config-credential or gateway work: a disabled method must fail with
            // a clean "not available" error, not a credential error from deeper in the stack.
            if (!$this->config->moduleIsActive((int)$quote->getStoreId())) {
                throw new GraphQlInputException(__('This payment method is not available.'));
            }

            /** @var Management $management */
            $management = $this->managementFactory->create();

            return $this->execute($management->setQuote($quote), $input);
        } catch (Throwable $exception) {
            throw $this->wrap($exception);
        }
    }

    /**
     * Run this mutation against a Management instance already bound to the authorized cart.
     *
     * @param Management $management
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    abstract protected function execute(Management $management, array $input): array;

    /**
     * Read and validate the mutation input argument.
     *
     * @param array|null $args
     * @return array<string, mixed>
     * @throws GraphQlInputException
     */
    protected function getInput(?array $args): array
    {
        if (!is_array($args) || !isset($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('Required `input` argument is missing.'));
        }

        return $args['input'];
    }

    /**
     * Resolve the cart for this request through the hardened authorization path.
     *
     * The supplied cart id must resolve, be active, and belong to the caller; guest carts are
     * addressable by possession of the masked id, as everywhere else. GraphQlAuthorizationException
     * and GraphQlNoSuchEntityException from that path ARE the answer and must reach the caller.
     *
     * @param mixed $context
     * @param array<string, mixed> $input
     * @return CartInterface
     * @throws GraphQlInputException
     * @throws GraphQlAuthorizationException
     */
    protected function getQuote(mixed $context, array $input): CartInterface
    {
        $cartId = $this->stringOrNull($input['cartId'] ?? null);

        if ($cartId === null) {
            // No session to fall back on here: without a cart there is nothing to authorize
            // against, so an absent cart id is refused rather than guessed.
            throw new GraphQlInputException(__('Required parameter "cartId" is missing.'));
        }

        if (!$context instanceof QueryContextInterface) {
            // Without the query context there is no caller identity to check the cart against;
            // deny rather than treat the request as an anonymous guest.
            throw new GraphQlAuthorizationException(
                __('The current user cannot perform operations on cart "%1"', $cartId)
            );
        }

        return $this->graphQL->getQuote($context->getUserId(), $cartId);
    }

    /**
     * Map an authentication outcome to the mutation payload. Never carries CAVV/ECI data.
     *
     * @param PayerAuthResultInterface $result
     * @return array<string, string|null>
     */
    protected function resultPayload(PayerAuthResultInterface $result): array
    {
        return [
            'status' => $result->getStatus(),
            'acsUrl' => $result->getAcsUrl(),
            'pareq' => $result->getPareq(),
        ];
    }

    /**
     * Normalize an input value to a non-empty string, or null.
     *
     * @param mixed $value
     * @return string|null
     */
    protected function stringOrNull(mixed $value): ?string
    {
        return is_scalar($value) && trim((string)$value) !== '' ? trim((string)$value) : null;
    }

    /**
     * Convert a failure into a client-safe GraphQL exception. Internals never reach the client.
     *
     * @param Throwable $exception
     * @return Throwable
     */
    protected function wrap(Throwable $exception): Throwable
    {
        if ($exception instanceof ClientAware) {
            // Already a client-safe GraphQL exception; rethrow untouched so its category and
            // message survive (an authorization denial must not surface as an input error).
            return $exception;
        }

        if ($exception instanceof AuthorizationException) {
            return new GraphQlAuthorizationException(__($exception->getMessage()), $exception);
        }

        if ($exception instanceof LocalizedException) {
            // Merchant/customer-safe messages (input validation, gateway unavailability) would
            // otherwise be masked as "Internal server error" outside developer mode.
            return new GraphQlInputException(__($exception->getMessage()), $exception);
        }

        $this->logger->error(
            sprintf(
                'CyberSource Payer Authentication GraphQL error: %s',
                $exception->getMessage()
            )
        );

        return new GraphQlInputException(
            __('Payer authentication is temporarily unavailable. Please try again.')
        );
    }
}
