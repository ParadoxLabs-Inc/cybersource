<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalDoRefTransactionReply
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
     * @var string $transactionId
     */
    protected $transactionId;

    /**
     * @var string $paypalTransactionType
     */
    protected $paypalTransactionType;

    /**
     * @var string $paypalPaymentType
     */
    protected $paypalPaymentType;

    /**
     * @var string $paypalOrderTime
     */
    protected $paypalOrderTime;

    /**
     * @var string $paypalAmount
     */
    protected $paypalAmount;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $paypalTaxAmount
     */
    protected $paypalTaxAmount;

    /**
     * @var string $paypalExchangeRate
     */
    protected $paypalExchangeRate;

    /**
     * @var string $paypalPaymentStatus
     */
    protected $paypalPaymentStatus;

    /**
     * @var string $paypalPendingReason
     */
    protected $paypalPendingReason;

    /**
     * @var string $paypalReasonCode
     */
    protected $paypalReasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalBillingAgreementId($paypalBillingAgreementId)
    {
        $this->paypalBillingAgreementId = $paypalBillingAgreementId;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalTransactionType()
    {
        return $this->paypalTransactionType;
    }

    /**
     * @param string $paypalTransactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalTransactionType($paypalTransactionType)
    {
        $this->paypalTransactionType = $paypalTransactionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPaymentType()
    {
        return $this->paypalPaymentType;
    }

    /**
     * @param string $paypalPaymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalPaymentType($paypalPaymentType)
    {
        $this->paypalPaymentType = $paypalPaymentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalOrderTime()
    {
        return $this->paypalOrderTime;
    }

    /**
     * @param string $paypalOrderTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalOrderTime($paypalOrderTime)
    {
        $this->paypalOrderTime = $paypalOrderTime;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalAmount($paypalAmount)
    {
        $this->paypalAmount = $paypalAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalTaxAmount()
    {
        return $this->paypalTaxAmount;
    }

    /**
     * @param string $paypalTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalTaxAmount($paypalTaxAmount)
    {
        $this->paypalTaxAmount = $paypalTaxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalExchangeRate()
    {
        return $this->paypalExchangeRate;
    }

    /**
     * @param string $paypalExchangeRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalExchangeRate($paypalExchangeRate)
    {
        $this->paypalExchangeRate = $paypalExchangeRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPaymentStatus()
    {
        return $this->paypalPaymentStatus;
    }

    /**
     * @param string $paypalPaymentStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalPaymentStatus($paypalPaymentStatus)
    {
        $this->paypalPaymentStatus = $paypalPaymentStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPendingReason()
    {
        return $this->paypalPendingReason;
    }

    /**
     * @param string $paypalPendingReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalPendingReason($paypalPendingReason)
    {
        $this->paypalPendingReason = $paypalPendingReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalReasonCode()
    {
        return $this->paypalReasonCode;
    }

    /**
     * @param string $paypalReasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setPaypalReasonCode($paypalReasonCode)
    {
        $this->paypalReasonCode = $paypalReasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply
     */
    public function setCorrelationID($correlationID)
    {
        $this->correlationID = $correlationID;

        return $this;
    }
}
