<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Provider
{
    /**
     * @var ProviderField[] $field
     */
    protected $field;

    /**
     * @param string $name
     */
    public function __construct(protected $name)
    {
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
    public function setField(?array $field = null)
    {
        $this->field = $field;

        return $this;
    }
}
