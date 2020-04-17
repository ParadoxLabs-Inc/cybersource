<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalPaymentService
{
    /**
     * @var string $cancelURL
     */
    protected $cancelURL;

    /**
     * @var string $successURL
     */
    protected $successURL;

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
    public function getCancelURL()
    {
        return $this->cancelURL;
    }

    /**
     * @param string $cancelURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPaymentService
     */
    public function setCancelURL($cancelURL)
    {
        $this->cancelURL = $cancelURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessURL()
    {
        return $this->successURL;
    }

    /**
     * @param string $successURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPaymentService
     */
    public function setSuccessURL($successURL)
    {
        $this->successURL = $successURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPaymentService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalPaymentService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
