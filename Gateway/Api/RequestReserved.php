<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class RequestReserved
{
    /**
     * @param string $name
     * @param string $value
     */
    public function __construct(
        protected $name,
        protected $value,
    ) {
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
