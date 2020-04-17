<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalTransactionSearchService
{
    /**
     * @var string $startDate
     */
    protected $startDate;

    /**
     * @var string $endDate
     */
    protected $endDate;

    /**
     * @var string $paypalCustomerEmail
     */
    protected $paypalCustomerEmail;

    /**
     * @var string $paypalReceiptId
     */
    protected $paypalReceiptId;

    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber;

    /**
     * @var float $grandTotalAmount
     */
    protected $grandTotalAmount;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $paymentStatus
     */
    protected $paymentStatus;

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
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalCustomerEmail()
    {
        return $this->paypalCustomerEmail;
    }

    /**
     * @param string $paypalCustomerEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setPaypalCustomerEmail($paypalCustomerEmail)
    {
        $this->paypalCustomerEmail = $paypalCustomerEmail;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setPaypalReceiptId($paypalReceiptId)
    {
        $this->paypalReceiptId = $paypalReceiptId;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return float
     */
    public function getGrandTotalAmount()
    {
        return $this->grandTotalAmount;
    }

    /**
     * @param float $grandTotalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setGrandTotalAmount($grandTotalAmount)
    {
        $this->grandTotalAmount = $grandTotalAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setPayerSuffix($payerSuffix)
    {
        $this->payerSuffix = $payerSuffix;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
