<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class issuer
{
    /**
     * @var string $additionalData
     */
    protected $additionalData;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $countryNumericCode
     */
    protected $countryNumericCode;

    /**
     * @var string $phoneNumber
     */
    protected $phoneNumber;

    /**
     * @var string $responseCode
     */
    protected $responseCode;

    /**
     * @var string $riskAnalysisExemptionResult
     */
    protected $riskAnalysisExemptionResult;

    /**
     * @var string $trustedMerchantExemptionResult
     */
    protected $trustedMerchantExemptionResult;

    /**
     * @var string $lowValueExemptionResult
     */
    protected $lowValueExemptionResult;

    /**
     * @var string $secureCorporatePaymentResult
     */
    protected $secureCorporatePaymentResult;

    /**
     * @var string $transactionRiskAnalysisExemptionResult
     */
    protected $transactionRiskAnalysisExemptionResult;

    /**
     * @var string $message
     */
    protected $message;

    /**
     * @var string $clearingData
     */
    protected $clearingData;

    /**
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @param string $additionalData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryNumericCode()
    {
        return $this->countryNumericCode;
    }

    /**
     * @param string $countryNumericCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setCountryNumericCode($countryNumericCode)
    {
        $this->countryNumericCode = $countryNumericCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskAnalysisExemptionResult()
    {
        return $this->riskAnalysisExemptionResult;
    }

    /**
     * @param string $riskAnalysisExemptionResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setRiskAnalysisExemptionResult($riskAnalysisExemptionResult)
    {
        $this->riskAnalysisExemptionResult = $riskAnalysisExemptionResult;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrustedMerchantExemptionResult()
    {
        return $this->trustedMerchantExemptionResult;
    }

    /**
     * @param string $trustedMerchantExemptionResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setTrustedMerchantExemptionResult($trustedMerchantExemptionResult)
    {
        $this->trustedMerchantExemptionResult = $trustedMerchantExemptionResult;

        return $this;
    }

    /**
     * @return string
     */
    public function getLowValueExemptionResult()
    {
        return $this->lowValueExemptionResult;
    }

    /**
     * @param string $lowValueExemptionResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setLowValueExemptionResult($lowValueExemptionResult)
    {
        $this->lowValueExemptionResult = $lowValueExemptionResult;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecureCorporatePaymentResult()
    {
        return $this->secureCorporatePaymentResult;
    }

    /**
     * @param string $secureCorporatePaymentResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setSecureCorporatePaymentResult($secureCorporatePaymentResult)
    {
        $this->secureCorporatePaymentResult = $secureCorporatePaymentResult;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionRiskAnalysisExemptionResult()
    {
        return $this->transactionRiskAnalysisExemptionResult;
    }

    /**
     * @param string $transactionRiskAnalysisExemptionResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setTransactionRiskAnalysisExemptionResult($transactionRiskAnalysisExemptionResult)
    {
        $this->transactionRiskAnalysisExemptionResult = $transactionRiskAnalysisExemptionResult;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getClearingData()
    {
        return $this->clearingData;
    }

    /**
     * @param string $clearingData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\issuer
     */
    public function setClearingData($clearingData)
    {
        $this->clearingData = $clearingData;

        return $this;
    }
}
