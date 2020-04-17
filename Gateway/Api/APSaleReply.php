<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APSaleReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $paymentStatus
     */
    protected $paymentStatus;

    /**
     * @var string $responseCode
     */
    protected $responseCode;

    /**
     * @var string $merchantURL
     */
    protected $merchantURL;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $processorTransactionFee
     */
    protected $processorTransactionFee;

    /**
     * @var string $amount
     */
    protected $amount;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $exchangeRate
     */
    protected $exchangeRate;

    /**
     * @var string $foreignCurrency
     */
    protected $foreignCurrency;

    /**
     * @var string $foreignAmount
     */
    protected $foreignAmount;

    /**
     * @var string $discountAmount
     */
    protected $discountAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setMerchantURL($merchantURL)
    {
        $this->merchantURL = $merchantURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorTransactionFee()
    {
        return $this->processorTransactionFee;
    }

    /**
     * @param string $processorTransactionFee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setProcessorTransactionFee($processorTransactionFee)
    {
        $this->processorTransactionFee = $processorTransactionFee;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param string $exchangeRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getForeignCurrency()
    {
        return $this->foreignCurrency;
    }

    /**
     * @param string $foreignCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setForeignCurrency($foreignCurrency)
    {
        $this->foreignCurrency = $foreignCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getForeignAmount()
    {
        return $this->foreignAmount;
    }

    /**
     * @param string $foreignAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setForeignAmount($foreignAmount)
    {
        $this->foreignAmount = $foreignAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param string $discountAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply
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
