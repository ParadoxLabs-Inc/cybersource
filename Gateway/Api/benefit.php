<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class benefit
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $amount
     */
    protected $amount;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\benefit
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\benefit
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
