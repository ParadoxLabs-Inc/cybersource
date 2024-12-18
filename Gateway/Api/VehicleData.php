<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VehicleData
{
    /**
     * @var int $connectorType
     */
    protected $connectorType;

    /**
     * @var int $chargingReasonCode
     */
    protected $chargingReasonCode;

    /**
     * @return int
     */
    public function getConnectorType()
    {
        return $this->connectorType;
    }

    /**
     * @param int $connectorType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VehicleData
     */
    public function setConnectorType($connectorType)
    {
        $this->connectorType = $connectorType;

        return $this;
    }

    /**
     * @return int
     */
    public function getChargingReasonCode()
    {
        return $this->chargingReasonCode;
    }

    /**
     * @param int $chargingReasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VehicleData
     */
    public function setChargingReasonCode($chargingReasonCode)
    {
        $this->chargingReasonCode = $chargingReasonCode;

        return $this;
    }
}
