<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class HealthCare
{
    /**
     * @var string $amountType
     */
    protected $amountType;

    /**
     * @var float $amount
     */
    protected $amount;

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
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * @param string $amountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HealthCare
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HealthCare
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HealthCare
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
