<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalPreapprovedUpdateService
{
    /**
     * @var string $mpID
     */
    protected $mpID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

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
    public function getMpID()
    {
        return $this->mpID;
    }

    /**
     * @param string $mpID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateService
     */
    public function setMpID($mpID)
    {
        $this->mpID = $mpID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
