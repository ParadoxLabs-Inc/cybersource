<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class ECCreditReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var string $settlementMethod
     */
    protected $settlementMethod = null;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime = null;

    /**
     * @var float $amount
     */
    protected $amount = null;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID = null;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID = null;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse = null;

    /**
     * @var string $verificationCode
     */
    protected $verificationCode = null;

    /**
     * @var string $verificationCodeRaw
     */
    protected $verificationCodeRaw = null;

    /**
     * @var string $correctedAccountNumber
     */
    protected $correctedAccountNumber = null;

    /**
     * @var string $correctedRoutingNumber
     */
    protected $correctedRoutingNumber = null;

    /**
     * @var string $ownerMerchantID
     */
    protected $ownerMerchantID = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSettlementMethod()
    {
        return $this->settlementMethod;
    }

    /**
     * @param string $settlementMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setSettlementMethod($settlementMethod)
    {
        $this->settlementMethod = $settlementMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * @param string $verificationCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationCodeRaw()
    {
        return $this->verificationCodeRaw;
    }

    /**
     * @param string $verificationCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setVerificationCodeRaw($verificationCodeRaw)
    {
        $this->verificationCodeRaw = $verificationCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrectedAccountNumber()
    {
        return $this->correctedAccountNumber;
    }

    /**
     * @param string $correctedAccountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setCorrectedAccountNumber($correctedAccountNumber)
    {
        $this->correctedAccountNumber = $correctedAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrectedRoutingNumber()
    {
        return $this->correctedRoutingNumber;
    }

    /**
     * @param string $correctedRoutingNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setCorrectedRoutingNumber($correctedRoutingNumber)
    {
        $this->correctedRoutingNumber = $correctedRoutingNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply
     */
    public function setOwnerMerchantID($ownerMerchantID)
    {
        $this->ownerMerchantID = $ownerMerchantID;

        return $this;
    }

}
