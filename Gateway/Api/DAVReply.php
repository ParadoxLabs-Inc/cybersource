<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DAVReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $addressType
     */
    protected $addressType;

    /**
     * @var string $apartmentInfo
     */
    protected $apartmentInfo;

    /**
     * @var string $barCode
     */
    protected $barCode;

    /**
     * @var string $barCodeCheckDigit
     */
    protected $barCodeCheckDigit;

    /**
     * @var string $careOf
     */
    protected $careOf;

    /**
     * @var string $cityInfo
     */
    protected $cityInfo;

    /**
     * @var string $countryInfo
     */
    protected $countryInfo;

    /**
     * @var string $directionalInfo
     */
    protected $directionalInfo;

    /**
     * @var string $lvrInfo
     */
    protected $lvrInfo;

    /**
     * @var int $matchScore
     */
    protected $matchScore;

    /**
     * @var string $standardizedAddress1
     */
    protected $standardizedAddress1;

    /**
     * @var string $standardizedAddress2
     */
    protected $standardizedAddress2;

    /**
     * @var string $standardizedAddress3
     */
    protected $standardizedAddress3;

    /**
     * @var string $standardizedAddress4
     */
    protected $standardizedAddress4;

    /**
     * @var string $standardizedAddressNoApt
     */
    protected $standardizedAddressNoApt;

    /**
     * @var string $standardizedCity
     */
    protected $standardizedCity;

    /**
     * @var string $standardizedCounty
     */
    protected $standardizedCounty;

    /**
     * @var string $standardizedCSP
     */
    protected $standardizedCSP;

    /**
     * @var string $standardizedState
     */
    protected $standardizedState;

    /**
     * @var string $standardizedPostalCode
     */
    protected $standardizedPostalCode;

    /**
     * @var string $standardizedCountry
     */
    protected $standardizedCountry;

    /**
     * @var string $standardizedISOCountry
     */
    protected $standardizedISOCountry;

    /**
     * @var string $stateInfo
     */
    protected $stateInfo;

    /**
     * @var string $streetInfo
     */
    protected $streetInfo;

    /**
     * @var string $suffixInfo
     */
    protected $suffixInfo;

    /**
     * @var string $postalCodeInfo
     */
    protected $postalCodeInfo;

    /**
     * @var string $overallInfo
     */
    protected $overallInfo;

    /**
     * @var string $usInfo
     */
    protected $usInfo;

    /**
     * @var string $caInfo
     */
    protected $caInfo;

    /**
     * @var string $intlInfo
     */
    protected $intlInfo;

    /**
     * @var string $usErrorInfo
     */
    protected $usErrorInfo;

    /**
     * @var string $caErrorInfo
     */
    protected $caErrorInfo;

    /**
     * @var string $intlErrorInfo
     */
    protected $intlErrorInfo;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressType()
    {
        return $this->addressType;
    }

    /**
     * @param string $addressType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setAddressType($addressType)
    {
        $this->addressType = $addressType;

        return $this;
    }

    /**
     * @return string
     */
    public function getApartmentInfo()
    {
        return $this->apartmentInfo;
    }

    /**
     * @param string $apartmentInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setApartmentInfo($apartmentInfo)
    {
        $this->apartmentInfo = $apartmentInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getBarCode()
    {
        return $this->barCode;
    }

    /**
     * @param string $barCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setBarCode($barCode)
    {
        $this->barCode = $barCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBarCodeCheckDigit()
    {
        return $this->barCodeCheckDigit;
    }

    /**
     * @param string $barCodeCheckDigit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setBarCodeCheckDigit($barCodeCheckDigit)
    {
        $this->barCodeCheckDigit = $barCodeCheckDigit;

        return $this;
    }

    /**
     * @return string
     */
    public function getCareOf()
    {
        return $this->careOf;
    }

    /**
     * @param string $careOf
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setCareOf($careOf)
    {
        $this->careOf = $careOf;

        return $this;
    }

    /**
     * @return string
     */
    public function getCityInfo()
    {
        return $this->cityInfo;
    }

    /**
     * @param string $cityInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setCityInfo($cityInfo)
    {
        $this->cityInfo = $cityInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryInfo()
    {
        return $this->countryInfo;
    }

    /**
     * @param string $countryInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setCountryInfo($countryInfo)
    {
        $this->countryInfo = $countryInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectionalInfo()
    {
        return $this->directionalInfo;
    }

    /**
     * @param string $directionalInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setDirectionalInfo($directionalInfo)
    {
        $this->directionalInfo = $directionalInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getLvrInfo()
    {
        return $this->lvrInfo;
    }

    /**
     * @param string $lvrInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setLvrInfo($lvrInfo)
    {
        $this->lvrInfo = $lvrInfo;

        return $this;
    }

    /**
     * @return int
     */
    public function getMatchScore()
    {
        return $this->matchScore;
    }

    /**
     * @param int $matchScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setMatchScore($matchScore)
    {
        $this->matchScore = $matchScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedAddress1()
    {
        return $this->standardizedAddress1;
    }

    /**
     * @param string $standardizedAddress1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedAddress1($standardizedAddress1)
    {
        $this->standardizedAddress1 = $standardizedAddress1;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedAddress2()
    {
        return $this->standardizedAddress2;
    }

    /**
     * @param string $standardizedAddress2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedAddress2($standardizedAddress2)
    {
        $this->standardizedAddress2 = $standardizedAddress2;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedAddress3()
    {
        return $this->standardizedAddress3;
    }

    /**
     * @param string $standardizedAddress3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedAddress3($standardizedAddress3)
    {
        $this->standardizedAddress3 = $standardizedAddress3;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedAddress4()
    {
        return $this->standardizedAddress4;
    }

    /**
     * @param string $standardizedAddress4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedAddress4($standardizedAddress4)
    {
        $this->standardizedAddress4 = $standardizedAddress4;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedAddressNoApt()
    {
        return $this->standardizedAddressNoApt;
    }

    /**
     * @param string $standardizedAddressNoApt
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedAddressNoApt($standardizedAddressNoApt)
    {
        $this->standardizedAddressNoApt = $standardizedAddressNoApt;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedCity()
    {
        return $this->standardizedCity;
    }

    /**
     * @param string $standardizedCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedCity($standardizedCity)
    {
        $this->standardizedCity = $standardizedCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedCounty()
    {
        return $this->standardizedCounty;
    }

    /**
     * @param string $standardizedCounty
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedCounty($standardizedCounty)
    {
        $this->standardizedCounty = $standardizedCounty;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedCSP()
    {
        return $this->standardizedCSP;
    }

    /**
     * @param string $standardizedCSP
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedCSP($standardizedCSP)
    {
        $this->standardizedCSP = $standardizedCSP;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedState()
    {
        return $this->standardizedState;
    }

    /**
     * @param string $standardizedState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedState($standardizedState)
    {
        $this->standardizedState = $standardizedState;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedPostalCode()
    {
        return $this->standardizedPostalCode;
    }

    /**
     * @param string $standardizedPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedPostalCode($standardizedPostalCode)
    {
        $this->standardizedPostalCode = $standardizedPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedCountry()
    {
        return $this->standardizedCountry;
    }

    /**
     * @param string $standardizedCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedCountry($standardizedCountry)
    {
        $this->standardizedCountry = $standardizedCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getStandardizedISOCountry()
    {
        return $this->standardizedISOCountry;
    }

    /**
     * @param string $standardizedISOCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStandardizedISOCountry($standardizedISOCountry)
    {
        $this->standardizedISOCountry = $standardizedISOCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getStateInfo()
    {
        return $this->stateInfo;
    }

    /**
     * @param string $stateInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStateInfo($stateInfo)
    {
        $this->stateInfo = $stateInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreetInfo()
    {
        return $this->streetInfo;
    }

    /**
     * @param string $streetInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setStreetInfo($streetInfo)
    {
        $this->streetInfo = $streetInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffixInfo()
    {
        return $this->suffixInfo;
    }

    /**
     * @param string $suffixInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setSuffixInfo($suffixInfo)
    {
        $this->suffixInfo = $suffixInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCodeInfo()
    {
        return $this->postalCodeInfo;
    }

    /**
     * @param string $postalCodeInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setPostalCodeInfo($postalCodeInfo)
    {
        $this->postalCodeInfo = $postalCodeInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getOverallInfo()
    {
        return $this->overallInfo;
    }

    /**
     * @param string $overallInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setOverallInfo($overallInfo)
    {
        $this->overallInfo = $overallInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsInfo()
    {
        return $this->usInfo;
    }

    /**
     * @param string $usInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setUsInfo($usInfo)
    {
        $this->usInfo = $usInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaInfo()
    {
        return $this->caInfo;
    }

    /**
     * @param string $caInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setCaInfo($caInfo)
    {
        $this->caInfo = $caInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntlInfo()
    {
        return $this->intlInfo;
    }

    /**
     * @param string $intlInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setIntlInfo($intlInfo)
    {
        $this->intlInfo = $intlInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsErrorInfo()
    {
        return $this->usErrorInfo;
    }

    /**
     * @param string $usErrorInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setUsErrorInfo($usErrorInfo)
    {
        $this->usErrorInfo = $usErrorInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaErrorInfo()
    {
        return $this->caErrorInfo;
    }

    /**
     * @param string $caErrorInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setCaErrorInfo($caErrorInfo)
    {
        $this->caErrorInfo = $caErrorInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntlErrorInfo()
    {
        return $this->intlErrorInfo;
    }

    /**
     * @param string $intlErrorInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DAVReply
     */
    public function setIntlErrorInfo($intlErrorInfo)
    {
        $this->intlErrorInfo = $intlErrorInfo;

        return $this;
    }
}
