<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ShipTo
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
     * @var string $street5
     */
    protected $street5;

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
     * @var string $buildingNumber
     */
    protected $buildingNumber;

    /**
     * @var string $district
     */
    protected $district;

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
     * @var string $shippingMethod
     */
    protected $shippingMethod;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var string $addressVerificationStatus
     */
    protected $addressVerificationStatus;

    /**
     * @var boolean $notApplicable
     */
    protected $notApplicable;

    /**
     * @var boolean $immutable
     */
    protected $immutable;

    /**
     * @var string $destinationCode
     */
    protected $destinationCode;

    /**
     * @var boolean $pointOfReference
     */
    protected $pointOfReference;

    /**
     * @var boolean $default
     */
    protected $default;

    /**
     * @var string $destinationTypes
     */
    protected $destinationTypes;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setFirstName($firstName)
    {
        $this->firstName = substr($firstName, 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setLastName($lastName)
    {
        $this->lastName = substr($lastName, 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setStreet1($street1)
    {
        $this->street1 = substr($street1, 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setStreet2($street2)
    {
        $this->street2 = substr($street2, 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setStreet3($street3)
    {
        $this->street3 = substr($street3, 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setStreet4($street4)
    {
        $this->street4 = substr($street4, 0, 60);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setCity($city)
    {
        $this->city = substr($city, 0, 50);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setCounty($county)
    {
        $this->county = strtoupper(substr($county, 0, 2));

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setState($state)
    {
        $this->state = strtoupper(substr($state, 0, 2));

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setBuildingNumber($buildingNumber)
    {
        $this->buildingNumber = substr($buildingNumber, 0, 15);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setDistrict($district)
    {
        $this->district = $district;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
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

        $this->postalCode = substr($postalCode, 0, 10);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = substr($phoneNumber, 0, 15);

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param string $shippingMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressVerificationStatus()
    {
        return $this->addressVerificationStatus;
    }

    /**
     * @param string $addressVerificationStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setAddressVerificationStatus($addressVerificationStatus)
    {
        $this->addressVerificationStatus = $addressVerificationStatus;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getNotApplicable()
    {
        return $this->notApplicable;
    }

    /**
     * @param boolean $notApplicable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setNotApplicable($notApplicable)
    {
        $this->notApplicable = $notApplicable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getImmutable()
    {
        return $this->immutable;
    }

    /**
     * @param boolean $immutable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setImmutable($immutable)
    {
        $this->immutable = $immutable;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationCode()
    {
        return $this->destinationCode;
    }

    /**
     * @param string $destinationCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setDestinationCode($destinationCode)
    {
        $this->destinationCode = $destinationCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setPointOfReference($pointOfReference)
    {
        $this->pointOfReference = $pointOfReference;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param boolean $default
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationTypes()
    {
        return $this->destinationTypes;
    }

    /**
     * @param string $destinationTypes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function setDestinationTypes($destinationTypes)
    {
        $this->destinationTypes = $destinationTypes;

        return $this;
    }
}
