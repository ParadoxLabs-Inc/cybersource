<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Field
{
    /**
     * @var string $provider
     */
    protected $provider;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $value
     */
    protected $value;

    /**
     * @param string $provider
     * @param string $name
     * @param string $value
     */
    public function __construct($provider, $name, $value)
    {
        $this->provider = $provider;
        $this->name     = $name;
        $this->value    = $value;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Field
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Field
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Field
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
