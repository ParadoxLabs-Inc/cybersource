<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalAuthorizationReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $transactionId
     */
    protected $transactionId;

    /**
     * @var string $paypalAmount
     */
    protected $paypalAmount;

    /**
     * @var string $amount
     */
    protected $amount;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $correlationID
     */
    protected $correlationID;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

    /**
     * @var string $protectionEligibility
     */
    protected $protectionEligibility;

    /**
     * @var string $protectionEligibilityType
     */
    protected $protectionEligibilityType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalAmount()
    {
        return $this->paypalAmount;
    }

    /**
     * @param string $paypalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setPaypalAmount($paypalAmount)
    {
        $this->paypalAmount = $paypalAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setCorrelationID($correlationID)
    {
        $this->correlationID = $correlationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProtectionEligibility()
    {
        return $this->protectionEligibility;
    }

    /**
     * @param string $protectionEligibility
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setProtectionEligibility($protectionEligibility)
    {
        $this->protectionEligibility = $protectionEligibility;

        return $this;
    }

    /**
     * @return string
     */
    public function getProtectionEligibilityType()
    {
        return $this->protectionEligibilityType;
    }

    /**
     * @param string $protectionEligibilityType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply
     */
    public function setProtectionEligibilityType($protectionEligibilityType)
    {
        $this->protectionEligibilityType = $protectionEligibilityType;

        return $this;
    }
}
