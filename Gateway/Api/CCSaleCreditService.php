<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCSaleCreditService
{
    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $refundReason
     */
    protected $refundReason;

    /**
     * @var string $saleRequestID
     */
    protected $saleRequestID;

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
    public function getOverridePaymentMethod()
    {
        return $this->overridePaymentMethod;
    }

    /**
     * @param string $overridePaymentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefundReason()
    {
        return $this->refundReason;
    }

    /**
     * @param string $refundReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditService
     */
    public function setRefundReason($refundReason)
    {
        $this->refundReason = $refundReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaleRequestID()
    {
        return $this->saleRequestID;
    }

    /**
     * @param string $saleRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditService
     */
    public function setSaleRequestID($saleRequestID)
    {
        $this->saleRequestID = $saleRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
