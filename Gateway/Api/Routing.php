<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Routing
{
    /**
     * @var string $networkType
     */
    protected $networkType;

    /**
     * @var string $networkLabel
     */
    protected $networkLabel;

    /**
     * @var string $signatureCVMRequired
     */
    protected $signatureCVMRequired;

    /**
     * @return string
     */
    public function getNetworkType()
    {
        return $this->networkType;
    }

    /**
     * @param string $networkType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Routing
     */
    public function setNetworkType($networkType)
    {
        $this->networkType = $networkType;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkLabel()
    {
        return $this->networkLabel;
    }

    /**
     * @param string $networkLabel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Routing
     */
    public function setNetworkLabel($networkLabel)
    {
        $this->networkLabel = $networkLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureCVMRequired()
    {
        return $this->signatureCVMRequired;
    }

    /**
     * @param string $signatureCVMRequired
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Routing
     */
    public function setSignatureCVMRequired($signatureCVMRequired)
    {
        $this->signatureCVMRequired = $signatureCVMRequired;

        return $this;
    }
}
