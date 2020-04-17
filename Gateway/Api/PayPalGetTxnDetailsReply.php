<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalGetTxnDetailsReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

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
     * @var string $addressID
     */
    protected $addressID;

    /**
     * @var string $addressStatus
     */
    protected $addressStatus;

    /**
     * @var string $shipToName
     */
    protected $shipToName;

    /**
     * @var string $shipToAddress1
     */
    protected $shipToAddress1;

    /**
     * @var string $shipToAddress2
     */
    protected $shipToAddress2;

    /**
     * @var string $shipToCity
     */
    protected $shipToCity;

    /**
     * @var string $shipToState
     */
    protected $shipToState;

    /**
     * @var string $shipToCountry
     */
    protected $shipToCountry;

    /**
     * @var string $shipToZip
     */
    protected $shipToZip;

    /**
     * @var string $payerPhone
     */
    protected $payerPhone;

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
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $paypalSettleAmount
     */
    protected $paypalSettleAmount;

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
     * @var string $protectionEligibility
     */
    protected $protectionEligibility;

    /**
     * @var string $protectionEligibilityType
     */
    protected $protectionEligibilityType;

    /**
     * @var string $paypalNote
     */
    protected $paypalNote;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber;

    /**
     * @var Item[] $item
     */
    protected $item;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setPayerSuffix($payerSuffix)
    {
        $this->payerSuffix = $payerSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressID()
    {
        return $this->addressID;
    }

    /**
     * @param string $addressID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setAddressID($addressID)
    {
        $this->addressID = $addressID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setAddressStatus($addressStatus)
    {
        $this->addressStatus = $addressStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToName()
    {
        return $this->shipToName;
    }

    /**
     * @param string $shipToName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setShipToName($shipToName)
    {
        $this->shipToName = $shipToName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToAddress1()
    {
        return $this->shipToAddress1;
    }

    /**
     * @param string $shipToAddress1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setShipToAddress1($shipToAddress1)
    {
        $this->shipToAddress1 = $shipToAddress1;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToAddress2()
    {
        return $this->shipToAddress2;
    }

    /**
     * @param string $shipToAddress2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setShipToAddress2($shipToAddress2)
    {
        $this->shipToAddress2 = $shipToAddress2;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToCity()
    {
        return $this->shipToCity;
    }

    /**
     * @param string $shipToCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setShipToCity($shipToCity)
    {
        $this->shipToCity = $shipToCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToState()
    {
        return $this->shipToState;
    }

    /**
     * @param string $shipToState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setShipToState($shipToState)
    {
        $this->shipToState = $shipToState;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToCountry()
    {
        return $this->shipToCountry;
    }

    /**
     * @param string $shipToCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setShipToCountry($shipToCountry)
    {
        $this->shipToCountry = $shipToCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToZip()
    {
        return $this->shipToZip;
    }

    /**
     * @param string $shipToZip
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setShipToZip($shipToZip)
    {
        $this->shipToZip = $shipToZip;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerPhone()
    {
        return $this->payerPhone;
    }

    /**
     * @param string $payerPhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setPayerPhone($payerPhone)
    {
        $this->payerPhone = $payerPhone;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setPaypalFeeAmount($paypalFeeAmount)
    {
        $this->paypalFeeAmount = $paypalFeeAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalSettleAmount()
    {
        return $this->paypalSettleAmount;
    }

    /**
     * @param string $paypalSettleAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setPaypalSettleAmount($paypalSettleAmount)
    {
        $this->paypalSettleAmount = $paypalSettleAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setPaypalReasonCode($paypalReasonCode)
    {
        $this->paypalReasonCode = $paypalReasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setProtectionEligibilityType($protectionEligibilityType)
    {
        $this->protectionEligibilityType = $protectionEligibilityType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalNote()
    {
        return $this->paypalNote;
    }

    /**
     * @param string $paypalNote
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setPaypalNote($paypalNote)
    {
        $this->paypalNote = $paypalNote;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item[] $item
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setItem(array $item = null)
    {
        $this->item = $item;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
