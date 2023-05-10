<?php
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
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Gateway;

/**
 * Context Class
 */
class Context
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    private $config;

    /**
     * @var \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder
     */
    private $objectBuilder;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Source\ResponseCode
     */
    private $responseCodeSource;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Rest
     */
    private $restClient;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor
     */
    private $payerAuthPersistor;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder
     */
    private $payerAuthJWTEncoder;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams
     */
    private $payerAuthEnrollParams;

    /**
     * Context constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder $objectBuilder
     * @param \ParadoxLabs\CyberSource\Model\Source\ResponseCode $responseCodeSource
     * @param \ParadoxLabs\CyberSource\Model\Service\Rest $restClient
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $payerAuthPersistor
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder $payerAuthJWTEncoder
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams $enrollmentParams
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder $objectBuilder,
        \ParadoxLabs\CyberSource\Model\Source\ResponseCode $responseCodeSource,
        \ParadoxLabs\CyberSource\Model\Service\Rest $restClient,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor $payerAuthPersistor,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder $payerAuthJWTEncoder,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams $enrollmentParams
    ) {
        $this->config = $config;
        $this->objectBuilder = $objectBuilder;
        $this->responseCodeSource = $responseCodeSource;
        $this->restClient = $restClient;
        $this->payerAuthPersistor = $payerAuthPersistor;
        $this->payerAuthJWTEncoder = $payerAuthJWTEncoder;
        $this->payerAuthEnrollParams = $enrollmentParams;
    }

    /**
     * Get config
     *
     * @return \ParadoxLabs\CyberSource\Model\Config\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get objectBuilder
     *
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder
     */
    public function getObjectBuilder()
    {
        return $this->objectBuilder;
    }

    /**
     * Get responseCodeSource
     *
     * @return \ParadoxLabs\CyberSource\Model\Source\ResponseCode
     */
    public function getResponseCodeSource()
    {
        return $this->responseCodeSource;
    }

    /**
     * Get restClient
     *
     * @return \ParadoxLabs\CyberSource\Model\Service\Rest
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * Get payerAuthPersistor
     *
     * @return \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\Persistor
     */
    public function getPayerAuthPersistor()
    {
        return $this->payerAuthPersistor;
    }

    /**
     * Get payerAuthJWTEncoder
     *
     * @return \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder
     */
    public function getPayerAuthJWTEncoder()
    {
        return $this->payerAuthJWTEncoder;
    }

    /**
     * Get enrollmentParams
     *
     * @return \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams
     */
    public function getPayerAuthEnrollParams()
    {
        return $this->payerAuthEnrollParams;
    }
}
