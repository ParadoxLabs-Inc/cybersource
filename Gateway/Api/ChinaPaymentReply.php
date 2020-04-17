<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class ChinaPaymentReply
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
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $formData
     */
    protected $formData;

    /**
     * @var string $verifyFailure
     */
    protected $verifyFailure;

    /**
     * @var string $verifyInProcess
     */
    protected $verifyInProcess;

    /**
     * @var string $verifySuccess
     */
    protected $verifySuccess;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @param string $formData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
     */
    public function setFormData($formData)
    {
        $this->formData = $formData;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerifyFailure()
    {
        return $this->verifyFailure;
    }

    /**
     * @param string $verifyFailure
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
     */
    public function setVerifyFailure($verifyFailure)
    {
        $this->verifyFailure = $verifyFailure;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerifyInProcess()
    {
        return $this->verifyInProcess;
    }

    /**
     * @param string $verifyInProcess
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
     */
    public function setVerifyInProcess($verifyInProcess)
    {
        $this->verifyInProcess = $verifyInProcess;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerifySuccess()
    {
        return $this->verifySuccess;
    }

    /**
     * @param string $verifySuccess
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply
     */
    public function setVerifySuccess($verifySuccess)
    {
        $this->verifySuccess = $verifySuccess;

        return $this;
    }
}
