<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class CCCreditReply
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
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $purchasingLevel3Enabled
     */
    protected $purchasingLevel3Enabled;

    /**
     * @var string $enhancedDataEnabled
     */
    protected $enhancedDataEnabled;

    /**
     * @var string $authorizationXID
     */
    protected $authorizationXID;

    /**
     * @var string $forwardCode
     */
    protected $forwardCode;

    /**
     * @var string $ownerMerchantID
     */
    protected $ownerMerchantID;

    /**
     * @var string $reconciliationReferenceNumber
     */
    protected $reconciliationReferenceNumber;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $paymentNetworkTransactionID
     */
    protected $paymentNetworkTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
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
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPurchasingLevel3Enabled()
    {
        return $this->purchasingLevel3Enabled;
    }

    /**
     * @param string $purchasingLevel3Enabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setPurchasingLevel3Enabled($purchasingLevel3Enabled)
    {
        $this->purchasingLevel3Enabled = $purchasingLevel3Enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnhancedDataEnabled()
    {
        return $this->enhancedDataEnabled;
    }

    /**
     * @param string $enhancedDataEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setEnhancedDataEnabled($enhancedDataEnabled)
    {
        $this->enhancedDataEnabled = $enhancedDataEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationXID()
    {
        return $this->authorizationXID;
    }

    /**
     * @param string $authorizationXID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setAuthorizationXID($authorizationXID)
    {
        $this->authorizationXID = $authorizationXID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setForwardCode($forwardCode)
    {
        $this->forwardCode = $forwardCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerMerchantID()
    {
        return $this->ownerMerchantID;
    }

    /**
     * @param string $ownerMerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setOwnerMerchantID($ownerMerchantID)
    {
        $this->ownerMerchantID = $ownerMerchantID;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationReferenceNumber()
    {
        return $this->reconciliationReferenceNumber;
    }

    /**
     * @param string $reconciliationReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setReconciliationReferenceNumber($reconciliationReferenceNumber)
    {
        $this->reconciliationReferenceNumber = $reconciliationReferenceNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply
     */
    public function setPaymentNetworkTransactionID($paymentNetworkTransactionID)
    {
        $this->paymentNetworkTransactionID = $paymentNetworkTransactionID;

        return $this;
    }
}
