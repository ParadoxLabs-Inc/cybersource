<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalUpdateAgreementService
{

    /**
     * @var string $paypalBillingAgreementId
     */
    protected $paypalBillingAgreementId = null;

    /**
     * @var string $paypalBillingAgreementStatus
     */
    protected $paypalBillingAgreementStatus = null;

    /**
     * @var string $paypalBillingAgreementDesc
     */
    protected $paypalBillingAgreementDesc = null;

    /**
     * @var string $paypalBillingAgreementCustom
     */
    protected $paypalBillingAgreementCustom = null;

    /**
     * @var boolean $run
     */
    protected $run = null;

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
    public function getPaypalBillingAgreementId()
    {
      return $this->paypalBillingAgreementId;
    }

    /**
     * @param string $paypalBillingAgreementId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementService
     */
    public function setPaypalBillingAgreementId($paypalBillingAgreementId)
    {
      $this->paypalBillingAgreementId = $paypalBillingAgreementId;
      return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementStatus()
    {
      return $this->paypalBillingAgreementStatus;
    }

    /**
     * @param string $paypalBillingAgreementStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementService
     */
    public function setPaypalBillingAgreementStatus($paypalBillingAgreementStatus)
    {
      $this->paypalBillingAgreementStatus = $paypalBillingAgreementStatus;
      return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementDesc()
    {
      return $this->paypalBillingAgreementDesc;
    }

    /**
     * @param string $paypalBillingAgreementDesc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementService
     */
    public function setPaypalBillingAgreementDesc($paypalBillingAgreementDesc)
    {
      $this->paypalBillingAgreementDesc = $paypalBillingAgreementDesc;
      return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementCustom()
    {
      return $this->paypalBillingAgreementCustom;
    }

    /**
     * @param string $paypalBillingAgreementCustom
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementService
     */
    public function setPaypalBillingAgreementCustom($paypalBillingAgreementCustom)
    {
      $this->paypalBillingAgreementCustom = $paypalBillingAgreementCustom;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementService
     */
    public function setRun($run)
    {
      $this->run = $run;
      return $this;
    }

}
