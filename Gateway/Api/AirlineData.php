<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AirlineData
{

    /**
     * @var string $agentCode
     */
    protected $agentCode = null;

    /**
     * @var string $agentName
     */
    protected $agentName = null;

    /**
     * @var string $ticketIssuerCity
     */
    protected $ticketIssuerCity = null;

    /**
     * @var string $ticketIssuerState
     */
    protected $ticketIssuerState = null;

    /**
     * @var string $ticketIssuerPostalCode
     */
    protected $ticketIssuerPostalCode = null;

    /**
     * @var string $ticketIssuerCountry
     */
    protected $ticketIssuerCountry = null;

    /**
     * @var string $ticketIssuerAddress
     */
    protected $ticketIssuerAddress = null;

    /**
     * @var string $ticketIssuerCode
     */
    protected $ticketIssuerCode = null;

    /**
     * @var string $ticketIssuerName
     */
    protected $ticketIssuerName = null;

    /**
     * @var string $ticketNumber
     */
    protected $ticketNumber = null;

    /**
     * @var int $checkDigit
     */
    protected $checkDigit = null;

    /**
     * @var int $restrictedTicketIndicator
     */
    protected $restrictedTicketIndicator = null;

    /**
     * @var string $transactionType
     */
    protected $transactionType = null;

    /**
     * @var string $extendedPaymentCode
     */
    protected $extendedPaymentCode = null;

    /**
     * @var string $carrierName
     */
    protected $carrierName = null;

    /**
     * @var string $passengerName
     */
    protected $passengerName = null;

    /**
     * @var Passenger[] $passenger
     */
    protected $passenger = null;

    /**
     * @var string $customerCode
     */
    protected $customerCode = null;

    /**
     * @var string $documentType
     */
    protected $documentType = null;

    /**
     * @var string $documentNumber
     */
    protected $documentNumber = null;

    /**
     * @var string $documentNumberOfParts
     */
    protected $documentNumberOfParts = null;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber = null;

    /**
     * @var string $invoiceDate
     */
    protected $invoiceDate = null;

    /**
     * @var string $chargeDetails
     */
    protected $chargeDetails = null;

    /**
     * @var string $bookingReference
     */
    protected $bookingReference = null;

    /**
     * @var float $totalFee
     */
    protected $totalFee = null;

    /**
     * @var string $clearingSequence
     */
    protected $clearingSequence = null;

    /**
     * @var int $clearingCount
     */
    protected $clearingCount = null;

    /**
     * @var float $totalClearingAmount
     */
    protected $totalClearingAmount = null;

    /**
     * @var Leg[] $leg
     */
    protected $leg = null;

    /**
     * @var string $numberOfPassengers
     */
    protected $numberOfPassengers = null;

    /**
     * @var string $reservationSystem
     */
    protected $reservationSystem = null;

    /**
     * @var string $processIdentifier
     */
    protected $processIdentifier = null;

    /**
     * @var string $iataNumericCode
     */
    protected $iataNumericCode = null;

    /**
     * @var string $ticketIssueDate
     */
    protected $ticketIssueDate = null;

    /**
     * @var string $electronicTicket
     */
    protected $electronicTicket = null;

    /**
     * @var string $originalTicketNumber
     */
    protected $originalTicketNumber = null;

    /**
     * @var string $purchaseType
     */
    protected $purchaseType = null;

    /**
     * @var string $creditReasonIndicator
     */
    protected $creditReasonIndicator = null;

    /**
     * @var string $ticketUpdateIndicator
     */
    protected $ticketUpdateIndicator = null;

    /**
     * @var string $planNumber
     */
    protected $planNumber = null;

    /**
     * @var string $arrivalDate
     */
    protected $arrivalDate = null;

    /**
     * @var string $ticketRestrictionText
     */
    protected $ticketRestrictionText = null;

    /**
     * @var float $exchangeTicketAmount
     */
    protected $exchangeTicketAmount = null;

    /**
     * @var float $exchangeTicketFee
     */
    protected $exchangeTicketFee = null;

    /**
     * @var string $journeyType
     */
    protected $journeyType = null;

    /**
     * @var string $boardingFee
     */
    protected $boardingFee = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getAgentCode()
    {
      return $this->agentCode;
    }

    /**
     * @param string $agentCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setAgentCode($agentCode)
    {
      $this->agentCode = $agentCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getAgentName()
    {
      return $this->agentName;
    }

    /**
     * @param string $agentName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setAgentName($agentName)
    {
      $this->agentName = $agentName;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssuerCity()
    {
      return $this->ticketIssuerCity;
    }

    /**
     * @param string $ticketIssuerCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssuerCity($ticketIssuerCity)
    {
      $this->ticketIssuerCity = $ticketIssuerCity;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssuerState()
    {
      return $this->ticketIssuerState;
    }

    /**
     * @param string $ticketIssuerState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssuerState($ticketIssuerState)
    {
      $this->ticketIssuerState = $ticketIssuerState;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssuerPostalCode()
    {
      return $this->ticketIssuerPostalCode;
    }

    /**
     * @param string $ticketIssuerPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssuerPostalCode($ticketIssuerPostalCode)
    {
      $this->ticketIssuerPostalCode = $ticketIssuerPostalCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssuerCountry()
    {
      return $this->ticketIssuerCountry;
    }

    /**
     * @param string $ticketIssuerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssuerCountry($ticketIssuerCountry)
    {
      $this->ticketIssuerCountry = $ticketIssuerCountry;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssuerAddress()
    {
      return $this->ticketIssuerAddress;
    }

    /**
     * @param string $ticketIssuerAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssuerAddress($ticketIssuerAddress)
    {
      $this->ticketIssuerAddress = $ticketIssuerAddress;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssuerCode()
    {
      return $this->ticketIssuerCode;
    }

    /**
     * @param string $ticketIssuerCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssuerCode($ticketIssuerCode)
    {
      $this->ticketIssuerCode = $ticketIssuerCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssuerName()
    {
      return $this->ticketIssuerName;
    }

    /**
     * @param string $ticketIssuerName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssuerName($ticketIssuerName)
    {
      $this->ticketIssuerName = $ticketIssuerName;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketNumber()
    {
      return $this->ticketNumber;
    }

    /**
     * @param string $ticketNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketNumber($ticketNumber)
    {
      $this->ticketNumber = $ticketNumber;
      return $this;
    }

    /**
     * @return int
     */
    public function getCheckDigit()
    {
      return $this->checkDigit;
    }

    /**
     * @param int $checkDigit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setCheckDigit($checkDigit)
    {
      $this->checkDigit = $checkDigit;
      return $this;
    }

    /**
     * @return int
     */
    public function getRestrictedTicketIndicator()
    {
      return $this->restrictedTicketIndicator;
    }

    /**
     * @param int $restrictedTicketIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setRestrictedTicketIndicator($restrictedTicketIndicator)
    {
      $this->restrictedTicketIndicator = $restrictedTicketIndicator;
      return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
      return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTransactionType($transactionType)
    {
      $this->transactionType = $transactionType;
      return $this;
    }

    /**
     * @return string
     */
    public function getExtendedPaymentCode()
    {
      return $this->extendedPaymentCode;
    }

    /**
     * @param string $extendedPaymentCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setExtendedPaymentCode($extendedPaymentCode)
    {
      $this->extendedPaymentCode = $extendedPaymentCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getCarrierName()
    {
      return $this->carrierName;
    }

    /**
     * @param string $carrierName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setCarrierName($carrierName)
    {
      $this->carrierName = $carrierName;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setPassengerName($passengerName)
    {
      $this->passengerName = $passengerName;
      return $this;
    }

    /**
     * @return Passenger[]
     */
    public function getPassenger()
    {
      return $this->passenger;
    }

    /**
     * @param Passenger[] $passenger
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setPassenger(array $passenger = null)
    {
      $this->passenger = $passenger;
      return $this;
    }

    /**
     * @return string
     */
    public function getCustomerCode()
    {
      return $this->customerCode;
    }

    /**
     * @param string $customerCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setCustomerCode($customerCode)
    {
      $this->customerCode = $customerCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getDocumentType()
    {
      return $this->documentType;
    }

    /**
     * @param string $documentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setDocumentType($documentType)
    {
      $this->documentType = $documentType;
      return $this;
    }

    /**
     * @return string
     */
    public function getDocumentNumber()
    {
      return $this->documentNumber;
    }

    /**
     * @param string $documentNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setDocumentNumber($documentNumber)
    {
      $this->documentNumber = $documentNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getDocumentNumberOfParts()
    {
      return $this->documentNumberOfParts;
    }

    /**
     * @param string $documentNumberOfParts
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setDocumentNumberOfParts($documentNumberOfParts)
    {
      $this->documentNumberOfParts = $documentNumberOfParts;
      return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
      return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setInvoiceNumber($invoiceNumber)
    {
      $this->invoiceNumber = $invoiceNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceDate()
    {
      return $this->invoiceDate;
    }

    /**
     * @param string $invoiceDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setInvoiceDate($invoiceDate)
    {
      $this->invoiceDate = $invoiceDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getChargeDetails()
    {
      return $this->chargeDetails;
    }

    /**
     * @param string $chargeDetails
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setChargeDetails($chargeDetails)
    {
      $this->chargeDetails = $chargeDetails;
      return $this;
    }

    /**
     * @return string
     */
    public function getBookingReference()
    {
      return $this->bookingReference;
    }

    /**
     * @param string $bookingReference
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setBookingReference($bookingReference)
    {
      $this->bookingReference = $bookingReference;
      return $this;
    }

    /**
     * @return float
     */
    public function getTotalFee()
    {
      return $this->totalFee;
    }

    /**
     * @param float $totalFee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTotalFee($totalFee)
    {
      $this->totalFee = $totalFee;
      return $this;
    }

    /**
     * @return string
     */
    public function getClearingSequence()
    {
      return $this->clearingSequence;
    }

    /**
     * @param string $clearingSequence
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setClearingSequence($clearingSequence)
    {
      $this->clearingSequence = $clearingSequence;
      return $this;
    }

    /**
     * @return int
     */
    public function getClearingCount()
    {
      return $this->clearingCount;
    }

    /**
     * @param int $clearingCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setClearingCount($clearingCount)
    {
      $this->clearingCount = $clearingCount;
      return $this;
    }

    /**
     * @return float
     */
    public function getTotalClearingAmount()
    {
      return $this->totalClearingAmount;
    }

    /**
     * @param float $totalClearingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTotalClearingAmount($totalClearingAmount)
    {
      $this->totalClearingAmount = $totalClearingAmount;
      return $this;
    }

    /**
     * @return Leg[]
     */
    public function getLeg()
    {
      return $this->leg;
    }

    /**
     * @param Leg[] $leg
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setLeg(array $leg = null)
    {
      $this->leg = $leg;
      return $this;
    }

    /**
     * @return string
     */
    public function getNumberOfPassengers()
    {
      return $this->numberOfPassengers;
    }

    /**
     * @param string $numberOfPassengers
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setNumberOfPassengers($numberOfPassengers)
    {
      $this->numberOfPassengers = $numberOfPassengers;
      return $this;
    }

    /**
     * @return string
     */
    public function getReservationSystem()
    {
      return $this->reservationSystem;
    }

    /**
     * @param string $reservationSystem
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setReservationSystem($reservationSystem)
    {
      $this->reservationSystem = $reservationSystem;
      return $this;
    }

    /**
     * @return string
     */
    public function getProcessIdentifier()
    {
      return $this->processIdentifier;
    }

    /**
     * @param string $processIdentifier
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setProcessIdentifier($processIdentifier)
    {
      $this->processIdentifier = $processIdentifier;
      return $this;
    }

    /**
     * @return string
     */
    public function getIataNumericCode()
    {
      return $this->iataNumericCode;
    }

    /**
     * @param string $iataNumericCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setIataNumericCode($iataNumericCode)
    {
      $this->iataNumericCode = $iataNumericCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketIssueDate()
    {
      return $this->ticketIssueDate;
    }

    /**
     * @param string $ticketIssueDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketIssueDate($ticketIssueDate)
    {
      $this->ticketIssueDate = $ticketIssueDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getElectronicTicket()
    {
      return $this->electronicTicket;
    }

    /**
     * @param string $electronicTicket
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setElectronicTicket($electronicTicket)
    {
      $this->electronicTicket = $electronicTicket;
      return $this;
    }

    /**
     * @return string
     */
    public function getOriginalTicketNumber()
    {
      return $this->originalTicketNumber;
    }

    /**
     * @param string $originalTicketNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setOriginalTicketNumber($originalTicketNumber)
    {
      $this->originalTicketNumber = $originalTicketNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getPurchaseType()
    {
      return $this->purchaseType;
    }

    /**
     * @param string $purchaseType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setPurchaseType($purchaseType)
    {
      $this->purchaseType = $purchaseType;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setCreditReasonIndicator($creditReasonIndicator)
    {
      $this->creditReasonIndicator = $creditReasonIndicator;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketUpdateIndicator()
    {
      return $this->ticketUpdateIndicator;
    }

    /**
     * @param string $ticketUpdateIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketUpdateIndicator($ticketUpdateIndicator)
    {
      $this->ticketUpdateIndicator = $ticketUpdateIndicator;
      return $this;
    }

    /**
     * @return string
     */
    public function getPlanNumber()
    {
      return $this->planNumber;
    }

    /**
     * @param string $planNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setPlanNumber($planNumber)
    {
      $this->planNumber = $planNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getArrivalDate()
    {
      return $this->arrivalDate;
    }

    /**
     * @param string $arrivalDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setArrivalDate($arrivalDate)
    {
      $this->arrivalDate = $arrivalDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getTicketRestrictionText()
    {
      return $this->ticketRestrictionText;
    }

    /**
     * @param string $ticketRestrictionText
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setTicketRestrictionText($ticketRestrictionText)
    {
      $this->ticketRestrictionText = $ticketRestrictionText;
      return $this;
    }

    /**
     * @return float
     */
    public function getExchangeTicketAmount()
    {
      return $this->exchangeTicketAmount;
    }

    /**
     * @param float $exchangeTicketAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setExchangeTicketAmount($exchangeTicketAmount)
    {
      $this->exchangeTicketAmount = $exchangeTicketAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getExchangeTicketFee()
    {
      return $this->exchangeTicketFee;
    }

    /**
     * @param float $exchangeTicketFee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setExchangeTicketFee($exchangeTicketFee)
    {
      $this->exchangeTicketFee = $exchangeTicketFee;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setJourneyType($journeyType)
    {
      $this->journeyType = $journeyType;
      return $this;
    }

    /**
     * @return string
     */
    public function getBoardingFee()
    {
      return $this->boardingFee;
    }

    /**
     * @param string $boardingFee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AirlineData
     */
    public function setBoardingFee($boardingFee)
    {
      $this->boardingFee = $boardingFee;
      return $this;
    }

}
