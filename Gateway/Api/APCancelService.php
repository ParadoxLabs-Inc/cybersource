<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APCancelService
{
    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $orderRequestID
     */
    protected $orderRequestID;

    /**
     * @var string $saleRequestID
     */
    protected $saleRequestID;

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
    public function getReconciliationID()
    {
        return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCancelService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCancelService
     */
    public function setOrderRequestID($orderRequestID)
    {
        $this->orderRequestID = $orderRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaleRequestID()
    {
        return $this->saleRequestID;
    }

    /**
     * @param string $saleRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCancelService
     */
    public function setSaleRequestID($saleRequestID)
    {
        $this->saleRequestID = $saleRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCancelService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
