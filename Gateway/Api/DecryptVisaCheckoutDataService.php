<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DecryptVisaCheckoutDataService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecryptVisaCheckoutDataService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
