<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class DecisionManagerTravelLeg
{
    /**
     * @var string $origin
     */
    protected $origin;

    /**
     * @var string $destination
     */
    protected $destination;

    /**
     * @var \DateTime $departureDateTime
     */
    protected $departureDateTime;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelLeg
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelLeg
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelLeg
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelLeg
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
