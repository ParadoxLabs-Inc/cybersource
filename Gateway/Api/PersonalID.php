<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PersonalID
{

    /**
     * @var string $number
     */
    protected $number = null;

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $country
     */
    protected $country = null;

    /**
     * @var string $address
     */
    protected $address = null;

    /**
     * @var string $issuedBy
     */
    protected $issuedBy = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PersonalID
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PersonalID
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PersonalID
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PersonalID
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PersonalID
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuedBy()
    {
        return $this->issuedBy;
    }

    /**
     * @param string $issuedBy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PersonalID
     */
    public function setIssuedBy($issuedBy)
    {
        $this->issuedBy = $issuedBy;

        return $this;
    }

}
