<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class language
{
    /**
     * @var string $code
     */
    protected $code;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\language
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
