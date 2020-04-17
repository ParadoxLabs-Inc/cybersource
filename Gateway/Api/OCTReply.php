<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class OCTReply
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
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $approvalCode
     */
    protected $approvalCode;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $paymentNetworkTransactionID
     */
    protected $paymentNetworkTransactionID;

    /**
     * @var string $prepaidBalanceCurrency
     */
    protected $prepaidBalanceCurrency;

    /**
     * @var string $prepaidBalanceAmount
     */
    protected $prepaidBalanceAmount;

    /**
     * @var string $processorResponseSource
     */
    protected $processorResponseSource;

    /**
     * @var string $reconciliationIdType
     */
    protected $reconciliationIdType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getApprovalCode()
    {
        return $this->approvalCode;
    }

    /**
     * @param string $approvalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setApprovalCode($approvalCode)
    {
        $this->approvalCode = $approvalCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentNetworkTransactionID()
    {
        return $this->paymentNetworkTransactionID;
    }

    /**
     * @param string $paymentNetworkTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setPaymentNetworkTransactionID($paymentNetworkTransactionID)
    {
        $this->paymentNetworkTransactionID = $paymentNetworkTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrepaidBalanceCurrency()
    {
        return $this->prepaidBalanceCurrency;
    }

    /**
     * @param string $prepaidBalanceCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setPrepaidBalanceCurrency($prepaidBalanceCurrency)
    {
        $this->prepaidBalanceCurrency = $prepaidBalanceCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrepaidBalanceAmount()
    {
        return $this->prepaidBalanceAmount;
    }

    /**
     * @param string $prepaidBalanceAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setPrepaidBalanceAmount($prepaidBalanceAmount)
    {
        $this->prepaidBalanceAmount = $prepaidBalanceAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorResponseSource()
    {
        return $this->processorResponseSource;
    }

    /**
     * @param string $processorResponseSource
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setProcessorResponseSource($processorResponseSource)
    {
        $this->processorResponseSource = $processorResponseSource;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationIdType()
    {
        return $this->reconciliationIdType;
    }

    /**
     * @param string $reconciliationIdType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTReply
     */
    public function setReconciliationIdType($reconciliationIdType)
    {
        $this->reconciliationIdType = $reconciliationIdType;

        return $this;
    }
}
