<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCAuthService
{
    /**
     * @var string $cavv
     */
    protected $cavv;

    /**
     * @var string $cavvAlgorithm
     */
    protected $cavvAlgorithm;

    /**
     * @var string $networkTokenCryptogram
     */
    protected $networkTokenCryptogram;

    /**
     * @var string $paSpecificationVersion
     */
    protected $paSpecificationVersion;

    /**
     * @var string $directoryServerTransactionID
     */
    protected $directoryServerTransactionID;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $eciRaw
     */
    protected $eciRaw;

    /**
     * @var string $xid
     */
    protected $xid;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $avsLevel
     */
    protected $avsLevel;

    /**
     * @var string $fxQuoteID
     */
    protected $fxQuoteID;

    /**
     * @var boolean $returnAuthRecord
     */
    protected $returnAuthRecord;

    /**
     * @var string $authType
     */
    protected $authType;

    /**
     * @var string $verbalAuthCode
     */
    protected $verbalAuthCode;

    /**
     * @var boolean $billPayment
     */
    protected $billPayment;

    /**
     * @var string $authenticationXID
     */
    protected $authenticationXID;

    /**
     * @var string $authorizationXID
     */
    protected $authorizationXID;

    /**
     * @var string $industryDatatype
     */
    protected $industryDatatype;

    /**
     * @var string $traceNumber
     */
    protected $traceNumber;

    /**
     * @var string $checksumKey
     */
    protected $checksumKey;

    /**
     * @var string $aggregatorID
     */
    protected $aggregatorID;

    /**
     * @var string $aggregatorName
     */
    protected $aggregatorName;

    /**
     * @var string $splitTenderIndicator
     */
    protected $splitTenderIndicator;

    /**
     * @var string $veresEnrolled
     */
    protected $veresEnrolled;

    /**
     * @var string $paresStatus
     */
    protected $paresStatus;

    /**
     * @var boolean $partialAuthIndicator
     */
    protected $partialAuthIndicator;

    /**
     * @var string $captureDate
     */
    protected $captureDate;

    /**
     * @var string $firstRecurringPayment
     */
    protected $firstRecurringPayment;

    /**
     * @var int $duration
     */
    protected $duration;

    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod;

    /**
     * @var string $mobileRemotePaymentType
     */
    protected $mobileRemotePaymentType;

    /**
     * @var string $cardholderVerificationMethod
     */
    protected $cardholderVerificationMethod;

    /**
     * @var string $dccRequestID
     */
    protected $dccRequestID;

    /**
     * @var string $overridePaymentDetails
     */
    protected $overridePaymentDetails;

    /**
     * @var string $cardholderAuthenticationMethod
     */
    protected $cardholderAuthenticationMethod;

    /**
     * @var boolean $leastCostRouting
     */
    protected $leastCostRouting;

    /**
     * @var string $verificationType
     */
    protected $verificationType;

    /**
     * @var string $cryptocurrencyPurchase
     */
    protected $cryptocurrencyPurchase;

    /**
     * @var string $lowValueExemptionIndicator
     */
    protected $lowValueExemptionIndicator;

    /**
     * @var string $riskAnalysisExemptionIndicator
     */
    protected $riskAnalysisExemptionIndicator;

    /**
     * @var string $trustedMerchantExemptionIndicator
     */
    protected $trustedMerchantExemptionIndicator;

    /**
     * @var string $secureCorporatePaymentIndicator
     */
    protected $secureCorporatePaymentIndicator;

    /**
     * @var string $deferredAuthIndicator
     */
    protected $deferredAuthIndicator;

    /**
     * @var string $delegatedAuthenticationExemptionIndicator
     */
    protected $delegatedAuthenticationExemptionIndicator;

    /**
     * @var string $transitTransactionType
     */
    protected $transitTransactionType;

    /**
     * @var string $transportationMode
     */
    protected $transportationMode;

    /**
     * @var string $totaloffersCount
     */
    protected $totaloffersCount;

    /**
     * @var string $effectiveAuthenticationType
     */
    protected $effectiveAuthenticationType;

    /**
     * @var string $paChallengeStatus
     */
    protected $paChallengeStatus;

    /**
     * @var string $paresStatusReason
     */
    protected $paresStatusReason;

    /**
     * @var string $paChallengeCancelCode
     */
    protected $paChallengeCancelCode;

    /**
     * @var string $paNetworkScore
     */
    protected $paNetworkScore;

    /**
     * @var string $paAuthenticationDate
     */
    protected $paAuthenticationDate;

    /**
     * @var boolean $run
     */
    protected $run;

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
    public function getCavv()
    {
        return $this->cavv;
    }

    /**
     * @param string $cavv
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCavv($cavv)
    {
        $this->cavv = $cavv;

        return $this;
    }

    /**
     * @return string
     */
    public function getCavvAlgorithm()
    {
        return $this->cavvAlgorithm;
    }

    /**
     * @param string $cavvAlgorithm
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCavvAlgorithm($cavvAlgorithm)
    {
        $this->cavvAlgorithm = $cavvAlgorithm;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkTokenCryptogram()
    {
        return $this->networkTokenCryptogram;
    }

    /**
     * @param string $networkTokenCryptogram
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setNetworkTokenCryptogram($networkTokenCryptogram)
    {
        $this->networkTokenCryptogram = $networkTokenCryptogram;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaSpecificationVersion()
    {
        return $this->paSpecificationVersion;
    }

    /**
     * @param string $paSpecificationVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPaSpecificationVersion($paSpecificationVersion)
    {
        $this->paSpecificationVersion = $paSpecificationVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectoryServerTransactionID()
    {
        return $this->directoryServerTransactionID;
    }

    /**
     * @param string $directoryServerTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDirectoryServerTransactionID($directoryServerTransactionID)
    {
        $this->directoryServerTransactionID = $directoryServerTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommerceIndicator()
    {
        return $this->commerceIndicator;
    }

    /**
     * @param string $commerceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getEciRaw()
    {
        return $this->eciRaw;
    }

    /**
     * @param string $eciRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setEciRaw($eciRaw)
    {
        $this->eciRaw = $eciRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getXid()
    {
        return $this->xid;
    }

    /**
     * @param string $xid
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setXid($xid)
    {
        $this->xid = $xid;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsLevel()
    {
        return $this->avsLevel;
    }

    /**
     * @param string $avsLevel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAvsLevel($avsLevel)
    {
        $this->avsLevel = $avsLevel;

        return $this;
    }

    /**
     * @return string
     */
    public function getFxQuoteID()
    {
        return $this->fxQuoteID;
    }

    /**
     * @param string $fxQuoteID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setFxQuoteID($fxQuoteID)
    {
        $this->fxQuoteID = $fxQuoteID;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getReturnAuthRecord()
    {
        return $this->returnAuthRecord;
    }

    /**
     * @param boolean $returnAuthRecord
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setReturnAuthRecord($returnAuthRecord)
    {
        $this->returnAuthRecord = $returnAuthRecord;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param string $authType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerbalAuthCode()
    {
        return $this->verbalAuthCode;
    }

    /**
     * @param string $verbalAuthCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setVerbalAuthCode($verbalAuthCode)
    {
        $this->verbalAuthCode = $verbalAuthCode;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getBillPayment()
    {
        return $this->billPayment;
    }

    /**
     * @param boolean $billPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setBillPayment($billPayment)
    {
        $this->billPayment = $billPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationXID()
    {
        return $this->authenticationXID;
    }

    /**
     * @param string $authenticationXID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAuthenticationXID($authenticationXID)
    {
        $this->authenticationXID = $authenticationXID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationXID()
    {
        return $this->authorizationXID;
    }

    /**
     * @param string $authorizationXID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAuthorizationXID($authorizationXID)
    {
        $this->authorizationXID = $authorizationXID;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndustryDatatype()
    {
        return $this->industryDatatype;
    }

    /**
     * @param string $industryDatatype
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setIndustryDatatype($industryDatatype)
    {
        $this->industryDatatype = $industryDatatype;

        return $this;
    }

    /**
     * @return string
     */
    public function getTraceNumber()
    {
        return $this->traceNumber;
    }

    /**
     * @param string $traceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setTraceNumber($traceNumber)
    {
        $this->traceNumber = $traceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getChecksumKey()
    {
        return $this->checksumKey;
    }

    /**
     * @param string $checksumKey
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setChecksumKey($checksumKey)
    {
        $this->checksumKey = $checksumKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorID()
    {
        return $this->aggregatorID;
    }

    /**
     * @param string $aggregatorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatorID($aggregatorID)
    {
        $this->aggregatorID = $aggregatorID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorName()
    {
        return $this->aggregatorName;
    }

    /**
     * @param string $aggregatorName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatorName($aggregatorName)
    {
        $this->aggregatorName = $aggregatorName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSplitTenderIndicator()
    {
        return $this->splitTenderIndicator;
    }

    /**
     * @param string $splitTenderIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setSplitTenderIndicator($splitTenderIndicator)
    {
        $this->splitTenderIndicator = $splitTenderIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getVeresEnrolled()
    {
        return $this->veresEnrolled;
    }

    /**
     * @param string $veresEnrolled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setVeresEnrolled($veresEnrolled)
    {
        $this->veresEnrolled = $veresEnrolled;

        return $this;
    }

    /**
     * @return string
     */
    public function getParesStatus()
    {
        return $this->paresStatus;
    }

    /**
     * @param string $paresStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setParesStatus($paresStatus)
    {
        $this->paresStatus = $paresStatus;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPartialAuthIndicator()
    {
        return $this->partialAuthIndicator;
    }

    /**
     * @param boolean $partialAuthIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPartialAuthIndicator($partialAuthIndicator)
    {
        $this->partialAuthIndicator = $partialAuthIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptureDate()
    {
        return $this->captureDate;
    }

    /**
     * @param string $captureDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCaptureDate($captureDate)
    {
        $this->captureDate = $captureDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstRecurringPayment()
    {
        return $this->firstRecurringPayment;
    }

    /**
     * @param string $firstRecurringPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setFirstRecurringPayment($firstRecurringPayment)
    {
        $this->firstRecurringPayment = $firstRecurringPayment;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getMobileRemotePaymentType()
    {
        return $this->mobileRemotePaymentType;
    }

    /**
     * @param string $mobileRemotePaymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setMobileRemotePaymentType($mobileRemotePaymentType)
    {
        $this->mobileRemotePaymentType = $mobileRemotePaymentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardholderVerificationMethod()
    {
        return $this->cardholderVerificationMethod;
    }

    /**
     * @param string $cardholderVerificationMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCardholderVerificationMethod($cardholderVerificationMethod)
    {
        $this->cardholderVerificationMethod = $cardholderVerificationMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getDccRequestID()
    {
        return $this->dccRequestID;
    }

    /**
     * @param string $dccRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDccRequestID($dccRequestID)
    {
        $this->dccRequestID = $dccRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getOverridePaymentDetails()
    {
        return $this->overridePaymentDetails;
    }

    /**
     * @param string $overridePaymentDetails
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setOverridePaymentDetails($overridePaymentDetails)
    {
        $this->overridePaymentDetails = $overridePaymentDetails;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardholderAuthenticationMethod()
    {
        return $this->cardholderAuthenticationMethod;
    }

    /**
     * @param string $cardholderAuthenticationMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCardholderAuthenticationMethod($cardholderAuthenticationMethod)
    {
        $this->cardholderAuthenticationMethod = $cardholderAuthenticationMethod;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getLeastCostRouting()
    {
        return $this->leastCostRouting;
    }

    /**
     * @param boolean $leastCostRouting
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setLeastCostRouting($leastCostRouting)
    {
        $this->leastCostRouting = $leastCostRouting;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationType()
    {
        return $this->verificationType;
    }

    /**
     * @param string $verificationType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setVerificationType($verificationType)
    {
        $this->verificationType = $verificationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCryptocurrencyPurchase()
    {
        return $this->cryptocurrencyPurchase;
    }

    /**
     * @param string $cryptocurrencyPurchase
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCryptocurrencyPurchase($cryptocurrencyPurchase)
    {
        $this->cryptocurrencyPurchase = $cryptocurrencyPurchase;

        return $this;
    }

    /**
     * @return string
     */
    public function getLowValueExemptionIndicator()
    {
        return $this->lowValueExemptionIndicator;
    }

    /**
     * @param string $lowValueExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setLowValueExemptionIndicator($lowValueExemptionIndicator)
    {
        $this->lowValueExemptionIndicator = $lowValueExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskAnalysisExemptionIndicator()
    {
        return $this->riskAnalysisExemptionIndicator;
    }

    /**
     * @param string $riskAnalysisExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setRiskAnalysisExemptionIndicator($riskAnalysisExemptionIndicator)
    {
        $this->riskAnalysisExemptionIndicator = $riskAnalysisExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrustedMerchantExemptionIndicator()
    {
        return $this->trustedMerchantExemptionIndicator;
    }

    /**
     * @param string $trustedMerchantExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setTrustedMerchantExemptionIndicator($trustedMerchantExemptionIndicator)
    {
        $this->trustedMerchantExemptionIndicator = $trustedMerchantExemptionIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setSecureCorporatePaymentIndicator($secureCorporatePaymentIndicator)
    {
        $this->secureCorporatePaymentIndicator = $secureCorporatePaymentIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeferredAuthIndicator()
    {
        return $this->deferredAuthIndicator;
    }

    /**
     * @param string $deferredAuthIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDeferredAuthIndicator($deferredAuthIndicator)
    {
        $this->deferredAuthIndicator = $deferredAuthIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDelegatedAuthenticationExemptionIndicator()
    {
        return $this->delegatedAuthenticationExemptionIndicator;
    }

    /**
     * @param string $delegatedAuthenticationExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDelegatedAuthenticationExemptionIndicator($delegatedAuthenticationExemptionIndicator)
    {
        $this->delegatedAuthenticationExemptionIndicator = $delegatedAuthenticationExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransitTransactionType()
    {
        return $this->transitTransactionType;
    }

    /**
     * @param string $transitTransactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setTransitTransactionType($transitTransactionType)
    {
        $this->transitTransactionType = $transitTransactionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransportationMode()
    {
        return $this->transportationMode;
    }

    /**
     * @param string $transportationMode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setTransportationMode($transportationMode)
    {
        $this->transportationMode = $transportationMode;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotaloffersCount()
    {
        return $this->totaloffersCount;
    }

    /**
     * @param string $totaloffersCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setTotaloffersCount($totaloffersCount)
    {
        $this->totaloffersCount = $totaloffersCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getEffectiveAuthenticationType()
    {
        return $this->effectiveAuthenticationType;
    }

    /**
     * @param string $effectiveAuthenticationType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setEffectiveAuthenticationType($effectiveAuthenticationType)
    {
        $this->effectiveAuthenticationType = $effectiveAuthenticationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaChallengeStatus()
    {
        return $this->paChallengeStatus;
    }

    /**
     * @param string $paChallengeStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPaChallengeStatus($paChallengeStatus)
    {
        $this->paChallengeStatus = $paChallengeStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getParesStatusReason()
    {
        return $this->paresStatusReason;
    }

    /**
     * @param string $paresStatusReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setParesStatusReason($paresStatusReason)
    {
        $this->paresStatusReason = $paresStatusReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaChallengeCancelCode()
    {
        return $this->paChallengeCancelCode;
    }

    /**
     * @param string $paChallengeCancelCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPaChallengeCancelCode($paChallengeCancelCode)
    {
        $this->paChallengeCancelCode = $paChallengeCancelCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaNetworkScore()
    {
        return $this->paNetworkScore;
    }

    /**
     * @param string $paNetworkScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPaNetworkScore($paNetworkScore)
    {
        $this->paNetworkScore = $paNetworkScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaAuthenticationDate()
    {
        return $this->paAuthenticationDate;
    }

    /**
     * @param string $paAuthenticationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPaAuthenticationDate($paAuthenticationDate)
    {
        $this->paAuthenticationDate = $paAuthenticationDate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
