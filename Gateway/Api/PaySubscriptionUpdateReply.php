<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionUpdateReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $subscriptionID
     */
    protected $subscriptionID;

    /**
     * @var string $subscriptionIDNew
     */
    protected $subscriptionIDNew;

    /**
     * @var string $ownerMerchantID
     */
    protected $ownerMerchantID;

    /**
     * @var string $instrumentIdentifierID
     */
    protected $instrumentIdentifierID;

    /**
     * @var string $instrumentIdentifierStatus
     */
    protected $instrumentIdentifierStatus;

    /**
     * @var string $instrumentIdentifierNew
     */
    protected $instrumentIdentifierNew;

    /**
     * @var string $instrumentIdentifierSuccessorID
     */
    protected $instrumentIdentifierSuccessorID;

    /**
     * @param int $reasonCode
     * @param string $subscriptionID
     */
    public function __construct($reasonCode, $subscriptionID)
    {
        $this->reasonCode     = $reasonCode;
        $this->subscriptionID = $subscriptionID;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubscriptionID()
    {
        return $this->subscriptionID;
    }

    /**
     * @param string $subscriptionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setSubscriptionID($subscriptionID)
    {
        $this->subscriptionID = $subscriptionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubscriptionIDNew()
    {
        return $this->subscriptionIDNew;
    }

    /**
     * @param string $subscriptionIDNew
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setSubscriptionIDNew($subscriptionIDNew)
    {
        $this->subscriptionIDNew = $subscriptionIDNew;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerMerchantID()
    {
        return $this->ownerMerchantID;
    }

    /**
     * @param string $ownerMerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setOwnerMerchantID($ownerMerchantID)
    {
        $this->ownerMerchantID = $ownerMerchantID;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstrumentIdentifierID()
    {
        return $this->instrumentIdentifierID;
    }

    /**
     * @param string $instrumentIdentifierID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setInstrumentIdentifierID($instrumentIdentifierID)
    {
        $this->instrumentIdentifierID = $instrumentIdentifierID;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstrumentIdentifierStatus()
    {
        return $this->instrumentIdentifierStatus;
    }

    /**
     * @param string $instrumentIdentifierStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setInstrumentIdentifierStatus($instrumentIdentifierStatus)
    {
        $this->instrumentIdentifierStatus = $instrumentIdentifierStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstrumentIdentifierNew()
    {
        return $this->instrumentIdentifierNew;
    }

    /**
     * @param string $instrumentIdentifierNew
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setInstrumentIdentifierNew($instrumentIdentifierNew)
    {
        $this->instrumentIdentifierNew = $instrumentIdentifierNew;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstrumentIdentifierSuccessorID()
    {
        return $this->instrumentIdentifierSuccessorID;
    }

    /**
     * @param string $instrumentIdentifierSuccessorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply
     */
    public function setInstrumentIdentifierSuccessorID($instrumentIdentifierSuccessorID)
    {
        $this->instrumentIdentifierSuccessorID = $instrumentIdentifierSuccessorID;

        return $this;
    }
}
