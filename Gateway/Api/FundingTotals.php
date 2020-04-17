<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class FundingTotals
{
    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var float $grandTotalAmount
     */
    protected $grandTotalAmount;

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FundingTotals
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FundingTotals
     */
    public function setGrandTotalAmount($grandTotalAmount)
    {
        $this->grandTotalAmount = $grandTotalAmount;

        return $this;
    }
}
