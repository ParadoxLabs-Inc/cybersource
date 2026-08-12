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
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequest;
use ParadoxLabs\CyberSource\Model\Service\Rest;

/**
 * POST /risk/v1/authentications — the enrollment check / frictionless authentication.
 *
 * Thin transport wrapper around Rest::post() plus classification. The reply's top-level status is
 * deliberately NOT interpreted here: ResultClassifier owns every outcome rule.
 */
class Authenticate
{
    /**
     * Authentications REST endpoint path.
     */
    public const AUTHENTICATIONS_PATH = '/risk/v1/authentications';

    /**
     * Authenticate constructor.
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
     * Run the authentication call and classify the reply.
     *
     * @param AuthenticationRequest $request
     * @param int|null $storeId Config/credential scope; null for the assumed scope.
     * @return AuthenticationResult
     * @throws InputException On an invalid request DTO (incomplete browser data, etc).
     * @throws RuntimeException On any non-2xx response.
     */
    public function execute(AuthenticationRequest $request, ?int $storeId = null): AuthenticationResult
    {
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $reply = $this->rest->post(self::AUTHENTICATIONS_PATH, $request->toArray());

        return $this->classifier->classify($reply);
    }
}
