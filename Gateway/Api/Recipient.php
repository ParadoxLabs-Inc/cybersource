<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Recipient
{
    /**
     * @var string $dateOfBirth
     */
    protected $dateOfBirth;

    /**
     * @var string $postalCode
     */
    protected $postalCode;

    /**
     * @var string $accountID
     */
    protected $accountID;

    /**
     * @var string $accountType
     */
    protected $accountType;

    /**
     * @var string $lastName
     */
    protected $lastName;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var float $billingAmount
     */
    protected $billingAmount;

    /**
     * @var string $billingCurrency
     */
    protected $billingCurrency;

    /**
     * @var float $billingConversionRate
     */
    protected $billingConversionRate;

    /**
     * @var string $firstName
     */
    protected $firstName;

    /**
     * @var string $middleName
     */
    protected $middleName;

    /**
     * @var string $middleInitial
     */
    protected $middleInitial;

    /**
     * @var string $address
     */
    protected $address;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $state
     */
    protected $state;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $phoneNumber
     */
    protected $phoneNumber;

    /**
     * @var string $buildingNumber
     */
    protected $buildingNumber;

    /**
     * @var string $address2
     */
    protected $address2;

    /**
     * @var string $streetName
     */
    protected $streetName;

    /**
     * @var string $aliasName
     */
    protected $aliasName;

    /**
     * @var string $nationality
     */
    protected $nationality;

    /**
     * @var string $countryOfBirth
     */
    protected $countryOfBirth;

    /**
     * @var string $occupation
     */
    protected $occupation;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $dateOfBirth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * @param string $accountID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setAccountID($accountID)
    {
        $this->accountID = $accountID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float
     */
    public function getBillingAmount()
    {
        return $this->billingAmount;
    }

    /**
     * @param float $billingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setBillingAmount($billingAmount)
    {
        $this->billingAmount = $billingAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingCurrency()
    {
        return $this->billingCurrency;
    }

    /**
     * @param string $billingCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setBillingCurrency($billingCurrency)
    {
        $this->billingCurrency = $billingCurrency;

        return $this;
    }

    /**
     * @return float
     */
    public function getBillingConversionRate()
    {
        return $this->billingConversionRate;
    }

    /**
     * @param float $billingConversionRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setBillingConversionRate($billingConversionRate)
    {
        $this->billingConversionRate = $billingConversionRate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleInitial()
    {
        return $this->middleInitial;
    }

    /**
     * @param string $middleInitial
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setMiddleInitial($middleInitial)
    {
        $this->middleInitial = $middleInitial;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setAddress($address)
    {
        $this->address = $address;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setCity($city)
    {
        $this->city = $city;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setState($state)
    {
        $this->state = $state;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setBuildingNumber($buildingNumber)
    {
        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAliasName()
    {
        return $this->aliasName;
    }

    /**
     * @param string $aliasName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setAliasName($aliasName)
    {
        $this->aliasName = $aliasName;

        return $this;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryOfBirth()
    {
        return $this->countryOfBirth;
    }

    /**
     * @param string $countryOfBirth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setCountryOfBirth($countryOfBirth)
    {
        $this->countryOfBirth = $countryOfBirth;

        return $this;
    }

    /**
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param string $occupation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recipient
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}
