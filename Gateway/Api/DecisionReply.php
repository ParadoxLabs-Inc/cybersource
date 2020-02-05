<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DecisionReply
{

    /**
     * @var int $casePriority
     */
    protected $casePriority = null;

    /**
     * @var ProfileReply $activeProfileReply
     */
    protected $activeProfileReply = null;

    /**
     * @var string $velocityInfoCode
     */
    protected $velocityInfoCode = null;

    /**
     * @var AdditionalFields $additionalFields
     */
    protected $additionalFields = null;

    /**
     * @var MorphingElement $morphingElement
     */
    protected $morphingElement = null;

    /**
     * @var ProviderFields $providerFields
     */
    protected $providerFields = null;

    /**
     * @var Travel $travel
     */
    protected $travel = null;

    /**
     * @var string $unavailableInfoCode
     */
    protected $unavailableInfoCode = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return int
     */
    public function getCasePriority()
    {
      return $this->casePriority;
    }

    /**
     * @param int $casePriority
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
     */
    public function setCasePriority($casePriority)
    {
      $this->casePriority = $casePriority;
      return $this;
    }

    /**
     * @return ProfileReply
     */
    public function getActiveProfileReply()
    {
      return $this->activeProfileReply;
    }

    /**
     * @param ProfileReply $activeProfileReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
     */
    public function setActiveProfileReply($activeProfileReply)
    {
      $this->activeProfileReply = $activeProfileReply;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
     */
    public function setVelocityInfoCode($velocityInfoCode)
    {
      $this->velocityInfoCode = $velocityInfoCode;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
     */
    public function setMorphingElement($morphingElement)
    {
      $this->morphingElement = $morphingElement;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
     */
    public function setProviderFields($providerFields)
    {
      $this->providerFields = $providerFields;
      return $this;
    }

    /**
     * @return Travel
     */
    public function getTravel()
    {
      return $this->travel;
    }

    /**
     * @param Travel $travel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
     */
    public function setTravel($travel)
    {
      $this->travel = $travel;
      return $this;
    }

    /**
     * @return string
     */
    public function getUnavailableInfoCode()
    {
      return $this->unavailableInfoCode;
    }

    /**
     * @param string $unavailableInfoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply
     */
    public function setUnavailableInfoCode($unavailableInfoCode)
    {
      $this->unavailableInfoCode = $unavailableInfoCode;
      return $this;
    }

}
