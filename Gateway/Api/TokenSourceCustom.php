<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TokenSourceCustom
{
    /**
     * @var string $transientToken
     */
    protected $transientToken;

    /**
     * @return string
     */
    public function getTransientToken()
    {
        return $this->transientToken;
    }

    /**
     * @param string $transientToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenSourceCustom
     */
    public function setTransientToken($transientToken)
    {
        $this->transientToken = $transientToken;

        return $this;
    }
}
