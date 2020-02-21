<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TaxReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var string $currency
     */
    protected $currency = null;

    /**
     * @var float $grandTotalAmount
     */
    protected $grandTotalAmount = null;

    /**
     * @var float $totalTaxableAmount
     */
    protected $totalTaxableAmount = null;

    /**
     * @var float $totalExemptAmount
     */
    protected $totalExemptAmount = null;

    /**
     * @var float $totalSpecialTaxAmount
     */
    protected $totalSpecialTaxAmount = null;

    /**
     * @var float $totalCityTaxAmount
     */
    protected $totalCityTaxAmount = null;

    /**
     * @var string $city
     */
    protected $city = null;

    /**
     * @var float $totalCountyTaxAmount
     */
    protected $totalCountyTaxAmount = null;

    /**
     * @var string $county
     */
    protected $county = null;

    /**
     * @var float $totalDistrictTaxAmount
     */
    protected $totalDistrictTaxAmount = null;

    /**
     * @var float $totalStateTaxAmount
     */
    protected $totalStateTaxAmount = null;

    /**
     * @var string $state
     */
    protected $state = null;

    /**
     * @var float $totalCountryTaxAmount
     */
    protected $totalCountryTaxAmount = null;

    /**
     * @var float $totalTaxAmount
     */
    protected $totalTaxAmount = null;

    /**
     * @var string $commitIndicator
     */
    protected $commitIndicator = null;

    /**
     * @var string $refundIndicator
     */
    protected $refundIndicator = null;

    /**
     * @var string $postalCode
     */
    protected $postalCode = null;

    /**
     * @var string $geocode
     */
    protected $geocode = null;

    /**
     * @var TaxReplyItem[] $item
     */
    protected $item = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return float
     */
    public function getGrandTotalAmount()
    {
        return $this->grandTotalAmount;
    }

    /**
     * @param float $grandTotalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setGrandTotalAmount($grandTotalAmount)
    {
        $this->grandTotalAmount = $grandTotalAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalTaxableAmount()
    {
        return $this->totalTaxableAmount;
    }

    /**
     * @param float $totalTaxableAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalTaxableAmount($totalTaxableAmount)
    {
        $this->totalTaxableAmount = $totalTaxableAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalExemptAmount()
    {
        return $this->totalExemptAmount;
    }

    /**
     * @param float $totalExemptAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalExemptAmount($totalExemptAmount)
    {
        $this->totalExemptAmount = $totalExemptAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalSpecialTaxAmount()
    {
        return $this->totalSpecialTaxAmount;
    }

    /**
     * @param float $totalSpecialTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalSpecialTaxAmount($totalSpecialTaxAmount)
    {
        $this->totalSpecialTaxAmount = $totalSpecialTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalCityTaxAmount()
    {
        return $this->totalCityTaxAmount;
    }

    /**
     * @param float $totalCityTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalCityTaxAmount($totalCityTaxAmount)
    {
        $this->totalCityTaxAmount = $totalCityTaxAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalCountyTaxAmount()
    {
        return $this->totalCountyTaxAmount;
    }

    /**
     * @param float $totalCountyTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalCountyTaxAmount($totalCountyTaxAmount)
    {
        $this->totalCountyTaxAmount = $totalCountyTaxAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setCounty($county)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalDistrictTaxAmount()
    {
        return $this->totalDistrictTaxAmount;
    }

    /**
     * @param float $totalDistrictTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalDistrictTaxAmount($totalDistrictTaxAmount)
    {
        $this->totalDistrictTaxAmount = $totalDistrictTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalStateTaxAmount()
    {
        return $this->totalStateTaxAmount;
    }

    /**
     * @param float $totalStateTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalStateTaxAmount($totalStateTaxAmount)
    {
        $this->totalStateTaxAmount = $totalStateTaxAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalCountryTaxAmount()
    {
        return $this->totalCountryTaxAmount;
    }

    /**
     * @param float $totalCountryTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalCountryTaxAmount($totalCountryTaxAmount)
    {
        $this->totalCountryTaxAmount = $totalCountryTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalTaxAmount()
    {
        return $this->totalTaxAmount;
    }

    /**
     * @param float $totalTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setTotalTaxAmount($totalTaxAmount)
    {
        $this->totalTaxAmount = $totalTaxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommitIndicator()
    {
        return $this->commitIndicator;
    }

    /**
     * @param string $commitIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setCommitIndicator($commitIndicator)
    {
        $this->commitIndicator = $commitIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefundIndicator()
    {
        return $this->refundIndicator;
    }

    /**
     * @param string $refundIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setRefundIndicator($refundIndicator)
    {
        $this->refundIndicator = $refundIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getGeocode()
    {
        return $this->geocode;
    }

    /**
     * @param string $geocode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setGeocode($geocode)
    {
        $this->geocode = $geocode;

        return $this;
    }

    /**
     * @return TaxReplyItem[]
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param TaxReplyItem[] $item
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReply
     */
    public function setItem(array $item = null)
    {
        $this->item = $item;

        return $this;
    }

}
