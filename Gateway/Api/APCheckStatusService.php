<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APCheckStatusService
{
    /**
     * @var string $apInitiateRequestID
     */
    protected $apInitiateRequestID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $checkStatusRequestID
     */
    protected $checkStatusRequestID;

    /**
     * @var string $sessionsRequestID
     */
    protected $sessionsRequestID;

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
    public function getApInitiateRequestID()
    {
        return $this->apInitiateRequestID;
    }

    /**
     * @param string $apInitiateRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusService
     */
    public function setApInitiateRequestID($apInitiateRequestID)
    {
        $this->apInitiateRequestID = $apInitiateRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckStatusRequestID()
    {
        return $this->checkStatusRequestID;
    }

    /**
     * @param string $checkStatusRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusService
     */
    public function setCheckStatusRequestID($checkStatusRequestID)
    {
        $this->checkStatusRequestID = $checkStatusRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionsRequestID()
    {
        return $this->sessionsRequestID;
    }

    /**
     * @param string $sessionsRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusService
     */
    public function setSessionsRequestID($sessionsRequestID)
    {
        $this->sessionsRequestID = $sessionsRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
