<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Capture
{
    /**
     * @var string $reason
     */
    protected $reason;

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Capture
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }
}
