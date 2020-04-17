<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class CCSaleReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $avsCode
     */
    protected $avsCode;

    /**
     * @var string $avsCodeRaw
     */
    protected $avsCodeRaw;

    /**
     * @var string $cvCode
     */
    protected $cvCode;

    /**
     * @var string $cvCodeRaw
     */
    protected $cvCodeRaw;

    /**
     * @var string $cavvResponseCode
     */
    protected $cavvResponseCode;

    /**
     * @var string $cavvResponseCodeRaw
     */
    protected $cavvResponseCodeRaw;

    /**
     * @var string $cardGroup
     */
    protected $cardGroup;

    /**
     * @var string $paymentNetworkTransactionID
     */
    protected $paymentNetworkTransactionID;

    /**
     * @var string $cardCategory
     */
    protected $cardCategory;

    /**
     * @var float $accountBalance
     */
    protected $accountBalance;

    /**
     * @var \DateTime $authorizedDateTime
     */
    protected $authorizedDateTime;

    /**
     * @var float $requestAmount
     */
    protected $requestAmount;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $accountBalanceCurrency
     */
    protected $accountBalanceCurrency;

    /**
     * @var string $accountBalanceSign
     */
    protected $accountBalanceSign;

    /**
     * @var string $cardReferenceData
     */
    protected $cardReferenceData;

    /**
     * @var string $partialPANandIBAN
     */
    protected $partialPANandIBAN;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCode()
    {
        return $this->avsCode;
    }

    /**
     * @param string $avsCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAvsCode($avsCode)
    {
        $this->avsCode = $avsCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCodeRaw()
    {
        return $this->avsCodeRaw;
    }

    /**
     * @param string $avsCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAvsCodeRaw($avsCodeRaw)
    {
        $this->avsCodeRaw = $avsCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvCode()
    {
        return $this->cvCode;
    }

    /**
     * @param string $cvCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setCvCode($cvCode)
    {
        $this->cvCode = $cvCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvCodeRaw()
    {
        return $this->cvCodeRaw;
    }

    /**
     * @param string $cvCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setCvCodeRaw($cvCodeRaw)
    {
        $this->cvCodeRaw = $cvCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCavvResponseCode()
    {
        return $this->cavvResponseCode;
    }

    /**
     * @param string $cavvResponseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setCavvResponseCode($cavvResponseCode)
    {
        $this->cavvResponseCode = $cavvResponseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCavvResponseCodeRaw()
    {
        return $this->cavvResponseCodeRaw;
    }

    /**
     * @param string $cavvResponseCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setCavvResponseCodeRaw($cavvResponseCodeRaw)
    {
        $this->cavvResponseCodeRaw = $cavvResponseCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardGroup()
    {
        return $this->cardGroup;
    }

    /**
     * @param string $cardGroup
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setCardGroup($cardGroup)
    {
        $this->cardGroup = $cardGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentNetworkTransactionID()
    {
        return $this->paymentNetworkTransactionID;
    }

    /**
     * @param string $paymentNetworkTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setPaymentNetworkTransactionID($paymentNetworkTransactionID)
    {
        $this->paymentNetworkTransactionID = $paymentNetworkTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardCategory()
    {
        return $this->cardCategory;
    }

    /**
     * @param string $cardCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setCardCategory($cardCategory)
    {
        $this->cardCategory = $cardCategory;

        return $this;
    }

    /**
     * @return float
     */
    public function getAccountBalance()
    {
        return $this->accountBalance;
    }

    /**
     * @param float $accountBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAccountBalance($accountBalance)
    {
        $this->accountBalance = $accountBalance;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAuthorizedDateTime()
    {
        if ($this->authorizedDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->authorizedDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $authorizedDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAuthorizedDateTime(DateTime $authorizedDateTime = null)
    {
        if ($authorizedDateTime == null) {
            $this->authorizedDateTime = null;
        } else {
            $this->authorizedDateTime = $authorizedDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getRequestAmount()
    {
        return $this->requestAmount;
    }

    /**
     * @param float $requestAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setRequestAmount($requestAmount)
    {
        $this->requestAmount = $requestAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountBalanceCurrency()
    {
        return $this->accountBalanceCurrency;
    }

    /**
     * @param string $accountBalanceCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAccountBalanceCurrency($accountBalanceCurrency)
    {
        $this->accountBalanceCurrency = $accountBalanceCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountBalanceSign()
    {
        return $this->accountBalanceSign;
    }

    /**
     * @param string $accountBalanceSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setAccountBalanceSign($accountBalanceSign)
    {
        $this->accountBalanceSign = $accountBalanceSign;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardReferenceData()
    {
        return $this->cardReferenceData;
    }

    /**
     * @param string $cardReferenceData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setCardReferenceData($cardReferenceData)
    {
        $this->cardReferenceData = $cardReferenceData;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartialPANandIBAN()
    {
        return $this->partialPANandIBAN;
    }

    /**
     * @param string $partialPANandIBAN
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply
     */
    public function setPartialPANandIBAN($partialPANandIBAN)
    {
        $this->partialPANandIBAN = $partialPANandIBAN;

        return $this;
    }
}
