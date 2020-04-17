<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ShipFrom
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
     * @var string $city
     */
    protected $city;

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
     * @var string $phoneNumber
     */
    protected $phoneNumber;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setStreet3($street3)
    {
        $this->street3 = $street3;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setStreet4($street4)
    {
        $this->street4 = $street4;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setCity($city)
    {
        $this->city = $city;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setState($state)
    {
        $this->state = $state;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setCompany($company)
    {
        $this->company = $company;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}
