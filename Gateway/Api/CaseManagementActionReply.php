<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CaseManagementActionReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CaseManagementActionReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }
}
