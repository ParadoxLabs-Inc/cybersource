<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class OCTService
{
    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $networkOrder
     */
    protected $networkOrder;

    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod;

    /**
     * @var string $aggregatorID
     */
    protected $aggregatorID;

    /**
     * @var string[] $octPurposeOfPayment
     */
    protected $octPurposeOfPayment;

    /**
     * @var string[] $transactionReason
     */
    protected $transactionReason;

    /**
     * @var string $serviceProviderName
     */
    protected $serviceProviderName;

    /**
     * @var string $initiatorType
     */
    protected $initiatorType;

    /**
     * @var string $cryptocurrencyPurchase
     */
    protected $cryptocurrencyPurchase;

    /**
     * @var string $aggregatorName
     */
    protected $aggregatorName;

    /**
     * @var string[] $invoiceNumber
     */
    protected $invoiceNumber;

    /**
     * @var string $deferredDateTime
     */
    protected $deferredDateTime;

    /**
     * @var string $aggregatorStreetAddress
     */
    protected $aggregatorStreetAddress;

    /**
     * @var string $aggregatorCity
     */
    protected $aggregatorCity;

    /**
     * @var string $aggregatorState
     */
    protected $aggregatorState;

    /**
     * @var string $aggregatorPostalcode
     */
    protected $aggregatorPostalcode;

    /**
     * @var string $aggregatorCountry
     */
    protected $aggregatorCountry;

    /**
     * @var benefit $benefit
     */
    protected $benefit;

    /**
     * @var language $language
     */
    protected $language;

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
    public function getCommerceIndicator()
    {
        return $this->commerceIndicator;
    }

    /**
     * @param string $commerceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setNetworkOrder($networkOrder)
    {
        $this->networkOrder = $networkOrder;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorID()
    {
        return $this->aggregatorID;
    }

    /**
     * @param string $aggregatorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setAggregatorID($aggregatorID)
    {
        $this->aggregatorID = $aggregatorID;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOctPurposeOfPayment()
    {
        return $this->octPurposeOfPayment;
    }

    /**
     * @param string[] $octPurposeOfPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setOctPurposeOfPayment(array $octPurposeOfPayment = null)
    {
        $this->octPurposeOfPayment = $octPurposeOfPayment;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTransactionReason()
    {
        return $this->transactionReason;
    }

    /**
     * @param string[] $transactionReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setTransactionReason(array $transactionReason = null)
    {
        $this->transactionReason = $transactionReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceProviderName()
    {
        return $this->serviceProviderName;
    }

    /**
     * @param string $serviceProviderName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setServiceProviderName($serviceProviderName)
    {
        $this->serviceProviderName = $serviceProviderName;

        return $this;
    }

    /**
     * @return string
     */
    public function getInitiatorType()
    {
        return $this->initiatorType;
    }

    /**
     * @param string $initiatorType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setInitiatorType($initiatorType)
    {
        $this->initiatorType = $initiatorType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCryptocurrencyPurchase()
    {
        return $this->cryptocurrencyPurchase;
    }

    /**
     * @param string $cryptocurrencyPurchase
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setCryptocurrencyPurchase($cryptocurrencyPurchase)
    {
        $this->cryptocurrencyPurchase = $cryptocurrencyPurchase;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorName()
    {
        return $this->aggregatorName;
    }

    /**
     * @param string $aggregatorName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setAggregatorName($aggregatorName)
    {
        $this->aggregatorName = $aggregatorName;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string[] $invoiceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setInvoiceNumber(array $invoiceNumber = null)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeferredDateTime()
    {
        return $this->deferredDateTime;
    }

    /**
     * @param string $deferredDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setDeferredDateTime($deferredDateTime)
    {
        $this->deferredDateTime = $deferredDateTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorStreetAddress()
    {
        return $this->aggregatorStreetAddress;
    }

    /**
     * @param string $aggregatorStreetAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setAggregatorStreetAddress($aggregatorStreetAddress)
    {
        $this->aggregatorStreetAddress = $aggregatorStreetAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorCity()
    {
        return $this->aggregatorCity;
    }

    /**
     * @param string $aggregatorCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setAggregatorCity($aggregatorCity)
    {
        $this->aggregatorCity = $aggregatorCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorState()
    {
        return $this->aggregatorState;
    }

    /**
     * @param string $aggregatorState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setAggregatorState($aggregatorState)
    {
        $this->aggregatorState = $aggregatorState;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorPostalcode()
    {
        return $this->aggregatorPostalcode;
    }

    /**
     * @param string $aggregatorPostalcode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setAggregatorPostalcode($aggregatorPostalcode)
    {
        $this->aggregatorPostalcode = $aggregatorPostalcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorCountry()
    {
        return $this->aggregatorCountry;
    }

    /**
     * @param string $aggregatorCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setAggregatorCountry($aggregatorCountry)
    {
        $this->aggregatorCountry = $aggregatorCountry;

        return $this;
    }

    /**
     * @return benefit
     */
    public function getBenefit()
    {
        return $this->benefit;
    }

    /**
     * @param benefit $benefit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setBenefit($benefit)
    {
        $this->benefit = $benefit;

        return $this;
    }

    /**
     * @return language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param language $language
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setLanguage($language)
    {
        $this->language = $language;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\OCTService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
