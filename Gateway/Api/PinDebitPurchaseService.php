<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PinDebitPurchaseService
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
     * @var boolean $partialAuthIndicator
     */
    protected $partialAuthIndicator;

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
     * @var string $ebtVoucherSerialNumber
     */
    protected $ebtVoucherSerialNumber;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPartialAuthIndicator()
    {
        return $this->partialAuthIndicator;
    }

    /**
     * @param boolean $partialAuthIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
     */
    public function setPartialAuthIndicator($partialAuthIndicator)
    {
        $this->partialAuthIndicator = $partialAuthIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getEbtVoucherSerialNumber()
    {
        return $this->ebtVoucherSerialNumber;
    }

    /**
     * @param string $ebtVoucherSerialNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
     */
    public function setEbtVoucherSerialNumber($ebtVoucherSerialNumber)
    {
        $this->ebtVoucherSerialNumber = $ebtVoucherSerialNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
