<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AdditionalToken
{
    /**
     * @var string $responseInformation
     */
    protected $responseInformation;

    /**
     * @return string
     */
    public function getResponseInformation()
    {
        return $this->responseInformation;
    }

    /**
     * @param string $responseInformation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AdditionalToken
     */
    public function setResponseInformation($responseInformation)
    {
        $this->responseInformation = $responseInformation;

        return $this;
    }
}
