<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class BankTransferReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $accountHolder
     */
    protected $accountHolder;

    /**
     * @var string $accountNumber
     */
    protected $accountNumber;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $bankName
     */
    protected $bankName;

    /**
     * @var string $bankCity
     */
    protected $bankCity;

    /**
     * @var string $bankCountry
     */
    protected $bankCountry;

    /**
     * @var string $paymentReference
     */
    protected $paymentReference;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $bankSwiftCode
     */
    protected $bankSwiftCode;

    /**
     * @var string $bankSpecialID
     */
    protected $bankSpecialID;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $iban
     */
    protected $iban;

    /**
     * @var string $bankCode
     */
    protected $bankCode;

    /**
     * @var string $branchCode
     */
    protected $branchCode;

    /**
     * @var string $reconciliationReferenceNumber
     */
    protected $reconciliationReferenceNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountHolder()
    {
        return $this->accountHolder;
    }

    /**
     * @param string $accountHolder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setAccountHolder($accountHolder)
    {
        $this->accountHolder = $accountHolder;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankCity()
    {
        return $this->bankCity;
    }

    /**
     * @param string $bankCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setBankCity($bankCity)
    {
        $this->bankCity = $bankCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankCountry()
    {
        return $this->bankCountry;
    }

    /**
     * @param string $bankCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setBankCountry($bankCountry)
    {
        $this->bankCountry = $bankCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentReference()
    {
        return $this->paymentReference;
    }

    /**
     * @param string $paymentReference
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setPaymentReference($paymentReference)
    {
        $this->paymentReference = $paymentReference;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorResponse()
    {
        return $this->processorResponse;
    }

    /**
     * @param string $processorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankSwiftCode()
    {
        return $this->bankSwiftCode;
    }

    /**
     * @param string $bankSwiftCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setBankSwiftCode($bankSwiftCode)
    {
        $this->bankSwiftCode = $bankSwiftCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankSpecialID()
    {
        return $this->bankSpecialID;
    }

    /**
     * @param string $bankSpecialID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setBankSpecialID($bankSpecialID)
    {
        $this->bankSpecialID = $bankSpecialID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * @param string $bankCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * @param string $branchCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setBranchCode($branchCode)
    {
        $this->branchCode = $branchCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationReferenceNumber()
    {
        return $this->reconciliationReferenceNumber;
    }

    /**
     * @param string $reconciliationReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply
     */
    public function setReconciliationReferenceNumber($reconciliationReferenceNumber)
    {
        $this->reconciliationReferenceNumber = $reconciliationReferenceNumber;

        return $this;
    }
}
