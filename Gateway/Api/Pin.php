<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Pin
{

    /**
     * @var string $entryCapability
     */
    protected $entryCapability = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getEntryCapability()
    {
        return $this->entryCapability;
    }

    /**
     * @param string $entryCapability
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pin
     */
    public function setEntryCapability($entryCapability)
    {
        $this->entryCapability = $entryCapability;

        return $this;
    }

}
