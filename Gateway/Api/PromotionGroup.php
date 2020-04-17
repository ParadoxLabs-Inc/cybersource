<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PromotionGroup
{
    /**
     * @var float $subtotalAmount
     */
    protected $subtotalAmount;

    /**
     * @var float $taxRate
     */
    protected $taxRate;

    /**
     * @var string $prohibitDiscount
     */
    protected $prohibitDiscount;

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
    public function getSubtotalAmount()
    {
        return $this->subtotalAmount;
    }

    /**
     * @param float $subtotalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroup
     */
    public function setSubtotalAmount($subtotalAmount)
    {
        $this->subtotalAmount = $subtotalAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param float $taxRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroup
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getProhibitDiscount()
    {
        return $this->prohibitDiscount;
    }

    /**
     * @param string $prohibitDiscount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroup
     */
    public function setProhibitDiscount($prohibitDiscount)
    {
        $this->prohibitDiscount = $prohibitDiscount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroup
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
