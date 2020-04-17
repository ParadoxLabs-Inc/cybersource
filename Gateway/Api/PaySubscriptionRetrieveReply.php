<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionRetrieveReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $approvalRequired
     */
    protected $approvalRequired;

    /**
     * @var string $automaticRenew
     */
    protected $automaticRenew;

    /**
     * @var string $cardAccountNumber
     */
    protected $cardAccountNumber;

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
     * @var string $cardType
     */
    protected $cardType;

    /**
     * @var string $checkAccountNumber
     */
    protected $checkAccountNumber;

    /**
     * @var string $checkAccountType
     */
    protected $checkAccountType;

    /**
     * @var string $checkBankTransitNumber
     */
    protected $checkBankTransitNumber;

    /**
     * @var string $checkSecCode
     */
    protected $checkSecCode;

    /**
     * @var string $checkAuthenticateID
     */
    protected $checkAuthenticateID;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $comments
     */
    protected $comments;

    /**
     * @var string $companyName
     */
    protected $companyName;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $customerAccountID
     */
    protected $customerAccountID;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var string $endDate
     */
    protected $endDate;

    /**
     * @var string $firstName
     */
    protected $firstName;

    /**
     * @var string $frequency
     */
    protected $frequency;

    /**
     * @var string $lastName
     */
    protected $lastName;

    /**
     * @var string $merchantReferenceCode
     */
    protected $merchantReferenceCode;

    /**
     * @var string $paymentMethod
     */
    protected $paymentMethod;

    /**
     * @var string $paymentsRemaining
     */
    protected $paymentsRemaining;

    /**
     * @var string $phoneNumber
     */
    protected $phoneNumber;

    /**
     * @var string $postalCode
     */
    protected $postalCode;

    /**
     * @var string $recurringAmount
     */
    protected $recurringAmount;

    /**
     * @var string $setupAmount
     */
    protected $setupAmount;

    /**
     * @var string $startDate
     */
    protected $startDate;

    /**
     * @var string $state
     */
    protected $state;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var string $street1
     */
    protected $street1;

    /**
     * @var string $street2
     */
    protected $street2;

    /**
     * @var string $subscriptionID
     */
    protected $subscriptionID;

    /**
     * @var string $subscriptionIDNew
     */
    protected $subscriptionIDNew;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $totalPayments
     */
    protected $totalPayments;

    /**
     * @var string $shipToFirstName
     */
    protected $shipToFirstName;

    /**
     * @var string $shipToLastName
     */
    protected $shipToLastName;

    /**
     * @var string $shipToStreet1
     */
    protected $shipToStreet1;

    /**
     * @var string $shipToStreet2
     */
    protected $shipToStreet2;

    /**
     * @var string $shipToCity
     */
    protected $shipToCity;

    /**
     * @var string $shipToState
     */
    protected $shipToState;

    /**
     * @var string $shipToPostalCode
     */
    protected $shipToPostalCode;

    /**
     * @var string $shipToCompany
     */
    protected $shipToCompany;

    /**
     * @var string $shipToCountry
     */
    protected $shipToCountry;

    /**
     * @var string $billPayment
     */
    protected $billPayment;

    /**
     * @var string $merchantDefinedDataField1
     */
    protected $merchantDefinedDataField1;

    /**
     * @var string $merchantDefinedDataField2
     */
    protected $merchantDefinedDataField2;

    /**
     * @var string $merchantDefinedDataField3
     */
    protected $merchantDefinedDataField3;

    /**
     * @var string $merchantDefinedDataField4
     */
    protected $merchantDefinedDataField4;

    /**
     * @var string $merchantSecureDataField1
     */
    protected $merchantSecureDataField1;

    /**
     * @var string $merchantSecureDataField2
     */
    protected $merchantSecureDataField2;

    /**
     * @var string $merchantSecureDataField3
     */
    protected $merchantSecureDataField3;

    /**
     * @var string $merchantSecureDataField4
     */
    protected $merchantSecureDataField4;

    /**
     * @var string $ownerMerchantID
     */
    protected $ownerMerchantID;

    /**
     * @var string $companyTaxID
     */
    protected $companyTaxID;

    /**
     * @var string $driversLicenseNumber
     */
    protected $driversLicenseNumber;

    /**
     * @var string $driversLicenseState
     */
    protected $driversLicenseState;

    /**
     * @var string $dateOfBirth
     */
    protected $dateOfBirth;

    /**
     * @var string $instrumentIdentifierID
     */
    protected $instrumentIdentifierID;

    /**
     * @var string $instrumentIdentifierStatus
     */
    protected $instrumentIdentifierStatus;

    /**
     * @var string $instrumentIdentifierSuccessorID
     */
    protected $instrumentIdentifierSuccessorID;

    /**
     * @var string $subsequentAuthTransactionID
     */
    protected $subsequentAuthTransactionID;

    /**
     * @var string $latestCardSuffix
     */
    protected $latestCardSuffix;

    /**
     * @var string $latestCardExpirationMonth
     */
    protected $latestCardExpirationMonth;

    /**
     * @var string $latestCardExpirationYear
     */
    protected $latestCardExpirationYear;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getApprovalRequired()
    {
        return $this->approvalRequired;
    }

    /**
     * @param string $approvalRequired
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setApprovalRequired($approvalRequired)
    {
        $this->approvalRequired = $approvalRequired;

        return $this;
    }

    /**
     * @return string
     */
    public function getAutomaticRenew()
    {
        return $this->automaticRenew;
    }

    /**
     * @param string $automaticRenew
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setAutomaticRenew($automaticRenew)
    {
        $this->automaticRenew = $automaticRenew;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCardAccountNumber($cardAccountNumber)
    {
        $this->cardAccountNumber = $cardAccountNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCardStartYear($cardStartYear)
    {
        $this->cardStartYear = $cardStartYear;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckAccountNumber()
    {
        return $this->checkAccountNumber;
    }

    /**
     * @param string $checkAccountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCheckAccountNumber($checkAccountNumber)
    {
        $this->checkAccountNumber = $checkAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckAccountType()
    {
        return $this->checkAccountType;
    }

    /**
     * @param string $checkAccountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCheckAccountType($checkAccountType)
    {
        $this->checkAccountType = $checkAccountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckBankTransitNumber()
    {
        return $this->checkBankTransitNumber;
    }

    /**
     * @param string $checkBankTransitNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCheckBankTransitNumber($checkBankTransitNumber)
    {
        $this->checkBankTransitNumber = $checkBankTransitNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckSecCode()
    {
        return $this->checkSecCode;
    }

    /**
     * @param string $checkSecCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCheckSecCode($checkSecCode)
    {
        $this->checkSecCode = $checkSecCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckAuthenticateID()
    {
        return $this->checkAuthenticateID;
    }

    /**
     * @param string $checkAuthenticateID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCheckAuthenticateID($checkAuthenticateID)
    {
        $this->checkAuthenticateID = $checkAuthenticateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerAccountID()
    {
        return $this->customerAccountID;
    }

    /**
     * @param string $customerAccountID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCustomerAccountID($customerAccountID)
    {
        $this->customerAccountID = $customerAccountID;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param string $frequency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantReferenceCode()
    {
        return $this->merchantReferenceCode;
    }

    /**
     * @param string $merchantReferenceCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantReferenceCode($merchantReferenceCode)
    {
        $this->merchantReferenceCode = $merchantReferenceCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentsRemaining()
    {
        return $this->paymentsRemaining;
    }

    /**
     * @param string $paymentsRemaining
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setPaymentsRemaining($paymentsRemaining)
    {
        $this->paymentsRemaining = $paymentsRemaining;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecurringAmount()
    {
        return $this->recurringAmount;
    }

    /**
     * @param string $recurringAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setRecurringAmount($recurringAmount)
    {
        $this->recurringAmount = $recurringAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getSetupAmount()
    {
        return $this->setupAmount;
    }

    /**
     * @param string $setupAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setSetupAmount($setupAmount)
    {
        $this->setupAmount = $setupAmount;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet1()
    {
        return $this->street1;
    }

    /**
     * @param string $street1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setStreet1($street1)
    {
        $this->street1 = $street1;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @param string $street2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubscriptionID()
    {
        return $this->subscriptionID;
    }

    /**
     * @param string $subscriptionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setSubscriptionID($subscriptionID)
    {
        $this->subscriptionID = $subscriptionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubscriptionIDNew()
    {
        return $this->subscriptionIDNew;
    }

    /**
     * @param string $subscriptionIDNew
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setSubscriptionIDNew($subscriptionIDNew)
    {
        $this->subscriptionIDNew = $subscriptionIDNew;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotalPayments()
    {
        return $this->totalPayments;
    }

    /**
     * @param string $totalPayments
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setTotalPayments($totalPayments)
    {
        $this->totalPayments = $totalPayments;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToFirstName()
    {
        return $this->shipToFirstName;
    }

    /**
     * @param string $shipToFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToFirstName($shipToFirstName)
    {
        $this->shipToFirstName = $shipToFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToLastName()
    {
        return $this->shipToLastName;
    }

    /**
     * @param string $shipToLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToLastName($shipToLastName)
    {
        $this->shipToLastName = $shipToLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToStreet1()
    {
        return $this->shipToStreet1;
    }

    /**
     * @param string $shipToStreet1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToStreet1($shipToStreet1)
    {
        $this->shipToStreet1 = $shipToStreet1;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToStreet2()
    {
        return $this->shipToStreet2;
    }

    /**
     * @param string $shipToStreet2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToStreet2($shipToStreet2)
    {
        $this->shipToStreet2 = $shipToStreet2;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToState($shipToState)
    {
        $this->shipToState = $shipToState;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToPostalCode()
    {
        return $this->shipToPostalCode;
    }

    /**
     * @param string $shipToPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToPostalCode($shipToPostalCode)
    {
        $this->shipToPostalCode = $shipToPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToCompany()
    {
        return $this->shipToCompany;
    }

    /**
     * @param string $shipToCompany
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToCompany($shipToCompany)
    {
        $this->shipToCompany = $shipToCompany;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setShipToCountry($shipToCountry)
    {
        $this->shipToCountry = $shipToCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillPayment()
    {
        return $this->billPayment;
    }

    /**
     * @param string $billPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setBillPayment($billPayment)
    {
        $this->billPayment = $billPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDefinedDataField1()
    {
        return $this->merchantDefinedDataField1;
    }

    /**
     * @param string $merchantDefinedDataField1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantDefinedDataField1($merchantDefinedDataField1)
    {
        $this->merchantDefinedDataField1 = $merchantDefinedDataField1;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDefinedDataField2()
    {
        return $this->merchantDefinedDataField2;
    }

    /**
     * @param string $merchantDefinedDataField2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantDefinedDataField2($merchantDefinedDataField2)
    {
        $this->merchantDefinedDataField2 = $merchantDefinedDataField2;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDefinedDataField3()
    {
        return $this->merchantDefinedDataField3;
    }

    /**
     * @param string $merchantDefinedDataField3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantDefinedDataField3($merchantDefinedDataField3)
    {
        $this->merchantDefinedDataField3 = $merchantDefinedDataField3;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDefinedDataField4()
    {
        return $this->merchantDefinedDataField4;
    }

    /**
     * @param string $merchantDefinedDataField4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantDefinedDataField4($merchantDefinedDataField4)
    {
        $this->merchantDefinedDataField4 = $merchantDefinedDataField4;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantSecureDataField1()
    {
        return $this->merchantSecureDataField1;
    }

    /**
     * @param string $merchantSecureDataField1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantSecureDataField1($merchantSecureDataField1)
    {
        $this->merchantSecureDataField1 = $merchantSecureDataField1;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantSecureDataField2()
    {
        return $this->merchantSecureDataField2;
    }

    /**
     * @param string $merchantSecureDataField2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantSecureDataField2($merchantSecureDataField2)
    {
        $this->merchantSecureDataField2 = $merchantSecureDataField2;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantSecureDataField3()
    {
        return $this->merchantSecureDataField3;
    }

    /**
     * @param string $merchantSecureDataField3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantSecureDataField3($merchantSecureDataField3)
    {
        $this->merchantSecureDataField3 = $merchantSecureDataField3;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantSecureDataField4()
    {
        return $this->merchantSecureDataField4;
    }

    /**
     * @param string $merchantSecureDataField4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setMerchantSecureDataField4($merchantSecureDataField4)
    {
        $this->merchantSecureDataField4 = $merchantSecureDataField4;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerMerchantID()
    {
        return $this->ownerMerchantID;
    }

    /**
     * @param string $ownerMerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setOwnerMerchantID($ownerMerchantID)
    {
        $this->ownerMerchantID = $ownerMerchantID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyTaxID()
    {
        return $this->companyTaxID;
    }

    /**
     * @param string $companyTaxID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setCompanyTaxID($companyTaxID)
    {
        $this->companyTaxID = $companyTaxID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDriversLicenseNumber()
    {
        return $this->driversLicenseNumber;
    }

    /**
     * @param string $driversLicenseNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setDriversLicenseNumber($driversLicenseNumber)
    {
        $this->driversLicenseNumber = $driversLicenseNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getDriversLicenseState()
    {
        return $this->driversLicenseState;
    }

    /**
     * @param string $driversLicenseState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setDriversLicenseState($driversLicenseState)
    {
        $this->driversLicenseState = $driversLicenseState;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $dateOfBirth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstrumentIdentifierID()
    {
        return $this->instrumentIdentifierID;
    }

    /**
     * @param string $instrumentIdentifierID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setInstrumentIdentifierID($instrumentIdentifierID)
    {
        $this->instrumentIdentifierID = $instrumentIdentifierID;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstrumentIdentifierStatus()
    {
        return $this->instrumentIdentifierStatus;
    }

    /**
     * @param string $instrumentIdentifierStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setInstrumentIdentifierStatus($instrumentIdentifierStatus)
    {
        $this->instrumentIdentifierStatus = $instrumentIdentifierStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstrumentIdentifierSuccessorID()
    {
        return $this->instrumentIdentifierSuccessorID;
    }

    /**
     * @param string $instrumentIdentifierSuccessorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setInstrumentIdentifierSuccessorID($instrumentIdentifierSuccessorID)
    {
        $this->instrumentIdentifierSuccessorID = $instrumentIdentifierSuccessorID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubsequentAuthTransactionID()
    {
        return $this->subsequentAuthTransactionID;
    }

    /**
     * @param string $subsequentAuthTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setSubsequentAuthTransactionID($subsequentAuthTransactionID)
    {
        $this->subsequentAuthTransactionID = $subsequentAuthTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getLatestCardSuffix()
    {
        return $this->latestCardSuffix;
    }

    /**
     * @param string $latestCardSuffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setLatestCardSuffix($latestCardSuffix)
    {
        $this->latestCardSuffix = $latestCardSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getLatestCardExpirationMonth()
    {
        return $this->latestCardExpirationMonth;
    }

    /**
     * @param string $latestCardExpirationMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setLatestCardExpirationMonth($latestCardExpirationMonth)
    {
        $this->latestCardExpirationMonth = $latestCardExpirationMonth;

        return $this;
    }

    /**
     * @return string
     */
    public function getLatestCardExpirationYear()
    {
        return $this->latestCardExpirationYear;
    }

    /**
     * @param string $latestCardExpirationYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply
     */
    public function setLatestCardExpirationYear($latestCardExpirationYear)
    {
        $this->latestCardExpirationYear = $latestCardExpirationYear;

        return $this;
    }
}
