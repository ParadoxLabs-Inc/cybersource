<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class merchant
{
    /**
     * @var string $acquirerBIN
     */
    protected $acquirerBIN;

    /**
     * @var string $cardAcceptorID
     */
    protected $cardAcceptorID;

    /**
     * @var string $visaMerchantID
     */
    protected $visaMerchantID;

    /**
     * @var string $acquirerCountry
     */
    protected $acquirerCountry;

    /**
     * @var string $transactionReferenceNumber
     */
    protected $transactionReferenceNumber;

    /**
     * @var string $riskPrediction
     */
    protected $riskPrediction;

    /**
     * @return string
     */
    public function getAcquirerBIN()
    {
        return $this->acquirerBIN;
    }

    /**
     * @param string $acquirerBIN
     * @return \ParadoxLabs\CyberSource\Gateway\Api\merchant
     */
    public function setAcquirerBIN($acquirerBIN)
    {
        $this->acquirerBIN = $acquirerBIN;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardAcceptorID()
    {
        return $this->cardAcceptorID;
    }

    /**
     * @param string $cardAcceptorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\merchant
     */
    public function setCardAcceptorID($cardAcceptorID)
    {
        $this->cardAcceptorID = $cardAcceptorID;

        return $this;
    }

    /**
     * @return string
     */
    public function getVisaMerchantID()
    {
        return $this->visaMerchantID;
    }

    /**
     * @param string $visaMerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\merchant
     */
    public function setVisaMerchantID($visaMerchantID)
    {
        $this->visaMerchantID = $visaMerchantID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcquirerCountry()
    {
        return $this->acquirerCountry;
    }

    /**
     * @param string $acquirerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\merchant
     */
    public function setAcquirerCountry($acquirerCountry)
    {
        $this->acquirerCountry = $acquirerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionReferenceNumber()
    {
        return $this->transactionReferenceNumber;
    }

    /**
     * @param string $transactionReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\merchant
     */
    public function setTransactionReferenceNumber($transactionReferenceNumber)
    {
        $this->transactionReferenceNumber = $transactionReferenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskPrediction()
    {
        return $this->riskPrediction;
    }

    /**
     * @param string $riskPrediction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\merchant
     */
    public function setRiskPrediction($riskPrediction)
    {
        $this->riskPrediction = $riskPrediction;

        return $this;
    }
}
