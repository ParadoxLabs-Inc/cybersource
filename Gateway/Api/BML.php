<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BML
{
    /**
     * @var boolean $customerBillingAddressChange
     */
    protected $customerBillingAddressChange;

    /**
     * @var boolean $customerEmailChange
     */
    protected $customerEmailChange;

    /**
     * @var boolean $customerHasCheckingAccount
     */
    protected $customerHasCheckingAccount;

    /**
     * @var boolean $customerHasSavingsAccount
     */
    protected $customerHasSavingsAccount;

    /**
     * @var boolean $customerPasswordChange
     */
    protected $customerPasswordChange;

    /**
     * @var boolean $customerPhoneChange
     */
    protected $customerPhoneChange;

    /**
     * @var string $customerRegistrationDate
     */
    protected $customerRegistrationDate;

    /**
     * @var string $customerTypeFlag
     */
    protected $customerTypeFlag;

    /**
     * @var float $grossHouseholdIncome
     */
    protected $grossHouseholdIncome;

    /**
     * @var string $householdIncomeCurrency
     */
    protected $householdIncomeCurrency;

    /**
     * @var string $itemCategory
     */
    protected $itemCategory;

    /**
     * @var string $merchantPromotionCode
     */
    protected $merchantPromotionCode;

    /**
     * @var string $preapprovalNumber
     */
    protected $preapprovalNumber;

    /**
     * @var string $productDeliveryTypeIndicator
     */
    protected $productDeliveryTypeIndicator;

    /**
     * @var string $residenceStatus
     */
    protected $residenceStatus;

    /**
     * @var string $tcVersion
     */
    protected $tcVersion;

    /**
     * @var int $yearsAtCurrentResidence
     */
    protected $yearsAtCurrentResidence;

    /**
     * @var int $yearsWithCurrentEmployer
     */
    protected $yearsWithCurrentEmployer;

    /**
     * @var string $employerStreet1
     */
    protected $employerStreet1;

    /**
     * @var string $employerStreet2
     */
    protected $employerStreet2;

    /**
     * @var string $employerCity
     */
    protected $employerCity;

    /**
     * @var string $employerCompanyName
     */
    protected $employerCompanyName;

    /**
     * @var string $employerCountry
     */
    protected $employerCountry;

    /**
     * @var string $employerPhoneNumber
     */
    protected $employerPhoneNumber;

    /**
     * @var string $employerPhoneType
     */
    protected $employerPhoneType;

    /**
     * @var string $employerState
     */
    protected $employerState;

    /**
     * @var string $employerPostalCode
     */
    protected $employerPostalCode;

    /**
     * @var string $shipToPhoneType
     */
    protected $shipToPhoneType;

    /**
     * @var string $billToPhoneType
     */
    protected $billToPhoneType;

    /**
     * @var string $methodOfPayment
     */
    protected $methodOfPayment;

    /**
     * @var string $productType
     */
    protected $productType;

    /**
     * @var string $customerAuthenticatedByMerchant
     */
    protected $customerAuthenticatedByMerchant;

    /**
     * @var string $backOfficeIndicator
     */
    protected $backOfficeIndicator;

    /**
     * @var string $shipToEqualsBillToNameIndicator
     */
    protected $shipToEqualsBillToNameIndicator;

    /**
     * @var string $shipToEqualsBillToAddressIndicator
     */
    protected $shipToEqualsBillToAddressIndicator;

    /**
     * @var string $alternateIPAddress
     */
    protected $alternateIPAddress;

    /**
     * @var string $businessLegalName
     */
    protected $businessLegalName;

    /**
     * @var string $dbaName
     */
    protected $dbaName;

    /**
     * @var string $businessAddress1
     */
    protected $businessAddress1;

    /**
     * @var string $businessAddress2
     */
    protected $businessAddress2;

    /**
     * @var string $businessCity
     */
    protected $businessCity;

    /**
     * @var string $businessState
     */
    protected $businessState;

    /**
     * @var string $businessPostalCode
     */
    protected $businessPostalCode;

    /**
     * @var string $businessCountry
     */
    protected $businessCountry;

    /**
     * @var string $businessMainPhone
     */
    protected $businessMainPhone;

    /**
     * @var string $userID
     */
    protected $userID;

    /**
     * @var string $pin
     */
    protected $pin;

    /**
     * @var string $adminLastName
     */
    protected $adminLastName;

    /**
     * @var string $adminFirstName
     */
    protected $adminFirstName;

    /**
     * @var string $adminPhone
     */
    protected $adminPhone;

    /**
     * @var string $adminFax
     */
    protected $adminFax;

    /**
     * @var string $adminEmailAddress
     */
    protected $adminEmailAddress;

    /**
     * @var string $adminTitle
     */
    protected $adminTitle;

    /**
     * @var string $supervisorLastName
     */
    protected $supervisorLastName;

    /**
     * @var string $supervisorFirstName
     */
    protected $supervisorFirstName;

    /**
     * @var string $supervisorEmailAddress
     */
    protected $supervisorEmailAddress;

    /**
     * @var string $businessDAndBNumber
     */
    protected $businessDAndBNumber;

    /**
     * @var string $businessTaxID
     */
    protected $businessTaxID;

    /**
     * @var string $businessNAICSCode
     */
    protected $businessNAICSCode;

    /**
     * @var string $businessType
     */
    protected $businessType;

    /**
     * @var string $businessYearsInBusiness
     */
    protected $businessYearsInBusiness;

    /**
     * @var string $businessNumberOfEmployees
     */
    protected $businessNumberOfEmployees;

    /**
     * @var string $businessPONumber
     */
    protected $businessPONumber;

    /**
     * @var string $businessLoanType
     */
    protected $businessLoanType;

    /**
     * @var string $businessApplicationID
     */
    protected $businessApplicationID;

    /**
     * @var string $businessProductCode
     */
    protected $businessProductCode;

    /**
     * @var string $pgLastName
     */
    protected $pgLastName;

    /**
     * @var string $pgFirstName
     */
    protected $pgFirstName;

    /**
     * @var string $pgSSN
     */
    protected $pgSSN;

    /**
     * @var string $pgDateOfBirth
     */
    protected $pgDateOfBirth;

    /**
     * @var string $pgAnnualIncome
     */
    protected $pgAnnualIncome;

    /**
     * @var string $pgIncomeCurrencyType
     */
    protected $pgIncomeCurrencyType;

    /**
     * @var string $pgResidenceStatus
     */
    protected $pgResidenceStatus;

    /**
     * @var string $pgCheckingAccountIndicator
     */
    protected $pgCheckingAccountIndicator;

    /**
     * @var string $pgSavingsAccountIndicator
     */
    protected $pgSavingsAccountIndicator;

    /**
     * @var string $pgYearsAtEmployer
     */
    protected $pgYearsAtEmployer;

    /**
     * @var string $pgYearsAtResidence
     */
    protected $pgYearsAtResidence;

    /**
     * @var string $pgHomeAddress1
     */
    protected $pgHomeAddress1;

    /**
     * @var string $pgHomeAddress2
     */
    protected $pgHomeAddress2;

    /**
     * @var string $pgHomeCity
     */
    protected $pgHomeCity;

    /**
     * @var string $pgHomeState
     */
    protected $pgHomeState;

    /**
     * @var string $pgHomePostalCode
     */
    protected $pgHomePostalCode;

    /**
     * @var string $pgHomeCountry
     */
    protected $pgHomeCountry;

    /**
     * @var string $pgEmailAddress
     */
    protected $pgEmailAddress;

    /**
     * @var string $pgHomePhone
     */
    protected $pgHomePhone;

    /**
     * @var string $pgTitle
     */
    protected $pgTitle;

    /**
     * @return boolean
     */
    public function getCustomerBillingAddressChange()
    {
        return $this->customerBillingAddressChange;
    }

    /**
     * @param boolean $customerBillingAddressChange
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerBillingAddressChange($customerBillingAddressChange)
    {
        $this->customerBillingAddressChange = $customerBillingAddressChange;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCustomerEmailChange()
    {
        return $this->customerEmailChange;
    }

    /**
     * @param boolean $customerEmailChange
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerEmailChange($customerEmailChange)
    {
        $this->customerEmailChange = $customerEmailChange;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCustomerHasCheckingAccount()
    {
        return $this->customerHasCheckingAccount;
    }

    /**
     * @param boolean $customerHasCheckingAccount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerHasCheckingAccount($customerHasCheckingAccount)
    {
        $this->customerHasCheckingAccount = $customerHasCheckingAccount;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCustomerHasSavingsAccount()
    {
        return $this->customerHasSavingsAccount;
    }

    /**
     * @param boolean $customerHasSavingsAccount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerHasSavingsAccount($customerHasSavingsAccount)
    {
        $this->customerHasSavingsAccount = $customerHasSavingsAccount;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCustomerPasswordChange()
    {
        return $this->customerPasswordChange;
    }

    /**
     * @param boolean $customerPasswordChange
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerPasswordChange($customerPasswordChange)
    {
        $this->customerPasswordChange = $customerPasswordChange;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCustomerPhoneChange()
    {
        return $this->customerPhoneChange;
    }

    /**
     * @param boolean $customerPhoneChange
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerPhoneChange($customerPhoneChange)
    {
        $this->customerPhoneChange = $customerPhoneChange;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerRegistrationDate()
    {
        return $this->customerRegistrationDate;
    }

    /**
     * @param string $customerRegistrationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerRegistrationDate($customerRegistrationDate)
    {
        $this->customerRegistrationDate = $customerRegistrationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerTypeFlag()
    {
        return $this->customerTypeFlag;
    }

    /**
     * @param string $customerTypeFlag
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerTypeFlag($customerTypeFlag)
    {
        $this->customerTypeFlag = $customerTypeFlag;

        return $this;
    }

    /**
     * @return float
     */
    public function getGrossHouseholdIncome()
    {
        return $this->grossHouseholdIncome;
    }

    /**
     * @param float $grossHouseholdIncome
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setGrossHouseholdIncome($grossHouseholdIncome)
    {
        $this->grossHouseholdIncome = $grossHouseholdIncome;

        return $this;
    }

    /**
     * @return string
     */
    public function getHouseholdIncomeCurrency()
    {
        return $this->householdIncomeCurrency;
    }

    /**
     * @param string $householdIncomeCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setHouseholdIncomeCurrency($householdIncomeCurrency)
    {
        $this->householdIncomeCurrency = $householdIncomeCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemCategory()
    {
        return $this->itemCategory;
    }

    /**
     * @param string $itemCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setItemCategory($itemCategory)
    {
        $this->itemCategory = $itemCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantPromotionCode()
    {
        return $this->merchantPromotionCode;
    }

    /**
     * @param string $merchantPromotionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setMerchantPromotionCode($merchantPromotionCode)
    {
        $this->merchantPromotionCode = $merchantPromotionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreapprovalNumber()
    {
        return $this->preapprovalNumber;
    }

    /**
     * @param string $preapprovalNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPreapprovalNumber($preapprovalNumber)
    {
        $this->preapprovalNumber = $preapprovalNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductDeliveryTypeIndicator()
    {
        return $this->productDeliveryTypeIndicator;
    }

    /**
     * @param string $productDeliveryTypeIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setProductDeliveryTypeIndicator($productDeliveryTypeIndicator)
    {
        $this->productDeliveryTypeIndicator = $productDeliveryTypeIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getResidenceStatus()
    {
        return $this->residenceStatus;
    }

    /**
     * @param string $residenceStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setResidenceStatus($residenceStatus)
    {
        $this->residenceStatus = $residenceStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getTcVersion()
    {
        return $this->tcVersion;
    }

    /**
     * @param string $tcVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setTcVersion($tcVersion)
    {
        $this->tcVersion = $tcVersion;

        return $this;
    }

    /**
     * @return int
     */
    public function getYearsAtCurrentResidence()
    {
        return $this->yearsAtCurrentResidence;
    }

    /**
     * @param int $yearsAtCurrentResidence
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setYearsAtCurrentResidence($yearsAtCurrentResidence)
    {
        $this->yearsAtCurrentResidence = $yearsAtCurrentResidence;

        return $this;
    }

    /**
     * @return int
     */
    public function getYearsWithCurrentEmployer()
    {
        return $this->yearsWithCurrentEmployer;
    }

    /**
     * @param int $yearsWithCurrentEmployer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setYearsWithCurrentEmployer($yearsWithCurrentEmployer)
    {
        $this->yearsWithCurrentEmployer = $yearsWithCurrentEmployer;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerStreet1()
    {
        return $this->employerStreet1;
    }

    /**
     * @param string $employerStreet1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerStreet1($employerStreet1)
    {
        $this->employerStreet1 = $employerStreet1;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerStreet2()
    {
        return $this->employerStreet2;
    }

    /**
     * @param string $employerStreet2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerStreet2($employerStreet2)
    {
        $this->employerStreet2 = $employerStreet2;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerCity()
    {
        return $this->employerCity;
    }

    /**
     * @param string $employerCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerCity($employerCity)
    {
        $this->employerCity = $employerCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerCompanyName()
    {
        return $this->employerCompanyName;
    }

    /**
     * @param string $employerCompanyName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerCompanyName($employerCompanyName)
    {
        $this->employerCompanyName = $employerCompanyName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerCountry()
    {
        return $this->employerCountry;
    }

    /**
     * @param string $employerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerCountry($employerCountry)
    {
        $this->employerCountry = $employerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerPhoneNumber()
    {
        return $this->employerPhoneNumber;
    }

    /**
     * @param string $employerPhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerPhoneNumber($employerPhoneNumber)
    {
        $this->employerPhoneNumber = $employerPhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerPhoneType()
    {
        return $this->employerPhoneType;
    }

    /**
     * @param string $employerPhoneType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerPhoneType($employerPhoneType)
    {
        $this->employerPhoneType = $employerPhoneType;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerState()
    {
        return $this->employerState;
    }

    /**
     * @param string $employerState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerState($employerState)
    {
        $this->employerState = $employerState;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmployerPostalCode()
    {
        return $this->employerPostalCode;
    }

    /**
     * @param string $employerPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setEmployerPostalCode($employerPostalCode)
    {
        $this->employerPostalCode = $employerPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToPhoneType()
    {
        return $this->shipToPhoneType;
    }

    /**
     * @param string $shipToPhoneType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setShipToPhoneType($shipToPhoneType)
    {
        $this->shipToPhoneType = $shipToPhoneType;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillToPhoneType()
    {
        return $this->billToPhoneType;
    }

    /**
     * @param string $billToPhoneType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBillToPhoneType($billToPhoneType)
    {
        $this->billToPhoneType = $billToPhoneType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethodOfPayment()
    {
        return $this->methodOfPayment;
    }

    /**
     * @param string $methodOfPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setMethodOfPayment($methodOfPayment)
    {
        $this->methodOfPayment = $methodOfPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @param string $productType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerAuthenticatedByMerchant()
    {
        return $this->customerAuthenticatedByMerchant;
    }

    /**
     * @param string $customerAuthenticatedByMerchant
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setCustomerAuthenticatedByMerchant($customerAuthenticatedByMerchant)
    {
        $this->customerAuthenticatedByMerchant = $customerAuthenticatedByMerchant;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackOfficeIndicator()
    {
        return $this->backOfficeIndicator;
    }

    /**
     * @param string $backOfficeIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBackOfficeIndicator($backOfficeIndicator)
    {
        $this->backOfficeIndicator = $backOfficeIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToEqualsBillToNameIndicator()
    {
        return $this->shipToEqualsBillToNameIndicator;
    }

    /**
     * @param string $shipToEqualsBillToNameIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setShipToEqualsBillToNameIndicator($shipToEqualsBillToNameIndicator)
    {
        $this->shipToEqualsBillToNameIndicator = $shipToEqualsBillToNameIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToEqualsBillToAddressIndicator()
    {
        return $this->shipToEqualsBillToAddressIndicator;
    }

    /**
     * @param string $shipToEqualsBillToAddressIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setShipToEqualsBillToAddressIndicator($shipToEqualsBillToAddressIndicator)
    {
        $this->shipToEqualsBillToAddressIndicator = $shipToEqualsBillToAddressIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateIPAddress()
    {
        return $this->alternateIPAddress;
    }

    /**
     * @param string $alternateIPAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setAlternateIPAddress($alternateIPAddress)
    {
        $this->alternateIPAddress = $alternateIPAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessLegalName()
    {
        return $this->businessLegalName;
    }

    /**
     * @param string $businessLegalName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessLegalName($businessLegalName)
    {
        $this->businessLegalName = $businessLegalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbaName()
    {
        return $this->dbaName;
    }

    /**
     * @param string $dbaName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setDbaName($dbaName)
    {
        $this->dbaName = $dbaName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessAddress1()
    {
        return $this->businessAddress1;
    }

    /**
     * @param string $businessAddress1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessAddress1($businessAddress1)
    {
        $this->businessAddress1 = $businessAddress1;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessAddress2()
    {
        return $this->businessAddress2;
    }

    /**
     * @param string $businessAddress2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessAddress2($businessAddress2)
    {
        $this->businessAddress2 = $businessAddress2;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessCity()
    {
        return $this->businessCity;
    }

    /**
     * @param string $businessCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessCity($businessCity)
    {
        $this->businessCity = $businessCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessState()
    {
        return $this->businessState;
    }

    /**
     * @param string $businessState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessState($businessState)
    {
        $this->businessState = $businessState;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessPostalCode()
    {
        return $this->businessPostalCode;
    }

    /**
     * @param string $businessPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessPostalCode($businessPostalCode)
    {
        $this->businessPostalCode = $businessPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessCountry()
    {
        return $this->businessCountry;
    }

    /**
     * @param string $businessCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessCountry($businessCountry)
    {
        $this->businessCountry = $businessCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessMainPhone()
    {
        return $this->businessMainPhone;
    }

    /**
     * @param string $businessMainPhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessMainPhone($businessMainPhone)
    {
        $this->businessMainPhone = $businessMainPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param string $userID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param string $pin
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminLastName()
    {
        return $this->adminLastName;
    }

    /**
     * @param string $adminLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setAdminLastName($adminLastName)
    {
        $this->adminLastName = $adminLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminFirstName()
    {
        return $this->adminFirstName;
    }

    /**
     * @param string $adminFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setAdminFirstName($adminFirstName)
    {
        $this->adminFirstName = $adminFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminPhone()
    {
        return $this->adminPhone;
    }

    /**
     * @param string $adminPhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setAdminPhone($adminPhone)
    {
        $this->adminPhone = $adminPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminFax()
    {
        return $this->adminFax;
    }

    /**
     * @param string $adminFax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setAdminFax($adminFax)
    {
        $this->adminFax = $adminFax;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminEmailAddress()
    {
        return $this->adminEmailAddress;
    }

    /**
     * @param string $adminEmailAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setAdminEmailAddress($adminEmailAddress)
    {
        $this->adminEmailAddress = $adminEmailAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminTitle()
    {
        return $this->adminTitle;
    }

    /**
     * @param string $adminTitle
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setAdminTitle($adminTitle)
    {
        $this->adminTitle = $adminTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getSupervisorLastName()
    {
        return $this->supervisorLastName;
    }

    /**
     * @param string $supervisorLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setSupervisorLastName($supervisorLastName)
    {
        $this->supervisorLastName = $supervisorLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSupervisorFirstName()
    {
        return $this->supervisorFirstName;
    }

    /**
     * @param string $supervisorFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setSupervisorFirstName($supervisorFirstName)
    {
        $this->supervisorFirstName = $supervisorFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSupervisorEmailAddress()
    {
        return $this->supervisorEmailAddress;
    }

    /**
     * @param string $supervisorEmailAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setSupervisorEmailAddress($supervisorEmailAddress)
    {
        $this->supervisorEmailAddress = $supervisorEmailAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessDAndBNumber()
    {
        return $this->businessDAndBNumber;
    }

    /**
     * @param string $businessDAndBNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessDAndBNumber($businessDAndBNumber)
    {
        $this->businessDAndBNumber = $businessDAndBNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessTaxID()
    {
        return $this->businessTaxID;
    }

    /**
     * @param string $businessTaxID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessTaxID($businessTaxID)
    {
        $this->businessTaxID = $businessTaxID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessNAICSCode()
    {
        return $this->businessNAICSCode;
    }

    /**
     * @param string $businessNAICSCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessNAICSCode($businessNAICSCode)
    {
        $this->businessNAICSCode = $businessNAICSCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessType()
    {
        return $this->businessType;
    }

    /**
     * @param string $businessType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessType($businessType)
    {
        $this->businessType = $businessType;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessYearsInBusiness()
    {
        return $this->businessYearsInBusiness;
    }

    /**
     * @param string $businessYearsInBusiness
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessYearsInBusiness($businessYearsInBusiness)
    {
        $this->businessYearsInBusiness = $businessYearsInBusiness;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessNumberOfEmployees()
    {
        return $this->businessNumberOfEmployees;
    }

    /**
     * @param string $businessNumberOfEmployees
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessNumberOfEmployees($businessNumberOfEmployees)
    {
        $this->businessNumberOfEmployees = $businessNumberOfEmployees;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessPONumber()
    {
        return $this->businessPONumber;
    }

    /**
     * @param string $businessPONumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessPONumber($businessPONumber)
    {
        $this->businessPONumber = $businessPONumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessLoanType()
    {
        return $this->businessLoanType;
    }

    /**
     * @param string $businessLoanType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessLoanType($businessLoanType)
    {
        $this->businessLoanType = $businessLoanType;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessApplicationID()
    {
        return $this->businessApplicationID;
    }

    /**
     * @param string $businessApplicationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessApplicationID($businessApplicationID)
    {
        $this->businessApplicationID = $businessApplicationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessProductCode()
    {
        return $this->businessProductCode;
    }

    /**
     * @param string $businessProductCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setBusinessProductCode($businessProductCode)
    {
        $this->businessProductCode = $businessProductCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgLastName()
    {
        return $this->pgLastName;
    }

    /**
     * @param string $pgLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgLastName($pgLastName)
    {
        $this->pgLastName = $pgLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgFirstName()
    {
        return $this->pgFirstName;
    }

    /**
     * @param string $pgFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgFirstName($pgFirstName)
    {
        $this->pgFirstName = $pgFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgSSN()
    {
        return $this->pgSSN;
    }

    /**
     * @param string $pgSSN
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgSSN($pgSSN)
    {
        $this->pgSSN = $pgSSN;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgDateOfBirth()
    {
        return $this->pgDateOfBirth;
    }

    /**
     * @param string $pgDateOfBirth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgDateOfBirth($pgDateOfBirth)
    {
        $this->pgDateOfBirth = $pgDateOfBirth;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgAnnualIncome()
    {
        return $this->pgAnnualIncome;
    }

    /**
     * @param string $pgAnnualIncome
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgAnnualIncome($pgAnnualIncome)
    {
        $this->pgAnnualIncome = $pgAnnualIncome;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgIncomeCurrencyType()
    {
        return $this->pgIncomeCurrencyType;
    }

    /**
     * @param string $pgIncomeCurrencyType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgIncomeCurrencyType($pgIncomeCurrencyType)
    {
        $this->pgIncomeCurrencyType = $pgIncomeCurrencyType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgResidenceStatus()
    {
        return $this->pgResidenceStatus;
    }

    /**
     * @param string $pgResidenceStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgResidenceStatus($pgResidenceStatus)
    {
        $this->pgResidenceStatus = $pgResidenceStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgCheckingAccountIndicator()
    {
        return $this->pgCheckingAccountIndicator;
    }

    /**
     * @param string $pgCheckingAccountIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgCheckingAccountIndicator($pgCheckingAccountIndicator)
    {
        $this->pgCheckingAccountIndicator = $pgCheckingAccountIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgSavingsAccountIndicator()
    {
        return $this->pgSavingsAccountIndicator;
    }

    /**
     * @param string $pgSavingsAccountIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgSavingsAccountIndicator($pgSavingsAccountIndicator)
    {
        $this->pgSavingsAccountIndicator = $pgSavingsAccountIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgYearsAtEmployer()
    {
        return $this->pgYearsAtEmployer;
    }

    /**
     * @param string $pgYearsAtEmployer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgYearsAtEmployer($pgYearsAtEmployer)
    {
        $this->pgYearsAtEmployer = $pgYearsAtEmployer;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgYearsAtResidence()
    {
        return $this->pgYearsAtResidence;
    }

    /**
     * @param string $pgYearsAtResidence
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgYearsAtResidence($pgYearsAtResidence)
    {
        $this->pgYearsAtResidence = $pgYearsAtResidence;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgHomeAddress1()
    {
        return $this->pgHomeAddress1;
    }

    /**
     * @param string $pgHomeAddress1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgHomeAddress1($pgHomeAddress1)
    {
        $this->pgHomeAddress1 = $pgHomeAddress1;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgHomeAddress2()
    {
        return $this->pgHomeAddress2;
    }

    /**
     * @param string $pgHomeAddress2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgHomeAddress2($pgHomeAddress2)
    {
        $this->pgHomeAddress2 = $pgHomeAddress2;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgHomeCity()
    {
        return $this->pgHomeCity;
    }

    /**
     * @param string $pgHomeCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgHomeCity($pgHomeCity)
    {
        $this->pgHomeCity = $pgHomeCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgHomeState()
    {
        return $this->pgHomeState;
    }

    /**
     * @param string $pgHomeState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgHomeState($pgHomeState)
    {
        $this->pgHomeState = $pgHomeState;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgHomePostalCode()
    {
        return $this->pgHomePostalCode;
    }

    /**
     * @param string $pgHomePostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgHomePostalCode($pgHomePostalCode)
    {
        $this->pgHomePostalCode = $pgHomePostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgHomeCountry()
    {
        return $this->pgHomeCountry;
    }

    /**
     * @param string $pgHomeCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgHomeCountry($pgHomeCountry)
    {
        $this->pgHomeCountry = $pgHomeCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgEmailAddress()
    {
        return $this->pgEmailAddress;
    }

    /**
     * @param string $pgEmailAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgEmailAddress($pgEmailAddress)
    {
        $this->pgEmailAddress = $pgEmailAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgHomePhone()
    {
        return $this->pgHomePhone;
    }

    /**
     * @param string $pgHomePhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgHomePhone($pgHomePhone)
    {
        $this->pgHomePhone = $pgHomePhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgTitle()
    {
        return $this->pgTitle;
    }

    /**
     * @param string $pgTitle
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BML
     */
    public function setPgTitle($pgTitle)
    {
        $this->pgTitle = $pgTitle;

        return $this;
    }
}
