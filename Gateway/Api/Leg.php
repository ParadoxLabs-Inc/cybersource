<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Leg
{
    /**
     * @var string $carrierCode
     */
    protected $carrierCode;

    /**
     * @var string $flightNumber
     */
    protected $flightNumber;

    /**
     * @var string $originatingAirportCode
     */
    protected $originatingAirportCode;

    /**
     * @var string $class
     */
    protected $class;

    /**
     * @var string $stopoverCode
     */
    protected $stopoverCode;

    /**
     * @var string $departureDate
     */
    protected $departureDate;

    /**
     * @var string $destination
     */
    protected $destination;

    /**
     * @var string $fareBasis
     */
    protected $fareBasis;

    /**
     * @var string $departTax
     */
    protected $departTax;

    /**
     * @var string $conjunctionTicket
     */
    protected $conjunctionTicket;

    /**
     * @var string $exchangeTicket
     */
    protected $exchangeTicket;

    /**
     * @var string $couponNumber
     */
    protected $couponNumber;

    /**
     * @var string $departureTime
     */
    protected $departureTime;

    /**
     * @var string $departureTimeSegment
     */
    protected $departureTimeSegment;

    /**
     * @var string $arrivalTime
     */
    protected $arrivalTime;

    /**
     * @var string $arrivalTimeSegment
     */
    protected $arrivalTimeSegment;

    /**
     * @var string $endorsementsRestrictions
     */
    protected $endorsementsRestrictions;

    /**
     * @var string $fare
     */
    protected $fare;

    /**
     * @var string $fee
     */
    protected $fee;

    /**
     * @var string $tax
     */
    protected $tax;

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
    public function getCarrierCode()
    {
        return $this->carrierCode;
    }

    /**
     * @param string $carrierCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setCarrierCode($carrierCode)
    {
        $this->carrierCode = $carrierCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getFlightNumber()
    {
        return $this->flightNumber;
    }

    /**
     * @param string $flightNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setFlightNumber($flightNumber)
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginatingAirportCode()
    {
        return $this->originatingAirportCode;
    }

    /**
     * @param string $originatingAirportCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setOriginatingAirportCode($originatingAirportCode)
    {
        $this->originatingAirportCode = $originatingAirportCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getStopoverCode()
    {
        return $this->stopoverCode;
    }

    /**
     * @param string $stopoverCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setStopoverCode($stopoverCode)
    {
        $this->stopoverCode = $stopoverCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepartureDate()
    {
        return $this->departureDate;
    }

    /**
     * @param string $departureDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setDepartureDate($departureDate)
    {
        $this->departureDate = $departureDate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return string
     */
    public function getFareBasis()
    {
        return $this->fareBasis;
    }

    /**
     * @param string $fareBasis
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setFareBasis($fareBasis)
    {
        $this->fareBasis = $fareBasis;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepartTax()
    {
        return $this->departTax;
    }

    /**
     * @param string $departTax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setDepartTax($departTax)
    {
        $this->departTax = $departTax;

        return $this;
    }

    /**
     * @return string
     */
    public function getConjunctionTicket()
    {
        return $this->conjunctionTicket;
    }

    /**
     * @param string $conjunctionTicket
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setConjunctionTicket($conjunctionTicket)
    {
        $this->conjunctionTicket = $conjunctionTicket;

        return $this;
    }

    /**
     * @return string
     */
    public function getExchangeTicket()
    {
        return $this->exchangeTicket;
    }

    /**
     * @param string $exchangeTicket
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setExchangeTicket($exchangeTicket)
    {
        $this->exchangeTicket = $exchangeTicket;

        return $this;
    }

    /**
     * @return string
     */
    public function getCouponNumber()
    {
        return $this->couponNumber;
    }

    /**
     * @param string $couponNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setCouponNumber($couponNumber)
    {
        $this->couponNumber = $couponNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * @param string $departureTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepartureTimeSegment()
    {
        return $this->departureTimeSegment;
    }

    /**
     * @param string $departureTimeSegment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setDepartureTimeSegment($departureTimeSegment)
    {
        $this->departureTimeSegment = $departureTimeSegment;

        return $this;
    }

    /**
     * @return string
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * @param string $arrivalTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getArrivalTimeSegment()
    {
        return $this->arrivalTimeSegment;
    }

    /**
     * @param string $arrivalTimeSegment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setArrivalTimeSegment($arrivalTimeSegment)
    {
        $this->arrivalTimeSegment = $arrivalTimeSegment;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndorsementsRestrictions()
    {
        return $this->endorsementsRestrictions;
    }

    /**
     * @param string $endorsementsRestrictions
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setEndorsementsRestrictions($endorsementsRestrictions)
    {
        $this->endorsementsRestrictions = $endorsementsRestrictions;

        return $this;
    }

    /**
     * @return string
     */
    public function getFare()
    {
        return $this->fare;
    }

    /**
     * @param string $fare
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setFare($fare)
    {
        $this->fare = $fare;

        return $this;
    }

    /**
     * @return string
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @param string $fee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * @return string
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param string $tax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Leg
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
