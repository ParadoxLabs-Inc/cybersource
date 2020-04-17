<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCSaleService
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
     * @var boolean $partialAuthIndicator
     */
    protected $partialAuthIndicator;

    /**
     * @var string $cavv
     */
    protected $cavv;

    /**
     * @var string $xid
     */
    protected $xid;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $industryDatatype
     */
    protected $industryDatatype;

    /**
     * @var string $networkTokenCryptogram
     */
    protected $networkTokenCryptogram;

    /**
     * @var string $paSpecificationVersion
     */
    protected $paSpecificationVersion;

    /**
     * @var string $directoryServerTransactionID
     */
    protected $directoryServerTransactionID;

    /**
     * @var string $cryptocurrencyPurchase
     */
    protected $cryptocurrencyPurchase;

    /**
     * @var string $lowValueExemptionIndicator
     */
    protected $lowValueExemptionIndicator;

    /**
     * @var string $riskAnalysisExemptionIndicator
     */
    protected $riskAnalysisExemptionIndicator;

    /**
     * @var string $trustedMerchantExemptionIndicator
     */
    protected $trustedMerchantExemptionIndicator;

    /**
     * @var string $secureCorporatePaymentIndicator
     */
    protected $secureCorporatePaymentIndicator;

    /**
     * @var string $deferredAuthIndicator
     */
    protected $deferredAuthIndicator;

    /**
     * @var string $delegatedAuthenticationExemptionIndicator
     */
    protected $delegatedAuthenticationExemptionIndicator;

    /**
     * @var string $transitTransactionType
     */
    protected $transitTransactionType;

    /**
     * @var string $transportationMode
     */
    protected $transportationMode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setPartialAuthIndicator($partialAuthIndicator)
    {
        $this->partialAuthIndicator = $partialAuthIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getCavv()
    {
        return $this->cavv;
    }

    /**
     * @param string $cavv
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setCavv($cavv)
    {
        $this->cavv = $cavv;

        return $this;
    }

    /**
     * @return string
     */
    public function getXid()
    {
        return $this->xid;
    }

    /**
     * @param string $xid
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setXid($xid)
    {
        $this->xid = $xid;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndustryDatatype()
    {
        return $this->industryDatatype;
    }

    /**
     * @param string $industryDatatype
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setIndustryDatatype($industryDatatype)
    {
        $this->industryDatatype = $industryDatatype;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkTokenCryptogram()
    {
        return $this->networkTokenCryptogram;
    }

    /**
     * @param string $networkTokenCryptogram
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setNetworkTokenCryptogram($networkTokenCryptogram)
    {
        $this->networkTokenCryptogram = $networkTokenCryptogram;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaSpecificationVersion()
    {
        return $this->paSpecificationVersion;
    }

    /**
     * @param string $paSpecificationVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setPaSpecificationVersion($paSpecificationVersion)
    {
        $this->paSpecificationVersion = $paSpecificationVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectoryServerTransactionID()
    {
        return $this->directoryServerTransactionID;
    }

    /**
     * @param string $directoryServerTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setDirectoryServerTransactionID($directoryServerTransactionID)
    {
        $this->directoryServerTransactionID = $directoryServerTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setCryptocurrencyPurchase($cryptocurrencyPurchase)
    {
        $this->cryptocurrencyPurchase = $cryptocurrencyPurchase;

        return $this;
    }

    /**
     * @return string
     */
    public function getLowValueExemptionIndicator()
    {
        return $this->lowValueExemptionIndicator;
    }

    /**
     * @param string $lowValueExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setLowValueExemptionIndicator($lowValueExemptionIndicator)
    {
        $this->lowValueExemptionIndicator = $lowValueExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskAnalysisExemptionIndicator()
    {
        return $this->riskAnalysisExemptionIndicator;
    }

    /**
     * @param string $riskAnalysisExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setRiskAnalysisExemptionIndicator($riskAnalysisExemptionIndicator)
    {
        $this->riskAnalysisExemptionIndicator = $riskAnalysisExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrustedMerchantExemptionIndicator()
    {
        return $this->trustedMerchantExemptionIndicator;
    }

    /**
     * @param string $trustedMerchantExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setTrustedMerchantExemptionIndicator($trustedMerchantExemptionIndicator)
    {
        $this->trustedMerchantExemptionIndicator = $trustedMerchantExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecureCorporatePaymentIndicator()
    {
        return $this->secureCorporatePaymentIndicator;
    }

    /**
     * @param string $secureCorporatePaymentIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setSecureCorporatePaymentIndicator($secureCorporatePaymentIndicator)
    {
        $this->secureCorporatePaymentIndicator = $secureCorporatePaymentIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeferredAuthIndicator()
    {
        return $this->deferredAuthIndicator;
    }

    /**
     * @param string $deferredAuthIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setDeferredAuthIndicator($deferredAuthIndicator)
    {
        $this->deferredAuthIndicator = $deferredAuthIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDelegatedAuthenticationExemptionIndicator()
    {
        return $this->delegatedAuthenticationExemptionIndicator;
    }

    /**
     * @param string $delegatedAuthenticationExemptionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setDelegatedAuthenticationExemptionIndicator($delegatedAuthenticationExemptionIndicator)
    {
        $this->delegatedAuthenticationExemptionIndicator = $delegatedAuthenticationExemptionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransitTransactionType()
    {
        return $this->transitTransactionType;
    }

    /**
     * @param string $transitTransactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setTransitTransactionType($transitTransactionType)
    {
        $this->transitTransactionType = $transitTransactionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransportationMode()
    {
        return $this->transportationMode;
    }

    /**
     * @param string $transportationMode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setTransportationMode($transportationMode)
    {
        $this->transportationMode = $transportationMode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
