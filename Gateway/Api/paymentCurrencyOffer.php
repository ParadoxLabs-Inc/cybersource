<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class paymentCurrencyOffer
{
    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $exchangeRate
     */
    protected $exchangeRate;

    /**
     * @var string $marginRatePercentage
     */
    protected $marginRatePercentage;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\paymentCurrencyOffer
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\paymentCurrencyOffer
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param string $exchangeRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\paymentCurrencyOffer
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarginRatePercentage()
    {
        return $this->marginRatePercentage;
    }

    /**
     * @param string $marginRatePercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\paymentCurrencyOffer
     */
    public function setMarginRatePercentage($marginRatePercentage)
    {
        $this->marginRatePercentage = $marginRatePercentage;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\paymentCurrencyOffer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
