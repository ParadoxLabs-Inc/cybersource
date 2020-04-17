<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class HostedDataRetrieveService
{
    /**
     * @var string $profileID
     */
    protected $profileID;

    /**
     * @var string $tokenValue
     */
    protected $tokenValue;

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
    public function getProfileID()
    {
        return $this->profileID;
    }

    /**
     * @param string $profileID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveService
     */
    public function setProfileID($profileID)
    {
        $this->profileID = $profileID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenValue()
    {
        return $this->tokenValue;
    }

    /**
     * @param string $tokenValue
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveService
     */
    public function setTokenValue($tokenValue)
    {
        $this->tokenValue = $tokenValue;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
