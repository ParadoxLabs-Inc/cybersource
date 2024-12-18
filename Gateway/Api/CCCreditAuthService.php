<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCCreditAuthService
{
    /**
     * @var string $captureRequestID
     */
    protected $captureRequestID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $purchasingLevel
     */
    protected $purchasingLevel;

    /**
     * @var string $industryDatatype
     */
    protected $industryDatatype;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator;

    /**
     * @var string $authCode
     */
    protected $authCode;

    /**
     * @var string $captureRequestToken
     */
    protected $captureRequestToken;

    /**
     * @var string $merchantReceiptNumber
     */
    protected $merchantReceiptNumber;

    /**
     * @var string $checksumKey
     */
    protected $checksumKey;

    /**
     * @var string $aggregatorID
     */
    protected $aggregatorID;

    /**
     * @var string $aggregatorName
     */
    protected $aggregatorName;

    /**
     * @var int $duration
     */
    protected $duration;

    /**
     * @var string $refundReason
     */
    protected $refundReason;

    /**
     * @var string $overridePaymentMethod
     */
    protected $overridePaymentMethod;

    /**
     * @var string $overridePaymentDetails
     */
    protected $overridePaymentDetails;

    /**
     * @var RestrictedString $networkPartnerId
     */
    protected $networkPartnerId;

    /**
     * @var string $paymentNetworkTransactionInformation
     */
    protected $paymentNetworkTransactionInformation;

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
    public function getCaptureRequestID()
    {
        return $this->captureRequestID;
    }

    /**
     * @param string $captureRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setCaptureRequestID($captureRequestID)
    {
        $this->captureRequestID = $captureRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPurchasingLevel()
    {
        return $this->purchasingLevel;
    }

    /**
     * @param string $purchasingLevel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setPurchasingLevel($purchasingLevel)
    {
        $this->purchasingLevel = $purchasingLevel;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setIndustryDatatype($industryDatatype)
    {
        $this->industryDatatype = $industryDatatype;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
        $this->commerceIndicator = $commerceIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptureRequestToken()
    {
        return $this->captureRequestToken;
    }

    /**
     * @param string $captureRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setCaptureRequestToken($captureRequestToken)
    {
        $this->captureRequestToken = $captureRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantReceiptNumber()
    {
        return $this->merchantReceiptNumber;
    }

    /**
     * @param string $merchantReceiptNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setMerchantReceiptNumber($merchantReceiptNumber)
    {
        $this->merchantReceiptNumber = $merchantReceiptNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getChecksumKey()
    {
        return $this->checksumKey;
    }

    /**
     * @param string $checksumKey
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setChecksumKey($checksumKey)
    {
        $this->checksumKey = $checksumKey;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setAggregatorID($aggregatorID)
    {
        $this->aggregatorID = $aggregatorID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setAggregatorName($aggregatorName)
    {
        $this->aggregatorName = $aggregatorName;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setRefundReason($refundReason)
    {
        $this->refundReason = $refundReason;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setOverridePaymentMethod($overridePaymentMethod)
    {
        $this->overridePaymentMethod = $overridePaymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getOverridePaymentDetails()
    {
        return $this->overridePaymentDetails;
    }

    /**
     * @param string $overridePaymentDetails
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setOverridePaymentDetails($overridePaymentDetails)
    {
        $this->overridePaymentDetails = $overridePaymentDetails;

        return $this;
    }

    /**
     * @return RestrictedString
     */
    public function getNetworkPartnerId()
    {
        return $this->networkPartnerId;
    }

    /**
     * @param RestrictedString $networkPartnerId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setNetworkPartnerId($networkPartnerId)
    {
        $this->networkPartnerId = $networkPartnerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentNetworkTransactionInformation()
    {
        return $this->paymentNetworkTransactionInformation;
    }

    /**
     * @param string $paymentNetworkTransactionInformation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setPaymentNetworkTransactionInformation($paymentNetworkTransactionInformation)
    {
        $this->paymentNetworkTransactionInformation = $paymentNetworkTransactionInformation;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
