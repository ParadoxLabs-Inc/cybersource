<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APUpdateMandateReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $mandateID
     */
    protected $mandateID;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var string $merchantURL
     */
    protected $merchantURL;

    /**
     * @var string $responseCode
     */
    protected $responseCode;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var string $riskScore
     */
    protected $riskScore;

    /**
     * @var string $encodedHTML
     */
    protected $encodedHTML;

    /**
     * @var string $encodedPopupHTML
     */
    protected $encodedPopupHTML;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @var \DateTime $dateSigned
     */
    protected $dateSigned;

    /**
     * @var \DateTime $dateCreated
     */
    protected $dateCreated;

    /**
     * @param int $reasonCode
     * @param string $mandateID
     * @param string $status
     * @param string $merchantURL
     */
    public function __construct($reasonCode, $mandateID, $status, $merchantURL)
    {
        $this->reasonCode  = $reasonCode;
        $this->mandateID   = $mandateID;
        $this->status      = $status;
        $this->merchantURL = $merchantURL;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandateID()
    {
        return $this->mandateID;
    }

    /**
     * @param string $mandateID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setMandateID($mandateID)
    {
        $this->mandateID = $mandateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantURL()
    {
        return $this->merchantURL;
    }

    /**
     * @param string $merchantURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setMerchantURL($merchantURL)
    {
        $this->merchantURL = $merchantURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorTransactionID()
    {
        return $this->processorTransactionID;
    }

    /**
     * @param string $processorTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskScore()
    {
        return $this->riskScore;
    }

    /**
     * @param string $riskScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setRiskScore($riskScore)
    {
        $this->riskScore = $riskScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncodedHTML()
    {
        return $this->encodedHTML;
    }

    /**
     * @param string $encodedHTML
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setEncodedHTML($encodedHTML)
    {
        $this->encodedHTML = $encodedHTML;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncodedPopupHTML()
    {
        return $this->encodedPopupHTML;
    }

    /**
     * @param string $encodedPopupHTML
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setEncodedPopupHTML($encodedPopupHTML)
    {
        $this->encodedPopupHTML = $encodedPopupHTML;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        if ($this->dateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->dateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $dateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setDateTime(DateTime $dateTime = null)
    {
        if ($dateTime == null) {
            $this->dateTime = null;
        } else {
            $this->dateTime = $dateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateSigned()
    {
        if ($this->dateSigned == null) {
            return null;
        } else {
            try {
                return new DateTime($this->dateSigned);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $dateSigned
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setDateSigned(DateTime $dateSigned = null)
    {
        if ($dateSigned == null) {
            $this->dateSigned = null;
        } else {
            $this->dateSigned = $dateSigned->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        if ($this->dateCreated == null) {
            return null;
        } else {
            try {
                return new DateTime($this->dateCreated);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $dateCreated
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply
     */
    public function setDateCreated(DateTime $dateCreated = null)
    {
        if ($dateCreated == null) {
            $this->dateCreated = null;
        } else {
            $this->dateCreated = $dateCreated->format(DateTime::ATOM);
        }

        return $this;
    }
}
