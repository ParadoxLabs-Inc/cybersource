<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APCreateMandateService
{
    /**
     * @var string $saleRequestID
     */
    protected $saleRequestID;

    /**
     * @var string $cancelURL
     */
    protected $cancelURL;

    /**
     * @var string $successURL
     */
    protected $successURL;

    /**
     * @var string $failureURL
     */
    protected $failureURL;

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
    public function getSaleRequestID()
    {
        return $this->saleRequestID;
    }

    /**
     * @param string $saleRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCreateMandateService
     */
    public function setSaleRequestID($saleRequestID)
    {
        $this->saleRequestID = $saleRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCreateMandateService
     */
    public function setCancelURL($cancelURL)
    {
        $this->cancelURL = $cancelURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCreateMandateService
     */
    public function setSuccessURL($successURL)
    {
        $this->successURL = $successURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getFailureURL()
    {
        return $this->failureURL;
    }

    /**
     * @param string $failureURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCreateMandateService
     */
    public function setFailureURL($failureURL)
    {
        $this->failureURL = $failureURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCreateMandateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
