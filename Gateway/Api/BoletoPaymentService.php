<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BoletoPaymentService
{

    /**
     * @var string $instruction
     */
    protected $instruction = null;

    /**
     * @var string $expirationDate
     */
    protected $expirationDate = null;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID = null;

    /**
     * @var boolean $run
     */
    protected $run = null;

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
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * @param string $instruction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentService
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentService
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }

}
