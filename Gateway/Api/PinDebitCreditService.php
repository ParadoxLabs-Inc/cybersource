<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PinDebitCreditService
{
    /**
     * @var string $networkOrder
     */
    protected $networkOrder;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod;

    /**
     * @var string $paymentType
     */
    protected $paymentType;

    /**
     * @var string $ebtCategory
     */
    protected $ebtCategory;

    /**
     * @var string $transactionType
     */
    protected $transactionType;

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
    public function getNetworkOrder()
    {
        return $this->networkOrder;
    }

    /**
     * @param string $networkOrder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
     */
    public function setNetworkOrder($networkOrder)
    {
        $this->networkOrder = $networkOrder;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getEbtCategory()
    {
        return $this->ebtCategory;
    }

    /**
     * @param string $ebtCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
     */
    public function setEbtCategory($ebtCategory)
    {
        $this->ebtCategory = $ebtCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
