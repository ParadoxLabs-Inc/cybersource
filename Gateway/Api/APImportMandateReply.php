<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APImportMandateReply
{

    /**
     * @var int $reasonCode
     */
    protected $reasonCode = null;

    /**
     * @var string $mandateID
     */
    protected $mandateID = null;

    /**
     * @var string $status
     */
    protected $status = null;

    /**
     * @var string $responseCode
     */
    protected $responseCode = null;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID = null;

    /**
     * @var \DateTime $dateSigned
     */
    protected $dateSigned = null;

    /**
     * @var \DateTime $dateCreated
     */
    protected $dateCreated = null;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime = null;

    /**
     * @param int $reasonCode
     * @param string $mandateID
     * @param string $status
     */
    public function __construct($reasonCode, $mandateID, $status)
    {
        $this->reasonCode = $reasonCode;
        $this->mandateID  = $mandateID;
        $this->status     = $status;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply
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

}
