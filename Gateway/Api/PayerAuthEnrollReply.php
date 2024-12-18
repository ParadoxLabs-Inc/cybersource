<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayerAuthEnrollReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $acsURL
     */
    protected $acsURL;

    /**
     * @var string $accessToken
     */
    protected $accessToken;

    /**
     * @var string $authenticationResult
     */
    protected $authenticationResult;

    /**
     * @var string $authenticationStatusMessage
     */
    protected $authenticationStatusMessage;

    /**
     * @var string $cavv
     */
    protected $cavv;

    /**
     * @var string $cavvAlgorithm
     */
    protected $cavvAlgorithm;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $eci
     */
    protected $eci;

    /**
     * @var string $eciRaw
     */
    protected $eciRaw;

    /**
     * @var string $paReq
     */
    protected $paReq;

    /**
     * @var string $proxyPAN
     */
    protected $proxyPAN;

    /**
     * @var string $xid
     */
    protected $xid;

    /**
     * @var string $proofXML
     */
    protected $proofXML;

    /**
     * @var string $ucafAuthenticationData
     */
    protected $ucafAuthenticationData;

    /**
     * @var string $ucafCollectionIndicator
     */
    protected $ucafCollectionIndicator;

    /**
     * @var string $paresStatus
     */
    protected $paresStatus;

    /**
     * @var string $veresEnrolled
     */
    protected $veresEnrolled;

    /**
     * @var string $authenticationPath
     */
    protected $authenticationPath;

    /**
     * @var string $specificationVersion
     */
    protected $specificationVersion;

    /**
     * @var string $authenticationTransactionID
     */
    protected $authenticationTransactionID;

    /**
     * @var string $directoryServerTransactionID
     */
    protected $directoryServerTransactionID;

    /**
     * @var boolean $challengeRequired
     */
    protected $challengeRequired;

    /**
     * @var string $threeDSServerTransactionID
     */
    protected $threeDSServerTransactionID;

    /**
     * @var string $acsRenderingType
     */
    protected $acsRenderingType;

    /**
     * @var string $acsTransactionID
     */
    protected $acsTransactionID;

    /**
     * @var int $authenticationType
     */
    protected $authenticationType;

    /**
     * @var string $cardholderMessage
     */
    protected $cardholderMessage;

    /**
     * @var int $directoryServerErrorCode
     */
    protected $directoryServerErrorCode;

    /**
     * @var string $directoryServerErrorDescription
     */
    protected $directoryServerErrorDescription;

    /**
     * @var boolean $ivrEnabledMessage
     */
    protected $ivrEnabledMessage;

    /**
     * @var string $ivrEncryptionKey
     */
    protected $ivrEncryptionKey;

    /**
     * @var boolean $ivrEncryptionMandatory
     */
    protected $ivrEncryptionMandatory;

    /**
     * @var string $ivrEncryptionType
     */
    protected $ivrEncryptionType;

    /**
     * @var string $ivrLabel
     */
    protected $ivrLabel;

    /**
     * @var string $ivrPrompt
     */
    protected $ivrPrompt;

    /**
     * @var string $ivrStatusMessage
     */
    protected $ivrStatusMessage;

    /**
     * @var string $sdkTransactionID
     */
    protected $sdkTransactionID;

    /**
     * @var string $stepUpUrl
     */
    protected $stepUpUrl;

    /**
     * @var string $whiteListStatus
     */
    protected $whiteListStatus;

    /**
     * @var int $whiteListStatusSource
     */
    protected $whiteListStatusSource;

    /**
     * @var string $effectiveAuthenticationType
     */
    protected $effectiveAuthenticationType;

    /**
     * @var string $authenticationStatusReason
     */
    protected $authenticationStatusReason;

    /**
     * @var string $networkScore
     */
    protected $networkScore;

    /**
     * @var base64Binary $authorizationPayload
     */
    protected $authorizationPayload;

    /**
     * @var string $challengeCancelCode
     */
    protected $challengeCancelCode;

    /**
     * @var string $decoupledAuthenticationIndicator
     */
    protected $decoupledAuthenticationIndicator;

    /**
     * @var string $cardBin
     */
    protected $cardBin;

    /**
     * @var string $cardTypeName
     */
    protected $cardTypeName;

    /**
     * @var string $transactionIndicator
     */
    protected $transactionIndicator;

    /**
     * @var string $resendCountRemaining
     */
    protected $resendCountRemaining;

    /**
     * @var string $acsReferenceNumber
     */
    protected $acsReferenceNumber;

    /**
     * @var string $acsOperatorId
     */
    protected $acsOperatorId;

    /**
     * @var int $idciScore
     */
    protected $idciScore;

    /**
     * @var string $idciDecision
     */
    protected $idciDecision;

    /**
     * @var string $idciReasonCode1
     */
    protected $idciReasonCode1;

    /**
     * @var string $idciReasonCode2
     */
    protected $idciReasonCode2;

    /**
     * @var int $authenticationOutageExemptionIndicator
     */
    protected $authenticationOutageExemptionIndicator;

    /**
     * @var string $exemptionDataRaw
     */
    protected $exemptionDataRaw;

    /**
     * @var string $threeDSServerOperatorId
     */
    protected $threeDSServerOperatorId;

    /**
     * @var string $dsReferenceNumber
     */
    protected $dsReferenceNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcsURL()
    {
        return $this->acsURL;
    }

    /**
     * @param string $acsURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAcsURL($acsURL)
    {
        $this->acsURL = $acsURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationResult()
    {
        return $this->authenticationResult;
    }

    /**
     * @param string $authenticationResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthenticationResult($authenticationResult)
    {
        $this->authenticationResult = $authenticationResult;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationStatusMessage()
    {
        return $this->authenticationStatusMessage;
    }

    /**
     * @param string $authenticationStatusMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthenticationStatusMessage($authenticationStatusMessage)
    {
        $this->authenticationStatusMessage = $authenticationStatusMessage;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setCavvAlgorithm($cavvAlgorithm)
    {
        $this->cavvAlgorithm = $cavvAlgorithm;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getEci()
    {
        return $this->eci;
    }

    /**
     * @param string $eci
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setEci($eci)
    {
        $this->eci = $eci;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setEciRaw($eciRaw)
    {
        $this->eciRaw = $eciRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaReq()
    {
        return $this->paReq;
    }

    /**
     * @param string $paReq
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setPaReq($paReq)
    {
        $this->paReq = $paReq;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyPAN()
    {
        return $this->proxyPAN;
    }

    /**
     * @param string $proxyPAN
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setProxyPAN($proxyPAN)
    {
        $this->proxyPAN = $proxyPAN;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setXid($xid)
    {
        $this->xid = $xid;

        return $this;
    }

    /**
     * @return string
     */
    public function getProofXML()
    {
        return $this->proofXML;
    }

    /**
     * @param string $proofXML
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setProofXML($proofXML)
    {
        $this->proofXML = $proofXML;

        return $this;
    }

    /**
     * @return string
     */
    public function getUcafAuthenticationData()
    {
        return $this->ucafAuthenticationData;
    }

    /**
     * @param string $ucafAuthenticationData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setUcafAuthenticationData($ucafAuthenticationData)
    {
        $this->ucafAuthenticationData = $ucafAuthenticationData;

        return $this;
    }

    /**
     * @return string
     */
    public function getUcafCollectionIndicator()
    {
        return $this->ucafCollectionIndicator;
    }

    /**
     * @param string $ucafCollectionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setUcafCollectionIndicator($ucafCollectionIndicator)
    {
        $this->ucafCollectionIndicator = $ucafCollectionIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setParesStatus($paresStatus)
    {
        $this->paresStatus = $paresStatus;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setVeresEnrolled($veresEnrolled)
    {
        $this->veresEnrolled = $veresEnrolled;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationPath()
    {
        return $this->authenticationPath;
    }

    /**
     * @param string $authenticationPath
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthenticationPath($authenticationPath)
    {
        $this->authenticationPath = $authenticationPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpecificationVersion()
    {
        return $this->specificationVersion;
    }

    /**
     * @param string $specificationVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setSpecificationVersion($specificationVersion)
    {
        $this->specificationVersion = $specificationVersion;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthenticationTransactionID($authenticationTransactionID)
    {
        $this->authenticationTransactionID = $authenticationTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setDirectoryServerTransactionID($directoryServerTransactionID)
    {
        $this->directoryServerTransactionID = $directoryServerTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setChallengeRequired($challengeRequired)
    {
        $this->challengeRequired = $challengeRequired;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setThreeDSServerTransactionID($threeDSServerTransactionID)
    {
        $this->threeDSServerTransactionID = $threeDSServerTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcsRenderingType()
    {
        return $this->acsRenderingType;
    }

    /**
     * @param string $acsRenderingType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAcsRenderingType($acsRenderingType)
    {
        $this->acsRenderingType = $acsRenderingType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcsTransactionID()
    {
        return $this->acsTransactionID;
    }

    /**
     * @param string $acsTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAcsTransactionID($acsTransactionID)
    {
        $this->acsTransactionID = $acsTransactionID;

        return $this;
    }

    /**
     * @return int
     */
    public function getAuthenticationType()
    {
        return $this->authenticationType;
    }

    /**
     * @param int $authenticationType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthenticationType($authenticationType)
    {
        $this->authenticationType = $authenticationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardholderMessage()
    {
        return $this->cardholderMessage;
    }

    /**
     * @param string $cardholderMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setCardholderMessage($cardholderMessage)
    {
        $this->cardholderMessage = $cardholderMessage;

        return $this;
    }

    /**
     * @return int
     */
    public function getDirectoryServerErrorCode()
    {
        return $this->directoryServerErrorCode;
    }

    /**
     * @param int $directoryServerErrorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setDirectoryServerErrorCode($directoryServerErrorCode)
    {
        $this->directoryServerErrorCode = $directoryServerErrorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectoryServerErrorDescription()
    {
        return $this->directoryServerErrorDescription;
    }

    /**
     * @param string $directoryServerErrorDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setDirectoryServerErrorDescription($directoryServerErrorDescription)
    {
        $this->directoryServerErrorDescription = $directoryServerErrorDescription;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIvrEnabledMessage()
    {
        return $this->ivrEnabledMessage;
    }

    /**
     * @param boolean $ivrEnabledMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIvrEnabledMessage($ivrEnabledMessage)
    {
        $this->ivrEnabledMessage = $ivrEnabledMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getIvrEncryptionKey()
    {
        return $this->ivrEncryptionKey;
    }

    /**
     * @param string $ivrEncryptionKey
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIvrEncryptionKey($ivrEncryptionKey)
    {
        $this->ivrEncryptionKey = $ivrEncryptionKey;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIvrEncryptionMandatory()
    {
        return $this->ivrEncryptionMandatory;
    }

    /**
     * @param boolean $ivrEncryptionMandatory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIvrEncryptionMandatory($ivrEncryptionMandatory)
    {
        $this->ivrEncryptionMandatory = $ivrEncryptionMandatory;

        return $this;
    }

    /**
     * @return string
     */
    public function getIvrEncryptionType()
    {
        return $this->ivrEncryptionType;
    }

    /**
     * @param string $ivrEncryptionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIvrEncryptionType($ivrEncryptionType)
    {
        $this->ivrEncryptionType = $ivrEncryptionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getIvrLabel()
    {
        return $this->ivrLabel;
    }

    /**
     * @param string $ivrLabel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIvrLabel($ivrLabel)
    {
        $this->ivrLabel = $ivrLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getIvrPrompt()
    {
        return $this->ivrPrompt;
    }

    /**
     * @param string $ivrPrompt
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIvrPrompt($ivrPrompt)
    {
        $this->ivrPrompt = $ivrPrompt;

        return $this;
    }

    /**
     * @return string
     */
    public function getIvrStatusMessage()
    {
        return $this->ivrStatusMessage;
    }

    /**
     * @param string $ivrStatusMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIvrStatusMessage($ivrStatusMessage)
    {
        $this->ivrStatusMessage = $ivrStatusMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdkTransactionID()
    {
        return $this->sdkTransactionID;
    }

    /**
     * @param string $sdkTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setSdkTransactionID($sdkTransactionID)
    {
        $this->sdkTransactionID = $sdkTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getStepUpUrl()
    {
        return $this->stepUpUrl;
    }

    /**
     * @param string $stepUpUrl
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setStepUpUrl($stepUpUrl)
    {
        $this->stepUpUrl = $stepUpUrl;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setWhiteListStatus($whiteListStatus)
    {
        $this->whiteListStatus = $whiteListStatus;

        return $this;
    }

    /**
     * @return int
     */
    public function getWhiteListStatusSource()
    {
        return $this->whiteListStatusSource;
    }

    /**
     * @param int $whiteListStatusSource
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setWhiteListStatusSource($whiteListStatusSource)
    {
        $this->whiteListStatusSource = $whiteListStatusSource;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setEffectiveAuthenticationType($effectiveAuthenticationType)
    {
        $this->effectiveAuthenticationType = $effectiveAuthenticationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationStatusReason()
    {
        return $this->authenticationStatusReason;
    }

    /**
     * @param string $authenticationStatusReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthenticationStatusReason($authenticationStatusReason)
    {
        $this->authenticationStatusReason = $authenticationStatusReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkScore()
    {
        return $this->networkScore;
    }

    /**
     * @param string $networkScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setNetworkScore($networkScore)
    {
        $this->networkScore = $networkScore;

        return $this;
    }

    /**
     * @return base64Binary
     */
    public function getAuthorizationPayload()
    {
        return $this->authorizationPayload;
    }

    /**
     * @param base64Binary $authorizationPayload
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthorizationPayload($authorizationPayload)
    {
        $this->authorizationPayload = $authorizationPayload;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setChallengeCancelCode($challengeCancelCode)
    {
        $this->challengeCancelCode = $challengeCancelCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setDecoupledAuthenticationIndicator($decoupledAuthenticationIndicator)
    {
        $this->decoupledAuthenticationIndicator = $decoupledAuthenticationIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardBin()
    {
        return $this->cardBin;
    }

    /**
     * @param string $cardBin
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setCardBin($cardBin)
    {
        $this->cardBin = $cardBin;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setCardTypeName($cardTypeName)
    {
        $this->cardTypeName = $cardTypeName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionIndicator()
    {
        return $this->transactionIndicator;
    }

    /**
     * @param string $transactionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setTransactionIndicator($transactionIndicator)
    {
        $this->transactionIndicator = $transactionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getResendCountRemaining()
    {
        return $this->resendCountRemaining;
    }

    /**
     * @param string $resendCountRemaining
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setResendCountRemaining($resendCountRemaining)
    {
        $this->resendCountRemaining = $resendCountRemaining;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAcsReferenceNumber($acsReferenceNumber)
    {
        $this->acsReferenceNumber = $acsReferenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcsOperatorId()
    {
        return $this->acsOperatorId;
    }

    /**
     * @param string $acsOperatorId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAcsOperatorId($acsOperatorId)
    {
        $this->acsOperatorId = $acsOperatorId;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdciScore()
    {
        return $this->idciScore;
    }

    /**
     * @param int $idciScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIdciScore($idciScore)
    {
        $this->idciScore = $idciScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdciDecision()
    {
        return $this->idciDecision;
    }

    /**
     * @param string $idciDecision
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIdciDecision($idciDecision)
    {
        $this->idciDecision = $idciDecision;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdciReasonCode1()
    {
        return $this->idciReasonCode1;
    }

    /**
     * @param string $idciReasonCode1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIdciReasonCode1($idciReasonCode1)
    {
        $this->idciReasonCode1 = $idciReasonCode1;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdciReasonCode2()
    {
        return $this->idciReasonCode2;
    }

    /**
     * @param string $idciReasonCode2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setIdciReasonCode2($idciReasonCode2)
    {
        $this->idciReasonCode2 = $idciReasonCode2;

        return $this;
    }

    /**
     * @return int
     */
    public function getAuthenticationOutageExemptionIndicator()
    {
        return $this->authenticationOutageExemptionIndicator;
    }

    /**
     * @param int $authenticationOutageExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setAuthenticationOutageExemptionIndicator($authenticationOutageExemptionIndicator)
    {
        $this->authenticationOutageExemptionIndicator = $authenticationOutageExemptionIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setExemptionDataRaw($exemptionDataRaw)
    {
        $this->exemptionDataRaw = $exemptionDataRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getThreeDSServerOperatorId()
    {
        return $this->threeDSServerOperatorId;
    }

    /**
     * @param string $threeDSServerOperatorId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setThreeDSServerOperatorId($threeDSServerOperatorId)
    {
        $this->threeDSServerOperatorId = $threeDSServerOperatorId;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply
     */
    public function setDsReferenceNumber($dsReferenceNumber)
    {
        $this->dsReferenceNumber = $dsReferenceNumber;

        return $this;
    }
}
