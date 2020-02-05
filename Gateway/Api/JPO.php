<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class JPO
{

    /**
     * @var int $paymentMethod
     */
    protected $paymentMethod = null;

    /**
     * @var float $bonusAmount
     */
    protected $bonusAmount = null;

    /**
     * @var int $bonuses
     */
    protected $bonuses = null;

    /**
     * @var int $installments
     */
    protected $installments = null;

    /**
     * @var int $firstBillingMonth
     */
    protected $firstBillingMonth = null;

    /**
     * @var int $jccaTerminalID
     */
    protected $jccaTerminalID = null;

    /**
     * @var int $issuerMessage
     */
    protected $issuerMessage = null;

    /**
     * @var string $jis2TrackData
     */
    protected $jis2TrackData = null;

    /**
     * @var string $businessNameAlphanumeric
     */
    protected $businessNameAlphanumeric = null;

    /**
     * @var string $businessNameJapanese
     */
    protected $businessNameJapanese = null;

    /**
     * @var string $businessNameKatakana
     */
    protected $businessNameKatakana = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return int
     */
    public function getPaymentMethod()
    {
      return $this->paymentMethod;
    }

    /**
     * @param int $paymentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setPaymentMethod($paymentMethod)
    {
      $this->paymentMethod = $paymentMethod;
      return $this;
    }

    /**
     * @return float
     */
    public function getBonusAmount()
    {
      return $this->bonusAmount;
    }

    /**
     * @param float $bonusAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setBonusAmount($bonusAmount)
    {
      $this->bonusAmount = $bonusAmount;
      return $this;
    }

    /**
     * @return int
     */
    public function getBonuses()
    {
      return $this->bonuses;
    }

    /**
     * @param int $bonuses
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setBonuses($bonuses)
    {
      $this->bonuses = $bonuses;
      return $this;
    }

    /**
     * @return int
     */
    public function getInstallments()
    {
      return $this->installments;
    }

    /**
     * @param int $installments
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setInstallments($installments)
    {
      $this->installments = $installments;
      return $this;
    }

    /**
     * @return int
     */
    public function getFirstBillingMonth()
    {
      return $this->firstBillingMonth;
    }

    /**
     * @param int $firstBillingMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setFirstBillingMonth($firstBillingMonth)
    {
      $this->firstBillingMonth = $firstBillingMonth;
      return $this;
    }

    /**
     * @return int
     */
    public function getJccaTerminalID()
    {
      return $this->jccaTerminalID;
    }

    /**
     * @param int $jccaTerminalID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setJccaTerminalID($jccaTerminalID)
    {
      $this->jccaTerminalID = $jccaTerminalID;
      return $this;
    }

    /**
     * @return int
     */
    public function getIssuerMessage()
    {
      return $this->issuerMessage;
    }

    /**
     * @param int $issuerMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setIssuerMessage($issuerMessage)
    {
      $this->issuerMessage = $issuerMessage;
      return $this;
    }

    /**
     * @return string
     */
    public function getJis2TrackData()
    {
      return $this->jis2TrackData;
    }

    /**
     * @param string $jis2TrackData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setJis2TrackData($jis2TrackData)
    {
      $this->jis2TrackData = $jis2TrackData;
      return $this;
    }

    /**
     * @return string
     */
    public function getBusinessNameAlphanumeric()
    {
      return $this->businessNameAlphanumeric;
    }

    /**
     * @param string $businessNameAlphanumeric
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setBusinessNameAlphanumeric($businessNameAlphanumeric)
    {
      $this->businessNameAlphanumeric = $businessNameAlphanumeric;
      return $this;
    }

    /**
     * @return string
     */
    public function getBusinessNameJapanese()
    {
      return $this->businessNameJapanese;
    }

    /**
     * @param string $businessNameJapanese
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setBusinessNameJapanese($businessNameJapanese)
    {
      $this->businessNameJapanese = $businessNameJapanese;
      return $this;
    }

    /**
     * @return string
     */
    public function getBusinessNameKatakana()
    {
      return $this->businessNameKatakana;
    }

    /**
     * @param string $businessNameKatakana
     * @return \ParadoxLabs\CyberSource\Gateway\Api\JPO
     */
    public function setBusinessNameKatakana($businessNameKatakana)
    {
      $this->businessNameKatakana = $businessNameKatakana;
      return $this;
    }

}
