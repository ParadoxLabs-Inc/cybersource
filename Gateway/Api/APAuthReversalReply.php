<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APAuthReversalReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @var string $providerResponse
     */
    protected $providerResponse;

    /**
     * @var string $paymentStatus
     */
    protected $paymentStatus;

    /**
     * @var string $responseCode
     */
    protected $responseCode;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @param string $transactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        if ($this->dateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->dateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $dateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setDateTime(DateTime $dateTime = null)
    {
        if ($dateTime == null) {
            $this->dateTime = null;
        } else {
            $this->dateTime = $dateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderResponse()
    {
        return $this->providerResponse;
    }

    /**
     * @param string $providerResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setProviderResponse($providerResponse)
    {
        $this->providerResponse = $providerResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

        return $this;
    }
}
