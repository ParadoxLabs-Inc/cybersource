<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayerAuthSetupReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $referenceID
     */
    protected $referenceID;

    /**
     * @var string $deviceDataCollectionURL
     */
    protected $deviceDataCollectionURL;

    /**
     * @var string $accessToken
     */
    protected $accessToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthSetupReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceID()
    {
        return $this->referenceID;
    }

    /**
     * @param string $referenceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthSetupReply
     */
    public function setReferenceID($referenceID)
    {
        $this->referenceID = $referenceID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceDataCollectionURL()
    {
        return $this->deviceDataCollectionURL;
    }

    /**
     * @param string $deviceDataCollectionURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthSetupReply
     */
    public function setDeviceDataCollectionURL($deviceDataCollectionURL)
    {
        $this->deviceDataCollectionURL = $deviceDataCollectionURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthSetupReply
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
