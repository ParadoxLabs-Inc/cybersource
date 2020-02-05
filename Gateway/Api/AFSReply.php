<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AFSReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var int $afsResult
     */
    protected $afsResult = null;

    /**
     * @var int $hostSeverity
     */
    protected $hostSeverity = null;

    /**
     * @var string $consumerLocalTime
     */
    protected $consumerLocalTime = null;

    /**
     * @var string $afsFactorCode
     */
    protected $afsFactorCode = null;

    /**
     * @var string $addressInfoCode
     */
    protected $addressInfoCode = null;

    /**
     * @var string $hotlistInfoCode
     */
    protected $hotlistInfoCode = null;

    /**
     * @var string $internetInfoCode
     */
    protected $internetInfoCode = null;

    /**
     * @var string $phoneInfoCode
     */
    protected $phoneInfoCode = null;

    /**
     * @var string $suspiciousInfoCode
     */
    protected $suspiciousInfoCode = null;

    /**
     * @var string $velocityInfoCode
     */
    protected $velocityInfoCode = null;

    /**
     * @var string $identityInfoCode
     */
    protected $identityInfoCode = null;

    /**
     * @var string $ipCountry
     */
    protected $ipCountry = null;

    /**
     * @var string $ipState
     */
    protected $ipState = null;

    /**
     * @var string $ipCity
     */
    protected $ipCity = null;

    /**
     * @var string $ipRoutingMethod
     */
    protected $ipRoutingMethod = null;

    /**
     * @var string $ipAnonymizerStatus
     */
    protected $ipAnonymizerStatus = null;

    /**
     * @var string $ipCarrier
     */
    protected $ipCarrier = null;

    /**
     * @var string $ipOrganization
     */
    protected $ipOrganization = null;

    /**
     * @var string $scoreModelUsed
     */
    protected $scoreModelUsed = null;

    /**
     * @var string $cardBin
     */
    protected $cardBin = null;

    /**
     * @var string $binCountry
     */
    protected $binCountry = null;

    /**
     * @var string $cardAccountType
     */
    protected $cardAccountType = null;

    /**
     * @var string $cardScheme
     */
    protected $cardScheme = null;

    /**
     * @var string $cardIssuer
     */
    protected $cardIssuer = null;

    /**
     * @var DeviceFingerprint $deviceFingerprint
     */
    protected $deviceFingerprint = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setReasonCode($reasonCode)
    {
      $this->reasonCode = $reasonCode;
      return $this;
    }

    /**
     * @return int
     */
    public function getAfsResult()
    {
      return $this->afsResult;
    }

    /**
     * @param int $afsResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setAfsResult($afsResult)
    {
      $this->afsResult = $afsResult;
      return $this;
    }

    /**
     * @return int
     */
    public function getHostSeverity()
    {
      return $this->hostSeverity;
    }

    /**
     * @param int $hostSeverity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setHostSeverity($hostSeverity)
    {
      $this->hostSeverity = $hostSeverity;
      return $this;
    }

    /**
     * @return string
     */
    public function getConsumerLocalTime()
    {
      return $this->consumerLocalTime;
    }

    /**
     * @param string $consumerLocalTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setConsumerLocalTime($consumerLocalTime)
    {
      $this->consumerLocalTime = $consumerLocalTime;
      return $this;
    }

    /**
     * @return string
     */
    public function getAfsFactorCode()
    {
      return $this->afsFactorCode;
    }

    /**
     * @param string $afsFactorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setAfsFactorCode($afsFactorCode)
    {
      $this->afsFactorCode = $afsFactorCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getAddressInfoCode()
    {
      return $this->addressInfoCode;
    }

    /**
     * @param string $addressInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setAddressInfoCode($addressInfoCode)
    {
      $this->addressInfoCode = $addressInfoCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getHotlistInfoCode()
    {
      return $this->hotlistInfoCode;
    }

    /**
     * @param string $hotlistInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setHotlistInfoCode($hotlistInfoCode)
    {
      $this->hotlistInfoCode = $hotlistInfoCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getInternetInfoCode()
    {
      return $this->internetInfoCode;
    }

    /**
     * @param string $internetInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setInternetInfoCode($internetInfoCode)
    {
      $this->internetInfoCode = $internetInfoCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getPhoneInfoCode()
    {
      return $this->phoneInfoCode;
    }

    /**
     * @param string $phoneInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setPhoneInfoCode($phoneInfoCode)
    {
      $this->phoneInfoCode = $phoneInfoCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getSuspiciousInfoCode()
    {
      return $this->suspiciousInfoCode;
    }

    /**
     * @param string $suspiciousInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setSuspiciousInfoCode($suspiciousInfoCode)
    {
      $this->suspiciousInfoCode = $suspiciousInfoCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getVelocityInfoCode()
    {
      return $this->velocityInfoCode;
    }

    /**
     * @param string $velocityInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setVelocityInfoCode($velocityInfoCode)
    {
      $this->velocityInfoCode = $velocityInfoCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getIdentityInfoCode()
    {
      return $this->identityInfoCode;
    }

    /**
     * @param string $identityInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIdentityInfoCode($identityInfoCode)
    {
      $this->identityInfoCode = $identityInfoCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getIpCountry()
    {
      return $this->ipCountry;
    }

    /**
     * @param string $ipCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIpCountry($ipCountry)
    {
      $this->ipCountry = $ipCountry;
      return $this;
    }

    /**
     * @return string
     */
    public function getIpState()
    {
      return $this->ipState;
    }

    /**
     * @param string $ipState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIpState($ipState)
    {
      $this->ipState = $ipState;
      return $this;
    }

    /**
     * @return string
     */
    public function getIpCity()
    {
      return $this->ipCity;
    }

    /**
     * @param string $ipCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIpCity($ipCity)
    {
      $this->ipCity = $ipCity;
      return $this;
    }

    /**
     * @return string
     */
    public function getIpRoutingMethod()
    {
      return $this->ipRoutingMethod;
    }

    /**
     * @param string $ipRoutingMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIpRoutingMethod($ipRoutingMethod)
    {
      $this->ipRoutingMethod = $ipRoutingMethod;
      return $this;
    }

    /**
     * @return string
     */
    public function getIpAnonymizerStatus()
    {
      return $this->ipAnonymizerStatus;
    }

    /**
     * @param string $ipAnonymizerStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIpAnonymizerStatus($ipAnonymizerStatus)
    {
      $this->ipAnonymizerStatus = $ipAnonymizerStatus;
      return $this;
    }

    /**
     * @return string
     */
    public function getIpCarrier()
    {
      return $this->ipCarrier;
    }

    /**
     * @param string $ipCarrier
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIpCarrier($ipCarrier)
    {
      $this->ipCarrier = $ipCarrier;
      return $this;
    }

    /**
     * @return string
     */
    public function getIpOrganization()
    {
      return $this->ipOrganization;
    }

    /**
     * @param string $ipOrganization
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setIpOrganization($ipOrganization)
    {
      $this->ipOrganization = $ipOrganization;
      return $this;
    }

    /**
     * @return string
     */
    public function getScoreModelUsed()
    {
      return $this->scoreModelUsed;
    }

    /**
     * @param string $scoreModelUsed
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setScoreModelUsed($scoreModelUsed)
    {
      $this->scoreModelUsed = $scoreModelUsed;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setCardBin($cardBin)
    {
      $this->cardBin = $cardBin;
      return $this;
    }

    /**
     * @return string
     */
    public function getBinCountry()
    {
      return $this->binCountry;
    }

    /**
     * @param string $binCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setBinCountry($binCountry)
    {
      $this->binCountry = $binCountry;
      return $this;
    }

    /**
     * @return string
     */
    public function getCardAccountType()
    {
      return $this->cardAccountType;
    }

    /**
     * @param string $cardAccountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setCardAccountType($cardAccountType)
    {
      $this->cardAccountType = $cardAccountType;
      return $this;
    }

    /**
     * @return string
     */
    public function getCardScheme()
    {
      return $this->cardScheme;
    }

    /**
     * @param string $cardScheme
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setCardScheme($cardScheme)
    {
      $this->cardScheme = $cardScheme;
      return $this;
    }

    /**
     * @return string
     */
    public function getCardIssuer()
    {
      return $this->cardIssuer;
    }

    /**
     * @param string $cardIssuer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setCardIssuer($cardIssuer)
    {
      $this->cardIssuer = $cardIssuer;
      return $this;
    }

    /**
     * @return DeviceFingerprint
     */
    public function getDeviceFingerprint()
    {
      return $this->deviceFingerprint;
    }

    /**
     * @param DeviceFingerprint $deviceFingerprint
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSReply
     */
    public function setDeviceFingerprint($deviceFingerprint)
    {
      $this->deviceFingerprint = $deviceFingerprint;
      return $this;
    }

}
