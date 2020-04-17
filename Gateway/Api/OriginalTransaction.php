<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class OriginalTransaction
{
    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $reasonCode
     */
    protected $reasonCode;

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OriginalTransaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param string $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OriginalTransaction
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }
}
