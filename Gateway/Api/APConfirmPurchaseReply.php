<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APConfirmPurchaseReply
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
     * @var string $processorResponse
     */
    protected $processorResponse = null;

    /**
     * @var float $amount
     */
    protected $amount = null;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime = null;

    /**
     * @var string $providerResponse
     */
    protected $providerResponse = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseReply
     */
    public function setStatus($status)
    {
      $this->status = $status;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseReply
     */
    public function setProcessorResponse($processorResponse)
    {
      $this->processorResponse = $processorResponse;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseReply
     */
    public function setAmount($amount)
    {
      $this->amount = $amount;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
      if ($this->dateTime == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->dateTime);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $dateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseReply
     */
    public function setDateTime(\DateTime $dateTime = null)
    {
      if ($dateTime == null) {
       $this->dateTime = null;
      } else {
        $this->dateTime = $dateTime->format(\DateTime::ATOM);
      }
      return $this;
    }

    /**
     * @return string
     */
    public function getProviderResponse()
    {
      return $this->providerResponse;
    }

    /**
     * @param string $providerResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseReply
     */
    public function setProviderResponse($providerResponse)
    {
      $this->providerResponse = $providerResponse;
      return $this;
    }

}
