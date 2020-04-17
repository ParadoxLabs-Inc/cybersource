<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DeniedPartiesMatch
{
    /**
     * @var string $list
     */
    protected $list;

    /**
     * @var string[] $name
     */
    protected $name;

    /**
     * @var string[] $address
     */
    protected $address;

    /**
     * @var string[] $program
     */
    protected $program;

    /**
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param string $list
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeniedPartiesMatch
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string[] $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeniedPartiesMatch
     */
    public function setName(array $name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string[] $address
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeniedPartiesMatch
     */
    public function setAddress(array $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @param string[] $program
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeniedPartiesMatch
     */
    public function setProgram(array $program = null)
    {
        $this->program = $program;

        return $this;
    }
}
