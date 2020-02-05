<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ProviderField
{

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $value
     */
    protected $value = null;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
      $this->name = $name;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProviderField
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProviderField
     */
    public function setValue($value)
    {
      $this->value = $value;
      return $this;
    }

}
