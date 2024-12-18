<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APUpdateOrderService
{
    /**
     * @var string $orderRequestID
     */
    protected $orderRequestID;

    /**
     * @var boolean $run
     */
    protected $run;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
        $this->run = $run;
    }

    /**
     * @return string
     */
    public function getOrderRequestID()
    {
        return $this->orderRequestID;
    }

    /**
     * @param string $orderRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateOrderService
     */
    public function setOrderRequestID($orderRequestID)
    {
        $this->orderRequestID = $orderRequestID;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateOrderService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
