<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class BoletoPaymentReply
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
     * @var string $boletoNumber
     */
    protected $boletoNumber;

    /**
     * @var string $expirationDate
     */
    protected $expirationDate;

    /**
     * @var string $url
     */
    protected $url;

    /**
     * @var string $avsCode
     */
    protected $avsCode;

    /**
     * @var string $avsCodeRaw
     */
    protected $avsCodeRaw;

    /**
     * @var string $barCodeNumber
     */
    protected $barCodeNumber;

    /**
     * @var string $assignor
     */
    protected $assignor;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBoletoNumber()
    {
        return $this->boletoNumber;
    }

    /**
     * @param string $boletoNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setBoletoNumber($boletoNumber)
    {
        $this->boletoNumber = $boletoNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param string $expirationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCode()
    {
        return $this->avsCode;
    }

    /**
     * @param string $avsCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setAvsCode($avsCode)
    {
        $this->avsCode = $avsCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCodeRaw()
    {
        return $this->avsCodeRaw;
    }

    /**
     * @param string $avsCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setAvsCodeRaw($avsCodeRaw)
    {
        $this->avsCodeRaw = $avsCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getBarCodeNumber()
    {
        return $this->barCodeNumber;
    }

    /**
     * @param string $barCodeNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setBarCodeNumber($barCodeNumber)
    {
        $this->barCodeNumber = $barCodeNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssignor()
    {
        return $this->assignor;
    }

    /**
     * @param string $assignor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply
     */
    public function setAssignor($assignor)
    {
        $this->assignor = $assignor;

        return $this;
    }
}
