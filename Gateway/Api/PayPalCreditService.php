<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalCreditService
{
    /**
     * @var string $payPalPaymentRequestID
     */
    protected $payPalPaymentRequestID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $payPalPaymentRequestToken
     */
    protected $payPalPaymentRequestToken;

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
    public function getPayPalPaymentRequestID()
    {
        return $this->payPalPaymentRequestID;
    }

    /**
     * @param string $payPalPaymentRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreditService
     */
    public function setPayPalPaymentRequestID($payPalPaymentRequestID)
    {
        $this->payPalPaymentRequestID = $payPalPaymentRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreditService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayPalPaymentRequestToken()
    {
        return $this->payPalPaymentRequestToken;
    }

    /**
     * @param string $payPalPaymentRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreditService
     */
    public function setPayPalPaymentRequestToken($payPalPaymentRequestToken)
    {
        $this->payPalPaymentRequestToken = $payPalPaymentRequestToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreditService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
