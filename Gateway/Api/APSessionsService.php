<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APSessionsService
{
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
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod;

    /**
     * @var string $paymentOptionID
     */
    protected $paymentOptionID;

    /**
     * @var string $sessionsType
     */
    protected $sessionsType;

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
    public function getCancelURL()
    {
        return $this->cancelURL;
    }

    /**
     * @param string $cancelURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
     */
    public function setFailureURL($failureURL)
    {
        $this->failureURL = $failureURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getOverridePaymentMethod()
    {
        return $this->overridePaymentMethod;
    }

    /**
     * @param string $overridePaymentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentOptionID()
    {
        return $this->paymentOptionID;
    }

    /**
     * @param string $paymentOptionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
     */
    public function setPaymentOptionID($paymentOptionID)
    {
        $this->paymentOptionID = $paymentOptionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionsType()
    {
        return $this->sessionsType;
    }

    /**
     * @param string $sessionsType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
     */
    public function setSessionsType($sessionsType)
    {
        $this->sessionsType = $sessionsType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
