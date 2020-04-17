<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VCCustomData
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
     * @var int $id
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCCustomData
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCCustomData
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCCustomData
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
