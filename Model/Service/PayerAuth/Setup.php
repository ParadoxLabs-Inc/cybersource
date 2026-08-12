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

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\RuntimeException;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequest;
use ParadoxLabs\CyberSource\Model\Service\Rest;

/**
 * POST /risk/v1/authentication-setups — device data collection setup.
 *
 * Thin transport wrapper: the caller (PayerAuth Management) owns quote/config orchestration and
 * error handling. Rest::post() throws on any non-2xx, and that RuntimeException propagates.
 */
class Setup
{
    /**
     * Authentication setup REST endpoint path.
     */
    public const SETUP_PATH = '/risk/v1/authentication-setups';

    /**
     * Setup constructor.
     *
     * @param Config $config
     * @param Rest $rest
     */
    public function __construct(
        private readonly Config $config,
        private readonly Rest $rest
    ) {
    }

    /**
     * Run the setup call and return the decoded reply.
     *
     * The caller reads consumerAuthenticationInformation.accessToken / deviceDataCollectionUrl /
     * referenceId from the reply to drive the hidden DDC iframe.
     *
     * @param SetupRequest $request
     * @param int|null $storeId Config/credential scope; null for the assumed scope.
     * @return array<string, mixed> Decoded authentication-setups reply.
     * @throws InputException On an invalid request DTO.
     * @throws RuntimeException On any non-2xx response.
     */
    public function execute(SetupRequest $request, ?int $storeId = null): array
    {
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        return $this->rest->post(self::SETUP_PATH, $request->toArray());
    }
}
