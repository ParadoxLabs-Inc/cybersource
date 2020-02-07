<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ChinaRefundReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime = null;

    /**
     * @var float $amount
     */
    protected $amount = null;

    /**
     * @var string $currency
     */
    protected $currency = null;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundReply
     */
    public function setReasonCode($reasonCode)
    {
      $this->reasonCode = $reasonCode;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundReply
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

    /**
     * @return float
     */
    public function getAmount()
    {
      return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundReply
     */
    public function setAmount($amount)
    {
      $this->amount = $amount;
      return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
      return $this->currency;
    }

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundReply
     */
    public function setCurrency($currency)
    {
      $this->currency = $currency;
      return $this;
    }

}
