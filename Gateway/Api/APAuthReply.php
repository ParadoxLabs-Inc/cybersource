<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APAuthReply
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
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $merchantURL
     */
    protected $merchantURL;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var string $orderId
     */
    protected $orderId;

    /**
     * @var string $orderStatus
     */
    protected $orderStatus;

    /**
     * @var string $completeRedirectURL
     */
    protected $completeRedirectURL;

    /**
     * @var \DateTime $updateDateTime
     */
    protected $updateDateTime;

    /**
     * @var \DateTime $expirationDateTime
     */
    protected $expirationDateTime;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @param \DateTime|null $dateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return string
     */
    public function getProviderResponse()
    {
        return $this->providerResponse;
    }

    /**
     * @param string $providerResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setMerchantURL($merchantURL)
    {
        $this->merchantURL = $merchantURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @param string $orderStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompleteRedirectURL()
    {
        return $this->completeRedirectURL;
    }

    /**
     * @param string $completeRedirectURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setCompleteRedirectURL($completeRedirectURL)
    {
        $this->completeRedirectURL = $completeRedirectURL;

        return $this;
    }

    /**
     * @return \DateTime|false
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
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
     * @return \DateTime|false
     */
    public function getExpirationDateTime()
    {
        if ($this->expirationDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->expirationDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime|null $expirationDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply
     */
    public function setExpirationDateTime(?DateTime $expirationDateTime = null)
    {
        if ($expirationDateTime == null) {
            $this->expirationDateTime = null;
        } else {
            $this->expirationDateTime = $expirationDateTime->format(DateTime::ATOM);
        }

        return $this;
    }
}
