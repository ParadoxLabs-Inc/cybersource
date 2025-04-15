<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class CCCreditAuthReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $paymentNetworkTransactionId
     */
    protected $paymentNetworkTransactionId;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime;

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
     * @var string $paymentNetworkTransactionInformation
     */
    protected $paymentNetworkTransactionInformation;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentNetworkTransactionId()
    {
        return $this->paymentNetworkTransactionId;
    }

    /**
     * @param string $paymentNetworkTransactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setPaymentNetworkTransactionId($paymentNetworkTransactionId)
    {
        $this->paymentNetworkTransactionId = $paymentNetworkTransactionId;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @param \DateTime|null $requestDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setRequestDateTime(?DateTime $requestDateTime = null)
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setEnhancedDataEnabled($enhancedDataEnabled)
    {
        $this->enhancedDataEnabled = $enhancedDataEnabled;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setReconciliationReferenceNumber($reconciliationReferenceNumber)
    {
        $this->reconciliationReferenceNumber = $reconciliationReferenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentNetworkTransactionInformation()
    {
        return $this->paymentNetworkTransactionInformation;
    }

    /**
     * @param string $paymentNetworkTransactionInformation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply
     */
    public function setPaymentNetworkTransactionInformation($paymentNetworkTransactionInformation)
    {
        $this->paymentNetworkTransactionInformation = $paymentNetworkTransactionInformation;

        return $this;
    }
}
