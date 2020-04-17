<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalButtonCreateService
{
    /**
     * @var string $buttonType
     */
    protected $buttonType;

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
    public function getButtonType()
    {
        return $this->buttonType;
    }

    /**
     * @param string $buttonType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateService
     */
    public function setButtonType($buttonType)
    {
        $this->buttonType = $buttonType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
