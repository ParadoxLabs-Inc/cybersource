<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionDeleteReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteReply
     */
    public function setInstrumentIdentifierID($instrumentIdentifierID)
    {
        $this->instrumentIdentifierID = $instrumentIdentifierID;

        return $this;
    }
}
