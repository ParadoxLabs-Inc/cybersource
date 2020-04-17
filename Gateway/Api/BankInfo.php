<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BankInfo
{
    /**
     * @var string $bankCode
     */
    protected $bankCode;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $address
     */
    protected $address;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $branchCode
     */
    protected $branchCode;

    /**
     * @var string $swiftCode
     */
    protected $swiftCode;

    /**
     * @var string $sortCode
     */
    protected $sortCode;

    /**
     * @var string $issuerID
     */
    protected $issuerID;

    /**
     * @return string
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * @param string $bankCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setCity($city)
    {
        $this->city = $city;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setCountry($country)
    {
        $this->country = $country;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setBranchCode($branchCode)
    {
        $this->branchCode = $branchCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSwiftCode()
    {
        return $this->swiftCode;
    }

    /**
     * @param string $swiftCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setSwiftCode($swiftCode)
    {
        $this->swiftCode = $swiftCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSortCode()
    {
        return $this->sortCode;
    }

    /**
     * @param string $sortCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setSortCode($sortCode)
    {
        $this->sortCode = $sortCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuerID()
    {
        return $this->issuerID;
    }

    /**
     * @param string $issuerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankInfo
     */
    public function setIssuerID($issuerID)
    {
        $this->issuerID = $issuerID;

        return $this;
    }
}
