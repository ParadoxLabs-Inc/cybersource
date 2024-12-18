<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AuxiliaryData
{
    /**
     * @var AuxiliaryField[] $field
     */
    protected $field;

    /**
     * @return AuxiliaryField[]
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param AuxiliaryField[] $field
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AuxiliaryData
     */
    public function setField(array $field = null)
    {
        $this->field = $field;

        return $this;
    }
}
