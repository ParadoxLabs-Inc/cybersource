<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DMEService
{
    /**
     * @var string $eventType
     */
    protected $eventType;

    /**
     * @var string $eventPolicy
     */
    protected $eventPolicy;

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
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEService
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventPolicy()
    {
        return $this->eventPolicy;
    }

    /**
     * @param string $eventPolicy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEService
     */
    public function setEventPolicy($eventPolicy)
    {
        $this->eventPolicy = $eventPolicy;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
