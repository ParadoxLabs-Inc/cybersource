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

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

/**
 * JsonWebTokenGenerator Class
 *
 * @see https://cardinaldocs.atlassian.net/wiki/spaces/CC/pages/32950/Request+Objects
 */
class JsonWebTokenGenerator
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder
     */
    protected $encoder;

    /**
     * JsonWebTokenGenerator constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder $encoder
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder $encoder
    ) {
        $this->config = $config;
        $this->encoder = $encoder;
    }

    /**
     * @return string
     */
    public function getJwt()
    {
        if ($this->config->isPayerAuthEnabled() === false) {
            return '';
        }

        // TODO: Add enable setting, enforce that somewhere in here

        $payload = [
            'jti' => uniqid('', true),
            'iat' => time(),
            'iss' => $this->config->getCardinalSecretKeyId(),
            'OrgUnitId' => $this->config->getCardinalOrgUnitId(),
            'Payload' => [
                'OrderDetails' => [
                    'OrderNumber' => '123', // TODO
                    'Amount' => '123', // TODO
                    'CurrencyCode' => 'USD', // TODO
                    'TransactionId' => '123', // TODO
                    'OrderChannel' => 'S', // TODO
                ],
            ], // TODO: "This object is usually an Order object"
            'ObjectifyPayload' => true,
        ];

        return $this->encoder->pack($payload);
    }
}
