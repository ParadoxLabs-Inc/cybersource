<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APUpdateMandateService
{
    /**
     * @var string $esign
     */
    protected $esign;

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
    public function getEsign()
    {
        return $this->esign;
    }

    /**
     * @param string $esign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateService
     */
    public function setEsign($esign)
    {
        $this->esign = $esign;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
