<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AuthorizationOptions
{
    /**
     * @var string $authType
     */
    protected $authType;

    /**
     * @return string
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param string $authType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AuthorizationOptions
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;

        return $this;
    }
}
