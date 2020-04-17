<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TokenSource
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenSource
     */
    public function setTransientToken($transientToken)
    {
        $this->transientToken = $transientToken;

        return $this;
    }
}
