<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AgencyInformation
{
    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AgencyInformation
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AgencyInformation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
