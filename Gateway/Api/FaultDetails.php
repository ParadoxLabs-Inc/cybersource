<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class FaultDetails
{
    /**
     * @var string $requestID
     */
    protected $requestID;

    /**
     * @param string $requestID
     */
    public function __construct($requestID)
    {
        $this->requestID = $requestID;
    }

    /**
     * @return string
     */
    public function getRequestID()
    {
        return $this->requestID;
    }

    /**
     * @param string $requestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FaultDetails
     */
    public function setRequestID($requestID)
    {
        $this->requestID = $requestID;

        return $this;
    }
}
