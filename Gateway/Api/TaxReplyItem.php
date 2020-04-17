<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TaxReplyItem
{
    /**
     * @var float $taxableAmount
     */
    protected $taxableAmount;

    /**
     * @var float $exemptAmount
     */
    protected $exemptAmount;

    /**
     * @var float $specialTaxAmount
     */
    protected $specialTaxAmount;

    /**
     * @var float $cityTaxAmount
     */
    protected $cityTaxAmount;

    /**
     * @var float $countyTaxAmount
     */
    protected $countyTaxAmount;

    /**
     * @var float $districtTaxAmount
     */
    protected $districtTaxAmount;

    /**
     * @var float $stateTaxAmount
     */
    protected $stateTaxAmount;

    /**
     * @var float $countryTaxAmount
     */
    protected $countryTaxAmount;

    /**
     * @var float $totalTaxAmount
     */
    protected $totalTaxAmount;

    /**
     * @var TaxReplyItemJurisdiction[] $jurisdiction
     */
    protected $jurisdiction;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param float $totalTaxAmount
     * @param int $id
     */
    public function __construct($totalTaxAmount, $id)
    {
        $this->totalTaxAmount = $totalTaxAmount;
        $this->id             = $id;
    }

    /**
     * @return float
     */
    public function getTaxableAmount()
    {
        return $this->taxableAmount;
    }

    /**
     * @param float $taxableAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setTaxableAmount($taxableAmount)
    {
        $this->taxableAmount = $taxableAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getExemptAmount()
    {
        return $this->exemptAmount;
    }

    /**
     * @param float $exemptAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setExemptAmount($exemptAmount)
    {
        $this->exemptAmount = $exemptAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getSpecialTaxAmount()
    {
        return $this->specialTaxAmount;
    }

    /**
     * @param float $specialTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setSpecialTaxAmount($specialTaxAmount)
    {
        $this->specialTaxAmount = $specialTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCityTaxAmount()
    {
        return $this->cityTaxAmount;
    }

    /**
     * @param float $cityTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setCityTaxAmount($cityTaxAmount)
    {
        $this->cityTaxAmount = $cityTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCountyTaxAmount()
    {
        return $this->countyTaxAmount;
    }

    /**
     * @param float $countyTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setCountyTaxAmount($countyTaxAmount)
    {
        $this->countyTaxAmount = $countyTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getDistrictTaxAmount()
    {
        return $this->districtTaxAmount;
    }

    /**
     * @param float $districtTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setDistrictTaxAmount($districtTaxAmount)
    {
        $this->districtTaxAmount = $districtTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getStateTaxAmount()
    {
        return $this->stateTaxAmount;
    }

    /**
     * @param float $stateTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setStateTaxAmount($stateTaxAmount)
    {
        $this->stateTaxAmount = $stateTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCountryTaxAmount()
    {
        return $this->countryTaxAmount;
    }

    /**
     * @param float $countryTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setCountryTaxAmount($countryTaxAmount)
    {
        $this->countryTaxAmount = $countryTaxAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setTotalTaxAmount($totalTaxAmount)
    {
        $this->totalTaxAmount = $totalTaxAmount;

        return $this;
    }

    /**
     * @return TaxReplyItemJurisdiction[]
     */
    public function getJurisdiction()
    {
        return $this->jurisdiction;
    }

    /**
     * @param TaxReplyItemJurisdiction[] $jurisdiction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setJurisdiction(array $jurisdiction = null)
    {
        $this->jurisdiction = $jurisdiction;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
