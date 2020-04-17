<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayerAuthValidateReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

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
     * @var string $xid
     */
    protected $xid;

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
     * @var string $specificationVersion
     */
    protected $specificationVersion;

    /**
     * @var string $directoryServerTransactionID
     */
    protected $directoryServerTransactionID;

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
     * @var int $directoryServerErrorCode
     */
    protected $directoryServerErrorCode;

    /**
     * @var string $directoryServerErrorDescription
     */
    protected $directoryServerErrorDescription;

    /**
     * @var int $interactionCounter
     */
    protected $interactionCounter;

    /**
     * @var string $sdkTransactionID
     */
    protected $sdkTransactionID;

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
     * @var int $authenticationStatusReason
     */
    protected $authenticationStatusReason;

    /**
     * @var int $challengeCancelCode
     */
    protected $challengeCancelCode;

    /**
     * @var base64Binary $authorizationPayload
     */
    protected $authorizationPayload;

    /**
     * @var string $cardBin
     */
    protected $cardBin;

    /**
     * @var string $cardTypeName
     */
    protected $cardTypeName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setXid($xid)
    {
        $this->xid = $xid;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setParesStatus($paresStatus)
    {
        $this->paresStatus = $paresStatus;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setSpecificationVersion($specificationVersion)
    {
        $this->specificationVersion = $specificationVersion;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setAuthenticationType($authenticationType)
    {
        $this->authenticationType = $authenticationType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setDirectoryServerErrorDescription($directoryServerErrorDescription)
    {
        $this->directoryServerErrorDescription = $directoryServerErrorDescription;

        return $this;
    }

    /**
     * @return int
     */
    public function getInteractionCounter()
    {
        return $this->interactionCounter;
    }

    /**
     * @param int $interactionCounter
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setInteractionCounter($interactionCounter)
    {
        $this->interactionCounter = $interactionCounter;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setSdkTransactionID($sdkTransactionID)
    {
        $this->sdkTransactionID = $sdkTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setEffectiveAuthenticationType($effectiveAuthenticationType)
    {
        $this->effectiveAuthenticationType = $effectiveAuthenticationType;

        return $this;
    }

    /**
     * @return int
     */
    public function getAuthenticationStatusReason()
    {
        return $this->authenticationStatusReason;
    }

    /**
     * @param int $authenticationStatusReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setAuthenticationStatusReason($authenticationStatusReason)
    {
        $this->authenticationStatusReason = $authenticationStatusReason;

        return $this;
    }

    /**
     * @return int
     */
    public function getChallengeCancelCode()
    {
        return $this->challengeCancelCode;
    }

    /**
     * @param int $challengeCancelCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setChallengeCancelCode($challengeCancelCode)
    {
        $this->challengeCancelCode = $challengeCancelCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setAuthorizationPayload($authorizationPayload)
    {
        $this->authorizationPayload = $authorizationPayload;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply
     */
    public function setCardTypeName($cardTypeName)
    {
        $this->cardTypeName = $cardTypeName;

        return $this;
    }
}
