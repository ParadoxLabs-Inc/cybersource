<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class PinDebitPurchaseReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $networkCode
     */
    protected $networkCode;

    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var float $requestAmount
     */
    protected $requestAmount;

    /**
     * @var string $requestCurrency
     */
    protected $requestCurrency;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @var string $accountType
     */
    protected $accountType;

    /**
     * @var string $amountType
     */
    protected $amountType;

    /**
     * @var string $accountBalance
     */
    protected $accountBalance;

    /**
     * @var string $accountBalanceCurrency
     */
    protected $accountBalanceCurrency;

    /**
     * @var string $accountBalanceSign
     */
    protected $accountBalanceSign;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkCode()
    {
        return $this->networkCode;
    }

    /**
     * @param string $networkCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setNetworkCode($networkCode)
    {
        $this->networkCode = $networkCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setRequestAmount($requestAmount)
    {
        $this->requestAmount = $requestAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestCurrency()
    {
        return $this->requestCurrency;
    }

    /**
     * @param string $requestCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setRequestCurrency($requestCurrency)
    {
        $this->requestCurrency = $requestCurrency;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
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
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * @param string $amountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountBalance()
    {
        return $this->accountBalance;
    }

    /**
     * @param string $accountBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setAccountBalance($accountBalance)
    {
        $this->accountBalance = $accountBalance;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply
     */
    public function setAccountBalanceSign($accountBalanceSign)
    {
        $this->accountBalanceSign = $accountBalanceSign;

        return $this;
    }
}
