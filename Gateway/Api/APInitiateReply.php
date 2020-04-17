<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APInitiateReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $merchantURL
     */
    protected $merchantURL;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @var string $signature
     */
    protected $signature;

    /**
     * @var string $publicKey
     */
    protected $publicKey;

    /**
     * @var string $paymentStatus
     */
    protected $paymentStatus;

    /**
     * @var string $responseCode
     */
    protected $responseCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
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
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }
}
