<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Authentication
{
    /**
     * @var string $outOfScope
     */
    protected $outOfScope;

    /**
     * @var string $exemption
     */
    protected $exemption;

    /**
     * @return string
     */
    public function getOutOfScope()
    {
        return $this->outOfScope;
    }

    /**
     * @param string $outOfScope
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Authentication
     */
    public function setOutOfScope($outOfScope)
    {
        $this->outOfScope = $outOfScope;

        return $this;
    }

    /**
     * @return string
     */
    public function getExemption()
    {
        return $this->exemption;
    }

    /**
     * @param string $exemption
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Authentication
     */
    public function setExemption($exemption)
    {
        $this->exemption = $exemption;

        return $this;
    }
}
