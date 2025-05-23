<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AbortService
{
    /**
     * @var string $authRequestID
     */
    protected $authRequestID;

    /**
     * @var string $creditRequestID
     */
    protected $creditRequestID;

    /**
     * @var string $cardholderVerificationMethod
     */
    protected $cardholderVerificationMethod;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var boolean $run
     */
    protected $run;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
        $this->run = $run;
    }

    /**
     * @return string
     */
    public function getAuthRequestID()
    {
        return $this->authRequestID;
    }

    /**
     * @param string $authRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AbortService
     */
    public function setAuthRequestID($authRequestID)
    {
        $this->authRequestID = $authRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreditRequestID()
    {
        return $this->creditRequestID;
    }

    /**
     * @param string $creditRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AbortService
     */
    public function setCreditRequestID($creditRequestID)
    {
        $this->creditRequestID = $creditRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardholderVerificationMethod()
    {
        return $this->cardholderVerificationMethod;
    }

    /**
     * @param string $cardholderVerificationMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AbortService
     */
    public function setCardholderVerificationMethod($cardholderVerificationMethod)
    {
        $this->cardholderVerificationMethod = $cardholderVerificationMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommerceIndicator()
    {
        return $this->commerceIndicator;
    }

    /**
     * @param string $commerceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AbortService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AbortService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
