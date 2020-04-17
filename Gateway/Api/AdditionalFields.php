<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AdditionalFields
{
    /**
     * @var Field[] $field
     */
    protected $field;

    /**
     * @return Field[]
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param Field[] $field
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AdditionalFields
     */
    public function setField(array $field = null)
    {
        $this->field = $field;

        return $this;
    }
}
