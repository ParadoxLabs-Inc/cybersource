<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APCaptureService
{
    /**
     * @var string $authRequestID
     */
    protected $authRequestID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var boolean $isFinal
     */
    protected $isFinal;

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
    public function getAuthRequestID()
    {
        return $this->authRequestID;
    }

    /**
     * @param string $authRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCaptureService
     */
    public function setAuthRequestID($authRequestID)
    {
        $this->authRequestID = $authRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCaptureService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsFinal()
    {
        return $this->isFinal;
    }

    /**
     * @param boolean $isFinal
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCaptureService
     */
    public function setIsFinal($isFinal)
    {
        $this->isFinal = $isFinal;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCaptureService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
