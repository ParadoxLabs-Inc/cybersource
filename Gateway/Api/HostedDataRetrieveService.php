<?php declare(strict_types=1);

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
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
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
