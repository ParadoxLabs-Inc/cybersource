<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Travel
{
    /**
     * @var RestrictedString $actualFinalDestinationCountry
     */
    protected $actualFinalDestinationCountry;

    /**
     * @var RestrictedString $actualFinalDestinationCity
     */
    protected $actualFinalDestinationCity;

    /**
     * @var RestrictedDecimal $actualFinalDestinationLatitude
     */
    protected $actualFinalDestinationLatitude;

    /**
     * @var RestrictedDecimal $actualFinalDestinationLongitude
     */
    protected $actualFinalDestinationLongitude;

    /**
     * @var RestrictedString $firstDepartureCountry
     */
    protected $firstDepartureCountry;

    /**
     * @var RestrictedString $firstDepartureCity
     */
    protected $firstDepartureCity;

    /**
     * @var RestrictedDecimal $firstDepartureLatitude
     */
    protected $firstDepartureLatitude;

    /**
     * @var RestrictedDecimal $firstDepartureLongitude
     */
    protected $firstDepartureLongitude;

    /**
     * @var RestrictedString $firstDestinationCountry
     */
    protected $firstDestinationCountry;

    /**
     * @var RestrictedString $firstDestinationCity
     */
    protected $firstDestinationCity;

    /**
     * @var RestrictedDecimal $firstDestinationLatitude
     */
    protected $firstDestinationLatitude;

    /**
     * @var RestrictedDecimal $firstDestinationLongitude
     */
    protected $firstDestinationLongitude;

    /**
     * @var RestrictedString $lastDestinationCountry
     */
    protected $lastDestinationCountry;

    /**
     * @var RestrictedString $lastDestinationCity
     */
    protected $lastDestinationCity;

    /**
     * @var RestrictedDecimal $lastDestinationLatitude
     */
    protected $lastDestinationLatitude;

    /**
     * @var RestrictedDecimal $lastDestinationLongitude
     */
    protected $lastDestinationLongitude;

    /**
     * @return RestrictedString
     */
    public function getActualFinalDestinationCountry()
    {
        return $this->actualFinalDestinationCountry;
    }

    /**
     * @param RestrictedString $actualFinalDestinationCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setActualFinalDestinationCountry($actualFinalDestinationCountry)
    {
        $this->actualFinalDestinationCountry = $actualFinalDestinationCountry;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getActualFinalDestinationCity()
    {
        return $this->actualFinalDestinationCity;
    }

    /**
     * @param RestrictedString $actualFinalDestinationCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setActualFinalDestinationCity($actualFinalDestinationCity)
    {
        $this->actualFinalDestinationCity = $actualFinalDestinationCity;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getActualFinalDestinationLatitude()
    {
        return $this->actualFinalDestinationLatitude;
    }

    /**
     * @param RestrictedDecimal $actualFinalDestinationLatitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setActualFinalDestinationLatitude($actualFinalDestinationLatitude)
    {
        $this->actualFinalDestinationLatitude = $actualFinalDestinationLatitude;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getActualFinalDestinationLongitude()
    {
        return $this->actualFinalDestinationLongitude;
    }

    /**
     * @param RestrictedDecimal $actualFinalDestinationLongitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setActualFinalDestinationLongitude($actualFinalDestinationLongitude)
    {
        $this->actualFinalDestinationLongitude = $actualFinalDestinationLongitude;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getFirstDepartureCountry()
    {
        return $this->firstDepartureCountry;
    }

    /**
     * @param RestrictedString $firstDepartureCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDepartureCountry($firstDepartureCountry)
    {
        $this->firstDepartureCountry = $firstDepartureCountry;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getFirstDepartureCity()
    {
        return $this->firstDepartureCity;
    }

    /**
     * @param RestrictedString $firstDepartureCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDepartureCity($firstDepartureCity)
    {
        $this->firstDepartureCity = $firstDepartureCity;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getFirstDepartureLatitude()
    {
        return $this->firstDepartureLatitude;
    }

    /**
     * @param RestrictedDecimal $firstDepartureLatitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDepartureLatitude($firstDepartureLatitude)
    {
        $this->firstDepartureLatitude = $firstDepartureLatitude;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getFirstDepartureLongitude()
    {
        return $this->firstDepartureLongitude;
    }

    /**
     * @param RestrictedDecimal $firstDepartureLongitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDepartureLongitude($firstDepartureLongitude)
    {
        $this->firstDepartureLongitude = $firstDepartureLongitude;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getFirstDestinationCountry()
    {
        return $this->firstDestinationCountry;
    }

    /**
     * @param RestrictedString $firstDestinationCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDestinationCountry($firstDestinationCountry)
    {
        $this->firstDestinationCountry = $firstDestinationCountry;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getFirstDestinationCity()
    {
        return $this->firstDestinationCity;
    }

    /**
     * @param RestrictedString $firstDestinationCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDestinationCity($firstDestinationCity)
    {
        $this->firstDestinationCity = $firstDestinationCity;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getFirstDestinationLatitude()
    {
        return $this->firstDestinationLatitude;
    }

    /**
     * @param RestrictedDecimal $firstDestinationLatitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDestinationLatitude($firstDestinationLatitude)
    {
        $this->firstDestinationLatitude = $firstDestinationLatitude;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getFirstDestinationLongitude()
    {
        return $this->firstDestinationLongitude;
    }

    /**
     * @param RestrictedDecimal $firstDestinationLongitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setFirstDestinationLongitude($firstDestinationLongitude)
    {
        $this->firstDestinationLongitude = $firstDestinationLongitude;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getLastDestinationCountry()
    {
        return $this->lastDestinationCountry;
    }

    /**
     * @param RestrictedString $lastDestinationCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setLastDestinationCountry($lastDestinationCountry)
    {
        $this->lastDestinationCountry = $lastDestinationCountry;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getLastDestinationCity()
    {
        return $this->lastDestinationCity;
    }

    /**
     * @param RestrictedString $lastDestinationCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setLastDestinationCity($lastDestinationCity)
    {
        $this->lastDestinationCity = $lastDestinationCity;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getLastDestinationLatitude()
    {
        return $this->lastDestinationLatitude;
    }

    /**
     * @param RestrictedDecimal $lastDestinationLatitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setLastDestinationLatitude($lastDestinationLatitude)
    {
        $this->lastDestinationLatitude = $lastDestinationLatitude;

        return $this;
    }

    /**
     * @return RestrictedDecimal
     */
    public function getLastDestinationLongitude()
    {
        return $this->lastDestinationLongitude;
    }

    /**
     * @param RestrictedDecimal $lastDestinationLongitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Travel
     */
    public function setLastDestinationLongitude($lastDestinationLongitude)
    {
        $this->lastDestinationLongitude = $lastDestinationLongitude;

        return $this;
    }
}
