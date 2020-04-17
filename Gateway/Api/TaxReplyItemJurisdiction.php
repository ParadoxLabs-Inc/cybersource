<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TaxReplyItemJurisdiction
{
    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $region
     */
    protected $region;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var float $taxable
     */
    protected $taxable;

    /**
     * @var float $rate
     */
    protected $rate;

    /**
     * @var float $taxAmount
     */
    protected $taxAmount;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $taxName
     */
    protected $taxName;

    /**
     * @var int $jurisId
     */
    protected $jurisId;

    /**
     * @param string $name
     * @param string $taxName
     * @param int $jurisId
     */
    public function __construct($name, $taxName, $jurisId)
    {
        $this->name    = $name;
        $this->taxName = $taxName;
        $this->jurisId = $jurisId;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setRegion($region)
    {
        $this->region = $region;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxable()
    {
        return $this->taxable;
    }

    /**
     * @param float $taxable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setTaxable($taxable)
    {
        $this->taxable = $taxable;

        return $this;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param float $taxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxName()
    {
        return $this->taxName;
    }

    /**
     * @param string $taxName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setTaxName($taxName)
    {
        $this->taxName = $taxName;

        return $this;
    }

    /**
     * @return int
     */
    public function getJurisId()
    {
        return $this->jurisId;
    }

    /**
     * @param int $jurisId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction
     */
    public function setJurisId($jurisId)
    {
        $this->jurisId = $jurisId;

        return $this;
    }
}
