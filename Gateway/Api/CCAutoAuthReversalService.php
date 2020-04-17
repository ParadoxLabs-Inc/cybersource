<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCAutoAuthReversalService
{
    /**
     * @var string $authPaymentServiceData
     */
    protected $authPaymentServiceData;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $authAmount
     */
    protected $authAmount;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $authRequestID
     */
    protected $authRequestID;

    /**
     * @var string $billAmount
     */
    protected $billAmount;

    /**
     * @var string $authCode
     */
    protected $authCode;

    /**
     * @var string $authType
     */
    protected $authType;

    /**
     * @var boolean $billPayment
     */
    protected $billPayment;

    /**
     * @var string $dateAdded
     */
    protected $dateAdded;

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
    public function getAuthPaymentServiceData()
    {
        return $this->authPaymentServiceData;
    }

    /**
     * @param string $authPaymentServiceData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setAuthPaymentServiceData($authPaymentServiceData)
    {
        $this->authPaymentServiceData = $authPaymentServiceData;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthAmount()
    {
        return $this->authAmount;
    }

    /**
     * @param string $authAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setAuthAmount($authAmount)
    {
        $this->authAmount = $authAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommerceIndicator()
    {
        return $this->commerceIndicator;
    }

    /**
     * @param string $commerceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setAuthRequestID($authRequestID)
    {
        $this->authRequestID = $authRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillAmount()
    {
        return $this->billAmount;
    }

    /**
     * @param string $billAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setBillAmount($billAmount)
    {
        $this->billAmount = $billAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param string $authType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getBillPayment()
    {
        return $this->billPayment;
    }

    /**
     * @param boolean $billPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setBillPayment($billPayment)
    {
        $this->billPayment = $billPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param string $dateAdded
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
