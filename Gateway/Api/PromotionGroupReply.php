<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PromotionGroupReply
{
    /**
     * @var float $discountApplied
     */
    protected $discountApplied;

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
    public function getDiscountApplied()
    {
        return $this->discountApplied;
    }

    /**
     * @param float $discountApplied
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroupReply
     */
    public function setDiscountApplied($discountApplied)
    {
        $this->discountApplied = $discountApplied;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroupReply
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
