<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class RequestReserved
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $value
     */
    protected $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestReserved
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestReserved
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
