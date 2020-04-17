<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Check
{
    /**
     * @var string $fullName
     */
    protected $fullName;

    /**
     * @var string $accountNumber
     */
    protected $accountNumber;

    /**
     * @var string $accountType
     */
    protected $accountType;

    /**
     * @var string $bankTransitNumber
     */
    protected $bankTransitNumber;

    /**
     * @var string $checkNumber
     */
    protected $checkNumber;

    /**
     * @var string $secCode
     */
    protected $secCode;

    /**
     * @var string $accountEncoderID
     */
    protected $accountEncoderID;

    /**
     * @var string $authenticateID
     */
    protected $authenticateID;

    /**
     * @var string $paymentInfo
     */
    protected $paymentInfo;

    /**
     * @var string $imageReferenceNumber
     */
    protected $imageReferenceNumber;

    /**
     * @var string $terminalCity
     */
    protected $terminalCity;

    /**
     * @var string $terminalState
     */
    protected $terminalState;

    /**
     * @var string $customerPresent
     */
    protected $customerPresent;

    /**
     * @var string $checkTransactionCode
     */
    protected $checkTransactionCode;

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankTransitNumber()
    {
        return $this->bankTransitNumber;
    }

    /**
     * @param string $bankTransitNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setBankTransitNumber($bankTransitNumber)
    {
        $this->bankTransitNumber = $bankTransitNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckNumber()
    {
        return $this->checkNumber;
    }

    /**
     * @param string $checkNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setCheckNumber($checkNumber)
    {
        $this->checkNumber = $checkNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecCode()
    {
        return $this->secCode;
    }

    /**
     * @param string $secCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setSecCode($secCode)
    {
        $this->secCode = $secCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountEncoderID()
    {
        return $this->accountEncoderID;
    }

    /**
     * @param string $accountEncoderID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setAccountEncoderID($accountEncoderID)
    {
        $this->accountEncoderID = $accountEncoderID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticateID()
    {
        return $this->authenticateID;
    }

    /**
     * @param string $authenticateID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setAuthenticateID($authenticateID)
    {
        $this->authenticateID = $authenticateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * @param string $paymentInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setPaymentInfo($paymentInfo)
    {
        $this->paymentInfo = $paymentInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageReferenceNumber()
    {
        return $this->imageReferenceNumber;
    }

    /**
     * @param string $imageReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setImageReferenceNumber($imageReferenceNumber)
    {
        $this->imageReferenceNumber = $imageReferenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalCity()
    {
        return $this->terminalCity;
    }

    /**
     * @param string $terminalCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setTerminalCity($terminalCity)
    {
        $this->terminalCity = $terminalCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalState()
    {
        return $this->terminalState;
    }

    /**
     * @param string $terminalState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setTerminalState($terminalState)
    {
        $this->terminalState = $terminalState;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerPresent()
    {
        return $this->customerPresent;
    }

    /**
     * @param string $customerPresent
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setCustomerPresent($customerPresent)
    {
        $this->customerPresent = $customerPresent;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckTransactionCode()
    {
        return $this->checkTransactionCode;
    }

    /**
     * @param string $checkTransactionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Check
     */
    public function setCheckTransactionCode($checkTransactionCode)
    {
        $this->checkTransactionCode = $checkTransactionCode;

        return $this;
    }
}
