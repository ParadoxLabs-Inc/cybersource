<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class HostedDataRetrieveReply
{
    /**
     * @var string $responseMessage
     */
    protected $responseMessage;

    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $aggregatorMerchantIdentifier
     */
    protected $aggregatorMerchantIdentifier;

    /**
     * @var string $customerFirstName
     */
    protected $customerFirstName;

    /**
     * @var string $customerLastName
     */
    protected $customerLastName;

    /**
     * @var string $customerID
     */
    protected $customerID;

    /**
     * @var string $paymentMethod
     */
    protected $paymentMethod;

    /**
     * @var string $billToStreet1
     */
    protected $billToStreet1;

    /**
     * @var string $billToStreet2
     */
    protected $billToStreet2;

    /**
     * @var string $billToEmail
     */
    protected $billToEmail;

    /**
     * @var string $billToState
     */
    protected $billToState;

    /**
     * @var string $billToFirstName
     */
    protected $billToFirstName;

    /**
     * @var string $billToLastName
     */
    protected $billToLastName;

    /**
     * @var string $billToCity
     */
    protected $billToCity;

    /**
     * @var string $billToCountry
     */
    protected $billToCountry;

    /**
     * @var string $billToPostalCode
     */
    protected $billToPostalCode;

    /**
     * @var string $cardAccountNumber
     */
    protected $cardAccountNumber;

    /**
     * @var string $cardType
     */
    protected $cardType;

    /**
     * @var string $cardExpirationMonth
     */
    protected $cardExpirationMonth;

    /**
     * @var string $cardExpirationYear
     */
    protected $cardExpirationYear;

    /**
     * @var string $cardIssueNumber
     */
    protected $cardIssueNumber;

    /**
     * @var string $cardStartMonth
     */
    protected $cardStartMonth;

    /**
     * @var string $cardStartYear
     */
    protected $cardStartYear;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    /**
     * @param string $responseMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setResponseMessage($responseMessage)
    {
        $this->responseMessage = $responseMessage;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorMerchantIdentifier()
    {
        return $this->aggregatorMerchantIdentifier;
    }

    /**
     * @param string $aggregatorMerchantIdentifier
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setAggregatorMerchantIdentifier($aggregatorMerchantIdentifier)
    {
        $this->aggregatorMerchantIdentifier = $aggregatorMerchantIdentifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }

    /**
     * @param string $customerFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCustomerFirstName($customerFirstName)
    {
        $this->customerFirstName = $customerFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }

    /**
     * @param string $customerLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCustomerLastName($customerLastName)
    {
        $this->customerLastName = $customerLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * @param string $customerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToStreet1()
    {
        return $this->billToStreet1;
    }

    /**
     * @param string $billToStreet1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToStreet1($billToStreet1)
    {
        $this->billToStreet1 = $billToStreet1;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToStreet2()
    {
        return $this->billToStreet2;
    }

    /**
     * @param string $billToStreet2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToStreet2($billToStreet2)
    {
        $this->billToStreet2 = $billToStreet2;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToEmail()
    {
        return $this->billToEmail;
    }

    /**
     * @param string $billToEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToEmail($billToEmail)
    {
        $this->billToEmail = $billToEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToState()
    {
        return $this->billToState;
    }

    /**
     * @param string $billToState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToState($billToState)
    {
        $this->billToState = $billToState;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToFirstName()
    {
        return $this->billToFirstName;
    }

    /**
     * @param string $billToFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToFirstName($billToFirstName)
    {
        $this->billToFirstName = $billToFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToLastName()
    {
        return $this->billToLastName;
    }

    /**
     * @param string $billToLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToLastName($billToLastName)
    {
        $this->billToLastName = $billToLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToCity()
    {
        return $this->billToCity;
    }

    /**
     * @param string $billToCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToCity($billToCity)
    {
        $this->billToCity = $billToCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToCountry()
    {
        return $this->billToCountry;
    }

    /**
     * @param string $billToCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToCountry($billToCountry)
    {
        $this->billToCountry = $billToCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToPostalCode()
    {
        return $this->billToPostalCode;
    }

    /**
     * @param string $billToPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setBillToPostalCode($billToPostalCode)
    {
        $this->billToPostalCode = $billToPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardAccountNumber()
    {
        return $this->cardAccountNumber;
    }

    /**
     * @param string $cardAccountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCardAccountNumber($cardAccountNumber)
    {
        $this->cardAccountNumber = $cardAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardExpirationMonth()
    {
        return $this->cardExpirationMonth;
    }

    /**
     * @param string $cardExpirationMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCardExpirationMonth($cardExpirationMonth)
    {
        $this->cardExpirationMonth = $cardExpirationMonth;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardExpirationYear()
    {
        return $this->cardExpirationYear;
    }

    /**
     * @param string $cardExpirationYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCardExpirationYear($cardExpirationYear)
    {
        $this->cardExpirationYear = $cardExpirationYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardIssueNumber()
    {
        return $this->cardIssueNumber;
    }

    /**
     * @param string $cardIssueNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCardIssueNumber($cardIssueNumber)
    {
        $this->cardIssueNumber = $cardIssueNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardStartMonth()
    {
        return $this->cardStartMonth;
    }

    /**
     * @param string $cardStartMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCardStartMonth($cardStartMonth)
    {
        $this->cardStartMonth = $cardStartMonth;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardStartYear()
    {
        return $this->cardStartYear;
    }

    /**
     * @param string $cardStartYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply
     */
    public function setCardStartYear($cardStartYear)
    {
        $this->cardStartYear = $cardStartYear;

        return $this;
    }
}
