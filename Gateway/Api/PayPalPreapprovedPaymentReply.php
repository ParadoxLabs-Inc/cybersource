<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class PayPalPreapprovedPaymentReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $payerStatus
     */
    protected $payerStatus;

    /**
     * @var string $payerName
     */
    protected $payerName;

    /**
     * @var string $transactionType
     */
    protected $transactionType;

    /**
     * @var string $feeAmount
     */
    protected $feeAmount;

    /**
     * @var string $payerCountry
     */
    protected $payerCountry;

    /**
     * @var string $pendingReason
     */
    protected $pendingReason;

    /**
     * @var string $paymentStatus
     */
    protected $paymentStatus;

    /**
     * @var string $mpStatus
     */
    protected $mpStatus;

    /**
     * @var string $payer
     */
    protected $payer;

    /**
     * @var string $payerID
     */
    protected $payerID;

    /**
     * @var string $payerBusiness
     */
    protected $payerBusiness;

    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var string $desc
     */
    protected $desc;

    /**
     * @var string $mpMax
     */
    protected $mpMax;

    /**
     * @var string $paymentType
     */
    protected $paymentType;

    /**
     * @var string $paymentDate
     */
    protected $paymentDate;

    /**
     * @var string $paymentGrossAmount
     */
    protected $paymentGrossAmount;

    /**
     * @var string $settleAmount
     */
    protected $settleAmount;

    /**
     * @var string $taxAmount
     */
    protected $taxAmount;

    /**
     * @var string $exchangeRate
     */
    protected $exchangeRate;

    /**
     * @var string $paymentSourceID
     */
    protected $paymentSourceID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestDateTime()
    {
        if ($this->requestDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->requestDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $requestDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setRequestDateTime(DateTime $requestDateTime = null)
    {
        if ($requestDateTime == null) {
            $this->requestDateTime = null;
        } else {
            $this->requestDateTime = $requestDateTime->format(DateTime::ATOM);
        }

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPayerStatus($payerStatus)
    {
        $this->payerStatus = $payerStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerName()
    {
        return $this->payerName;
    }

    /**
     * @param string $payerName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPayerName($payerName)
    {
        $this->payerName = $payerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeeAmount()
    {
        return $this->feeAmount;
    }

    /**
     * @param string $feeAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setFeeAmount($feeAmount)
    {
        $this->feeAmount = $feeAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPayerCountry($payerCountry)
    {
        $this->payerCountry = $payerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getPendingReason()
    {
        return $this->pendingReason;
    }

    /**
     * @param string $pendingReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPendingReason($pendingReason)
    {
        $this->pendingReason = $pendingReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getMpStatus()
    {
        return $this->mpStatus;
    }

    /**
     * @param string $mpStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setMpStatus($mpStatus)
    {
        $this->mpStatus = $mpStatus;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerID()
    {
        return $this->payerID;
    }

    /**
     * @param string $payerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPayerID($payerID)
    {
        $this->payerID = $payerID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPayerBusiness($payerBusiness)
    {
        $this->payerBusiness = $payerBusiness;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @param string $transactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * @return string
     */
    public function getMpMax()
    {
        return $this->mpMax;
    }

    /**
     * @param string $mpMax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setMpMax($mpMax)
    {
        $this->mpMax = $mpMax;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @param string $paymentDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentGrossAmount()
    {
        return $this->paymentGrossAmount;
    }

    /**
     * @param string $paymentGrossAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPaymentGrossAmount($paymentGrossAmount)
    {
        $this->paymentGrossAmount = $paymentGrossAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getSettleAmount()
    {
        return $this->settleAmount;
    }

    /**
     * @param string $settleAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setSettleAmount($settleAmount)
    {
        $this->settleAmount = $settleAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param string $taxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param string $exchangeRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentSourceID()
    {
        return $this->paymentSourceID;
    }

    /**
     * @param string $paymentSourceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply
     */
    public function setPaymentSourceID($paymentSourceID)
    {
        $this->paymentSourceID = $paymentSourceID;

        return $this;
    }
}
