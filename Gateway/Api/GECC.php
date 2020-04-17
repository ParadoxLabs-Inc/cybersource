<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class GECC
{
    /**
     * @var string $saleType
     */
    protected $saleType;

    /**
     * @var string $planNumber
     */
    protected $planNumber;

    /**
     * @var string $sequenceNumber
     */
    protected $sequenceNumber;

    /**
     * @var string $promotionEndDate
     */
    protected $promotionEndDate;

    /**
     * @var string $promotionPlan
     */
    protected $promotionPlan;

    /**
     * @var string[] $line
     */
    protected $line;

    /**
     * @return string
     */
    public function getSaleType()
    {
        return $this->saleType;
    }

    /**
     * @param string $saleType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GECC
     */
    public function setSaleType($saleType)
    {
        $this->saleType = $saleType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlanNumber()
    {
        return $this->planNumber;
    }

    /**
     * @param string $planNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GECC
     */
    public function setPlanNumber($planNumber)
    {
        $this->planNumber = $planNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @param string $sequenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GECC
     */
    public function setSequenceNumber($sequenceNumber)
    {
        $this->sequenceNumber = $sequenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromotionEndDate()
    {
        return $this->promotionEndDate;
    }

    /**
     * @param string $promotionEndDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GECC
     */
    public function setPromotionEndDate($promotionEndDate)
    {
        $this->promotionEndDate = $promotionEndDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromotionPlan()
    {
        return $this->promotionPlan;
    }

    /**
     * @param string $promotionPlan
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GECC
     */
    public function setPromotionPlan($promotionPlan)
    {
        $this->promotionPlan = $promotionPlan;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string[] $line
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GECC
     */
    public function setLine(array $line = null)
    {
        $this->line = $line;

        return $this;
    }
}
