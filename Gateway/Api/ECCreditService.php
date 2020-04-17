<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ECCreditService
{
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
     * @var string $debitRequestID
     */
    protected $debitRequestID;

    /**
     * @var string $partialPaymentID
     */
    protected $partialPaymentID;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $debitRequestToken
     */
    protected $debitRequestToken;

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
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $referenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
     */
    public function setTransactionToken($transactionToken)
    {
        $this->transactionToken = $transactionToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
     */
    public function setDebitRequestID($debitRequestID)
    {
        $this->debitRequestID = $debitRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDebitRequestToken()
    {
        return $this->debitRequestToken;
    }

    /**
     * @param string $debitRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
     */
    public function setDebitRequestToken($debitRequestToken)
    {
        $this->debitRequestToken = $debitRequestToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
