<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AncillaryData
{
    /**
     * @var string $ticketNumber
     */
    protected $ticketNumber;

    /**
     * @var string $passengerName
     */
    protected $passengerName;

    /**
     * @var string $connectedTicketNumber
     */
    protected $connectedTicketNumber;

    /**
     * @var string $creditReasonIndicator
     */
    protected $creditReasonIndicator;

    /**
     * @var Service[] $service
     */
    protected $service;

    /**
     * @return string
     */
    public function getTicketNumber()
    {
        return $this->ticketNumber;
    }

    /**
     * @param string $ticketNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AncillaryData
     */
    public function setTicketNumber($ticketNumber)
    {
        $this->ticketNumber = $ticketNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerName()
    {
        return $this->passengerName;
    }

    /**
     * @param string $passengerName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AncillaryData
     */
    public function setPassengerName($passengerName)
    {
        $this->passengerName = $passengerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getConnectedTicketNumber()
    {
        return $this->connectedTicketNumber;
    }

    /**
     * @param string $connectedTicketNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AncillaryData
     */
    public function setConnectedTicketNumber($connectedTicketNumber)
    {
        $this->connectedTicketNumber = $connectedTicketNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreditReasonIndicator()
    {
        return $this->creditReasonIndicator;
    }

    /**
     * @param string $creditReasonIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AncillaryData
     */
    public function setCreditReasonIndicator($creditReasonIndicator)
    {
        $this->creditReasonIndicator = $creditReasonIndicator;

        return $this;
    }

    /**
     * @return Service[]
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param Service[] $service
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AncillaryData
     */
    public function setService(array $service = null)
    {
        $this->service = $service;

        return $this;
    }
}
