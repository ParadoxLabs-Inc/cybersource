<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCSaleReversalReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var float $amount
     */
    protected $amount = null;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode = null;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse = null;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID = null;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalReply
     */
    public function setReasonCode($reasonCode)
    {
      $this->reasonCode = $reasonCode;
      return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
      return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalReply
     */
    public function setAmount($amount)
    {
      $this->amount = $amount;
      return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode()
    {
      return $this->authorizationCode;
    }

    /**
     * @param string $authorizationCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
      $this->authorizationCode = $authorizationCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getProcessorResponse()
    {
      return $this->processorResponse;
    }

    /**
     * @param string $processorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalReply
     */
    public function setProcessorResponse($processorResponse)
    {
      $this->processorResponse = $processorResponse;
      return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationID()
    {
      return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalReply
     */
    public function setReconciliationID($reconciliationID)
    {
      $this->reconciliationID = $reconciliationID;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestDateTime()
    {
      if ($this->requestDateTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->requestDateTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $requestDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalReply
     */
    public function setRequestDateTime(\DateTime $requestDateTime = null)
    {
      if ($requestDateTime == null) {
       $this->requestDateTime = null;
      } else {
        $this->requestDateTime = $requestDateTime->format(\DateTime::ATOM);
      }
      return $this;
    }

}