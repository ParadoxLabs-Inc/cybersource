<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PinDebitReversalService
{
    /**
     * @var string $pinDebitRequestID
     */
    protected $pinDebitRequestID;

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
    public function getPinDebitRequestID()
    {
        return $this->pinDebitRequestID;
    }

    /**
     * @param string $pinDebitRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitReversalService
     */
    public function setPinDebitRequestID($pinDebitRequestID)
    {
        $this->pinDebitRequestID = $pinDebitRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitReversalService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
