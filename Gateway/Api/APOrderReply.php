<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APOrderReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $amount
     */
    protected $amount;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @var \DateTime $updateDateTime
     */
    protected $updateDateTime;

    /**
     * @var string $orderProcessorTransactionId
     */
    protected $orderProcessorTransactionId;

    /**
     * @var string $merchantURL
     */
    protected $merchantURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
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
     * @param \DateTime|null $dateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
     */
    public function setDateTime(?DateTime $dateTime = null)
    {
        if ($dateTime == null) {
            $this->dateTime = null;
        } else {
            $this->dateTime = $dateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDateTime()
    {
        if ($this->updateDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->updateDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime|null $updateDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
     */
    public function setUpdateDateTime(?DateTime $updateDateTime = null)
    {
        if ($updateDateTime == null) {
            $this->updateDateTime = null;
        } else {
            $this->updateDateTime = $updateDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderProcessorTransactionId()
    {
        return $this->orderProcessorTransactionId;
    }

    /**
     * @param string $orderProcessorTransactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
     */
    public function setOrderProcessorTransactionId($orderProcessorTransactionId)
    {
        $this->orderProcessorTransactionId = $orderProcessorTransactionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantURL()
    {
        return $this->merchantURL;
    }

    /**
     * @param string $merchantURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply
     */
    public function setMerchantURL($merchantURL)
    {
        $this->merchantURL = $merchantURL;

        return $this;
    }
}
