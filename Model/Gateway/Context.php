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

namespace ParadoxLabs\CyberSource\Model\Gateway;

use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UnifiedCheckoutResponse;

class Context
{
    /**
     * Context constructor.
     *
     * @param Config $config
     * @param Rest $restClient
     * @param UnifiedCheckoutResponse $unifiedCheckoutResponse
     * @param FollowOn $unifiedCheckoutFollowOn
     */
    public function __construct(
        private readonly Config $config,
        private readonly Rest $restClient,
        private readonly UnifiedCheckoutResponse $unifiedCheckoutResponse,
        private readonly FollowOn $unifiedCheckoutFollowOn,
    ) {
    }

    /**
     * Get config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get restClient
     *
     * @return Rest
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * Get the Unified Checkout auth/sale service (A1).
     *
     * @return UnifiedCheckoutResponse
     */
    public function getUnifiedCheckoutResponse()
    {
        return $this->unifiedCheckoutResponse;
    }

    /**
     * Get the Unified Checkout follow-on (capture/refund/void/delete) service (A3).
     *
     * @return FollowOn
     */
    public function getUnifiedCheckoutFollowOn()
    {
        return $this->unifiedCheckoutFollowOn;
    }
}
