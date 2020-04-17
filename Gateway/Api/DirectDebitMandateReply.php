<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class DirectDebitMandateReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $mandateID
     */
    protected $mandateID;

    /**
     * @var string $mandateMaturationDate
     */
    protected $mandateMaturationDate;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandateID()
    {
        return $this->mandateID;
    }

    /**
     * @param string $mandateID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateReply
     */
    public function setMandateID($mandateID)
    {
        $this->mandateID = $mandateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandateMaturationDate()
    {
        return $this->mandateMaturationDate;
    }

    /**
     * @param string $mandateMaturationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateReply
     */
    public function setMandateMaturationDate($mandateMaturationDate)
    {
        $this->mandateMaturationDate = $mandateMaturationDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestDateTime()
    {
        if ($this->requestDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->requestDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $requestDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateReply
     */
    public function setRequestDateTime(DateTime $requestDateTime = null)
    {
        if ($requestDateTime == null) {
            $this->requestDateTime = null;
        } else {
            $this->requestDateTime = $requestDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationID()
    {
        return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorResponse()
    {
        return $this->processorResponse;
    }

    /**
     * @param string $processorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }
}
