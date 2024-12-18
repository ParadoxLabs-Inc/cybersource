<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DecisionManager
{
    /**
     * @var boolean $enabled
     */
    protected $enabled;

    /**
     * @var string $profile
     */
    protected $profile;

    /**
     * @var string $pausedRequestID
     */
    protected $pausedRequestID;

    /**
     * @var Authentication $authentication
     */
    protected $authentication;

    /**
     * @var DecisionManagerTravelData $travelData
     */
    protected $travelData;

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManager
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param string $profile
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManager
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return string
     */
    public function getPausedRequestID()
    {
        return $this->pausedRequestID;
    }

    /**
     * @param string $pausedRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManager
     */
    public function setPausedRequestID($pausedRequestID)
    {
        $this->pausedRequestID = $pausedRequestID;

        return $this;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManager
     */
    public function setAuthentication($authentication)
    {
        $this->authentication = $authentication;

        return $this;
    }

    /**
     * @return DecisionManagerTravelData
     */
    public function getTravelData()
    {
        return $this->travelData;
    }

    /**
     * @param DecisionManagerTravelData $travelData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManager
     */
    public function setTravelData($travelData)
    {
        $this->travelData = $travelData;

        return $this;
    }
}
