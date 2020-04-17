<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class MorphingElement
{
    /**
     * @var Element[] $element
     */
    protected $element;

    /**
     * @return Element[]
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param Element[] $element
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MorphingElement
     */
    public function setElement(array $element = null)
    {
        $this->element = $element;

        return $this;
    }
}
