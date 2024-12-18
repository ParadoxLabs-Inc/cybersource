<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VelocityCounts
{
    /**
     * @var VelocityElement[] $element
     */
    protected $element;

    /**
     * @return VelocityElement[]
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param VelocityElement[] $element
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VelocityCounts
     */
    public function setElement(array $element = null)
    {
        $this->element = $element;

        return $this;
    }
}
