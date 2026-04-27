<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APExtendOrderService
{
    /**
     * @var string $orderRequestID
     */
    protected $orderRequestID;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APExtendOrderService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APExtendOrderService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
