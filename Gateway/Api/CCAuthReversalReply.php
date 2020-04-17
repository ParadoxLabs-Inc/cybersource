<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class CCAuthReversalReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime;

    /**
     * @var string $forwardCode
     */
    protected $forwardCode;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var string $paymentCardService
     */
    protected $paymentCardService;

    /**
     * @var string $paymentCardServiceResult
     */
    protected $paymentCardServiceResult;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * @param string $authorizationCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
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
    public function getForwardCode()
    {
        return $this->forwardCode;
    }

    /**
     * @param string $forwardCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setForwardCode($forwardCode)
    {
        $this->forwardCode = $forwardCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorTransactionID()
    {
        return $this->processorTransactionID;
    }

    /**
     * @param string $processorTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentCardService()
    {
        return $this->paymentCardService;
    }

    /**
     * @param string $paymentCardService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setPaymentCardService($paymentCardService)
    {
        $this->paymentCardService = $paymentCardService;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentCardServiceResult()
    {
        return $this->paymentCardServiceResult;
    }

    /**
     * @param string $paymentCardServiceResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply
     */
    public function setPaymentCardServiceResult($paymentCardServiceResult)
    {
        $this->paymentCardServiceResult = $paymentCardServiceResult;

        return $this;
    }
}
