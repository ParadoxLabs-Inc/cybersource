<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class APRevokeMandateReply
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
     * @var string $responseCode
     */
    protected $responseCode;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var \DateTime $dateSigned
     */
    protected $dateSigned;

    /**
     * @var \DateTime $dateCreated
     */
    protected $dateCreated;

    /**
     * @var \DateTime $dateRevoked
     */
    protected $dateRevoked;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
    public function getDateRevoked()
    {
        if ($this->dateRevoked == null) {
            return null;
        } else {
            try {
                return new DateTime($this->dateRevoked);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $dateRevoked
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
     */
    public function setDateRevoked(DateTime $dateRevoked = null)
    {
        if ($dateRevoked == null) {
            $this->dateRevoked = null;
        } else {
            $this->dateRevoked = $dateRevoked->format(DateTime::ATOM);
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply
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
