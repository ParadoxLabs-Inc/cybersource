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
     * @var string $fastFundsBusinessFundedTransferDomestic
     */
    protected $fastFundsBusinessFundedTransferDomestic;

    /**
     * @var string $fastFundsBusinessFundedTransferCrossBorder
     */
    protected $fastFundsBusinessFundedTransferCrossBorder;

    /**
     * @var string $fastFundsConsumerFundedTransferDomestic
     */
    protected $fastFundsConsumerFundedTransferDomestic;

    /**
     * @var string $fastFundsConsumerFundedTransferCrossBorder
     */
    protected $fastFundsConsumerFundedTransferCrossBorder;

    /**
     * @var string $octBusinessFundedTransferDomestic
     */
    protected $octBusinessFundedTransferDomestic;

    /**
     * @var string $octBusinessFundedTransferCrossBorder
     */
    protected $octBusinessFundedTransferCrossBorder;

    /**
     * @var string $octConsumerFundedTransferDomestic
     */
    protected $octConsumerFundedTransferDomestic;

    /**
     * @var string $octConsumerFundedTransferCrossBorder
     */
    protected $octConsumerFundedTransferCrossBorder;

    /**
     * @var string $octGamblingDomestic
     */
    protected $octGamblingDomestic;

    /**
     * @var string $octGamblingCrossBorder
     */
    protected $octGamblingCrossBorder;

    /**
     * @var string $fastFundsGamblingDomestic
     */
    protected $fastFundsGamblingDomestic;

    /**
     * @var string $fastFundsGamblingCrossBorder
     */
    protected $fastFundsGamblingCrossBorder;

    /**
     * @var string $octGeoRestrictionIndicator
     */
    protected $octGeoRestrictionIndicator;

    /**
     * @var string $comboCardType
     */
    protected $comboCardType;

    /**
     * @var string $prepaidIndicator
     */
    protected $prepaidIndicator;

    /**
     * @var string $passPhrase
     */
    protected $passPhrase;

    /**
     * @var string $personalData
     */
    protected $personalData;

    /**
     * @var string $hashedAccountNumber
     */
    protected $hashedAccountNumber;

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

    /**
     * @return string
     */
    public function getFastFundsBusinessFundedTransferDomestic()
    {
        return $this->fastFundsBusinessFundedTransferDomestic;
    }

    /**
     * @param string $fastFundsBusinessFundedTransferDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setFastFundsBusinessFundedTransferDomestic($fastFundsBusinessFundedTransferDomestic)
    {
        $this->fastFundsBusinessFundedTransferDomestic = $fastFundsBusinessFundedTransferDomestic;

        return $this;
    }

    /**
     * @return string
     */
    public function getFastFundsBusinessFundedTransferCrossBorder()
    {
        return $this->fastFundsBusinessFundedTransferCrossBorder;
    }

    /**
     * @param string $fastFundsBusinessFundedTransferCrossBorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setFastFundsBusinessFundedTransferCrossBorder($fastFundsBusinessFundedTransferCrossBorder)
    {
        $this->fastFundsBusinessFundedTransferCrossBorder = $fastFundsBusinessFundedTransferCrossBorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getFastFundsConsumerFundedTransferDomestic()
    {
        return $this->fastFundsConsumerFundedTransferDomestic;
    }

    /**
     * @param string $fastFundsConsumerFundedTransferDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setFastFundsConsumerFundedTransferDomestic($fastFundsConsumerFundedTransferDomestic)
    {
        $this->fastFundsConsumerFundedTransferDomestic = $fastFundsConsumerFundedTransferDomestic;

        return $this;
    }

    /**
     * @return string
     */
    public function getFastFundsConsumerFundedTransferCrossBorder()
    {
        return $this->fastFundsConsumerFundedTransferCrossBorder;
    }

    /**
     * @param string $fastFundsConsumerFundedTransferCrossBorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setFastFundsConsumerFundedTransferCrossBorder($fastFundsConsumerFundedTransferCrossBorder)
    {
        $this->fastFundsConsumerFundedTransferCrossBorder = $fastFundsConsumerFundedTransferCrossBorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctBusinessFundedTransferDomestic()
    {
        return $this->octBusinessFundedTransferDomestic;
    }

    /**
     * @param string $octBusinessFundedTransferDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctBusinessFundedTransferDomestic($octBusinessFundedTransferDomestic)
    {
        $this->octBusinessFundedTransferDomestic = $octBusinessFundedTransferDomestic;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctBusinessFundedTransferCrossBorder()
    {
        return $this->octBusinessFundedTransferCrossBorder;
    }

    /**
     * @param string $octBusinessFundedTransferCrossBorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctBusinessFundedTransferCrossBorder($octBusinessFundedTransferCrossBorder)
    {
        $this->octBusinessFundedTransferCrossBorder = $octBusinessFundedTransferCrossBorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctConsumerFundedTransferDomestic()
    {
        return $this->octConsumerFundedTransferDomestic;
    }

    /**
     * @param string $octConsumerFundedTransferDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctConsumerFundedTransferDomestic($octConsumerFundedTransferDomestic)
    {
        $this->octConsumerFundedTransferDomestic = $octConsumerFundedTransferDomestic;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctConsumerFundedTransferCrossBorder()
    {
        return $this->octConsumerFundedTransferCrossBorder;
    }

    /**
     * @param string $octConsumerFundedTransferCrossBorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctConsumerFundedTransferCrossBorder($octConsumerFundedTransferCrossBorder)
    {
        $this->octConsumerFundedTransferCrossBorder = $octConsumerFundedTransferCrossBorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctGamblingDomestic()
    {
        return $this->octGamblingDomestic;
    }

    /**
     * @param string $octGamblingDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctGamblingDomestic($octGamblingDomestic)
    {
        $this->octGamblingDomestic = $octGamblingDomestic;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctGamblingCrossBorder()
    {
        return $this->octGamblingCrossBorder;
    }

    /**
     * @param string $octGamblingCrossBorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctGamblingCrossBorder($octGamblingCrossBorder)
    {
        $this->octGamblingCrossBorder = $octGamblingCrossBorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getFastFundsGamblingDomestic()
    {
        return $this->fastFundsGamblingDomestic;
    }

    /**
     * @param string $fastFundsGamblingDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setFastFundsGamblingDomestic($fastFundsGamblingDomestic)
    {
        $this->fastFundsGamblingDomestic = $fastFundsGamblingDomestic;

        return $this;
    }

    /**
     * @return string
     */
    public function getFastFundsGamblingCrossBorder()
    {
        return $this->fastFundsGamblingCrossBorder;
    }

    /**
     * @param string $fastFundsGamblingCrossBorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setFastFundsGamblingCrossBorder($fastFundsGamblingCrossBorder)
    {
        $this->fastFundsGamblingCrossBorder = $fastFundsGamblingCrossBorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctGeoRestrictionIndicator()
    {
        return $this->octGeoRestrictionIndicator;
    }

    /**
     * @param string $octGeoRestrictionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setOctGeoRestrictionIndicator($octGeoRestrictionIndicator)
    {
        $this->octGeoRestrictionIndicator = $octGeoRestrictionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getComboCardType()
    {
        return $this->comboCardType;
    }

    /**
     * @param string $comboCardType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setComboCardType($comboCardType)
    {
        $this->comboCardType = $comboCardType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrepaidIndicator()
    {
        return $this->prepaidIndicator;
    }

    /**
     * @param string $prepaidIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setPrepaidIndicator($prepaidIndicator)
    {
        $this->prepaidIndicator = $prepaidIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    /**
     * @param string $passPhrase
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setPassPhrase($passPhrase)
    {
        $this->passPhrase = $passPhrase;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalData()
    {
        return $this->personalData;
    }

    /**
     * @param string $personalData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setPersonalData($personalData)
    {
        $this->personalData = $personalData;

        return $this;
    }

    /**
     * @return string
     */
    public function getHashedAccountNumber()
    {
        return $this->hashedAccountNumber;
    }

    /**
     * @param string $hashedAccountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function setHashedAccountNumber($hashedAccountNumber)
    {
        $this->hashedAccountNumber = $hashedAccountNumber;

        return $this;
    }
}
