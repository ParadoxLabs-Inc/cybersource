<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class FraudUpdateService
{

    /**
     * @var string $actionCode
     */
    protected $actionCode = null;

    /**
     * @var string $markedData
     */
    protected $markedData = null;

    /**
     * @var string $markingReason
     */
    protected $markingReason = null;

    /**
     * @var string $markingNotes
     */
    protected $markingNotes = null;

    /**
     * @var string $markingRequestID
     */
    protected $markingRequestID = null;

    /**
     * @var string $markingTransactionDate
     */
    protected $markingTransactionDate = null;

    /**
     * @var float $markingAmount
     */
    protected $markingAmount = null;

    /**
     * @var string $markingCurrency
     */
    protected $markingCurrency = null;

    /**
     * @var string $markingIndicator
     */
    protected $markingIndicator = null;

    /**
     * @var boolean $run
     */
    protected $run = null;

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
    public function getActionCode()
    {
        return $this->actionCode;
    }

    /**
     * @param string $actionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setActionCode($actionCode)
    {
        $this->actionCode = $actionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkedData()
    {
        return $this->markedData;
    }

    /**
     * @param string $markedData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkedData($markedData)
    {
        $this->markedData = $markedData;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingReason()
    {
        return $this->markingReason;
    }

    /**
     * @param string $markingReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkingReason($markingReason)
    {
        $this->markingReason = $markingReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingNotes()
    {
        return $this->markingNotes;
    }

    /**
     * @param string $markingNotes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkingNotes($markingNotes)
    {
        $this->markingNotes = $markingNotes;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingRequestID()
    {
        return $this->markingRequestID;
    }

    /**
     * @param string $markingRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkingRequestID($markingRequestID)
    {
        $this->markingRequestID = $markingRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingTransactionDate()
    {
        return $this->markingTransactionDate;
    }

    /**
     * @param string $markingTransactionDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkingTransactionDate($markingTransactionDate)
    {
        $this->markingTransactionDate = $markingTransactionDate;

        return $this;
    }

    /**
     * @return float
     */
    public function getMarkingAmount()
    {
        return $this->markingAmount;
    }

    /**
     * @param float $markingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkingAmount($markingAmount)
    {
        $this->markingAmount = $markingAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingCurrency()
    {
        return $this->markingCurrency;
    }

    /**
     * @param string $markingCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkingCurrency($markingCurrency)
    {
        $this->markingCurrency = $markingCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getMarkingIndicator()
    {
        return $this->markingIndicator;
    }

    /**
     * @param string $markingIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setMarkingIndicator($markingIndicator)
    {
        $this->markingIndicator = $markingIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }

}
