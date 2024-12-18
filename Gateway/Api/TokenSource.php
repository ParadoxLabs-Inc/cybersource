<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TokenSource
{
    /**
     * @var string $transientToken
     */
    protected $transientToken;

    /**
     * @var string $networkTokenOption
     */
    protected $networkTokenOption;

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

    /**
     * @return string
     */
    public function getNetworkTokenOption()
    {
        return $this->networkTokenOption;
    }

    /**
     * @param string $networkTokenOption
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenSource
     */
    public function setNetworkTokenOption($networkTokenOption)
    {
        $this->networkTokenOption = $networkTokenOption;

        return $this;
    }
}
