<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BalanceInfo
{
    /**
     * @var string $accountType
     */
    protected $accountType;

    /**
     * @var string $amount
     */
    protected $amount;

    /**
     * @var string $amountType
     */
    protected $amountType;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $sign
     */
    protected $sign;

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
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BalanceInfo
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BalanceInfo
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * @param string $amountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BalanceInfo
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BalanceInfo
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BalanceInfo
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BalanceInfo
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
