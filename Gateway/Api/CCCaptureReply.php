<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class CCCaptureReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var \DateTime $requestDateTime
     */
    protected $requestDateTime;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var FundingTotals $fundingTotals
     */
    protected $fundingTotals;

    /**
     * @var string $fxQuoteID
     */
    protected $fxQuoteID;

    /**
     * @var \DateTime $fxQuoteRate
     */
    protected $fxQuoteRate;

    /**
     * @var string $fxQuoteType
     */
    protected $fxQuoteType;

    /**
     * @var \DateTime $fxQuoteExpirationDateTime
     */
    protected $fxQuoteExpirationDateTime;

    /**
     * @var string $purchasingLevel3Enabled
     */
    protected $purchasingLevel3Enabled;

    /**
     * @var string $enhancedDataEnabled
     */
    protected $enhancedDataEnabled;

    /**
     * @var string $reconciliationReferenceNumber
     */
    protected $reconciliationReferenceNumber;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestDateTime()
    {
        if ($this->requestDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->requestDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $requestDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setRequestDateTime(DateTime $requestDateTime = null)
    {
        if ($requestDateTime == null) {
            $this->requestDateTime = null;
        } else {
            $this->requestDateTime = $requestDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return FundingTotals
     */
    public function getFundingTotals()
    {
        return $this->fundingTotals;
    }

    /**
     * @param FundingTotals $fundingTotals
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setFundingTotals($fundingTotals)
    {
        $this->fundingTotals = $fundingTotals;

        return $this;
    }

    /**
     * @return string
     */
    public function getFxQuoteID()
    {
        return $this->fxQuoteID;
    }

    /**
     * @param string $fxQuoteID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setFxQuoteID($fxQuoteID)
    {
        $this->fxQuoteID = $fxQuoteID;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFxQuoteRate()
    {
        if ($this->fxQuoteRate == null) {
            return null;
        } else {
            try {
                return new DateTime($this->fxQuoteRate);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $fxQuoteRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setFxQuoteRate(DateTime $fxQuoteRate = null)
    {
        if ($fxQuoteRate == null) {
            $this->fxQuoteRate = null;
        } else {
            $this->fxQuoteRate = $fxQuoteRate->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFxQuoteType()
    {
        return $this->fxQuoteType;
    }

    /**
     * @param string $fxQuoteType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setFxQuoteType($fxQuoteType)
    {
        $this->fxQuoteType = $fxQuoteType;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFxQuoteExpirationDateTime()
    {
        if ($this->fxQuoteExpirationDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->fxQuoteExpirationDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $fxQuoteExpirationDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setFxQuoteExpirationDateTime(DateTime $fxQuoteExpirationDateTime = null)
    {
        if ($fxQuoteExpirationDateTime == null) {
            $this->fxQuoteExpirationDateTime = null;
        } else {
            $this->fxQuoteExpirationDateTime = $fxQuoteExpirationDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPurchasingLevel3Enabled()
    {
        return $this->purchasingLevel3Enabled;
    }

    /**
     * @param string $purchasingLevel3Enabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setPurchasingLevel3Enabled($purchasingLevel3Enabled)
    {
        $this->purchasingLevel3Enabled = $purchasingLevel3Enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnhancedDataEnabled()
    {
        return $this->enhancedDataEnabled;
    }

    /**
     * @param string $enhancedDataEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setEnhancedDataEnabled($enhancedDataEnabled)
    {
        $this->enhancedDataEnabled = $enhancedDataEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationReferenceNumber()
    {
        return $this->reconciliationReferenceNumber;
    }

    /**
     * @param string $reconciliationReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply
     */
    public function setReconciliationReferenceNumber($reconciliationReferenceNumber)
    {
        $this->reconciliationReferenceNumber = $reconciliationReferenceNumber;

        return $this;
    }
}
