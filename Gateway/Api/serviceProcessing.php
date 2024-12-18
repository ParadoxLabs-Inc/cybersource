<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class serviceProcessing
{
    /**
     * @var string $serviceType
     */
    protected $serviceType;

    /**
     * @return string
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }

    /**
     * @param string $serviceType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\serviceProcessing
     */
    public function setServiceType($serviceType)
    {
        $this->serviceType = $serviceType;

        return $this;
    }
}
