<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalEcGetDetailsReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $paypalToken
     */
    protected $paypalToken;

    /**
     * @var string $payer
     */
    protected $payer;

    /**
     * @var string $payerId
     */
    protected $payerId;

    /**
     * @var string $payerStatus
     */
    protected $payerStatus;

    /**
     * @var string $payerSalutation
     */
    protected $payerSalutation;

    /**
     * @var string $payerFirstname
     */
    protected $payerFirstname;

    /**
     * @var string $payerMiddlename
     */
    protected $payerMiddlename;

    /**
     * @var string $payerLastname
     */
    protected $payerLastname;

    /**
     * @var string $payerSuffix
     */
    protected $payerSuffix;

    /**
     * @var string $payerCountry
     */
    protected $payerCountry;

    /**
     * @var string $payerBusiness
     */
    protected $payerBusiness;

    /**
     * @var string $shipToName
     */
    protected $shipToName;

    /**
     * @var string $shipToAddress1
     */
    protected $shipToAddress1;

    /**
     * @var string $shipToAddress2
     */
    protected $shipToAddress2;

    /**
     * @var string $shipToCity
     */
    protected $shipToCity;

    /**
     * @var string $shipToState
     */
    protected $shipToState;

    /**
     * @var string $shipToCountry
     */
    protected $shipToCountry;

    /**
     * @var string $shipToZip
     */
    protected $shipToZip;

    /**
     * @var string $addressStatus
     */
    protected $addressStatus;

    /**
     * @var string $payerPhone
     */
    protected $payerPhone;

    /**
     * @var string $avsCode
     */
    protected $avsCode;

    /**
     * @var string $street1
     */
    protected $street1;

    /**
     * @var string $street2
     */
    protected $street2;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $state
     */
    protected $state;

    /**
     * @var string $postalCode
     */
    protected $postalCode;

    /**
     * @var string $countryCode
     */
    protected $countryCode;

    /**
     * @var string $countryName
     */
    protected $countryName;

    /**
     * @var string $addressID
     */
    protected $addressID;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

    /**
     * @var string $correlationID
     */
    protected $correlationID;

    /**
     * @var string $paypalBillingAgreementAcceptedStatus
     */
    protected $paypalBillingAgreementAcceptedStatus;

    /**
     * @var string $paypalTaxAmount
     */
    protected $paypalTaxAmount;

    /**
     * @var Item[] $item
     */
    protected $item;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalToken()
    {
        return $this->paypalToken;
    }

    /**
     * @param string $paypalToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPaypalToken($paypalToken)
    {
        $this->paypalToken = $paypalToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param string $payer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerId()
    {
        return $this->payerId;
    }

    /**
     * @param string $payerId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerId($payerId)
    {
        $this->payerId = $payerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerStatus()
    {
        return $this->payerStatus;
    }

    /**
     * @param string $payerStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerStatus($payerStatus)
    {
        $this->payerStatus = $payerStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerSalutation()
    {
        return $this->payerSalutation;
    }

    /**
     * @param string $payerSalutation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerSalutation($payerSalutation)
    {
        $this->payerSalutation = $payerSalutation;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerFirstname()
    {
        return $this->payerFirstname;
    }

    /**
     * @param string $payerFirstname
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerFirstname($payerFirstname)
    {
        $this->payerFirstname = $payerFirstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerMiddlename()
    {
        return $this->payerMiddlename;
    }

    /**
     * @param string $payerMiddlename
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerMiddlename($payerMiddlename)
    {
        $this->payerMiddlename = $payerMiddlename;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerLastname()
    {
        return $this->payerLastname;
    }

    /**
     * @param string $payerLastname
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerLastname($payerLastname)
    {
        $this->payerLastname = $payerLastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerSuffix()
    {
        return $this->payerSuffix;
    }

    /**
     * @param string $payerSuffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerSuffix($payerSuffix)
    {
        $this->payerSuffix = $payerSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerCountry()
    {
        return $this->payerCountry;
    }

    /**
     * @param string $payerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerCountry($payerCountry)
    {
        $this->payerCountry = $payerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerBusiness()
    {
        return $this->payerBusiness;
    }

    /**
     * @param string $payerBusiness
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerBusiness($payerBusiness)
    {
        $this->payerBusiness = $payerBusiness;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToName()
    {
        return $this->shipToName;
    }

    /**
     * @param string $shipToName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setShipToName($shipToName)
    {
        $this->shipToName = $shipToName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToAddress1()
    {
        return $this->shipToAddress1;
    }

    /**
     * @param string $shipToAddress1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setShipToAddress1($shipToAddress1)
    {
        $this->shipToAddress1 = $shipToAddress1;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToAddress2()
    {
        return $this->shipToAddress2;
    }

    /**
     * @param string $shipToAddress2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setShipToAddress2($shipToAddress2)
    {
        $this->shipToAddress2 = $shipToAddress2;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setShipToState($shipToState)
    {
        $this->shipToState = $shipToState;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setShipToCountry($shipToCountry)
    {
        $this->shipToCountry = $shipToCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipToZip()
    {
        return $this->shipToZip;
    }

    /**
     * @param string $shipToZip
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setShipToZip($shipToZip)
    {
        $this->shipToZip = $shipToZip;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressStatus()
    {
        return $this->addressStatus;
    }

    /**
     * @param string $addressStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setAddressStatus($addressStatus)
    {
        $this->addressStatus = $addressStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerPhone()
    {
        return $this->payerPhone;
    }

    /**
     * @param string $payerPhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPayerPhone($payerPhone)
    {
        $this->payerPhone = $payerPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCode()
    {
        return $this->avsCode;
    }

    /**
     * @param string $avsCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setAvsCode($avsCode)
    {
        $this->avsCode = $avsCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setStreet2($street2)
    {
        $this->street2 = $street2;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressID()
    {
        return $this->addressID;
    }

    /**
     * @param string $addressID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setAddressID($addressID)
    {
        $this->addressID = $addressID;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrelationID()
    {
        return $this->correlationID;
    }

    /**
     * @param string $correlationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setCorrelationID($correlationID)
    {
        $this->correlationID = $correlationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementAcceptedStatus()
    {
        return $this->paypalBillingAgreementAcceptedStatus;
    }

    /**
     * @param string $paypalBillingAgreementAcceptedStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPaypalBillingAgreementAcceptedStatus($paypalBillingAgreementAcceptedStatus)
    {
        $this->paypalBillingAgreementAcceptedStatus = $paypalBillingAgreementAcceptedStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalTaxAmount()
    {
        return $this->paypalTaxAmount;
    }

    /**
     * @param string $paypalTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setPaypalTaxAmount($paypalTaxAmount)
    {
        $this->paypalTaxAmount = $paypalTaxAmount;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item[] $item
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply
     */
    public function setItem(array $item = null)
    {
        $this->item = $item;

        return $this;
    }
}
