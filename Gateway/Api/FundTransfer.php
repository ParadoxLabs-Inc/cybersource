<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class FundTransfer
{
    /**
     * @var string $accountNumber
     */
    protected $accountNumber;

    /**
     * @var string $accountName
     */
    protected $accountName;

    /**
     * @var string $bankCheckDigit
     */
    protected $bankCheckDigit;

    /**
     * @var string $iban
     */
    protected $iban;

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
