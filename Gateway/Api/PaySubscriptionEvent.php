<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionEvent
{
    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $approvedBy
     */
    protected $approvedBy;

    /**
     * @var int $number
     */
    protected $number;

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEvent
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param string $approvedBy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEvent
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEvent
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }
}
