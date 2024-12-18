<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VelocityElement
{
    /**
     * @var string $infoCode
     */
    protected $infoCode;

    /**
     * @var int $count
     */
    protected $count;

    /**
     * @param string $infoCode
     * @param int $count
     */
    public function __construct($infoCode, $count)
    {
        $this->infoCode = $infoCode;
        $this->count    = $count;
    }

    /**
     * @return string
     */
    public function getInfoCode()
    {
        return $this->infoCode;
    }

    /**
     * @param string $infoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VelocityElement
     */
    public function setInfoCode($infoCode)
    {
        $this->infoCode = $infoCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VelocityElement
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}
