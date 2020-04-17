<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalGetTxnDetailsService
{
    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var boolean $run
     */
    protected $run;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
        $this->run = $run;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @param string $transactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsService
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
