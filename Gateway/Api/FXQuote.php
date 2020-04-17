<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class FXQuote
{
    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var string $rate
     */
    protected $rate;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var \DateTime $expirationDateTime
     */
    protected $expirationDateTime;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var string $fundingCurrency
     */
    protected $fundingCurrency;

    /**
     * @var \DateTime $receivedDateTime
     */
    protected $receivedDateTime;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXQuote
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param string $rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXQuote
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXQuote
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDateTime()
    {
        if ($this->expirationDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->expirationDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $expirationDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXQuote
     */
    public function setExpirationDateTime(DateTime $expirationDateTime = null)
    {
        if ($expirationDateTime == null) {
            $this->expirationDateTime = null;
        } else {
            $this->expirationDateTime = $expirationDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXQuote
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getFundingCurrency()
    {
        return $this->fundingCurrency;
    }

    /**
     * @param string $fundingCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXQuote
     */
    public function setFundingCurrency($fundingCurrency)
    {
        $this->fundingCurrency = $fundingCurrency;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedDateTime()
    {
        if ($this->receivedDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->receivedDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $receivedDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXQuote
     */
    public function setReceivedDateTime(DateTime $receivedDateTime = null)
    {
        if ($receivedDateTime == null) {
            $this->receivedDateTime = null;
        } else {
            $this->receivedDateTime = $receivedDateTime->format(DateTime::ATOM);
        }

        return $this;
    }
}
