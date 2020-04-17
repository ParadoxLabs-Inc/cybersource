<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APTransactionDetailsService
{
    /**
     * @var string $transactionDetailsRequestID
     */
    protected $transactionDetailsRequestID;

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
    public function getTransactionDetailsRequestID()
    {
        return $this->transactionDetailsRequestID;
    }

    /**
     * @param string $transactionDetailsRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APTransactionDetailsService
     */
    public function setTransactionDetailsRequestID($transactionDetailsRequestID)
    {
        $this->transactionDetailsRequestID = $transactionDetailsRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APTransactionDetailsService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
