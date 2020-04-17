<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class GiftCard
{
    /**
     * @var string $originalRequestID
     */
    protected $originalRequestID;

    /**
     * @var string $redemptionType
     */
    protected $redemptionType;

    /**
     * @var string $count
     */
    protected $count;

    /**
     * @var boolean $escheatable
     */
    protected $escheatable;

    /**
     * @var string $groupID
     */
    protected $groupID;

    /**
     * @var string $securityValue
     */
    protected $securityValue;

    /**
     * @var string $transactionPostingDate
     */
    protected $transactionPostingDate;

    /**
     * @var string $additionalAccountNumber
     */
    protected $additionalAccountNumber;

    /**
     * @var string $promoCode
     */
    protected $promoCode;

    /**
     * @var string $balanceCurrency
     */
    protected $balanceCurrency;

    /**
     * @var string $extendedAccountNumber
     */
    protected $extendedAccountNumber;

    /**
     * @var string $previousBalance
     */
    protected $previousBalance;

    /**
     * @var string $currentBalance
     */
    protected $currentBalance;

    /**
     * @var string $baseCurrencyPreviousBalance
     */
    protected $baseCurrencyPreviousBalance;

    /**
     * @var string $baseCurrencyCurrentBalance
     */
    protected $baseCurrencyCurrentBalance;

    /**
     * @var string $baseCurrencyCashbackAmount
     */
    protected $baseCurrencyCashbackAmount;

    /**
     * @var string $baseCurrency
     */
    protected $baseCurrency;

    /**
     * @var string $expirationDate
     */
    protected $expirationDate;

    /**
     * @var string $exchangeRate
     */
    protected $exchangeRate;

    /**
     * @var string $bonusAmount
     */
    protected $bonusAmount;

    /**
     * @var string $discountAmount
     */
    protected $discountAmount;

    /**
     * @return string
     */
    public function getOriginalRequestID()
    {
        return $this->originalRequestID;
    }

    /**
     * @param string $originalRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setOriginalRequestID($originalRequestID)
    {
        $this->originalRequestID = $originalRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedemptionType()
    {
        return $this->redemptionType;
    }

    /**
     * @param string $redemptionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setRedemptionType($redemptionType)
    {
        $this->redemptionType = $redemptionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param string $count
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEscheatable()
    {
        return $this->escheatable;
    }

    /**
     * @param boolean $escheatable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setEscheatable($escheatable)
    {
        $this->escheatable = $escheatable;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroupID()
    {
        return $this->groupID;
    }

    /**
     * @param string $groupID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setGroupID($groupID)
    {
        $this->groupID = $groupID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityValue()
    {
        return $this->securityValue;
    }

    /**
     * @param string $securityValue
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setSecurityValue($securityValue)
    {
        $this->securityValue = $securityValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionPostingDate()
    {
        return $this->transactionPostingDate;
    }

    /**
     * @param string $transactionPostingDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setTransactionPostingDate($transactionPostingDate)
    {
        $this->transactionPostingDate = $transactionPostingDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAccountNumber()
    {
        return $this->additionalAccountNumber;
    }

    /**
     * @param string $additionalAccountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setAdditionalAccountNumber($additionalAccountNumber)
    {
        $this->additionalAccountNumber = $additionalAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromoCode()
    {
        return $this->promoCode;
    }

    /**
     * @param string $promoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setPromoCode($promoCode)
    {
        $this->promoCode = $promoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBalanceCurrency()
    {
        return $this->balanceCurrency;
    }

    /**
     * @param string $balanceCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setBalanceCurrency($balanceCurrency)
    {
        $this->balanceCurrency = $balanceCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtendedAccountNumber()
    {
        return $this->extendedAccountNumber;
    }

    /**
     * @param string $extendedAccountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setExtendedAccountNumber($extendedAccountNumber)
    {
        $this->extendedAccountNumber = $extendedAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreviousBalance()
    {
        return $this->previousBalance;
    }

    /**
     * @param string $previousBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setPreviousBalance($previousBalance)
    {
        $this->previousBalance = $previousBalance;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentBalance()
    {
        return $this->currentBalance;
    }

    /**
     * @param string $currentBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setCurrentBalance($currentBalance)
    {
        $this->currentBalance = $currentBalance;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseCurrencyPreviousBalance()
    {
        return $this->baseCurrencyPreviousBalance;
    }

    /**
     * @param string $baseCurrencyPreviousBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setBaseCurrencyPreviousBalance($baseCurrencyPreviousBalance)
    {
        $this->baseCurrencyPreviousBalance = $baseCurrencyPreviousBalance;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseCurrencyCurrentBalance()
    {
        return $this->baseCurrencyCurrentBalance;
    }

    /**
     * @param string $baseCurrencyCurrentBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setBaseCurrencyCurrentBalance($baseCurrencyCurrentBalance)
    {
        $this->baseCurrencyCurrentBalance = $baseCurrencyCurrentBalance;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseCurrencyCashbackAmount()
    {
        return $this->baseCurrencyCashbackAmount;
    }

    /**
     * @param string $baseCurrencyCashbackAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setBaseCurrencyCashbackAmount($baseCurrencyCashbackAmount)
    {
        $this->baseCurrencyCashbackAmount = $baseCurrencyCashbackAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * @param string $baseCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setBaseCurrency($baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param string $expirationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getBonusAmount()
    {
        return $this->bonusAmount;
    }

    /**
     * @param string $bonusAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setBonusAmount($bonusAmount)
    {
        $this->bonusAmount = $bonusAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCard
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }
}
