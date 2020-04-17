<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class CCIncrementalAuthReply
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
     * @var \DateTime $authorizedDateTime
     */
    protected $authorizedDateTime;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $paymentNetworkTransactionID
     */
    protected $paymentNetworkTransactionID;

    /**
     * @var string $cardCategory
     */
    protected $cardCategory;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAuthorizedDateTime()
    {
        if ($this->authorizedDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->authorizedDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $authorizedDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
     */
    public function setAuthorizedDateTime(DateTime $authorizedDateTime = null)
    {
        if ($authorizedDateTime == null) {
            $this->authorizedDateTime = null;
        } else {
            $this->authorizedDateTime = $authorizedDateTime->format(DateTime::ATOM);
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
     */
    public function setPaymentNetworkTransactionID($paymentNetworkTransactionID)
    {
        $this->paymentNetworkTransactionID = $paymentNetworkTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardCategory()
    {
        return $this->cardCategory;
    }

    /**
     * @param string $cardCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply
     */
    public function setCardCategory($cardCategory)
    {
        $this->cardCategory = $cardCategory;

        return $this;
    }
}
