<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ExportReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var int $ipCountryConfidence
     */
    protected $ipCountryConfidence;

    /**
     * @var string $infoCode
     */
    protected $infoCode;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getIpCountryConfidence()
    {
        return $this->ipCountryConfidence;
    }

    /**
     * @param int $ipCountryConfidence
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportReply
     */
    public function setIpCountryConfidence($ipCountryConfidence)
    {
        $this->ipCountryConfidence = $ipCountryConfidence;

        return $this;
    }

    /**
     * @return string
     */
    public function getInfoCode()
    {
        return $this->infoCode;
    }

    /**
     * @param string $infoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportReply
     */
    public function setInfoCode($infoCode)
    {
        $this->infoCode = $infoCode;

        return $this;
    }
}
