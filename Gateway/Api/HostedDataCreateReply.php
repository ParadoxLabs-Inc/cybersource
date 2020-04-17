<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class HostedDataCreateReply
{
    /**
     * @var string $responseMessage
     */
    protected $responseMessage;

    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $cardAccountNumberToken
     */
    protected $cardAccountNumberToken;

    /**
     * @var string $customerID
     */
    protected $customerID;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    /**
     * @param string $responseMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateReply
     */
    public function setResponseMessage($responseMessage)
    {
        $this->responseMessage = $responseMessage;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardAccountNumberToken()
    {
        return $this->cardAccountNumberToken;
    }

    /**
     * @param string $cardAccountNumberToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateReply
     */
    public function setCardAccountNumberToken($cardAccountNumberToken)
    {
        $this->cardAccountNumberToken = $cardAccountNumberToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * @param string $customerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateReply
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;

        return $this;
    }
}
