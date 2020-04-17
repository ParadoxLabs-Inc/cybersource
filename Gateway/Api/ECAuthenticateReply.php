<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class ECAuthenticateReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $checkpointSummary
     */
    protected $checkpointSummary;

    /**
     * @var string $fraudShieldIndicators
     */
    protected $fraudShieldIndicators;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateReply
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
    public function getProcessorResponse()
    {
        return $this->processorResponse;
    }

    /**
     * @param string $processorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckpointSummary()
    {
        return $this->checkpointSummary;
    }

    /**
     * @param string $checkpointSummary
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateReply
     */
    public function setCheckpointSummary($checkpointSummary)
    {
        $this->checkpointSummary = $checkpointSummary;

        return $this;
    }

    /**
     * @return string
     */
    public function getFraudShieldIndicators()
    {
        return $this->fraudShieldIndicators;
    }

    /**
     * @param string $fraudShieldIndicators
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateReply
     */
    public function setFraudShieldIndicators($fraudShieldIndicators)
    {
        $this->fraudShieldIndicators = $fraudShieldIndicators;

        return $this;
    }
}
