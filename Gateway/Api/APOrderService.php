<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APOrderService
{
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
    public function getSessionsRequestID()
    {
        return $this->sessionsRequestID;
    }

    /**
     * @param string $sessionsRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
