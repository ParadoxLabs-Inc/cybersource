<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class FundTransfer
{

    /**
     * @var string $accountNumber
     */
    protected $accountNumber = null;

    /**
     * @var string $accountName
     */
    protected $accountName = null;

    /**
     * @var string $bankCheckDigit
     */
    protected $bankCheckDigit = null;

    /**
     * @var string $iban
     */
    protected $iban = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
      return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FundTransfer
     */
    public function setAccountNumber($accountNumber)
    {
      $this->accountNumber = $accountNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getAccountName()
    {
      return $this->accountName;
    }

    /**
     * @param string $accountName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FundTransfer
     */
    public function setAccountName($accountName)
    {
      $this->accountName = $accountName;
      return $this;
    }

    /**
     * @return string
     */
    public function getBankCheckDigit()
    {
      return $this->bankCheckDigit;
    }

    /**
     * @param string $bankCheckDigit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FundTransfer
     */
    public function setBankCheckDigit($bankCheckDigit)
    {
      $this->bankCheckDigit = $bankCheckDigit;
      return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
      return $this->iban;
    }

    /**
     * @param string $iban
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FundTransfer
     */
    public function setIban($iban)
    {
      $this->iban = $iban;
      return $this;
    }

}
