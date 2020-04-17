<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VC
{
    /**
     * @var string $orderID
     */
    protected $orderID;

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @param string $orderID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VC
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }
}
