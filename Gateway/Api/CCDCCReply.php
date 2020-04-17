<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCDCCReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var boolean $dccSupported
     */
    protected $dccSupported;

    /**
     * @var string $validHours
     */
    protected $validHours;

    /**
     * @var string $marginRatePercentage
     */
    protected $marginRatePercentage;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDccSupported()
    {
        return $this->dccSupported;
    }

    /**
     * @param boolean $dccSupported
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCReply
     */
    public function setDccSupported($dccSupported)
    {
        $this->dccSupported = $dccSupported;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidHours()
    {
        return $this->validHours;
    }

    /**
     * @param string $validHours
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCReply
     */
    public function setValidHours($validHours)
    {
        $this->validHours = $validHours;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarginRatePercentage()
    {
        return $this->marginRatePercentage;
    }

    /**
     * @param string $marginRatePercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCReply
     */
    public function setMarginRatePercentage($marginRatePercentage)
    {
        $this->marginRatePercentage = $marginRatePercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationID()
    {
        return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }
}
