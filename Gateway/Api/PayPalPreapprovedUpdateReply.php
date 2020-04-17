<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class PayPalPreapprovedUpdateReply
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
     * @var string $payerStatus
     */
    protected $payerStatus;

    /**
     * @var string $payerName
     */
    protected $payerName;

    /**
     * @var string $payerCountry
     */
    protected $payerCountry;

    /**
     * @var string $mpStatus
     */
    protected $mpStatus;

    /**
     * @var string $payer
     */
    protected $payer;

    /**
     * @var string $payerID
     */
    protected $payerID;

    /**
     * @var string $payerBusiness
     */
    protected $payerBusiness;

    /**
     * @var string $desc
     */
    protected $desc;

    /**
     * @var string $mpMax
     */
    protected $mpMax;

    /**
     * @var string $paymentSourceID
     */
    protected $paymentSourceID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerStatus()
    {
        return $this->payerStatus;
    }

    /**
     * @param string $payerStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setPayerStatus($payerStatus)
    {
        $this->payerStatus = $payerStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerName()
    {
        return $this->payerName;
    }

    /**
     * @param string $payerName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setPayerName($payerName)
    {
        $this->payerName = $payerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerCountry()
    {
        return $this->payerCountry;
    }

    /**
     * @param string $payerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setPayerCountry($payerCountry)
    {
        $this->payerCountry = $payerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getMpStatus()
    {
        return $this->mpStatus;
    }

    /**
     * @param string $mpStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setMpStatus($mpStatus)
    {
        $this->mpStatus = $mpStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param string $payer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerID()
    {
        return $this->payerID;
    }

    /**
     * @param string $payerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setPayerID($payerID)
    {
        $this->payerID = $payerID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerBusiness()
    {
        return $this->payerBusiness;
    }

    /**
     * @param string $payerBusiness
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setPayerBusiness($payerBusiness)
    {
        $this->payerBusiness = $payerBusiness;

        return $this;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * @return string
     */
    public function getMpMax()
    {
        return $this->mpMax;
    }

    /**
     * @param string $mpMax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setMpMax($mpMax)
    {
        $this->mpMax = $mpMax;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentSourceID()
    {
        return $this->paymentSourceID;
    }

    /**
     * @param string $paymentSourceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply
     */
    public function setPaymentSourceID($paymentSourceID)
    {
        $this->paymentSourceID = $paymentSourceID;

        return $this;
    }
}
