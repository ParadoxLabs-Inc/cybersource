<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Provider
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var ProviderField[] $field
     */
    protected $field;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Provider
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ProviderField[]
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param ProviderField[] $field
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Provider
     */
    public function setField(array $field = null)
    {
        $this->field = $field;

        return $this;
    }
}
