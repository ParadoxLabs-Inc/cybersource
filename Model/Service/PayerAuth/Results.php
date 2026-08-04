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
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequest;
use ParadoxLabs\CyberSource\Model\Service\Rest;

/**
 * POST /risk/v1/authentication-results — finalize a challenge (step-up) authentication.
 *
 * Thin transport wrapper: same reply shape as /risk/v1/authentications, so the same classifier
 * rules apply to the finalized outcome.
 */
class Results
{
    /**
     * Authentication results REST endpoint path.
     */
    public const RESULTS_PATH = '/risk/v1/authentication-results';

    /**
     * Results constructor.
     *
     * @param Config $config
     * @param Rest $rest
     * @param ResultClassifier $classifier
     */
    public function __construct(
        private readonly Config $config,
        private readonly Rest $rest,
        private readonly ResultClassifier $classifier
    ) {
    }

    /**
     * Fetch and classify the final authentication result for a challenged transaction.
     *
     * @param ResultsRequest $request
     * @param int|null $storeId Config/credential scope; null for the assumed scope.
     * @return AuthenticationResult
     * @throws InputException On an invalid request DTO (missing transaction id).
     * @throws RuntimeException On any non-2xx response.
     */
    public function execute(ResultsRequest $request, ?int $storeId = null): AuthenticationResult
    {
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $reply = $this->rest->post(self::RESULTS_PATH, $request->toArray());

        return $this->classifier->classify($reply);
    }
}
