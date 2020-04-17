<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCCaptureService
{
    /**
     * @var string $authType
     */
    protected $authType;

    /**
     * @var string $verbalAuthCode
     */
    protected $verbalAuthCode;

    /**
     * @var string $authRequestID
     */
    protected $authRequestID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $partialPaymentID
     */
    protected $partialPaymentID;

    /**
     * @var string $purchasingLevel
     */
    protected $purchasingLevel;

    /**
     * @var string $industryDatatype
     */
    protected $industryDatatype;

    /**
     * @var string $authRequestToken
     */
    protected $authRequestToken;

    /**
     * @var string $merchantReceiptNumber
     */
    protected $merchantReceiptNumber;

    /**
     * @var string $posData
     */
    protected $posData;

    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var string $checksumKey
     */
    protected $checksumKey;

    /**
     * @var string $gratuityAmount
     */
    protected $gratuityAmount;

    /**
     * @var int $duration
     */
    protected $duration;

    /**
     * @var int $dpdeBillingMonth
     */
    protected $dpdeBillingMonth;

    /**
     * @var string $sequence
     */
    protected $sequence;

    /**
     * @var string $totalCount
     */
    protected $totalCount;

    /**
     * @var string $reconciliationIDAlternate
     */
    protected $reconciliationIDAlternate;

    /**
     * @var string $aggregatorID
     */
    protected $aggregatorID;

    /**
     * @var string $aggregatorName
     */
    protected $aggregatorName;

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
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param string $authType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setAuthType($authType)
    {
        $this->authType = $authType;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerbalAuthCode()
    {
        return $this->verbalAuthCode;
    }

    /**
     * @param string $verbalAuthCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setVerbalAuthCode($verbalAuthCode)
    {
        $this->verbalAuthCode = $verbalAuthCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthRequestID()
    {
        return $this->authRequestID;
    }

    /**
     * @param string $authRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setAuthRequestID($authRequestID)
    {
        $this->authRequestID = $authRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartialPaymentID()
    {
        return $this->partialPaymentID;
    }

    /**
     * @param string $partialPaymentID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setPartialPaymentID($partialPaymentID)
    {
        $this->partialPaymentID = $partialPaymentID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setIndustryDatatype($industryDatatype)
    {
        $this->industryDatatype = $industryDatatype;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthRequestToken()
    {
        return $this->authRequestToken;
    }

    /**
     * @param string $authRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setAuthRequestToken($authRequestToken)
    {
        $this->authRequestToken = $authRequestToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setMerchantReceiptNumber($merchantReceiptNumber)
    {
        $this->merchantReceiptNumber = $merchantReceiptNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosData()
    {
        return $this->posData;
    }

    /**
     * @param string $posData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setPosData($posData)
    {
        $this->posData = $posData;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @param string $transactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setChecksumKey($checksumKey)
    {
        $this->checksumKey = $checksumKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getGratuityAmount()
    {
        return $this->gratuityAmount;
    }

    /**
     * @param string $gratuityAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setGratuityAmount($gratuityAmount)
    {
        $this->gratuityAmount = $gratuityAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return int
     */
    public function getDpdeBillingMonth()
    {
        return $this->dpdeBillingMonth;
    }

    /**
     * @param int $dpdeBillingMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setDpdeBillingMonth($dpdeBillingMonth)
    {
        $this->dpdeBillingMonth = $dpdeBillingMonth;

        return $this;
    }

    /**
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param string $sequence
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param string $totalCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationIDAlternate()
    {
        return $this->reconciliationIDAlternate;
    }

    /**
     * @param string $reconciliationIDAlternate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setReconciliationIDAlternate($reconciliationIDAlternate)
    {
        $this->reconciliationIDAlternate = $reconciliationIDAlternate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setAggregatorName($aggregatorName)
    {
        $this->aggregatorName = $aggregatorName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
