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
}
