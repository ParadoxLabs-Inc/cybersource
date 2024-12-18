<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APCheckStatusReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $paymentStatus
     */
    protected $paymentStatus;

    /**
     * @var string $processorTradeNo
     */
    protected $processorTradeNo;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var string $ibanSuffix
     */
    protected $ibanSuffix;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $processorToken
     */
    protected $processorToken;

    /**
     * @var string $fundingSource
     */
    protected $fundingSource;

    /**
     * @var string $payment_ewalletName
     */
    protected $payment_ewalletName;

    /**
     * @var string $card_issuerCode
     */
    protected $card_issuerCode;

    /**
     * @var string $card_issuerName
     */
    protected $card_issuerName;

    /**
     * @var string $refundBalance
     */
    protected $refundBalance;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorTradeNo()
    {
        return $this->processorTradeNo;
    }

    /**
     * @param string $processorTradeNo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setProcessorTradeNo($processorTradeNo)
    {
        $this->processorTradeNo = $processorTradeNo;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getIbanSuffix()
    {
        return $this->ibanSuffix;
    }

    /**
     * @param string $ibanSuffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setIbanSuffix($ibanSuffix)
    {
        $this->ibanSuffix = $ibanSuffix;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorToken()
    {
        return $this->processorToken;
    }

    /**
     * @param string $processorToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setProcessorToken($processorToken)
    {
        $this->processorToken = $processorToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getFundingSource()
    {
        return $this->fundingSource;
    }

    /**
     * @param string $fundingSource
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setFundingSource($fundingSource)
    {
        $this->fundingSource = $fundingSource;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayment_ewalletName()
    {
        return $this->payment_ewalletName;
    }

    /**
     * @param string $payment_ewalletName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setPayment_ewalletName($payment_ewalletName)
    {
        $this->payment_ewalletName = $payment_ewalletName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCard_issuerCode()
    {
        return $this->card_issuerCode;
    }

    /**
     * @param string $card_issuerCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setCard_issuerCode($card_issuerCode)
    {
        $this->card_issuerCode = $card_issuerCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCard_issuerName()
    {
        return $this->card_issuerName;
    }

    /**
     * @param string $card_issuerName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setCard_issuerName($card_issuerName)
    {
        $this->card_issuerName = $card_issuerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefundBalance()
    {
        return $this->refundBalance;
    }

    /**
     * @param string $refundBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setRefundBalance($refundBalance)
    {
        $this->refundBalance = $refundBalance;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
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
     * @return \DateTime
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
     * @param \DateTime $updateDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setUpdateDateTime(DateTime $updateDateTime = null)
    {
        if ($updateDateTime == null) {
            $this->updateDateTime = null;
        } else {
            $this->updateDateTime = $updateDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return \DateTime
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
     * @param \DateTime $expirationDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply
     */
    public function setExpirationDateTime(DateTime $expirationDateTime = null)
    {
        if ($expirationDateTime == null) {
            $this->expirationDateTime = null;
        } else {
            $this->expirationDateTime = $expirationDateTime->format(DateTime::ATOM);
        }

        return $this;
    }
}
