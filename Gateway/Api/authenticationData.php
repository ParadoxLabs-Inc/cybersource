<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class authenticationData
{
    /**
     * @var string $qualityIndicator
     */
    protected $qualityIndicator;

    /**
     * @return string
     */
    public function getQualityIndicator()
    {
        return $this->qualityIndicator;
    }

    /**
     * @param string $qualityIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\authenticationData
     */
    public function setQualityIndicator($qualityIndicator)
    {
        $this->qualityIndicator = $qualityIndicator;

        return $this;
    }
}
