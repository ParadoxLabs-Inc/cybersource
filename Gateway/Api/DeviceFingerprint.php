<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class DeviceFingerprint
{

    /**
     * @var boolean $cookiesEnabled
     */
    protected $cookiesEnabled = null;

    /**
     * @var boolean $flashEnabled
     */
    protected $flashEnabled = null;

    /**
     * @var string $hash
     */
    protected $hash = null;

    /**
     * @var boolean $imagesEnabled
     */
    protected $imagesEnabled = null;

    /**
     * @var boolean $javascriptEnabled
     */
    protected $javascriptEnabled = null;

    /**
     * @var string $proxyIPAddress
     */
    protected $proxyIPAddress = null;

    /**
     * @var string $proxyIPAddressActivities
     */
    protected $proxyIPAddressActivities = null;

    /**
     * @var string $proxyIPAddressAttributes
     */
    protected $proxyIPAddressAttributes = null;

    /**
     * @var string $proxyServerType
     */
    protected $proxyServerType = null;

    /**
     * @var string $trueIPAddress
     */
    protected $trueIPAddress = null;

    /**
     * @var string $trueIPAddressActivities
     */
    protected $trueIPAddressActivities = null;

    /**
     * @var string $trueIPAddressAttributes
     */
    protected $trueIPAddressAttributes = null;

    /**
     * @var string $trueIPAddressCity
     */
    protected $trueIPAddressCity = null;

    /**
     * @var string $trueIPAddressState
     */
    protected $trueIPAddressState = null;

    /**
     * @var string $trueIPAddressCountry
     */
    protected $trueIPAddressCountry = null;

    /**
     * @var string $smartID
     */
    protected $smartID = null;

    /**
     * @var string $smartIDConfidenceLevel
     */
    protected $smartIDConfidenceLevel = null;

    /**
     * @var string $screenResolution
     */
    protected $screenResolution = null;

    /**
     * @var string $browserLanguage
     */
    protected $browserLanguage = null;

    /**
     * @var string $agentType
     */
    protected $agentType = null;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime = null;

    /**
     * @var int $profileDuration
     */
    protected $profileDuration = null;

    /**
     * @var string $profiledURL
     */
    protected $profiledURL = null;

    /**
     * @var int $timeOnPage
     */
    protected $timeOnPage = null;

    /**
     * @var string $deviceMatch
     */
    protected $deviceMatch = null;

    /**
     * @var string $firstEncounter
     */
    protected $firstEncounter = null;

    /**
     * @var string $flashOS
     */
    protected $flashOS = null;

    /**
     * @var string $flashVersion
     */
    protected $flashVersion = null;

    /**
     * @var string $deviceLatitude
     */
    protected $deviceLatitude = null;

    /**
     * @var string $deviceLongitude
     */
    protected $deviceLongitude = null;

    /**
     * @var string $gpsAccuracy
     */
    protected $gpsAccuracy = null;

    /**
     * @var int $jbRoot
     */
    protected $jbRoot = null;

    /**
     * @var string $jbRootReason
     */
    protected $jbRootReason = null;

    public function __construct()
    {
    }

    /**
     * @return boolean
     */
    public function getCookiesEnabled()
    {
        return $this->cookiesEnabled;
    }

    /**
     * @param boolean $cookiesEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setCookiesEnabled($cookiesEnabled)
    {
        $this->cookiesEnabled = $cookiesEnabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getFlashEnabled()
    {
        return $this->flashEnabled;
    }

    /**
     * @param boolean $flashEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setFlashEnabled($flashEnabled)
    {
        $this->flashEnabled = $flashEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getImagesEnabled()
    {
        return $this->imagesEnabled;
    }

    /**
     * @param boolean $imagesEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setImagesEnabled($imagesEnabled)
    {
        $this->imagesEnabled = $imagesEnabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getJavascriptEnabled()
    {
        return $this->javascriptEnabled;
    }

    /**
     * @param boolean $javascriptEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setJavascriptEnabled($javascriptEnabled)
    {
        $this->javascriptEnabled = $javascriptEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyIPAddress()
    {
        return $this->proxyIPAddress;
    }

    /**
     * @param string $proxyIPAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setProxyIPAddress($proxyIPAddress)
    {
        $this->proxyIPAddress = $proxyIPAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyIPAddressActivities()
    {
        return $this->proxyIPAddressActivities;
    }

    /**
     * @param string $proxyIPAddressActivities
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setProxyIPAddressActivities($proxyIPAddressActivities)
    {
        $this->proxyIPAddressActivities = $proxyIPAddressActivities;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyIPAddressAttributes()
    {
        return $this->proxyIPAddressAttributes;
    }

    /**
     * @param string $proxyIPAddressAttributes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setProxyIPAddressAttributes($proxyIPAddressAttributes)
    {
        $this->proxyIPAddressAttributes = $proxyIPAddressAttributes;

        return $this;
    }

    /**
     * @return string
     */
    public function getProxyServerType()
    {
        return $this->proxyServerType;
    }

    /**
     * @param string $proxyServerType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setProxyServerType($proxyServerType)
    {
        $this->proxyServerType = $proxyServerType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrueIPAddress()
    {
        return $this->trueIPAddress;
    }

    /**
     * @param string $trueIPAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setTrueIPAddress($trueIPAddress)
    {
        $this->trueIPAddress = $trueIPAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrueIPAddressActivities()
    {
        return $this->trueIPAddressActivities;
    }

    /**
     * @param string $trueIPAddressActivities
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setTrueIPAddressActivities($trueIPAddressActivities)
    {
        $this->trueIPAddressActivities = $trueIPAddressActivities;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrueIPAddressAttributes()
    {
        return $this->trueIPAddressAttributes;
    }

    /**
     * @param string $trueIPAddressAttributes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setTrueIPAddressAttributes($trueIPAddressAttributes)
    {
        $this->trueIPAddressAttributes = $trueIPAddressAttributes;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrueIPAddressCity()
    {
        return $this->trueIPAddressCity;
    }

    /**
     * @param string $trueIPAddressCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setTrueIPAddressCity($trueIPAddressCity)
    {
        $this->trueIPAddressCity = $trueIPAddressCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrueIPAddressState()
    {
        return $this->trueIPAddressState;
    }

    /**
     * @param string $trueIPAddressState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setTrueIPAddressState($trueIPAddressState)
    {
        $this->trueIPAddressState = $trueIPAddressState;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrueIPAddressCountry()
    {
        return $this->trueIPAddressCountry;
    }

    /**
     * @param string $trueIPAddressCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setTrueIPAddressCountry($trueIPAddressCountry)
    {
        $this->trueIPAddressCountry = $trueIPAddressCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getSmartID()
    {
        return $this->smartID;
    }

    /**
     * @param string $smartID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setSmartID($smartID)
    {
        $this->smartID = $smartID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSmartIDConfidenceLevel()
    {
        return $this->smartIDConfidenceLevel;
    }

    /**
     * @param string $smartIDConfidenceLevel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setSmartIDConfidenceLevel($smartIDConfidenceLevel)
    {
        $this->smartIDConfidenceLevel = $smartIDConfidenceLevel;

        return $this;
    }

    /**
     * @return string
     */
    public function getScreenResolution()
    {
        return $this->screenResolution;
    }

    /**
     * @param string $screenResolution
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setScreenResolution($screenResolution)
    {
        $this->screenResolution = $screenResolution;

        return $this;
    }

    /**
     * @return string
     */
    public function getBrowserLanguage()
    {
        return $this->browserLanguage;
    }

    /**
     * @param string $browserLanguage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setBrowserLanguage($browserLanguage)
    {
        $this->browserLanguage = $browserLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getAgentType()
    {
        return $this->agentType;
    }

    /**
     * @param string $agentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setAgentType($agentType)
    {
        $this->agentType = $agentType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
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
     * @return int
     */
    public function getProfileDuration()
    {
        return $this->profileDuration;
    }

    /**
     * @param int $profileDuration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setProfileDuration($profileDuration)
    {
        $this->profileDuration = $profileDuration;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfiledURL()
    {
        return $this->profiledURL;
    }

    /**
     * @param string $profiledURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setProfiledURL($profiledURL)
    {
        $this->profiledURL = $profiledURL;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeOnPage()
    {
        return $this->timeOnPage;
    }

    /**
     * @param int $timeOnPage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setTimeOnPage($timeOnPage)
    {
        $this->timeOnPage = $timeOnPage;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceMatch()
    {
        return $this->deviceMatch;
    }

    /**
     * @param string $deviceMatch
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setDeviceMatch($deviceMatch)
    {
        $this->deviceMatch = $deviceMatch;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstEncounter()
    {
        return $this->firstEncounter;
    }

    /**
     * @param string $firstEncounter
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setFirstEncounter($firstEncounter)
    {
        $this->firstEncounter = $firstEncounter;

        return $this;
    }

    /**
     * @return string
     */
    public function getFlashOS()
    {
        return $this->flashOS;
    }

    /**
     * @param string $flashOS
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setFlashOS($flashOS)
    {
        $this->flashOS = $flashOS;

        return $this;
    }

    /**
     * @return string
     */
    public function getFlashVersion()
    {
        return $this->flashVersion;
    }

    /**
     * @param string $flashVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setFlashVersion($flashVersion)
    {
        $this->flashVersion = $flashVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceLatitude()
    {
        return $this->deviceLatitude;
    }

    /**
     * @param string $deviceLatitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setDeviceLatitude($deviceLatitude)
    {
        $this->deviceLatitude = $deviceLatitude;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceLongitude()
    {
        return $this->deviceLongitude;
    }

    /**
     * @param string $deviceLongitude
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setDeviceLongitude($deviceLongitude)
    {
        $this->deviceLongitude = $deviceLongitude;

        return $this;
    }

    /**
     * @return string
     */
    public function getGpsAccuracy()
    {
        return $this->gpsAccuracy;
    }

    /**
     * @param string $gpsAccuracy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setGpsAccuracy($gpsAccuracy)
    {
        $this->gpsAccuracy = $gpsAccuracy;

        return $this;
    }

    /**
     * @return int
     */
    public function getJbRoot()
    {
        return $this->jbRoot;
    }

    /**
     * @param int $jbRoot
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setJbRoot($jbRoot)
    {
        $this->jbRoot = $jbRoot;

        return $this;
    }

    /**
     * @return string
     */
    public function getJbRootReason()
    {
        return $this->jbRootReason;
    }

    /**
     * @param string $jbRootReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint
     */
    public function setJbRootReason($jbRootReason)
    {
        $this->jbRootReason = $jbRootReason;

        return $this;
    }

}
