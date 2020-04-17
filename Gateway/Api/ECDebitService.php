<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ECDebitService
{
    /**
     * @var int $paymentMode
     */
    protected $paymentMode;

    /**
     * @var string $referenceNumber
     */
    protected $referenceNumber;

    /**
     * @var string $settlementMethod
     */
    protected $settlementMethod;

    /**
     * @var string $transactionToken
     */
    protected $transactionToken;

    /**
     * @var int $verificationLevel
     */
    protected $verificationLevel;

    /**
     * @var string $partialPaymentID
     */
    protected $partialPaymentID;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $debitRequestID
     */
    protected $debitRequestID;

    /**
     * @var string $effectiveDate
     */
    protected $effectiveDate;

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
     * @return int
     */
    public function getPaymentMode()
    {
        return $this->paymentMode;
    }

    /**
     * @param int $paymentMode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setPaymentMode($paymentMode)
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $referenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getSettlementMethod()
    {
        return $this->settlementMethod;
    }

    /**
     * @param string $settlementMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setSettlementMethod($settlementMethod)
    {
        $this->settlementMethod = $settlementMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionToken()
    {
        return $this->transactionToken;
    }

    /**
     * @param string $transactionToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setTransactionToken($transactionToken)
    {
        $this->transactionToken = $transactionToken;

        return $this;
    }

    /**
     * @return int
     */
    public function getVerificationLevel()
    {
        return $this->verificationLevel;
    }

    /**
     * @param int $verificationLevel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setVerificationLevel($verificationLevel)
    {
        $this->verificationLevel = $verificationLevel;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartialPaymentID()
    {
        return $this->partialPaymentID;
    }

    /**
     * @param string $partialPaymentID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setPartialPaymentID($partialPaymentID)
    {
        $this->partialPaymentID = $partialPaymentID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommerceIndicator()
    {
        return $this->commerceIndicator;
    }

    /**
     * @param string $commerceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDebitRequestID()
    {
        return $this->debitRequestID;
    }

    /**
     * @param string $debitRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setDebitRequestID($debitRequestID)
    {
        $this->debitRequestID = $debitRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * @param string $effectiveDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
