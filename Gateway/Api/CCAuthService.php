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
     * @var string $threeDSServerTransactionID
     */
    protected $threeDSServerTransactionID;

    /**
     * @var string $acsServerTransactionID
     */
    protected $acsServerTransactionID;

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
     * @var string $aggregatedAuthIndicator
     */
    protected $aggregatedAuthIndicator;

    /**
     * @var string $debtRecoveryIndicator
     */
    protected $debtRecoveryIndicator;

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
     * @var string $paChallengeCode
     */
    protected $paChallengeCode;

    /**
     * @var string $paresStatusReason
     */
    protected $paresStatusReason;

    /**
     * @var string $challengeCancelCode
     */
    protected $challengeCancelCode;

    /**
     * @var string $paNetworkScore
     */
    protected $paNetworkScore;

    /**
     * @var string $paAuthenticationDate
     */
    protected $paAuthenticationDate;

    /**
     * @var string $authenticationOutageExemptionIndicator
     */
    protected $authenticationOutageExemptionIndicator;

    /**
     * @var string $verificationResultsPassportNumber
     */
    protected $verificationResultsPassportNumber;

    /**
     * @var string $verificationResultsPersonalId
     */
    protected $verificationResultsPersonalId;

    /**
     * @var string $verificationResultsDriversLicenseNo
     */
    protected $verificationResultsDriversLicenseNo;

    /**
     * @var string $verificationResultsBuyerRegistration
     */
    protected $verificationResultsBuyerRegistration;

    /**
     * @var string $delegatedAuthenticationResult
     */
    protected $delegatedAuthenticationResult;

    /**
     * @var string[] $paymentNetworkTransactionInformation
     */
    protected $paymentNetworkTransactionInformation;

    /**
     * @var string[] $transactionReason
     */
    protected $transactionReason;

    /**
     * @var string $panReturnIndicator
     */
    protected $panReturnIndicator;

    /**
     * @var string $cashAdvanceIndicator
     */
    protected $cashAdvanceIndicator;

    /**
     * @var boolean $splitPaymentTransaction
     */
    protected $splitPaymentTransaction;

    /**
     * @var boolean $cardVerificationIndicator
     */
    protected $cardVerificationIndicator;

    /**
     * @var string $exemptionDataRaw
     */
    protected $exemptionDataRaw;

    /**
     * @var string $transactionFlowIndicator
     */
    protected $transactionFlowIndicator;

    /**
     * @var string $networkPartnerId
     */
    protected $networkPartnerId;

    /**
     * @var string $acquirerMerchantId
     */
    protected $acquirerMerchantId;

    /**
     * @var string $merchantVerificationValue
     */
    protected $merchantVerificationValue;

    /**
     * @var string $extendAuthIndicator
     */
    protected $extendAuthIndicator;

    /**
     * @var string $acsReferenceNumber
     */
    protected $acsReferenceNumber;

    /**
     * @var string $dsReferenceNumber
     */
    protected $dsReferenceNumber;

    /**
     * @var string $initiatorType
     */
    protected $initiatorType;

    /**
     * @var string $purposeOfPayment
     */
    protected $purposeOfPayment;

    /**
     * @var string $aggregatorStreetAddress
     */
    protected $aggregatorStreetAddress;

    /**
     * @var string $aggregatorCity
     */
    protected $aggregatorCity;

    /**
     * @var string $aggregatorState
     */
    protected $aggregatorState;

    /**
     * @var string $aggregatorPostalcode
     */
    protected $aggregatorPostalcode;

    /**
     * @var string $aggregatorCountry
     */
    protected $aggregatorCountry;

    /**
     * @var string $eligibilityIndicator
     */
    protected $eligibilityIndicator;

    /**
     * @var serviceProcessing $serviceProcessing
     */
    protected $serviceProcessing;

    /**
     * @var benefit $benefit
     */
    protected $benefit;

    /**
     * @var language $language
     */
    protected $language;

    /**
     * @var string $originalPaymentId
     */
    protected $originalPaymentId;

    /**
     * @var string $authenticationBrand
     */
    protected $authenticationBrand;

    /**
     * @var string $gratuityAmount
     */
    protected $gratuityAmount;

    /**
     * @var string $cardProductSubtype
     */
    protected $cardProductSubtype;

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
    public function getThreeDSServerTransactionID()
    {
        return $this->threeDSServerTransactionID;
    }

    /**
     * @param string $threeDSServerTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setThreeDSServerTransactionID($threeDSServerTransactionID)
    {
        $this->threeDSServerTransactionID = $threeDSServerTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcsServerTransactionID()
    {
        return $this->acsServerTransactionID;
    }

    /**
     * @param string $acsServerTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAcsServerTransactionID($acsServerTransactionID)
    {
        $this->acsServerTransactionID = $acsServerTransactionID;

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
    public function getAggregatedAuthIndicator()
    {
        return $this->aggregatedAuthIndicator;
    }

    /**
     * @param string $aggregatedAuthIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatedAuthIndicator($aggregatedAuthIndicator)
    {
        $this->aggregatedAuthIndicator = $aggregatedAuthIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDebtRecoveryIndicator()
    {
        return $this->debtRecoveryIndicator;
    }

    /**
     * @param string $debtRecoveryIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDebtRecoveryIndicator($debtRecoveryIndicator)
    {
        $this->debtRecoveryIndicator = $debtRecoveryIndicator;

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
    public function getPaChallengeCode()
    {
        return $this->paChallengeCode;
    }

    /**
     * @param string $paChallengeCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPaChallengeCode($paChallengeCode)
    {
        $this->paChallengeCode = $paChallengeCode;

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
    public function getChallengeCancelCode()
    {
        return $this->challengeCancelCode;
    }

    /**
     * @param string $challengeCancelCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setChallengeCancelCode($challengeCancelCode)
    {
        $this->challengeCancelCode = $challengeCancelCode;

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
     * @return string
     */
    public function getAuthenticationOutageExemptionIndicator()
    {
        return $this->authenticationOutageExemptionIndicator;
    }

    /**
     * @param string $authenticationOutageExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAuthenticationOutageExemptionIndicator($authenticationOutageExemptionIndicator)
    {
        $this->authenticationOutageExemptionIndicator = $authenticationOutageExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationResultsPassportNumber()
    {
        return $this->verificationResultsPassportNumber;
    }

    /**
     * @param string $verificationResultsPassportNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setVerificationResultsPassportNumber($verificationResultsPassportNumber)
    {
        $this->verificationResultsPassportNumber = $verificationResultsPassportNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationResultsPersonalId()
    {
        return $this->verificationResultsPersonalId;
    }

    /**
     * @param string $verificationResultsPersonalId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setVerificationResultsPersonalId($verificationResultsPersonalId)
    {
        $this->verificationResultsPersonalId = $verificationResultsPersonalId;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationResultsDriversLicenseNo()
    {
        return $this->verificationResultsDriversLicenseNo;
    }

    /**
     * @param string $verificationResultsDriversLicenseNo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setVerificationResultsDriversLicenseNo($verificationResultsDriversLicenseNo)
    {
        $this->verificationResultsDriversLicenseNo = $verificationResultsDriversLicenseNo;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationResultsBuyerRegistration()
    {
        return $this->verificationResultsBuyerRegistration;
    }

    /**
     * @param string $verificationResultsBuyerRegistration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setVerificationResultsBuyerRegistration($verificationResultsBuyerRegistration)
    {
        $this->verificationResultsBuyerRegistration = $verificationResultsBuyerRegistration;

        return $this;
    }

    /**
     * @return string
     */
    public function getDelegatedAuthenticationResult()
    {
        return $this->delegatedAuthenticationResult;
    }

    /**
     * @param string $delegatedAuthenticationResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDelegatedAuthenticationResult($delegatedAuthenticationResult)
    {
        $this->delegatedAuthenticationResult = $delegatedAuthenticationResult;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPaymentNetworkTransactionInformation()
    {
        return $this->paymentNetworkTransactionInformation;
    }

    /**
     * @param string[] $paymentNetworkTransactionInformation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPaymentNetworkTransactionInformation(array $paymentNetworkTransactionInformation = null)
    {
        $this->paymentNetworkTransactionInformation = $paymentNetworkTransactionInformation;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTransactionReason()
    {
        return $this->transactionReason;
    }

    /**
     * @param string[] $transactionReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setTransactionReason(array $transactionReason = null)
    {
        $this->transactionReason = $transactionReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getPanReturnIndicator()
    {
        return $this->panReturnIndicator;
    }

    /**
     * @param string $panReturnIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPanReturnIndicator($panReturnIndicator)
    {
        $this->panReturnIndicator = $panReturnIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getCashAdvanceIndicator()
    {
        return $this->cashAdvanceIndicator;
    }

    /**
     * @param string $cashAdvanceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCashAdvanceIndicator($cashAdvanceIndicator)
    {
        $this->cashAdvanceIndicator = $cashAdvanceIndicator;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getSplitPaymentTransaction()
    {
        return $this->splitPaymentTransaction;
    }

    /**
     * @param boolean $splitPaymentTransaction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setSplitPaymentTransaction($splitPaymentTransaction)
    {
        $this->splitPaymentTransaction = $splitPaymentTransaction;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCardVerificationIndicator()
    {
        return $this->cardVerificationIndicator;
    }

    /**
     * @param boolean $cardVerificationIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCardVerificationIndicator($cardVerificationIndicator)
    {
        $this->cardVerificationIndicator = $cardVerificationIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getExemptionDataRaw()
    {
        return $this->exemptionDataRaw;
    }

    /**
     * @param string $exemptionDataRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setExemptionDataRaw($exemptionDataRaw)
    {
        $this->exemptionDataRaw = $exemptionDataRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionFlowIndicator()
    {
        return $this->transactionFlowIndicator;
    }

    /**
     * @param string $transactionFlowIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setTransactionFlowIndicator($transactionFlowIndicator)
    {
        $this->transactionFlowIndicator = $transactionFlowIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkPartnerId()
    {
        return $this->networkPartnerId;
    }

    /**
     * @param string $networkPartnerId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setNetworkPartnerId($networkPartnerId)
    {
        $this->networkPartnerId = $networkPartnerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcquirerMerchantId()
    {
        return $this->acquirerMerchantId;
    }

    /**
     * @param string $acquirerMerchantId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAcquirerMerchantId($acquirerMerchantId)
    {
        $this->acquirerMerchantId = $acquirerMerchantId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantVerificationValue()
    {
        return $this->merchantVerificationValue;
    }

    /**
     * @param string $merchantVerificationValue
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setMerchantVerificationValue($merchantVerificationValue)
    {
        $this->merchantVerificationValue = $merchantVerificationValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtendAuthIndicator()
    {
        return $this->extendAuthIndicator;
    }

    /**
     * @param string $extendAuthIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setExtendAuthIndicator($extendAuthIndicator)
    {
        $this->extendAuthIndicator = $extendAuthIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcsReferenceNumber()
    {
        return $this->acsReferenceNumber;
    }

    /**
     * @param string $acsReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAcsReferenceNumber($acsReferenceNumber)
    {
        $this->acsReferenceNumber = $acsReferenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getDsReferenceNumber()
    {
        return $this->dsReferenceNumber;
    }

    /**
     * @param string $dsReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setDsReferenceNumber($dsReferenceNumber)
    {
        $this->dsReferenceNumber = $dsReferenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getInitiatorType()
    {
        return $this->initiatorType;
    }

    /**
     * @param string $initiatorType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setInitiatorType($initiatorType)
    {
        $this->initiatorType = $initiatorType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPurposeOfPayment()
    {
        return $this->purposeOfPayment;
    }

    /**
     * @param string $purposeOfPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setPurposeOfPayment($purposeOfPayment)
    {
        $this->purposeOfPayment = $purposeOfPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorStreetAddress()
    {
        return $this->aggregatorStreetAddress;
    }

    /**
     * @param string $aggregatorStreetAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatorStreetAddress($aggregatorStreetAddress)
    {
        $this->aggregatorStreetAddress = $aggregatorStreetAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorCity()
    {
        return $this->aggregatorCity;
    }

    /**
     * @param string $aggregatorCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatorCity($aggregatorCity)
    {
        $this->aggregatorCity = $aggregatorCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorState()
    {
        return $this->aggregatorState;
    }

    /**
     * @param string $aggregatorState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatorState($aggregatorState)
    {
        $this->aggregatorState = $aggregatorState;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorPostalcode()
    {
        return $this->aggregatorPostalcode;
    }

    /**
     * @param string $aggregatorPostalcode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatorPostalcode($aggregatorPostalcode)
    {
        $this->aggregatorPostalcode = $aggregatorPostalcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorCountry()
    {
        return $this->aggregatorCountry;
    }

    /**
     * @param string $aggregatorCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAggregatorCountry($aggregatorCountry)
    {
        $this->aggregatorCountry = $aggregatorCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getEligibilityIndicator()
    {
        return $this->eligibilityIndicator;
    }

    /**
     * @param string $eligibilityIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setEligibilityIndicator($eligibilityIndicator)
    {
        $this->eligibilityIndicator = $eligibilityIndicator;

        return $this;
    }

    /**
     * @return serviceProcessing
     */
    public function getServiceProcessing()
    {
        return $this->serviceProcessing;
    }

    /**
     * @param serviceProcessing $serviceProcessing
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setServiceProcessing($serviceProcessing)
    {
        $this->serviceProcessing = $serviceProcessing;

        return $this;
    }

    /**
     * @return benefit
     */
    public function getBenefit()
    {
        return $this->benefit;
    }

    /**
     * @param benefit $benefit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setBenefit($benefit)
    {
        $this->benefit = $benefit;

        return $this;
    }

    /**
     * @return language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param language $language
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalPaymentId()
    {
        return $this->originalPaymentId;
    }

    /**
     * @param string $originalPaymentId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setOriginalPaymentId($originalPaymentId)
    {
        $this->originalPaymentId = $originalPaymentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationBrand()
    {
        return $this->authenticationBrand;
    }

    /**
     * @param string $authenticationBrand
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setAuthenticationBrand($authenticationBrand)
    {
        $this->authenticationBrand = $authenticationBrand;

        return $this;
    }

    /**
     * @return string
     */
    public function getGratuityAmount()
    {
        return $this->gratuityAmount;
    }

    /**
     * @param string $gratuityAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setGratuityAmount($gratuityAmount)
    {
        $this->gratuityAmount = $gratuityAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardProductSubtype()
    {
        return $this->cardProductSubtype;
    }

    /**
     * @param string $cardProductSubtype
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function setCardProductSubtype($cardProductSubtype)
    {
        $this->cardProductSubtype = $cardProductSubtype;

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
