<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalUpdateAgreementReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $paypalBillingAgreementId
     */
    protected $paypalBillingAgreementId;

    /**
     * @var string $paypalBillingAgreementDesc
     */
    protected $paypalBillingAgreementDesc;

    /**
     * @var string $paypalBillingAgreementCustom
     */
    protected $paypalBillingAgreementCustom;

    /**
     * @var string $paypalBillingAgreementStatus
     */
    protected $paypalBillingAgreementStatus;

    /**
     * @var string $payer
     */
    protected $payer;

    /**
     * @var string $payerId
     */
    protected $payerId;

    /**
     * @var string $payerStatus
     */
    protected $payerStatus;

    /**
     * @var string $payerCountry
     */
    protected $payerCountry;

    /**
     * @var string $payerBusiness
     */
    protected $payerBusiness;

    /**
     * @var string $payerSalutation
     */
    protected $payerSalutation;

    /**
     * @var string $payerFirstname
     */
    protected $payerFirstname;

    /**
     * @var string $payerMiddlename
     */
    protected $payerMiddlename;

    /**
     * @var string $payerLastname
     */
    protected $payerLastname;

    /**
     * @var string $payerSuffix
     */
    protected $payerSuffix;

    /**
     * @var string $addressStatus
     */
    protected $addressStatus;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

    /**
     * @var string $correlationID
     */
    protected $correlationID;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementId()
    {
        return $this->paypalBillingAgreementId;
    }

    /**
     * @param string $paypalBillingAgreementId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPaypalBillingAgreementId($paypalBillingAgreementId)
    {
        $this->paypalBillingAgreementId = $paypalBillingAgreementId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementDesc()
    {
        return $this->paypalBillingAgreementDesc;
    }

    /**
     * @param string $paypalBillingAgreementDesc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPaypalBillingAgreementDesc($paypalBillingAgreementDesc)
    {
        $this->paypalBillingAgreementDesc = $paypalBillingAgreementDesc;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementCustom()
    {
        return $this->paypalBillingAgreementCustom;
    }

    /**
     * @param string $paypalBillingAgreementCustom
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPaypalBillingAgreementCustom($paypalBillingAgreementCustom)
    {
        $this->paypalBillingAgreementCustom = $paypalBillingAgreementCustom;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementStatus()
    {
        return $this->paypalBillingAgreementStatus;
    }

    /**
     * @param string $paypalBillingAgreementStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPaypalBillingAgreementStatus($paypalBillingAgreementStatus)
    {
        $this->paypalBillingAgreementStatus = $paypalBillingAgreementStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param string $payer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerId()
    {
        return $this->payerId;
    }

    /**
     * @param string $payerId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerId($payerId)
    {
        $this->payerId = $payerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerStatus()
    {
        return $this->payerStatus;
    }

    /**
     * @param string $payerStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerStatus($payerStatus)
    {
        $this->payerStatus = $payerStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerCountry()
    {
        return $this->payerCountry;
    }

    /**
     * @param string $payerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerCountry($payerCountry)
    {
        $this->payerCountry = $payerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerBusiness()
    {
        return $this->payerBusiness;
    }

    /**
     * @param string $payerBusiness
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerBusiness($payerBusiness)
    {
        $this->payerBusiness = $payerBusiness;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerSalutation()
    {
        return $this->payerSalutation;
    }

    /**
     * @param string $payerSalutation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerSalutation($payerSalutation)
    {
        $this->payerSalutation = $payerSalutation;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerFirstname()
    {
        return $this->payerFirstname;
    }

    /**
     * @param string $payerFirstname
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerFirstname($payerFirstname)
    {
        $this->payerFirstname = $payerFirstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerMiddlename()
    {
        return $this->payerMiddlename;
    }

    /**
     * @param string $payerMiddlename
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerMiddlename($payerMiddlename)
    {
        $this->payerMiddlename = $payerMiddlename;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerLastname()
    {
        return $this->payerLastname;
    }

    /**
     * @param string $payerLastname
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerLastname($payerLastname)
    {
        $this->payerLastname = $payerLastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerSuffix()
    {
        return $this->payerSuffix;
    }

    /**
     * @param string $payerSuffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setPayerSuffix($payerSuffix)
    {
        $this->payerSuffix = $payerSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressStatus()
    {
        return $this->addressStatus;
    }

    /**
     * @param string $addressStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setAddressStatus($addressStatus)
    {
        $this->addressStatus = $addressStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrelationID()
    {
        return $this->correlationID;
    }

    /**
     * @param string $correlationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply
     */
    public function setCorrelationID($correlationID)
    {
        $this->correlationID = $correlationID;

        return $this;
    }
}
