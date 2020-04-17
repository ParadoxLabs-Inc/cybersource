<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class GetMasterpassDataReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GetMasterpassDataReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }
}
