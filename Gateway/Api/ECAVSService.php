<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ECAVSService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
