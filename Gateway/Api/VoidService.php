<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VoidService
{
    /**
     * @var string $voidRequestID
     */
    protected $voidRequestID;

    /**
     * @var string $voidRequestToken
     */
    protected $voidRequestToken;

    /**
     * @var string $voidReason
     */
    protected $voidReason;

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
    public function getVoidRequestID()
    {
        return $this->voidRequestID;
    }

    /**
     * @param string $voidRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VoidService
     */
    public function setVoidRequestID($voidRequestID)
    {
        $this->voidRequestID = $voidRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getVoidRequestToken()
    {
        return $this->voidRequestToken;
    }

    /**
     * @param string $voidRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VoidService
     */
    public function setVoidRequestToken($voidRequestToken)
    {
        $this->voidRequestToken = $voidRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getVoidReason()
    {
        return $this->voidReason;
    }

    /**
     * @param string $voidReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VoidService
     */
    public function setVoidReason($voidReason)
    {
        $this->voidReason = $voidReason;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VoidService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
