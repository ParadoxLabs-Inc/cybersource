<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class mPOS
{
    /**
     * @var string $deviceType
     */
    protected $deviceType;

    /**
     * @return string
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }

    /**
     * @param string $deviceType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\mPOS
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;

        return $this;
    }
}
