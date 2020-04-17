<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APRevokeMandateService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
