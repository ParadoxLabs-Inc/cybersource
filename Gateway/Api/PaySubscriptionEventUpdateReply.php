<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionEventUpdateReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $ownerMerchantID
     */
    protected $ownerMerchantID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEventUpdateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEventUpdateReply
     */
    public function setOwnerMerchantID($ownerMerchantID)
    {
        $this->ownerMerchantID = $ownerMerchantID;

        return $this;
    }
}
