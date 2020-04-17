<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class DecisionManagerTravelData
{
    /**
     * @var DecisionManagerTravelLeg[] $leg
     */
    protected $leg;

    /**
     * @var \DateTime $departureDateTime
     */
    protected $departureDateTime;

    /**
     * @var string $completeRoute
     */
    protected $completeRoute;

    /**
     * @var string $journeyType
     */
    protected $journeyType;

    /**
     * @var string $actualFinalDestination
     */
    protected $actualFinalDestination;

    /**
     * @return DecisionManagerTravelLeg[]
     */
    public function getLeg()
    {
        return $this->leg;
    }

    /**
     * @param DecisionManagerTravelLeg[] $leg
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelData
     */
    public function setLeg(array $leg = null)
    {
        $this->leg = $leg;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDepartureDateTime()
    {
        if ($this->departureDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->departureDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $departureDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelData
     */
    public function setDepartureDateTime(DateTime $departureDateTime = null)
    {
        if ($departureDateTime == null) {
            $this->departureDateTime = null;
        } else {
            $this->departureDateTime = $departureDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCompleteRoute()
    {
        return $this->completeRoute;
    }

    /**
     * @param string $completeRoute
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelData
     */
    public function setCompleteRoute($completeRoute)
    {
        $this->completeRoute = $completeRoute;

        return $this;
    }

    /**
     * @return string
     */
    public function getJourneyType()
    {
        return $this->journeyType;
    }

    /**
     * @param string $journeyType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelData
     */
    public function setJourneyType($journeyType)
    {
        $this->journeyType = $journeyType;

        return $this;
    }

    /**
     * @return string
     */
    public function getActualFinalDestination()
    {
        return $this->actualFinalDestination;
    }

    /**
     * @param string $actualFinalDestination
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelData
     */
    public function setActualFinalDestination($actualFinalDestination)
    {
        $this->actualFinalDestination = $actualFinalDestination;

        return $this;
    }
}
