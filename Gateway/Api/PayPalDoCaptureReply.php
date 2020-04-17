<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalDoCaptureReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $authorizationId
     */
    protected $authorizationId;

    /**
     * @var string $transactionId
     */
    protected $transactionId;

    /**
     * @var string $parentTransactionId
     */
    protected $parentTransactionId;

    /**
     * @var string $paypalReceiptId
     */
    protected $paypalReceiptId;

    /**
     * @var string $paypalTransactiontype
     */
    protected $paypalTransactiontype;

    /**
     * @var string $paypalPaymentType
     */
    protected $paypalPaymentType;

    /**
     * @var string $paypalOrderTime
     */
    protected $paypalOrderTime;

    /**
     * @var string $paypalPaymentGrossAmount
     */
    protected $paypalPaymentGrossAmount;

    /**
     * @var string $paypalFeeAmount
     */
    protected $paypalFeeAmount;

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
     * @var string $paypalPendingReason
     */
    protected $paypalPendingReason;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationId()
    {
        return $this->authorizationId;
    }

    /**
     * @param string $authorizationId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setAuthorizationId($authorizationId)
    {
        $this->authorizationId = $authorizationId;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentTransactionId()
    {
        return $this->parentTransactionId;
    }

    /**
     * @param string $parentTransactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setParentTransactionId($parentTransactionId)
    {
        $this->parentTransactionId = $parentTransactionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalReceiptId()
    {
        return $this->paypalReceiptId;
    }

    /**
     * @param string $paypalReceiptId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setPaypalReceiptId($paypalReceiptId)
    {
        $this->paypalReceiptId = $paypalReceiptId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalTransactiontype()
    {
        return $this->paypalTransactiontype;
    }

    /**
     * @param string $paypalTransactiontype
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setPaypalTransactiontype($paypalTransactiontype)
    {
        $this->paypalTransactiontype = $paypalTransactiontype;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setPaypalOrderTime($paypalOrderTime)
    {
        $this->paypalOrderTime = $paypalOrderTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPaymentGrossAmount()
    {
        return $this->paypalPaymentGrossAmount;
    }

    /**
     * @param string $paypalPaymentGrossAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setPaypalPaymentGrossAmount($paypalPaymentGrossAmount)
    {
        $this->paypalPaymentGrossAmount = $paypalPaymentGrossAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalFeeAmount()
    {
        return $this->paypalFeeAmount;
    }

    /**
     * @param string $paypalFeeAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setPaypalFeeAmount($paypalFeeAmount)
    {
        $this->paypalFeeAmount = $paypalFeeAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setPaypalPaymentStatus($paypalPaymentStatus)
    {
        $this->paypalPaymentStatus = $paypalPaymentStatus;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply
     */
    public function setPaypalPendingReason($paypalPendingReason)
    {
        $this->paypalPendingReason = $paypalPendingReason;

        return $this;
    }
}
