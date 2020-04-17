<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class PaypalTransaction
{
    /**
     * @var \DateTime $transactionTime
     */
    protected $transactionTime;

    /**
     * @var string $transactionTimeZone
     */
    protected $transactionTimeZone;

    /**
     * @var string $transactionType
     */
    protected $transactionType;

    /**
     * @var string $paypalPayerOrPayeeEmail
     */
    protected $paypalPayerOrPayeeEmail;

    /**
     * @var string $customerDisplayName
     */
    protected $customerDisplayName;

    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var string $paypalPaymentStatus
     */
    protected $paypalPaymentStatus;

    /**
     * @var string $grandTotalAmount
     */
    protected $grandTotalAmount;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $paypalFeeAmount
     */
    protected $paypalFeeAmount;

    /**
     * @var string $paypalNetAmount
     */
    protected $paypalNetAmount;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getTransactionTime()
    {
        if ($this->transactionTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->transactionTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $transactionTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setTransactionTime(DateTime $transactionTime = null)
    {
        if ($transactionTime == null) {
            $this->transactionTime = null;
        } else {
            $this->transactionTime = $transactionTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionTimeZone()
    {
        return $this->transactionTimeZone;
    }

    /**
     * @param string $transactionTimeZone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setTransactionTimeZone($transactionTimeZone)
    {
        $this->transactionTimeZone = $transactionTimeZone;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPayerOrPayeeEmail()
    {
        return $this->paypalPayerOrPayeeEmail;
    }

    /**
     * @param string $paypalPayerOrPayeeEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setPaypalPayerOrPayeeEmail($paypalPayerOrPayeeEmail)
    {
        $this->paypalPayerOrPayeeEmail = $paypalPayerOrPayeeEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerDisplayName()
    {
        return $this->customerDisplayName;
    }

    /**
     * @param string $customerDisplayName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setCustomerDisplayName($customerDisplayName)
    {
        $this->customerDisplayName = $customerDisplayName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPaymentStatus()
    {
        return $this->paypalPaymentStatus;
    }

    /**
     * @param string $paypalPaymentStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setPaypalPaymentStatus($paypalPaymentStatus)
    {
        $this->paypalPaymentStatus = $paypalPaymentStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getGrandTotalAmount()
    {
        return $this->grandTotalAmount;
    }

    /**
     * @param string $grandTotalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setGrandTotalAmount($grandTotalAmount)
    {
        $this->grandTotalAmount = $grandTotalAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalFeeAmount()
    {
        return $this->paypalFeeAmount;
    }

    /**
     * @param string $paypalFeeAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setPaypalFeeAmount($paypalFeeAmount)
    {
        $this->paypalFeeAmount = $paypalFeeAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalNetAmount()
    {
        return $this->paypalNetAmount;
    }

    /**
     * @param string $paypalNetAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setPaypalNetAmount($paypalNetAmount)
    {
        $this->paypalNetAmount = $paypalNetAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
