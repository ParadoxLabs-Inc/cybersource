<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class RiskUpdateService
{
    /**
     * @var string $actionCode
     */
    protected $actionCode;

    /**
     * @var string $recordID
     */
    protected $recordID;

    /**
     * @var string $recordName
     */
    protected $recordName;

    /**
     * @var Address $negativeAddress
     */
    protected $negativeAddress;

    /**
     * @var string $markingReason
     */
    protected $markingReason;

    /**
     * @var string $markingNotes
     */
    protected $markingNotes;

    /**
     * @var string $markingRequestID
     */
    protected $markingRequestID;

    /**
     * @var string $deviceFingerprintSmartID
     */
    protected $deviceFingerprintSmartID;

    /**
     * @var string $deviceFingerprintTrueIPAddress
     */
    protected $deviceFingerprintTrueIPAddress;

    /**
     * @var string $deviceFingerprintProxyIPAddress
     */
    protected $deviceFingerprintProxyIPAddress;

    /**
     * @var boolean $run
     */
    protected $run;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
        $this->run = $run;
    }

    /**
     * @return string
     */
    public function getActionCode()
    {
        return $this->actionCode;
    }

    /**
     * @param string $actionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setActionCode($actionCode)
    {
        $this->actionCode = $actionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecordID()
    {
        return $this->recordID;
    }

    /**
     * @param string $recordID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setRecordID($recordID)
    {
        $this->recordID = $recordID;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecordName()
    {
        return $this->recordName;
    }

    /**
     * @param string $recordName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setRecordName($recordName)
    {
        $this->recordName = $recordName;

        return $this;
    }

    /**
     * @return Address
     */
    public function getNegativeAddress()
    {
        return $this->negativeAddress;
    }

    /**
     * @param Address $negativeAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setNegativeAddress($negativeAddress)
    {
        $this->negativeAddress = $negativeAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingReason()
    {
        return $this->markingReason;
    }

    /**
     * @param string $markingReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setMarkingReason($markingReason)
    {
        $this->markingReason = $markingReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingNotes()
    {
        return $this->markingNotes;
    }

    /**
     * @param string $markingNotes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setMarkingNotes($markingNotes)
    {
        $this->markingNotes = $markingNotes;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingRequestID()
    {
        return $this->markingRequestID;
    }

    /**
     * @param string $markingRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setMarkingRequestID($markingRequestID)
    {
        $this->markingRequestID = $markingRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceFingerprintSmartID()
    {
        return $this->deviceFingerprintSmartID;
    }

    /**
     * @param string $deviceFingerprintSmartID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setDeviceFingerprintSmartID($deviceFingerprintSmartID)
    {
        $this->deviceFingerprintSmartID = $deviceFingerprintSmartID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceFingerprintTrueIPAddress()
    {
        return $this->deviceFingerprintTrueIPAddress;
    }

    /**
     * @param string $deviceFingerprintTrueIPAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setDeviceFingerprintTrueIPAddress($deviceFingerprintTrueIPAddress)
    {
        $this->deviceFingerprintTrueIPAddress = $deviceFingerprintTrueIPAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceFingerprintProxyIPAddress()
    {
        return $this->deviceFingerprintProxyIPAddress;
    }

    /**
     * @param string $deviceFingerprintProxyIPAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setDeviceFingerprintProxyIPAddress($deviceFingerprintProxyIPAddress)
    {
        $this->deviceFingerprintProxyIPAddress = $deviceFingerprintProxyIPAddress;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
