<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCSaleReversalService
{
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
    public function getSaleRequestID()
    {
        return $this->saleRequestID;
    }

    /**
     * @param string $saleRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
