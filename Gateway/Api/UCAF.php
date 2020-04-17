<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class UCAF
{
    /**
     * @var string $authenticationData
     */
    protected $authenticationData;

    /**
     * @var string $collectionIndicator
     */
    protected $collectionIndicator;

    /**
     * @var string $downgradeReasonCode
     */
    protected $downgradeReasonCode;

    /**
     * @return string
     */
    public function getAuthenticationData()
    {
        return $this->authenticationData;
    }

    /**
     * @param string $authenticationData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\UCAF
     */
    public function setAuthenticationData($authenticationData)
    {
        $this->authenticationData = $authenticationData;

        return $this;
    }

    /**
     * @return string
     */
    public function getCollectionIndicator()
    {
        return $this->collectionIndicator;
    }

    /**
     * @param string $collectionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\UCAF
     */
    public function setCollectionIndicator($collectionIndicator)
    {
        $this->collectionIndicator = $collectionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDowngradeReasonCode()
    {
        return $this->downgradeReasonCode;
    }

    /**
     * @param string $downgradeReasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\UCAF
     */
    public function setDowngradeReasonCode($downgradeReasonCode)
    {
        $this->downgradeReasonCode = $downgradeReasonCode;

        return $this;
    }
}
