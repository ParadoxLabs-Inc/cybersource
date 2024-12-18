<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VelocityCountsEarly
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VelocityCountsEarly
     */
    public function setElement(array $element = null)
    {
        $this->element = $element;

        return $this;
    }
}
