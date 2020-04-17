<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Card
{
    /**
     * @var string $fullName
     */
    protected $fullName;

    /**
     * @var string $accountNumber
     */
    protected $accountNumber;

    /**
     * @var int $expirationMonth
     */
    protected $expirationMonth;

    /**
     * @var int $expirationYear
     */
    protected $expirationYear;

    /**
     * @var string $cvIndicator
     */
    protected $cvIndicator;

    /**
     * @var string $cvNumber
     */
    protected $cvNumber;

    /**
     * @var string $cardType
     */
    protected $cardType;

    /**
     * @var string $issueNumber
     */
    protected $issueNumber;

    /**
     * @var int $startMonth
     */
    protected $startMonth;

    /**
     * @var int $startYear
     */
    protected $startYear;

    /**
     * @var string $pin
     */
    protected $pin;

    /**
     * @var string $accountEncoderID
     */
    protected $accountEncoderID;

    /**
     * @var string $bin
     */
    protected $bin;

    /**
     * @var string $encryptedData
     */
    protected $encryptedData;

    /**
     * @var string $suffix
     */
    protected $suffix;

    /**
     * @var boolean $virtual
     */
    protected $virtual;

    /**
     * @var string $prefix
     */
    protected $prefix;

    /**
     * @var string $cardTypeName
     */
    protected $cardTypeName;

    /**
     * @var string $cardSubType
     */
    protected $cardSubType;

    /**
     * @var string $level2Eligible
     */
    protected $level2Eligible;

    /**
     * @var string $level3Eligible
     */
    protected $level3Eligible;

    /**
     * @var string $productCategory
     */
    protected $productCategory;

    /**
     * @var string $crossBorderIndicator
     */
    protected $crossBorderIndicator;

    /**
     * @var string $billingCurrency
     */
    protected $billingCurrency;

    /**
     * @var string $billingCurrencyNumericCode
     */
    protected $billingCurrencyNumericCode;

    /**
     * @var string $billingCurrencyMinorDigits
     */
    protected $billingCurrencyMinorDigits;

    /**
     * @var string $octFastFundsIndicator
     */
    protected $octFastFundsIndicator;

    /**
     * @var string $octBlockIndicator
     */
    protected $octBlockIndicator;

    /**
     * @var string $onlineGamblingBlockIndicator
     */
    protected $onlineGamblingBlockIndicator;

    /**
     * @var string $productName
     */
    protected $productName;

    /**
     * @var string $usage
     */
    protected $usage;

    /**
     * @var string $prepaidReloadable
     */
    protected $prepaidReloadable;

    /**
     * @var string $prepaidType
     */
    protected $prepaidType;

    /**
     * @var Brands[] $brands
     */
    protected $brands;

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpirationMonth()
    {
        return $this->expirationMonth;
    }

    /**
     * @param int $expirationMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setExpirationMonth($expirationMonth)
    {
        $this->expirationMonth = $expirationMonth;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpirationYear()
    {
        return $this->expirationYear;
    }

    /**
     * @param int $expirationYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setExpirationYear($expirationYear)
    {
        $this->expirationYear = $expirationYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvIndicator()
    {
        return $this->cvIndicator;
    }

    /**
     * @param string $cvIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setCvIndicator($cvIndicator)
    {
        $this->cvIndicator = $cvIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvNumber()
    {
        return $this->cvNumber;
    }

    /**
     * @param string $cvNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setCvNumber($cvNumber)
    {
        $this->cvNumber = $cvNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssueNumber()
    {
        return $this->issueNumber;
    }

    /**
     * @param string $issueNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setIssueNumber($issueNumber)
    {
        $this->issueNumber = $issueNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getStartMonth()
    {
        return $this->startMonth;
    }

    /**
     * @param int $startMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setStartMonth($startMonth)
    {
        $this->startMonth = $startMonth;

        return $this;
    }

    /**
     * @return int
     */
    public function getStartYear()
    {
        return $this->startYear;
    }

    /**
     * @param int $startYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setStartYear($startYear)
    {
        $this->startYear = $startYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param string $pin
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountEncoderID()
    {
        return $this->accountEncoderID;
    }

    /**
     * @param string $accountEncoderID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setAccountEncoderID($accountEncoderID)
    {
        $this->accountEncoderID = $accountEncoderID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * @param string $bin
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setBin($bin)
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedData()
    {
        return $this->encryptedData;
    }

    /**
     * @param string $encryptedData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setEncryptedData($encryptedData)
    {
        $this->encryptedData = $encryptedData;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getVirtual()
    {
        return $this->virtual;
    }

    /**
     * @param boolean $virtual
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setVirtual($virtual)
    {
        $this->virtual = $virtual;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardTypeName()
    {
        return $this->cardTypeName;
    }

    /**
     * @param string $cardTypeName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setCardTypeName($cardTypeName)
    {
        $this->cardTypeName = $cardTypeName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardSubType()
    {
        return $this->cardSubType;
    }

    /**
     * @param string $cardSubType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setCardSubType($cardSubType)
    {
        $this->cardSubType = $cardSubType;

        return $this;
    }

    /**
     * @return string
     */
    public function getLevel2Eligible()
    {
        return $this->level2Eligible;
    }

    /**
     * @param string $level2Eligible
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setLevel2Eligible($level2Eligible)
    {
        $this->level2Eligible = $level2Eligible;

        return $this;
    }

    /**
     * @return string
     */
    public function getLevel3Eligible()
    {
        return $this->level3Eligible;
    }

    /**
     * @param string $level3Eligible
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setLevel3Eligible($level3Eligible)
    {
        $this->level3Eligible = $level3Eligible;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductCategory()
    {
        return $this->productCategory;
    }

    /**
     * @param string $productCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setProductCategory($productCategory)
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getCrossBorderIndicator()
    {
        return $this->crossBorderIndicator;
    }

    /**
     * @param string $crossBorderIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setCrossBorderIndicator($crossBorderIndicator)
    {
        $this->crossBorderIndicator = $crossBorderIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingCurrency()
    {
        return $this->billingCurrency;
    }

    /**
     * @param string $billingCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setBillingCurrency($billingCurrency)
    {
        $this->billingCurrency = $billingCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingCurrencyNumericCode()
    {
        return $this->billingCurrencyNumericCode;
    }

    /**
     * @param string $billingCurrencyNumericCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setBillingCurrencyNumericCode($billingCurrencyNumericCode)
    {
        $this->billingCurrencyNumericCode = $billingCurrencyNumericCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingCurrencyMinorDigits()
    {
        return $this->billingCurrencyMinorDigits;
    }

    /**
     * @param string $billingCurrencyMinorDigits
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setBillingCurrencyMinorDigits($billingCurrencyMinorDigits)
    {
        $this->billingCurrencyMinorDigits = $billingCurrencyMinorDigits;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctFastFundsIndicator()
    {
        return $this->octFastFundsIndicator;
    }

    /**
     * @param string $octFastFundsIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctFastFundsIndicator($octFastFundsIndicator)
    {
        $this->octFastFundsIndicator = $octFastFundsIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctBlockIndicator()
    {
        return $this->octBlockIndicator;
    }

    /**
     * @param string $octBlockIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctBlockIndicator($octBlockIndicator)
    {
        $this->octBlockIndicator = $octBlockIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnlineGamblingBlockIndicator()
    {
        return $this->onlineGamblingBlockIndicator;
    }

    /**
     * @param string $onlineGamblingBlockIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOnlineGamblingBlockIndicator($onlineGamblingBlockIndicator)
    {
        $this->onlineGamblingBlockIndicator = $onlineGamblingBlockIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @param string $usage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrepaidReloadable()
    {
        return $this->prepaidReloadable;
    }

    /**
     * @param string $prepaidReloadable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setPrepaidReloadable($prepaidReloadable)
    {
        $this->prepaidReloadable = $prepaidReloadable;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrepaidType()
    {
        return $this->prepaidType;
    }

    /**
     * @param string $prepaidType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setPrepaidType($prepaidType)
    {
        $this->prepaidType = $prepaidType;

        return $this;
    }

    /**
     * @return Brands[]
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param Brands[] $brands
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setBrands(array $brands = null)
    {
        $this->brands = $brands;

        return $this;
    }
}
