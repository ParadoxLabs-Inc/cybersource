<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCDCCUpdateReply
{
    /**
     * @param int $reasonCode
     */
    public function __construct(protected $reasonCode)
    {
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }
}
