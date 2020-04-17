<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APAuthService
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
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $preapprovalToken
     */
    protected $preapprovalToken;

    /**
     * @var string $orderRequestID
     */
    protected $orderRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
     */
    public function setFailureURL($failureURL)
    {
        $this->failureURL = $failureURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreapprovalToken()
    {
        return $this->preapprovalToken;
    }

    /**
     * @param string $preapprovalToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
     */
    public function setPreapprovalToken($preapprovalToken)
    {
        $this->preapprovalToken = $preapprovalToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderRequestID()
    {
        return $this->orderRequestID;
    }

    /**
     * @param string $orderRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
     */
    public function setOrderRequestID($orderRequestID)
    {
        $this->orderRequestID = $orderRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
