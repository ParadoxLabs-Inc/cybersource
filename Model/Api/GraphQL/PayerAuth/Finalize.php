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

use Magento\Quote\Api\Data\CartInterface;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;

/**
 * GraphQL (headless storefront) Payer Authentication finalize resolver.
 *
 * Runs after a challenge: fetches the authentication result for the persisted attempt and returns
 * the reclassified outcome. Authentication data itself stays server-side.
 */
class Finalize extends AbstractResolver
{
    /**
     * Run authentication-results for the authorized cart.
     *
     * @param Management $management
     * @param array<string, mixed> $input
     * @param CartInterface $quote
     * @return array<string, mixed>
     */
    protected function execute(Management $management, array $input, CartInterface $quote): array
    {
        return $this->resultPayload($management->finalize());
    }
}
