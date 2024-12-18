<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TokenProvisioningInformation
{
    /**
     * @var boolean $consumerConsentObtained
     */
    protected $consumerConsentObtained;

    /**
     * @var boolean $multiFactorAuthenticated
     */
    protected $multiFactorAuthenticated;

    /**
     * @return boolean
     */
    public function getConsumerConsentObtained()
    {
        return $this->consumerConsentObtained;
    }

    /**
     * @param boolean $consumerConsentObtained
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenProvisioningInformation
     */
    public function setConsumerConsentObtained($consumerConsentObtained)
    {
        $this->consumerConsentObtained = $consumerConsentObtained;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getMultiFactorAuthenticated()
    {
        return $this->multiFactorAuthenticated;
    }

    /**
     * @param boolean $multiFactorAuthenticated
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenProvisioningInformation
     */
    public function setMultiFactorAuthenticated($multiFactorAuthenticated)
    {
        $this->multiFactorAuthenticated = $multiFactorAuthenticated;

        return $this;
    }
}
