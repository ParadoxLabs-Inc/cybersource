<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DMEReply
{
    /**
     * @var string $eventType
     */
    protected $eventType;

    /**
     * @var string $eventInfo
     */
    protected $eventInfo;

    /**
     * @var string $eventHotlistInfo
     */
    protected $eventHotlistInfo;

    /**
     * @var string $eventPolicy
     */
    protected $eventPolicy;

    /**
     * @var string $eventVelocityInfoCode
     */
    protected $eventVelocityInfoCode;

    /**
     * @var AdditionalFields $additionalFields
     */
    protected $additionalFields;

    /**
     * @var MorphingElement $morphingElement
     */
    protected $morphingElement;

    /**
     * @var string $cardBin
     */
    protected $cardBin;

    /**
     * @var string $binCountry
     */
    protected $binCountry;

    /**
     * @var string $cardAccountType
     */
    protected $cardAccountType;

    /**
     * @var string $cardScheme
     */
    protected $cardScheme;

    /**
     * @var string $cardIssuer
     */
    protected $cardIssuer;

    /**
     * @var ProviderFields $providerFields
     */
    protected $providerFields;

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventInfo()
    {
        return $this->eventInfo;
    }

    /**
     * @param string $eventInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setEventInfo($eventInfo)
    {
        $this->eventInfo = $eventInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventHotlistInfo()
    {
        return $this->eventHotlistInfo;
    }

    /**
     * @param string $eventHotlistInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setEventHotlistInfo($eventHotlistInfo)
    {
        $this->eventHotlistInfo = $eventHotlistInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventPolicy()
    {
        return $this->eventPolicy;
    }

    /**
     * @param string $eventPolicy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setEventPolicy($eventPolicy)
    {
        $this->eventPolicy = $eventPolicy;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventVelocityInfoCode()
    {
        return $this->eventVelocityInfoCode;
    }

    /**
     * @param string $eventVelocityInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setEventVelocityInfoCode($eventVelocityInfoCode)
    {
        $this->eventVelocityInfoCode = $eventVelocityInfoCode;

        return $this;
    }

    /**
     * @return AdditionalFields
     */
    public function getAdditionalFields()
    {
        return $this->additionalFields;
    }

    /**
     * @param AdditionalFields $additionalFields
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setAdditionalFields($additionalFields)
    {
        $this->additionalFields = $additionalFields;

        return $this;
    }

    /**
     * @return MorphingElement
     */
    public function getMorphingElement()
    {
        return $this->morphingElement;
    }

    /**
     * @param MorphingElement $morphingElement
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setMorphingElement($morphingElement)
    {
        $this->morphingElement = $morphingElement;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setCardIssuer($cardIssuer)
    {
        $this->cardIssuer = $cardIssuer;

        return $this;
    }

    /**
     * @return ProviderFields
     */
    public function getProviderFields()
    {
        return $this->providerFields;
    }

    /**
     * @param ProviderFields $providerFields
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DMEReply
     */
    public function setProviderFields($providerFields)
    {
        $this->providerFields = $providerFields;

        return $this;
    }
}
