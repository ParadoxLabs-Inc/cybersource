<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalAuthReversalReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $authorizationId
     */
    protected $authorizationId;

    /**
     * @var string $correlationID
     */
    protected $correlationID;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthReversalReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationId()
    {
        return $this->authorizationId;
    }

    /**
     * @param string $authorizationId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthReversalReply
     */
    public function setAuthorizationId($authorizationId)
    {
        $this->authorizationId = $authorizationId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrelationID()
    {
        return $this->correlationID;
    }

    /**
     * @param string $correlationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthReversalReply
     */
    public function setCorrelationID($correlationID)
    {
        $this->correlationID = $correlationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthReversalReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
