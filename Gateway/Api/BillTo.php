<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BillTo
{
    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $firstName
     */
    protected $firstName;

    /**
     * @var string $middleName
     */
    protected $middleName;

    /**
     * @var string $lastName
     */
    protected $lastName;

    /**
     * @var string $suffix
     */
    protected $suffix;

    /**
     * @var string $buildingNumber
     */
    protected $buildingNumber;

    /**
     * @var string $street1
     */
    protected $street1;

    /**
     * @var string $street2
     */
    protected $street2;

    /**
     * @var string $street3
     */
    protected $street3;

    /**
     * @var string $street4
     */
    protected $street4;

    /**
     * @var string $street5
     */
    protected $street5;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $district
     */
    protected $district;

    /**
     * @var string $county
     */
    protected $county;

    /**
     * @var string $state
     */
    protected $state;

    /**
     * @var string $postalCode
     */
    protected $postalCode;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $company
     */
    protected $company;

    /**
     * @var string $companyTaxID
     */
    protected $companyTaxID;

    /**
     * @var string $phoneNumber
     */
    protected $phoneNumber;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var string $ipAddress
     */
    protected $ipAddress;

    /**
     * @var string $customerUserName
     */
    protected $customerUserName;

    /**
     * @var string $customerPassword
     */
    protected $customerPassword;

    /**
     * @var string $ipNetworkAddress
     */
    protected $ipNetworkAddress;

    /**
     * @var string $hostname
     */
    protected $hostname;

    /**
     * @var string $domainName
     */
    protected $domainName;

    /**
     * @var string $dateOfBirth
     */
    protected $dateOfBirth;

    /**
     * @var string $driversLicenseNumber
     */
    protected $driversLicenseNumber;

    /**
     * @var string $driversLicenseState
     */
    protected $driversLicenseState;

    /**
     * @var string $ssn
     */
    protected $ssn;

    /**
     * @var string $customerID
     */
    protected $customerID;

    /**
     * @var string $httpBrowserType
     */
    protected $httpBrowserType;

    /**
     * @var string $httpBrowserEmail
     */
    protected $httpBrowserEmail;

    /**
     * @var boolean $httpBrowserCookiesAccepted
     */
    protected $httpBrowserCookiesAccepted;

    /**
     * @var string $nif
     */
    protected $nif;

    /**
     * @var string $personalID
     */
    protected $personalID;

    /**
     * @var string $language
     */
    protected $language;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $gender
     */
    protected $gender;

    /**
     * @var string $merchantTaxID
     */
    protected $merchantTaxID;

    /**
     * @var string $passportNumber
     */
    protected $passportNumber;

    /**
     * @var string $passportCountry
     */
    protected $passportCountry;

    /**
     * @var string $customerAccountCreateDate
     */
    protected $customerAccountCreateDate;

    /**
     * @var string $customerAccountChangeDate
     */
    protected $customerAccountChangeDate;

    /**
     * @var string $customerAccountPasswordChangeDate
     */
    protected $customerAccountPasswordChangeDate;

    /**
     * @var boolean $pointOfReference
     */
    protected $pointOfReference;

    /**
     * @var boolean $defaultIndicator
     */
    protected $defaultIndicator;

    /**
     * @var string $companyStreet1
     */
    protected $companyStreet1;

    /**
     * @var string $companyStreet2
     */
    protected $companyStreet2;

    /**
     * @var string $companyCity
     */
    protected $companyCity;

    /**
     * @var string $companyCountry
     */
    protected $companyCountry;

    /**
     * @var string $companyState
     */
    protected $companyState;

    /**
     * @var string $companyPostalCode
     */
    protected $companyPostalCode;

    /**
     * @var string $prefix
     */
    protected $prefix;

    /**
     * @var string $companyPhoneNumber
     */
    protected $companyPhoneNumber;

    /**
     * @var string $httpBrowserColorDepth
     */
    protected $httpBrowserColorDepth;

    /**
     * @var boolean $httpBrowserJavaEnabled
     */
    protected $httpBrowserJavaEnabled;

    /**
     * @var boolean $httpBrowserJavaScriptEnabled
     */
    protected $httpBrowserJavaScriptEnabled;

    /**
     * @var string $httpBrowserLanguage
     */
    protected $httpBrowserLanguage;

    /**
     * @var string $httpBrowserScreenHeight
     */
    protected $httpBrowserScreenHeight;

    /**
     * @var string $httpBrowserScreenWidth
     */
    protected $httpBrowserScreenWidth;

    /**
     * @var string $httpBrowserTimeDifference
     */
    protected $httpBrowserTimeDifference;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setFirstName($firstName)
    {
        $this->firstName = substr(str_replace(':', '', $firstName), 0, 60);

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setLastName($lastName)
    {
        $this->lastName = substr(str_replace(':', '', $lastName), 0, 60);

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getBuildingNumber()
    {
        return $this->buildingNumber;
    }

    /**
     * @param string $buildingNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setBuildingNumber($buildingNumber)
    {
        $this->buildingNumber = substr(str_replace(':', '', $buildingNumber), 0, 255);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setStreet1($street1)
    {
        $this->street1 = substr(str_replace(':', '', $street1), 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setStreet2($street2)
    {
        $this->street2 = substr(str_replace(':', '', $street2), 0, 60);

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet3()
    {
        return $this->street3;
    }

    /**
     * @param string $street3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setStreet3($street3)
    {
        $this->street3 = substr(str_replace(':', '', $street3), 0, 60);

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet4()
    {
        return $this->street4;
    }

    /**
     * @param string $street4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setStreet4($street4)
    {
        $this->street4 = substr(str_replace(':', '', $street4), 0, 60);

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet5()
    {
        return $this->street5;
    }

    /**
     * @param string $street5
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setStreet5($street5)
    {
        $this->street5 = $street5;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCity($city)
    {
        $this->city = substr(str_replace(':', '', $city), 0, 50);

        return $this;
    }

    /**
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param string $district
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @param string $county
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCounty($county)
    {
        $this->county = $county;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setState($state)
    {
        $this->state = strtoupper(substr(str_replace(':', '', $state), 0, 2));

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setPostalCode($postalCode)
    {
        if ($this->getCountry() === 'US') {
            $postalCode = preg_replace('/[^\d]/', '', $postalCode);
            if (strlen($postalCode) > 5) {
                $postalCode = substr($postalCode, 0, 5) . '-' . substr($postalCode, 5, 4);
            } elseif (strlen($postalCode) < 5) {
                $postalCode = str_pad($postalCode, 5, '0', STR_PAD_LEFT);
            }
        } elseif ($this->getCountry() === 'CA') {
            $postalCode = preg_replace('/[^a-zA-Z0-9]/', '', $postalCode);
            $postalCode = substr($postalCode, 0, 3) . ' ' . substr($postalCode, 3, 3);
        }

        $this->postalCode = substr(str_replace(':', '', $postalCode), 0, 10);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCountry($country)
    {
        $this->country = strtoupper(substr(str_replace(':', '', $country), 0, 2));

        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompany($company)
    {
        $this->company = substr(str_replace(':', '', $company), 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyTaxID($companyTaxID)
    {
        $this->companyTaxID = substr(str_replace(':', '', $companyTaxID), 0, 9);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = substr(str_replace(':', '', $phoneNumber), 0, 15);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setEmail($email)
    {
        $this->email = substr(str_replace(':', '', $email), 0, 255);

        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = substr(preg_replace('/[^\d.]/', '', $ipAddress), 0, 15);

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerUserName()
    {
        return $this->customerUserName;
    }

    /**
     * @param string $customerUserName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCustomerUserName($customerUserName)
    {
        $this->customerUserName = $customerUserName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerPassword()
    {
        return $this->customerPassword;
    }

    /**
     * @param string $customerPassword
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCustomerPassword($customerPassword)
    {
        $this->customerPassword = $customerPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getIpNetworkAddress()
    {
        return $this->ipNetworkAddress;
    }

    /**
     * @param string $ipNetworkAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setIpNetworkAddress($ipNetworkAddress)
    {
        $this->ipNetworkAddress = $ipNetworkAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHostname($hostname)
    {
        $this->hostname = substr(str_replace(':', '', $hostname), 0, 60);

        return $this;
    }

    /**
     * @return string
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * @param string $domainName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setDriversLicenseState($driversLicenseState)
    {
        $this->driversLicenseState = $driversLicenseState;

        return $this;
    }

    /**
     * @return string
     */
    public function getSsn()
    {
        return $this->ssn;
    }

    /**
     * @param string $ssn
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setSsn($ssn)
    {
        $this->ssn = $ssn;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = substr(str_replace(':', '', $customerID), 0, 30);

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpBrowserType()
    {
        return $this->httpBrowserType;
    }

    /**
     * @param string $httpBrowserType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserType($httpBrowserType)
    {
        $this->httpBrowserType = substr(str_replace(':', '', $httpBrowserType), 0, 40);

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpBrowserEmail()
    {
        return $this->httpBrowserEmail;
    }

    /**
     * @param string $httpBrowserEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserEmail($httpBrowserEmail)
    {
        $this->httpBrowserEmail = $httpBrowserEmail;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHttpBrowserCookiesAccepted()
    {
        return $this->httpBrowserCookiesAccepted;
    }

    /**
     * @param boolean $httpBrowserCookiesAccepted
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserCookiesAccepted($httpBrowserCookiesAccepted)
    {
        $this->httpBrowserCookiesAccepted = $httpBrowserCookiesAccepted;

        return $this;
    }

    /**
     * @return string
     */
    public function getNif()
    {
        return $this->nif;
    }

    /**
     * @param string $nif
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setNif($nif)
    {
        $this->nif = $nif;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalID()
    {
        return $this->personalID;
    }

    /**
     * @param string $personalID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setPersonalID($personalID)
    {
        $this->personalID = substr(str_replace(':', '', $personalID), 0, 26);

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantTaxID()
    {
        return $this->merchantTaxID;
    }

    /**
     * @param string $merchantTaxID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setMerchantTaxID($merchantTaxID)
    {
        $this->merchantTaxID = substr(str_replace(':', '', $merchantTaxID), 0, 15);

        return $this;
    }

    /**
     * @return string
     */
    public function getPassportNumber()
    {
        return $this->passportNumber;
    }

    /**
     * @param string $passportNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setPassportNumber($passportNumber)
    {
        $this->passportNumber = $passportNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassportCountry()
    {
        return $this->passportCountry;
    }

    /**
     * @param string $passportCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setPassportCountry($passportCountry)
    {
        $this->passportCountry = $passportCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerAccountCreateDate()
    {
        return $this->customerAccountCreateDate;
    }

    /**
     * @param string $customerAccountCreateDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCustomerAccountCreateDate($customerAccountCreateDate)
    {
        $this->customerAccountCreateDate = $customerAccountCreateDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerAccountChangeDate()
    {
        return $this->customerAccountChangeDate;
    }

    /**
     * @param string $customerAccountChangeDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCustomerAccountChangeDate($customerAccountChangeDate)
    {
        $this->customerAccountChangeDate = $customerAccountChangeDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerAccountPasswordChangeDate()
    {
        return $this->customerAccountPasswordChangeDate;
    }

    /**
     * @param string $customerAccountPasswordChangeDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCustomerAccountPasswordChangeDate($customerAccountPasswordChangeDate)
    {
        $this->customerAccountPasswordChangeDate = $customerAccountPasswordChangeDate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPointOfReference()
    {
        return $this->pointOfReference;
    }

    /**
     * @param boolean $pointOfReference
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setPointOfReference($pointOfReference)
    {
        $this->pointOfReference = $pointOfReference;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDefaultIndicator()
    {
        return $this->defaultIndicator;
    }

    /**
     * @param boolean $defaultIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setDefaultIndicator($defaultIndicator)
    {
        $this->defaultIndicator = $defaultIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyStreet1()
    {
        return $this->companyStreet1;
    }

    /**
     * @param string $companyStreet1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyStreet1($companyStreet1)
    {
        $this->companyStreet1 = $companyStreet1;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyStreet2()
    {
        return $this->companyStreet2;
    }

    /**
     * @param string $companyStreet2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyStreet2($companyStreet2)
    {
        $this->companyStreet2 = $companyStreet2;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyCity()
    {
        return $this->companyCity;
    }

    /**
     * @param string $companyCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyCity($companyCity)
    {
        $this->companyCity = $companyCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyCountry()
    {
        return $this->companyCountry;
    }

    /**
     * @param string $companyCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyCountry($companyCountry)
    {
        $this->companyCountry = $companyCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyState()
    {
        return $this->companyState;
    }

    /**
     * @param string $companyState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyState($companyState)
    {
        $this->companyState = $companyState;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyPostalCode()
    {
        return $this->companyPostalCode;
    }

    /**
     * @param string $companyPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyPostalCode($companyPostalCode)
    {
        $this->companyPostalCode = $companyPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyPhoneNumber()
    {
        return $this->companyPhoneNumber;
    }

    /**
     * @param string $companyPhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setCompanyPhoneNumber($companyPhoneNumber)
    {
        $this->companyPhoneNumber = $companyPhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpBrowserColorDepth()
    {
        return $this->httpBrowserColorDepth;
    }

    /**
     * @param string $httpBrowserColorDepth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserColorDepth($httpBrowserColorDepth)
    {
        $this->httpBrowserColorDepth = $httpBrowserColorDepth;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHttpBrowserJavaEnabled()
    {
        return $this->httpBrowserJavaEnabled;
    }

    /**
     * @param boolean $httpBrowserJavaEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserJavaEnabled($httpBrowserJavaEnabled)
    {
        $this->httpBrowserJavaEnabled = $httpBrowserJavaEnabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHttpBrowserJavaScriptEnabled()
    {
        return $this->httpBrowserJavaScriptEnabled;
    }

    /**
     * @param boolean $httpBrowserJavaScriptEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserJavaScriptEnabled($httpBrowserJavaScriptEnabled)
    {
        $this->httpBrowserJavaScriptEnabled = $httpBrowserJavaScriptEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpBrowserLanguage()
    {
        return $this->httpBrowserLanguage;
    }

    /**
     * @param string $httpBrowserLanguage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserLanguage($httpBrowserLanguage)
    {
        $this->httpBrowserLanguage = $httpBrowserLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpBrowserScreenHeight()
    {
        return $this->httpBrowserScreenHeight;
    }

    /**
     * @param string $httpBrowserScreenHeight
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserScreenHeight($httpBrowserScreenHeight)
    {
        $this->httpBrowserScreenHeight = $httpBrowserScreenHeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpBrowserScreenWidth()
    {
        return $this->httpBrowserScreenWidth;
    }

    /**
     * @param string $httpBrowserScreenWidth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserScreenWidth($httpBrowserScreenWidth)
    {
        $this->httpBrowserScreenWidth = $httpBrowserScreenWidth;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpBrowserTimeDifference()
    {
        return $this->httpBrowserTimeDifference;
    }

    /**
     * @param string $httpBrowserTimeDifference
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function setHttpBrowserTimeDifference($httpBrowserTimeDifference)
    {
        $this->httpBrowserTimeDifference = $httpBrowserTimeDifference;

        return $this;
    }
}
