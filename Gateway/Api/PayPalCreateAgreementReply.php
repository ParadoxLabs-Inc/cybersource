<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalCreateAgreementReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $paypalBillingAgreementId
     */
    protected $paypalBillingAgreementId;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

    /**
     * @var string $correlationID
     */
    protected $correlationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreateAgreementReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementId()
    {
        return $this->paypalBillingAgreementId;
    }

    /**
     * @param string $paypalBillingAgreementId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreateAgreementReply
     */
    public function setPaypalBillingAgreementId($paypalBillingAgreementId)
    {
        $this->paypalBillingAgreementId = $paypalBillingAgreementId;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreateAgreementReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrelationID()
    {
        return $this->correlationID;
    }

    /**
     * @param string $correlationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreateAgreementReply
     */
    public function setCorrelationID($correlationID)
    {
        $this->correlationID = $correlationID;

        return $this;
    }
}
