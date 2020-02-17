<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APBillingAgreementReply
{

    /**
     * @var string $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var string $amount
     */
    protected $amount = null;

    /**
     * @var string $status
     */
    protected $status = null;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse = null;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getReasonCode()
    {
      return $this->reasonCode;
    }

    /**
     * @param string $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementReply
     */
    public function setReasonCode($reasonCode)
    {
      $this->reasonCode = $reasonCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
      return $this->amount;
    }

    /**
     * @param string $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementReply
     */
    public function setAmount($amount)
    {
      $this->amount = $amount;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementReply
     */
    public function setProcessorResponse($processorResponse)
    {
      $this->processorResponse = $processorResponse;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementReply
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

}