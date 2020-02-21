<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APInitiateService
{

    /**
     * @var string $returnURL
     */
    protected $returnURL = null;

    /**
     * @var string $productName
     */
    protected $productName = null;

    /**
     * @var string $productDescription
     */
    protected $productDescription = null;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID = null;

    /**
     * @var string $bankID
     */
    protected $bankID = null;

    /**
     * @var string $countryCode
     */
    protected $countryCode = null;

    /**
     * @var string $escrowAgreement
     */
    protected $escrowAgreement = null;

    /**
     * @var string $languageInterface
     */
    protected $languageInterface = null;

    /**
     * @var string $intent
     */
    protected $intent = null;

    /**
     * @var string $successURL
     */
    protected $successURL = null;

    /**
     * @var string $cancelURL
     */
    protected $cancelURL = null;

    /**
     * @var string $failureURL
     */
    protected $failureURL = null;

    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod = null;

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
    public function getReturnURL()
    {
        return $this->returnURL;
    }

    /**
     * @param string $returnURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setReturnURL($returnURL)
    {
        $this->returnURL = $returnURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductDescription()
    {
        return $this->productDescription;
    }

    /**
     * @param string $productDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankID()
    {
        return $this->bankID;
    }

    /**
     * @param string $bankID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setBankID($bankID)
    {
        $this->bankID = $bankID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEscrowAgreement()
    {
        return $this->escrowAgreement;
    }

    /**
     * @param string $escrowAgreement
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setEscrowAgreement($escrowAgreement)
    {
        $this->escrowAgreement = $escrowAgreement;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguageInterface()
    {
        return $this->languageInterface;
    }

    /**
     * @param string $languageInterface
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setLanguageInterface($languageInterface)
    {
        $this->languageInterface = $languageInterface;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @param string $intent
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setIntent($intent)
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessURL()
    {
        return $this->successURL;
    }

    /**
     * @param string $successURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setSuccessURL($successURL)
    {
        $this->successURL = $successURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getCancelURL()
    {
        return $this->cancelURL;
    }

    /**
     * @param string $cancelURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setCancelURL($cancelURL)
    {
        $this->cancelURL = $cancelURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getFailureURL()
    {
        return $this->failureURL;
    }

    /**
     * @param string $failureURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setFailureURL($failureURL)
    {
        $this->failureURL = $failureURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }

}
