<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APCheckStatusReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $paymentStatus
     */
    protected $paymentStatus;

    /**
     * @var string $processorTradeNo
     */
    protected $processorTradeNo;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var string $ibanSuffix
     */
    protected $ibanSuffix;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorTradeNo()
    {
        return $this->processorTradeNo;
    }

    /**
     * @param string $processorTradeNo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setProcessorTradeNo($processorTradeNo)
    {
        $this->processorTradeNo = $processorTradeNo;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getIbanSuffix()
    {
        return $this->ibanSuffix;
    }

    /**
     * @param string $ibanSuffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setIbanSuffix($ibanSuffix)
    {
        $this->ibanSuffix = $ibanSuffix;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
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
}
