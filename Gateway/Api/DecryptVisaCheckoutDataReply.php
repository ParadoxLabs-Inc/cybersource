<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DecryptVisaCheckoutDataReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecryptVisaCheckoutDataReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

}
