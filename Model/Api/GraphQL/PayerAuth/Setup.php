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

use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;

/**
 * GraphQL (headless storefront) Payer Authentication setup resolver.
 *
 * Starts an attempt for the cart's instrument and returns the device-data-collection parameters,
 * or `skipped` when Payer Authentication is off or excluded for the card type.
 */
class Setup extends AbstractResolver
{
    /**
     * Run authentication-setups for the authorized cart.
     *
     * @param Management $management
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    protected function execute(Management $management, array $input): array
    {
        $result = $management->setup(
            $this->stringOrNull($input['transientToken'] ?? null),
            $this->stringOrNull($input['cardHash'] ?? null)
        );

        return [
            'skipped' => $result->getSkipped(),
            'accessToken' => $result->getAccessToken(),
            'deviceDataCollectionUrl' => $result->getDeviceDataCollectionUrl(),
        ];
    }
}
