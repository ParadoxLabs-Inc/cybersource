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

use ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Source\ResponseCode;

class Context
{
    /**
     * Context constructor.
     *
     * @param Config $config
     * @param ObjectBuilder $objectBuilder
     * @param ResponseCode $responseCodeSource
     * @param Rest $restClient
     * @param Persistor $payerAuthPersistor
     * @param JsonWebTokenEncoder $payerAuthJWTEncoder
     * @param EnrollmentParams $payerAuthEnrollParams
     */
    public function __construct(
        private readonly Config $config,
        private readonly ObjectBuilder $objectBuilder,
        private readonly ResponseCode $responseCodeSource,
        private readonly Rest $restClient,
        private readonly Persistor $payerAuthPersistor,
        private readonly JsonWebTokenEncoder $payerAuthJWTEncoder,
        private readonly EnrollmentParams $payerAuthEnrollParams
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
     * Get objectBuilder
     *
     * @return ObjectBuilder
     */
    public function getObjectBuilder()
    {
        return $this->objectBuilder;
    }

    /**
     * Get responseCodeSource
     *
     * @return ResponseCode
     */
    public function getResponseCodeSource()
    {
        return $this->responseCodeSource;
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
     * Get payerAuthPersistor
     *
     * @return Persistor
     */
    public function getPayerAuthPersistor()
    {
        return $this->payerAuthPersistor;
    }

    /**
     * Get payerAuthJWTEncoder
     *
     * @return JsonWebTokenEncoder
     */
    public function getPayerAuthJWTEncoder()
    {
        return $this->payerAuthJWTEncoder;
    }

    /**
     * Get enrollmentParams
     *
     * @return EnrollmentParams
     */
    public function getPayerAuthEnrollParams()
    {
        return $this->payerAuthEnrollParams;
    }
}
