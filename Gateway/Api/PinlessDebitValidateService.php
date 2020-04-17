<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PinlessDebitValidateService
{
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
     * @return boolean
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitValidateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
