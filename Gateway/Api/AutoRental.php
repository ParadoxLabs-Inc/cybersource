<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AutoRental
{
    /**
     * @var Promotion $promotion
     */
    protected $promotion;

    /**
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param Promotion $promotion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRental
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }
}
