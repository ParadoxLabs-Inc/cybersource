<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionDeleteService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
