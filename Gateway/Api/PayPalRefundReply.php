<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalRefundReply
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
     * @var string $paypalNetRefundAmount
     */
    protected $paypalNetRefundAmount;

    /**
     * @var string $paypalFeeRefundAmount
     */
    protected $paypalFeeRefundAmount;

    /**
     * @var string $paypalGrossRefundAmount
     */
    protected $paypalGrossRefundAmount;

    /**
     * @var string $correlationID
     */
    protected $correlationID;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalNetRefundAmount()
    {
        return $this->paypalNetRefundAmount;
    }

    /**
     * @param string $paypalNetRefundAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply
     */
    public function setPaypalNetRefundAmount($paypalNetRefundAmount)
    {
        $this->paypalNetRefundAmount = $paypalNetRefundAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalFeeRefundAmount()
    {
        return $this->paypalFeeRefundAmount;
    }

    /**
     * @param string $paypalFeeRefundAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply
     */
    public function setPaypalFeeRefundAmount($paypalFeeRefundAmount)
    {
        $this->paypalFeeRefundAmount = $paypalFeeRefundAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalGrossRefundAmount()
    {
        return $this->paypalGrossRefundAmount;
    }

    /**
     * @param string $paypalGrossRefundAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply
     */
    public function setPaypalGrossRefundAmount($paypalGrossRefundAmount)
    {
        $this->paypalGrossRefundAmount = $paypalGrossRefundAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
