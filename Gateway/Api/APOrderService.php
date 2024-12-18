<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APOrderService
{
    /**
     * @var string $sessionsRequestID
     */
    protected $sessionsRequestID;

    /**
     * @var string $successURL
     */
    protected $successURL;

    /**
     * @var string $cancelURL
     */
    protected $cancelURL;

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
     * @return string
     */
    public function getSuccessURL()
    {
        return $this->successURL;
    }

    /**
     * @param string $successURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderService
     */
    public function setSuccessURL($successURL)
    {
        $this->successURL = $successURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getCancelURL()
    {
        return $this->cancelURL;
    }

    /**
     * @param string $cancelURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderService
     */
    public function setCancelURL($cancelURL)
    {
        $this->cancelURL = $cancelURL;

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
