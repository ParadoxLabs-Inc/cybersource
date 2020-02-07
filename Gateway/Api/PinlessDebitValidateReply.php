<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PinlessDebitValidateReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var string $status
     */
    protected $status = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitValidateReply
     */
    public function setReasonCode($reasonCode)
    {
      $this->reasonCode = $reasonCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param string $status
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitValidateReply
     */
    public function setStatus($status)
    {
      $this->status = $status;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitValidateReply
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
