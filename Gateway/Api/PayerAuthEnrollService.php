<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayerAuthEnrollService
{

    /**
     * @var string $httpAccept
     */
    protected $httpAccept = null;

    /**
     * @var string $httpUserAgent
     */
    protected $httpUserAgent = null;

    /**
     * @var string $merchantName
     */
    protected $merchantName = null;

    /**
     * @var string $merchantURL
     */
    protected $merchantURL = null;

    /**
     * @var string $purchaseDescription
     */
    protected $purchaseDescription = null;

    /**
     * @var \DateTime $purchaseTime
     */
    protected $purchaseTime = null;

    /**
     * @var string $countryCode
     */
    protected $countryCode = null;

    /**
     * @var string $acquirerBin
     */
    protected $acquirerBin = null;

    /**
     * @var string $loginID
     */
    protected $loginID = null;

    /**
     * @var string $password
     */
    protected $password = null;

    /**
     * @var string $merchantID
     */
    protected $merchantID = null;

    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod = null;

    /**
     * @var string $mobilePhone
     */
    protected $mobilePhone = null;

    /**
     * @var string $MCC
     */
    protected $MCC = null;

    /**
     * @var string $productCode
     */
    protected $productCode = null;

    /**
     * @var string $referenceID
     */
    protected $referenceID = null;

    /**
     * @var boolean $marketingOptIn
     */
    protected $marketingOptIn = null;

    /**
     * @var string $marketingSource
     */
    protected $marketingSource = null;

    /**
     * @var boolean $defaultCard
     */
    protected $defaultCard = null;

    /**
     * @var string $shipAddressUsageDate
     */
    protected $shipAddressUsageDate = null;

    /**
     * @var string $transactionCountDay
     */
    protected $transactionCountDay = null;

    /**
     * @var string $transactionCountYear
     */
    protected $transactionCountYear = null;

    /**
     * @var string $addCardAttempts
     */
    protected $addCardAttempts = null;

    /**
     * @var string $accountPurchases
     */
    protected $accountPurchases = null;

    /**
     * @var boolean $fraudActivity
     */
    protected $fraudActivity = null;

    /**
     * @var string $paymentAccountDate
     */
    protected $paymentAccountDate = null;

    /**
     * @var string $alternateAuthenticationMethod
     */
    protected $alternateAuthenticationMethod = null;

    /**
     * @var string $alternateAuthenticationDate
     */
    protected $alternateAuthenticationDate = null;

    /**
     * @var string $alternateAuthenticationData
     */
    protected $alternateAuthenticationData = null;

    /**
     * @var boolean $challengeRequired
     */
    protected $challengeRequired = null;

    /**
     * @var string $challengeCode
     */
    protected $challengeCode = null;

    /**
     * @var string $preorder
     */
    protected $preorder = null;

    /**
     * @var string $reorder
     */
    protected $reorder = null;

    /**
     * @var string $preorderDate
     */
    protected $preorderDate = null;

    /**
     * @var string $giftCardAmount
     */
    protected $giftCardAmount = null;

    /**
     * @var string $giftCardCurrency
     */
    protected $giftCardCurrency = null;

    /**
     * @var string $giftCardCount
     */
    protected $giftCardCount = null;

    /**
     * @var string $messageCategory
     */
    protected $messageCategory = null;

    /**
     * @var string $npaCode
     */
    protected $npaCode = null;

    /**
     * @var string $recurringOriginalPurchaseDate
     */
    protected $recurringOriginalPurchaseDate = null;

    /**
     * @var string $transactionMode
     */
    protected $transactionMode = null;

    /**
     * @var string $recurringEndDate
     */
    protected $recurringEndDate = null;

    /**
     * @var string $recurringFrequency
     */
    protected $recurringFrequency = null;

    /**
     * @var string $merchantNewCustomer
     */
    protected $merchantNewCustomer = null;

    /**
     * @var string $customerCCAlias
     */
    protected $customerCCAlias = null;

    /**
     * @var string $installmentTotalCount
     */
    protected $installmentTotalCount = null;

    /**
     * @var string $authenticationTransactionID
     */
    protected $authenticationTransactionID = null;

    /**
     * @var string $httpUserAccept
     */
    protected $httpUserAccept = null;

    /**
     * @var string $mobilePhoneDomestic
     */
    protected $mobilePhoneDomestic = null;

    /**
     * @var string $pareqChannel
     */
    protected $pareqChannel = null;

    /**
     * @var string $shoppingChannel
     */
    protected $shoppingChannel = null;

    /**
     * @var string $authenticationChannel
     */
    protected $authenticationChannel = null;

    /**
     * @var string $merchantTTPCredential
     */
    protected $merchantTTPCredential = null;

    /**
     * @var string $requestorID
     */
    protected $requestorID = null;

    /**
     * @var string $requestorName
     */
    protected $requestorName = null;

    /**
     * @var int $acsWindowSize
     */
    protected $acsWindowSize = null;

    /**
     * @var string $decoupledAuthenticationIndicator
     */
    protected $decoupledAuthenticationIndicator = null;

    /**
     * @var int $decoupledAuthenticationMaxTime
     */
    protected $decoupledAuthenticationMaxTime = null;

    /**
     * @var string $deviceChannel
     */
    protected $deviceChannel = null;

    /**
     * @var string $priorAuthenticationReferenceID
     */
    protected $priorAuthenticationReferenceID = null;

    /**
     * @var string $priorAuthenticationData
     */
    protected $priorAuthenticationData = null;

    /**
     * @var int $priorAuthenticationMethod
     */
    protected $priorAuthenticationMethod = null;

    /**
     * @var int $priorAuthenticationTime
     */
    protected $priorAuthenticationTime = null;

    /**
     * @var int $requestorInitiatedAuthenticationIndicator
     */
    protected $requestorInitiatedAuthenticationIndicator = null;

    /**
     * @var string $sdkMaxTimeout
     */
    protected $sdkMaxTimeout = null;

    /**
     * @var int $authenticationIndicator
     */
    protected $authenticationIndicator = null;

    /**
     * @var string $whiteListStatus
     */
    protected $whiteListStatus = null;

    /**
     * @var int $totalOffersCount
     */
    protected $totalOffersCount = null;

    /**
     * @var string $merchantScore
     */
    protected $merchantScore = null;

    /**
     * @var int $merchantFraudRate
     */
    protected $merchantFraudRate = null;

    /**
     * @var string $acquirerCountry
     */
    protected $acquirerCountry = null;

    /**
     * @var string $secureCorporatePaymentIndicator
     */
    protected $secureCorporatePaymentIndicator = null;

    /**
     * @var boolean $run
     */
    protected $run = null;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
      $this->run = $run;
    }

    /**
     * @return string
     */
    public function getHttpAccept()
    {
      return $this->httpAccept;
    }

    /**
     * @param string $httpAccept
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setHttpAccept($httpAccept)
    {
      $this->httpAccept = $httpAccept;
      return $this;
    }

    /**
     * @return string
     */
    public function getHttpUserAgent()
    {
      return $this->httpUserAgent;
    }

    /**
     * @param string $httpUserAgent
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setHttpUserAgent($httpUserAgent)
    {
      $this->httpUserAgent = $httpUserAgent;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantName()
    {
      return $this->merchantName;
    }

    /**
     * @param string $merchantName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMerchantName($merchantName)
    {
      $this->merchantName = $merchantName;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantURL()
    {
      return $this->merchantURL;
    }

    /**
     * @param string $merchantURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMerchantURL($merchantURL)
    {
      $this->merchantURL = $merchantURL;
      return $this;
    }

    /**
     * @return string
     */
    public function getPurchaseDescription()
    {
      return $this->purchaseDescription;
    }

    /**
     * @param string $purchaseDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPurchaseDescription($purchaseDescription)
    {
      $this->purchaseDescription = $purchaseDescription;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPurchaseTime()
    {
      if ($this->purchaseTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->purchaseTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $purchaseTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPurchaseTime(\DateTime $purchaseTime = null)
    {
      if ($purchaseTime == null) {
       $this->purchaseTime = null;
      } else {
        $this->purchaseTime = $purchaseTime->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
      return $this->countryCode;
    }

    /**
     * @param string $countryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setCountryCode($countryCode)
    {
      $this->countryCode = $countryCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getAcquirerBin()
    {
      return $this->acquirerBin;
    }

    /**
     * @param string $acquirerBin
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAcquirerBin($acquirerBin)
    {
      $this->acquirerBin = $acquirerBin;
      return $this;
    }

    /**
     * @return string
     */
    public function getLoginID()
    {
      return $this->loginID;
    }

    /**
     * @param string $loginID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setLoginID($loginID)
    {
      $this->loginID = $loginID;
      return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
      return $this->password;
    }

    /**
     * @param string $password
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPassword($password)
    {
      $this->password = $password;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantID()
    {
      return $this->merchantID;
    }

    /**
     * @param string $merchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMerchantID($merchantID)
    {
      $this->merchantID = $merchantID;
      return $this;
    }

    /**
     * @return string
     */
    public function getOverridePaymentMethod()
    {
      return $this->overridePaymentMethod;
    }

    /**
     * @param string $overridePaymentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
      $this->overridePaymentMethod = $overridePaymentMethod;
      return $this;
    }

    /**
     * @return string
     */
    public function getMobilePhone()
    {
      return $this->mobilePhone;
    }

    /**
     * @param string $mobilePhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMobilePhone($mobilePhone)
    {
      $this->mobilePhone = $mobilePhone;
      return $this;
    }

    /**
     * @return string
     */
    public function getMCC()
    {
      return $this->MCC;
    }

    /**
     * @param string $MCC
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMCC($MCC)
    {
      $this->MCC = $MCC;
      return $this;
    }

    /**
     * @return string
     */
    public function getProductCode()
    {
      return $this->productCode;
    }

    /**
     * @param string $productCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setProductCode($productCode)
    {
      $this->productCode = $productCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getReferenceID()
    {
      return $this->referenceID;
    }

    /**
     * @param string $referenceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setReferenceID($referenceID)
    {
      $this->referenceID = $referenceID;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getMarketingOptIn()
    {
      return $this->marketingOptIn;
    }

    /**
     * @param boolean $marketingOptIn
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMarketingOptIn($marketingOptIn)
    {
      $this->marketingOptIn = $marketingOptIn;
      return $this;
    }

    /**
     * @return string
     */
    public function getMarketingSource()
    {
      return $this->marketingSource;
    }

    /**
     * @param string $marketingSource
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMarketingSource($marketingSource)
    {
      $this->marketingSource = $marketingSource;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getDefaultCard()
    {
      return $this->defaultCard;
    }

    /**
     * @param boolean $defaultCard
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setDefaultCard($defaultCard)
    {
      $this->defaultCard = $defaultCard;
      return $this;
    }

    /**
     * @return string
     */
    public function getShipAddressUsageDate()
    {
      return $this->shipAddressUsageDate;
    }

    /**
     * @param string $shipAddressUsageDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setShipAddressUsageDate($shipAddressUsageDate)
    {
      $this->shipAddressUsageDate = $shipAddressUsageDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getTransactionCountDay()
    {
      return $this->transactionCountDay;
    }

    /**
     * @param string $transactionCountDay
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setTransactionCountDay($transactionCountDay)
    {
      $this->transactionCountDay = $transactionCountDay;
      return $this;
    }

    /**
     * @return string
     */
    public function getTransactionCountYear()
    {
      return $this->transactionCountYear;
    }

    /**
     * @param string $transactionCountYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setTransactionCountYear($transactionCountYear)
    {
      $this->transactionCountYear = $transactionCountYear;
      return $this;
    }

    /**
     * @return string
     */
    public function getAddCardAttempts()
    {
      return $this->addCardAttempts;
    }

    /**
     * @param string $addCardAttempts
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAddCardAttempts($addCardAttempts)
    {
      $this->addCardAttempts = $addCardAttempts;
      return $this;
    }

    /**
     * @return string
     */
    public function getAccountPurchases()
    {
      return $this->accountPurchases;
    }

    /**
     * @param string $accountPurchases
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAccountPurchases($accountPurchases)
    {
      $this->accountPurchases = $accountPurchases;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getFraudActivity()
    {
      return $this->fraudActivity;
    }

    /**
     * @param boolean $fraudActivity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setFraudActivity($fraudActivity)
    {
      $this->fraudActivity = $fraudActivity;
      return $this;
    }

    /**
     * @return string
     */
    public function getPaymentAccountDate()
    {
      return $this->paymentAccountDate;
    }

    /**
     * @param string $paymentAccountDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPaymentAccountDate($paymentAccountDate)
    {
      $this->paymentAccountDate = $paymentAccountDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getAlternateAuthenticationMethod()
    {
      return $this->alternateAuthenticationMethod;
    }

    /**
     * @param string $alternateAuthenticationMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAlternateAuthenticationMethod($alternateAuthenticationMethod)
    {
      $this->alternateAuthenticationMethod = $alternateAuthenticationMethod;
      return $this;
    }

    /**
     * @return string
     */
    public function getAlternateAuthenticationDate()
    {
      return $this->alternateAuthenticationDate;
    }

    /**
     * @param string $alternateAuthenticationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAlternateAuthenticationDate($alternateAuthenticationDate)
    {
      $this->alternateAuthenticationDate = $alternateAuthenticationDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getAlternateAuthenticationData()
    {
      return $this->alternateAuthenticationData;
    }

    /**
     * @param string $alternateAuthenticationData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAlternateAuthenticationData($alternateAuthenticationData)
    {
      $this->alternateAuthenticationData = $alternateAuthenticationData;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getChallengeRequired()
    {
      return $this->challengeRequired;
    }

    /**
     * @param boolean $challengeRequired
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setChallengeRequired($challengeRequired)
    {
      $this->challengeRequired = $challengeRequired;
      return $this;
    }

    /**
     * @return string
     */
    public function getChallengeCode()
    {
      return $this->challengeCode;
    }

    /**
     * @param string $challengeCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setChallengeCode($challengeCode)
    {
      $this->challengeCode = $challengeCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getPreorder()
    {
      return $this->preorder;
    }

    /**
     * @param string $preorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPreorder($preorder)
    {
      $this->preorder = $preorder;
      return $this;
    }

    /**
     * @return string
     */
    public function getReorder()
    {
      return $this->reorder;
    }

    /**
     * @param string $reorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setReorder($reorder)
    {
      $this->reorder = $reorder;
      return $this;
    }

    /**
     * @return string
     */
    public function getPreorderDate()
    {
      return $this->preorderDate;
    }

    /**
     * @param string $preorderDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPreorderDate($preorderDate)
    {
      $this->preorderDate = $preorderDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getGiftCardAmount()
    {
      return $this->giftCardAmount;
    }

    /**
     * @param string $giftCardAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setGiftCardAmount($giftCardAmount)
    {
      $this->giftCardAmount = $giftCardAmount;
      return $this;
    }

    /**
     * @return string
     */
    public function getGiftCardCurrency()
    {
      return $this->giftCardCurrency;
    }

    /**
     * @param string $giftCardCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setGiftCardCurrency($giftCardCurrency)
    {
      $this->giftCardCurrency = $giftCardCurrency;
      return $this;
    }

    /**
     * @return string
     */
    public function getGiftCardCount()
    {
      return $this->giftCardCount;
    }

    /**
     * @param string $giftCardCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setGiftCardCount($giftCardCount)
    {
      $this->giftCardCount = $giftCardCount;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageCategory()
    {
      return $this->messageCategory;
    }

    /**
     * @param string $messageCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMessageCategory($messageCategory)
    {
      $this->messageCategory = $messageCategory;
      return $this;
    }

    /**
     * @return string
     */
    public function getNpaCode()
    {
      return $this->npaCode;
    }

    /**
     * @param string $npaCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setNpaCode($npaCode)
    {
      $this->npaCode = $npaCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getRecurringOriginalPurchaseDate()
    {
      return $this->recurringOriginalPurchaseDate;
    }

    /**
     * @param string $recurringOriginalPurchaseDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setRecurringOriginalPurchaseDate($recurringOriginalPurchaseDate)
    {
      $this->recurringOriginalPurchaseDate = $recurringOriginalPurchaseDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getTransactionMode()
    {
      return $this->transactionMode;
    }

    /**
     * @param string $transactionMode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setTransactionMode($transactionMode)
    {
      $this->transactionMode = $transactionMode;
      return $this;
    }

    /**
     * @return string
     */
    public function getRecurringEndDate()
    {
      return $this->recurringEndDate;
    }

    /**
     * @param string $recurringEndDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setRecurringEndDate($recurringEndDate)
    {
      $this->recurringEndDate = $recurringEndDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getRecurringFrequency()
    {
      return $this->recurringFrequency;
    }

    /**
     * @param string $recurringFrequency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setRecurringFrequency($recurringFrequency)
    {
      $this->recurringFrequency = $recurringFrequency;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantNewCustomer()
    {
      return $this->merchantNewCustomer;
    }

    /**
     * @param string $merchantNewCustomer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMerchantNewCustomer($merchantNewCustomer)
    {
      $this->merchantNewCustomer = $merchantNewCustomer;
      return $this;
    }

    /**
     * @return string
     */
    public function getCustomerCCAlias()
    {
      return $this->customerCCAlias;
    }

    /**
     * @param string $customerCCAlias
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setCustomerCCAlias($customerCCAlias)
    {
      $this->customerCCAlias = $customerCCAlias;
      return $this;
    }

    /**
     * @return string
     */
    public function getInstallmentTotalCount()
    {
      return $this->installmentTotalCount;
    }

    /**
     * @param string $installmentTotalCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setInstallmentTotalCount($installmentTotalCount)
    {
      $this->installmentTotalCount = $installmentTotalCount;
      return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationTransactionID()
    {
      return $this->authenticationTransactionID;
    }

    /**
     * @param string $authenticationTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAuthenticationTransactionID($authenticationTransactionID)
    {
      $this->authenticationTransactionID = $authenticationTransactionID;
      return $this;
    }

    /**
     * @return string
     */
    public function getHttpUserAccept()
    {
      return $this->httpUserAccept;
    }

    /**
     * @param string $httpUserAccept
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setHttpUserAccept($httpUserAccept)
    {
      $this->httpUserAccept = $httpUserAccept;
      return $this;
    }

    /**
     * @return string
     */
    public function getMobilePhoneDomestic()
    {
      return $this->mobilePhoneDomestic;
    }

    /**
     * @param string $mobilePhoneDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMobilePhoneDomestic($mobilePhoneDomestic)
    {
      $this->mobilePhoneDomestic = $mobilePhoneDomestic;
      return $this;
    }

    /**
     * @return string
     */
    public function getPareqChannel()
    {
      return $this->pareqChannel;
    }

    /**
     * @param string $pareqChannel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPareqChannel($pareqChannel)
    {
      $this->pareqChannel = $pareqChannel;
      return $this;
    }

    /**
     * @return string
     */
    public function getShoppingChannel()
    {
      return $this->shoppingChannel;
    }

    /**
     * @param string $shoppingChannel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setShoppingChannel($shoppingChannel)
    {
      $this->shoppingChannel = $shoppingChannel;
      return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationChannel()
    {
      return $this->authenticationChannel;
    }

    /**
     * @param string $authenticationChannel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAuthenticationChannel($authenticationChannel)
    {
      $this->authenticationChannel = $authenticationChannel;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantTTPCredential()
    {
      return $this->merchantTTPCredential;
    }

    /**
     * @param string $merchantTTPCredential
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMerchantTTPCredential($merchantTTPCredential)
    {
      $this->merchantTTPCredential = $merchantTTPCredential;
      return $this;
    }

    /**
     * @return string
     */
    public function getRequestorID()
    {
      return $this->requestorID;
    }

    /**
     * @param string $requestorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setRequestorID($requestorID)
    {
      $this->requestorID = $requestorID;
      return $this;
    }

    /**
     * @return string
     */
    public function getRequestorName()
    {
      return $this->requestorName;
    }

    /**
     * @param string $requestorName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setRequestorName($requestorName)
    {
      $this->requestorName = $requestorName;
      return $this;
    }

    /**
     * @return int
     */
    public function getAcsWindowSize()
    {
      return $this->acsWindowSize;
    }

    /**
     * @param int $acsWindowSize
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAcsWindowSize($acsWindowSize)
    {
      $this->acsWindowSize = $acsWindowSize;
      return $this;
    }

    /**
     * @return string
     */
    public function getDecoupledAuthenticationIndicator()
    {
      return $this->decoupledAuthenticationIndicator;
    }

    /**
     * @param string $decoupledAuthenticationIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setDecoupledAuthenticationIndicator($decoupledAuthenticationIndicator)
    {
      $this->decoupledAuthenticationIndicator = $decoupledAuthenticationIndicator;
      return $this;
    }

    /**
     * @return int
     */
    public function getDecoupledAuthenticationMaxTime()
    {
      return $this->decoupledAuthenticationMaxTime;
    }

    /**
     * @param int $decoupledAuthenticationMaxTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setDecoupledAuthenticationMaxTime($decoupledAuthenticationMaxTime)
    {
      $this->decoupledAuthenticationMaxTime = $decoupledAuthenticationMaxTime;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeviceChannel()
    {
      return $this->deviceChannel;
    }

    /**
     * @param string $deviceChannel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setDeviceChannel($deviceChannel)
    {
      $this->deviceChannel = $deviceChannel;
      return $this;
    }

    /**
     * @return string
     */
    public function getPriorAuthenticationReferenceID()
    {
      return $this->priorAuthenticationReferenceID;
    }

    /**
     * @param string $priorAuthenticationReferenceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPriorAuthenticationReferenceID($priorAuthenticationReferenceID)
    {
      $this->priorAuthenticationReferenceID = $priorAuthenticationReferenceID;
      return $this;
    }

    /**
     * @return string
     */
    public function getPriorAuthenticationData()
    {
      return $this->priorAuthenticationData;
    }

    /**
     * @param string $priorAuthenticationData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPriorAuthenticationData($priorAuthenticationData)
    {
      $this->priorAuthenticationData = $priorAuthenticationData;
      return $this;
    }

    /**
     * @return int
     */
    public function getPriorAuthenticationMethod()
    {
      return $this->priorAuthenticationMethod;
    }

    /**
     * @param int $priorAuthenticationMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPriorAuthenticationMethod($priorAuthenticationMethod)
    {
      $this->priorAuthenticationMethod = $priorAuthenticationMethod;
      return $this;
    }

    /**
     * @return int
     */
    public function getPriorAuthenticationTime()
    {
      return $this->priorAuthenticationTime;
    }

    /**
     * @param int $priorAuthenticationTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setPriorAuthenticationTime($priorAuthenticationTime)
    {
      $this->priorAuthenticationTime = $priorAuthenticationTime;
      return $this;
    }

    /**
     * @return int
     */
    public function getRequestorInitiatedAuthenticationIndicator()
    {
      return $this->requestorInitiatedAuthenticationIndicator;
    }

    /**
     * @param int $requestorInitiatedAuthenticationIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setRequestorInitiatedAuthenticationIndicator($requestorInitiatedAuthenticationIndicator)
    {
      $this->requestorInitiatedAuthenticationIndicator = $requestorInitiatedAuthenticationIndicator;
      return $this;
    }

    /**
     * @return string
     */
    public function getSdkMaxTimeout()
    {
      return $this->sdkMaxTimeout;
    }

    /**
     * @param string $sdkMaxTimeout
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setSdkMaxTimeout($sdkMaxTimeout)
    {
      $this->sdkMaxTimeout = $sdkMaxTimeout;
      return $this;
    }

    /**
     * @return int
     */
    public function getAuthenticationIndicator()
    {
      return $this->authenticationIndicator;
    }

    /**
     * @param int $authenticationIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAuthenticationIndicator($authenticationIndicator)
    {
      $this->authenticationIndicator = $authenticationIndicator;
      return $this;
    }

    /**
     * @return string
     */
    public function getWhiteListStatus()
    {
      return $this->whiteListStatus;
    }

    /**
     * @param string $whiteListStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setWhiteListStatus($whiteListStatus)
    {
      $this->whiteListStatus = $whiteListStatus;
      return $this;
    }

    /**
     * @return int
     */
    public function getTotalOffersCount()
    {
      return $this->totalOffersCount;
    }

    /**
     * @param int $totalOffersCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setTotalOffersCount($totalOffersCount)
    {
      $this->totalOffersCount = $totalOffersCount;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantScore()
    {
      return $this->merchantScore;
    }

    /**
     * @param string $merchantScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMerchantScore($merchantScore)
    {
      $this->merchantScore = $merchantScore;
      return $this;
    }

    /**
     * @return int
     */
    public function getMerchantFraudRate()
    {
      return $this->merchantFraudRate;
    }

    /**
     * @param int $merchantFraudRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setMerchantFraudRate($merchantFraudRate)
    {
      $this->merchantFraudRate = $merchantFraudRate;
      return $this;
    }

    /**
     * @return string
     */
    public function getAcquirerCountry()
    {
      return $this->acquirerCountry;
    }

    /**
     * @param string $acquirerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setAcquirerCountry($acquirerCountry)
    {
      $this->acquirerCountry = $acquirerCountry;
      return $this;
    }

    /**
     * @return string
     */
    public function getSecureCorporatePaymentIndicator()
    {
      return $this->secureCorporatePaymentIndicator;
    }

    /**
     * @param string $secureCorporatePaymentIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setSecureCorporatePaymentIndicator($secureCorporatePaymentIndicator)
    {
      $this->secureCorporatePaymentIndicator = $secureCorporatePaymentIndicator;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
      return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function setRun($run)
    {
      $this->run = $run;
      return $this;
    }

}
