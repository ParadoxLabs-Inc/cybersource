<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class OtherTax
{
    /**
     * @var float $vatTaxAmount
     */
    protected $vatTaxAmount;

    /**
     * @var float $vatTaxRate
     */
    protected $vatTaxRate;

    /**
     * @var string $vatTaxAmountSign
     */
    protected $vatTaxAmountSign;

    /**
     * @var float $alternateTaxAmount
     */
    protected $alternateTaxAmount;

    /**
     * @var string $alternateTaxIndicator
     */
    protected $alternateTaxIndicator;

    /**
     * @var string $alternateTaxID
     */
    protected $alternateTaxID;

    /**
     * @var float $localTaxAmount
     */
    protected $localTaxAmount;

    /**
     * @var int $localTaxIndicator
     */
    protected $localTaxIndicator;

    /**
     * @var float $nationalTaxAmount
     */
    protected $nationalTaxAmount;

    /**
     * @var int $nationalTaxIndicator
     */
    protected $nationalTaxIndicator;

    /**
     * @return float
     */
    public function getVatTaxAmount()
    {
        return $this->vatTaxAmount;
    }

    /**
     * @param float $vatTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setVatTaxAmount($vatTaxAmount)
    {
        $this->vatTaxAmount = $vatTaxAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getVatTaxRate()
    {
        return $this->vatTaxRate;
    }

    /**
     * @param float $vatTaxRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setVatTaxRate($vatTaxRate)
    {
        $this->vatTaxRate = $vatTaxRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getVatTaxAmountSign()
    {
        return $this->vatTaxAmountSign;
    }

    /**
     * @param string $vatTaxAmountSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setVatTaxAmountSign($vatTaxAmountSign)
    {
        $this->vatTaxAmountSign = $vatTaxAmountSign;

        return $this;
    }

    /**
     * @return float
     */
    public function getAlternateTaxAmount()
    {
        return $this->alternateTaxAmount;
    }

    /**
     * @param float $alternateTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setAlternateTaxAmount($alternateTaxAmount)
    {
        $this->alternateTaxAmount = $alternateTaxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateTaxIndicator()
    {
        return $this->alternateTaxIndicator;
    }

    /**
     * @param string $alternateTaxIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setAlternateTaxIndicator($alternateTaxIndicator)
    {
        $this->alternateTaxIndicator = $alternateTaxIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateTaxID()
    {
        return $this->alternateTaxID;
    }

    /**
     * @param string $alternateTaxID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setAlternateTaxID($alternateTaxID)
    {
        $this->alternateTaxID = $alternateTaxID;

        return $this;
    }

    /**
     * @return float
     */
    public function getLocalTaxAmount()
    {
        return $this->localTaxAmount;
    }

    /**
     * @param float $localTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setLocalTaxAmount($localTaxAmount)
    {
        $this->localTaxAmount = $localTaxAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getLocalTaxIndicator()
    {
        return $this->localTaxIndicator;
    }

    /**
     * @param int $localTaxIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setLocalTaxIndicator($localTaxIndicator)
    {
        $this->localTaxIndicator = $localTaxIndicator;

        return $this;
    }

    /**
     * @return float
     */
    public function getNationalTaxAmount()
    {
        return $this->nationalTaxAmount;
    }

    /**
     * @param float $nationalTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setNationalTaxAmount($nationalTaxAmount)
    {
        $this->nationalTaxAmount = $nationalTaxAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getNationalTaxIndicator()
    {
        return $this->nationalTaxIndicator;
    }

    /**
     * @param int $nationalTaxIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OtherTax
     */
    public function setNationalTaxIndicator($nationalTaxIndicator)
    {
        $this->nationalTaxIndicator = $nationalTaxIndicator;

        return $this;
    }
}
