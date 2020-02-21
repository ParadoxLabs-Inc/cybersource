<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class issuer
{

    /**
     * @var string $additionalData
     */
    protected $additionalData = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $country
     */
    protected $country = null;

    /**
     * @var string $countryNumericCode
     */
    protected $countryNumericCode = null;

    /**
     * @var string $phoneNumber
     */
    protected $phoneNumber = null;

    /**
     * @var string $responseCode
     */
    protected $responseCode = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @param string $additionalData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryNumericCode()
    {
        return $this->countryNumericCode;
    }

    /**
     * @param string $countryNumericCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setCountryNumericCode($countryNumericCode)
    {
        $this->countryNumericCode = $countryNumericCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

}
