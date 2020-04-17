<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APSaleService
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
     * @var string $transactionTimeout
     */
    protected $transactionTimeout;

    /**
     * @var string $orderRequestID
     */
    protected $orderRequestID;

    /**
     * @var string $billingAgreementID
     */
    protected $billingAgreementID;

    /**
     * @var string $mandateID
     */
    protected $mandateID;

    /**
     * @var string $dateCollect
     */
    protected $dateCollect;

    /**
     * @var string $preapprovalToken
     */
    protected $preapprovalToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setPaymentOptionID($paymentOptionID)
    {
        $this->paymentOptionID = $paymentOptionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionTimeout()
    {
        return $this->transactionTimeout;
    }

    /**
     * @param string $transactionTimeout
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setTransactionTimeout($transactionTimeout)
    {
        $this->transactionTimeout = $transactionTimeout;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setOrderRequestID($orderRequestID)
    {
        $this->orderRequestID = $orderRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAgreementID()
    {
        return $this->billingAgreementID;
    }

    /**
     * @param string $billingAgreementID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setBillingAgreementID($billingAgreementID)
    {
        $this->billingAgreementID = $billingAgreementID;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandateID()
    {
        return $this->mandateID;
    }

    /**
     * @param string $mandateID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setMandateID($mandateID)
    {
        $this->mandateID = $mandateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateCollect()
    {
        return $this->dateCollect;
    }

    /**
     * @param string $dateCollect
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setDateCollect($dateCollect)
    {
        $this->dateCollect = $dateCollect;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setPreapprovalToken($preapprovalToken)
    {
        $this->preapprovalToken = $preapprovalToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
