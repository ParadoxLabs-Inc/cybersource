<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaymentNetworkToken
{
    /**
     * @var string $requestorID
     */
    protected $requestorID;

    /**
     * @var string $transactionType
     */
    protected $transactionType;

    /**
     * @var string $assuranceLevel
     */
    protected $assuranceLevel;

    /**
     * @var string $accountStatus
     */
    protected $accountStatus;

    /**
     * @var string $originalCardCategory
     */
    protected $originalCardCategory;

    /**
     * @var string $deviceTechType
     */
    protected $deviceTechType;

    /**
     * @return string
     */
    public function getRequestorID()
    {
        return $this->requestorID;
    }

    /**
     * @param string $requestorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentNetworkToken
     */
    public function setRequestorID($requestorID)
    {
        $this->requestorID = $requestorID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentNetworkToken
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssuranceLevel()
    {
        return $this->assuranceLevel;
    }

    /**
     * @param string $assuranceLevel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentNetworkToken
     */
    public function setAssuranceLevel($assuranceLevel)
    {
        $this->assuranceLevel = $assuranceLevel;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountStatus()
    {
        return $this->accountStatus;
    }

    /**
     * @param string $accountStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentNetworkToken
     */
    public function setAccountStatus($accountStatus)
    {
        $this->accountStatus = $accountStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalCardCategory()
    {
        return $this->originalCardCategory;
    }

    /**
     * @param string $originalCardCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentNetworkToken
     */
    public function setOriginalCardCategory($originalCardCategory)
    {
        $this->originalCardCategory = $originalCardCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceTechType()
    {
        return $this->deviceTechType;
    }

    /**
     * @param string $deviceTechType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentNetworkToken
     */
    public function setDeviceTechType($deviceTechType)
    {
        $this->deviceTechType = $deviceTechType;

        return $this;
    }
}
