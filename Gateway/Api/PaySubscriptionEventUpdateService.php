<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionEventUpdateService
{
    /**
     * @var string $action
     */
    protected $action;

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
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEventUpdateService
     */
    public function setAction($action)
    {
        $this->action = $action;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEventUpdateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
