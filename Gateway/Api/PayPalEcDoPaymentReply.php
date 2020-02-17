<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalEcDoPaymentReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var string $paypalToken
     */
    protected $paypalToken = null;

    /**
     * @var string $transactionId
     */
    protected $transactionId = null;

    /**
     * @var string $paypalTransactiontype
     */
    protected $paypalTransactiontype = null;

    /**
     * @var string $paymentType
     */
    protected $paymentType = null;

    /**
     * @var string $paypalOrderTime
     */
    protected $paypalOrderTime = null;

    /**
     * @var string $paypalAmount
     */
    protected $paypalAmount = null;

    /**
     * @var string $paypalFeeAmount
     */
    protected $paypalFeeAmount = null;

    /**
     * @var string $paypalTaxAmount
     */
    protected $paypalTaxAmount = null;

    /**
     * @var string $paypalExchangeRate
     */
    protected $paypalExchangeRate = null;

    /**
     * @var string $paypalPaymentStatus
     */
    protected $paypalPaymentStatus = null;

    /**
     * @var string $paypalPendingReason
     */
    protected $paypalPendingReason = null;

    /**
     * @var string $orderId
     */
    protected $orderId = null;

    /**
     * @var string $paypalReasonCode
     */
    protected $paypalReasonCode = null;

    /**
     * @var string $amount
     */
    protected $amount = null;

    /**
     * @var string $currency
     */
    protected $currency = null;

    /**
     * @var string $correlationID
     */
    protected $correlationID = null;

    /**
     * @var string $errorCode
     */
    protected $errorCode = null;

    /**
     * @var string $paypalBillingAgreementId
     */
    protected $paypalBillingAgreementId = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setReasonCode($reasonCode)
    {
      $this->reasonCode = $reasonCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getPaypalToken()
    {
      return $this->paypalToken;
    }

    /**
     * @param string $paypalToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setPaypalToken($paypalToken)
    {
      $this->paypalToken = $paypalToken;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setTransactionId($transactionId)
    {
      $this->transactionId = $transactionId;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setPaypalTransactiontype($paypalTransactiontype)
    {
      $this->paypalTransactiontype = $paypalTransactiontype;
      return $this;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
      return $this->paymentType;
    }

    /**
     * @param string $paymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setPaymentType($paymentType)
    {
      $this->paymentType = $paymentType;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setPaypalAmount($paypalAmount)
    {
      $this->paypalAmount = $paypalAmount;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setPaypalPendingReason($paypalPendingReason)
    {
      $this->paypalPendingReason = $paypalPendingReason;
      return $this;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
      return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setOrderId($orderId)
    {
      $this->orderId = $orderId;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setPaypalReasonCode($paypalReasonCode)
    {
      $this->paypalReasonCode = $paypalReasonCode;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setErrorCode($errorCode)
    {
      $this->errorCode = $errorCode;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply
     */
    public function setPaypalBillingAgreementId($paypalBillingAgreementId)
    {
      $this->paypalBillingAgreementId = $paypalBillingAgreementId;
      return $this;
    }

}