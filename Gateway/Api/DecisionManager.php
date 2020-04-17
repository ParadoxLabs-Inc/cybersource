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
