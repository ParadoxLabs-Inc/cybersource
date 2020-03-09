<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
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
