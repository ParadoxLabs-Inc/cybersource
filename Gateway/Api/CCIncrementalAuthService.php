<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCIncrementalAuthService
{
    /**
     * @var string $authRequestID
     */
    protected $authRequestID;

    /**
     * @var int $duration
     */
    protected $duration;

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
    public function getAuthRequestID()
    {
        return $this->authRequestID;
    }

    /**
     * @param string $authRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthService
     */
    public function setAuthRequestID($authRequestID)
    {
        $this->authRequestID = $authRequestID;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthService
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
