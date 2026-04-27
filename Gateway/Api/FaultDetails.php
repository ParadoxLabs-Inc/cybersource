<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class FaultDetails
{
    /**
     * @param string $requestID
     */
    public function __construct(protected $requestID)
    {
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
