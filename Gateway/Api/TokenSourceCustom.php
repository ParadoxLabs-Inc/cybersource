<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TokenSourceCustom
{

    /**
     * @var string $transientToken
     */
    protected $transientToken = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getTransientToken()
    {
        return $this->transientToken;
    }

    /**
     * @param string $transientToken
     * @return TokenSource
     */
    public function setTransientToken($transientToken)
    {
        $this->transientToken = $transientToken;

        return $this;
    }

}
