<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionCreateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateReply
     */
    public function setSubscriptionID($subscriptionID)
    {
        $this->subscriptionID = $subscriptionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateReply
     */
    public function setInstrumentIdentifierSuccessorID($instrumentIdentifierSuccessorID)
    {
        $this->instrumentIdentifierSuccessorID = $instrumentIdentifierSuccessorID;

        return $this;
    }
}
