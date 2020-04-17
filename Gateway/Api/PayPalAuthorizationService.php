<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalAuthorizationService
{
    /**
     * @var string $paypalOrderId
     */
    protected $paypalOrderId;

    /**
     * @var string $paypalEcOrderSetupRequestID
     */
    protected $paypalEcOrderSetupRequestID;

    /**
     * @var string $paypalEcOrderSetupRequestToken
     */
    protected $paypalEcOrderSetupRequestToken;

    /**
     * @var string $paypalDoRefTransactionRequestID
     */
    protected $paypalDoRefTransactionRequestID;

    /**
     * @var string $paypalDoRefTransactionRequestToken
     */
    protected $paypalDoRefTransactionRequestToken;

    /**
     * @var string $paypalCustomerEmail
     */
    protected $paypalCustomerEmail;

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
    public function getPaypalOrderId()
    {
        return $this->paypalOrderId;
    }

    /**
     * @param string $paypalOrderId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService
     */
    public function setPaypalOrderId($paypalOrderId)
    {
        $this->paypalOrderId = $paypalOrderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalEcOrderSetupRequestID()
    {
        return $this->paypalEcOrderSetupRequestID;
    }

    /**
     * @param string $paypalEcOrderSetupRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService
     */
    public function setPaypalEcOrderSetupRequestID($paypalEcOrderSetupRequestID)
    {
        $this->paypalEcOrderSetupRequestID = $paypalEcOrderSetupRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalEcOrderSetupRequestToken()
    {
        return $this->paypalEcOrderSetupRequestToken;
    }

    /**
     * @param string $paypalEcOrderSetupRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService
     */
    public function setPaypalEcOrderSetupRequestToken($paypalEcOrderSetupRequestToken)
    {
        $this->paypalEcOrderSetupRequestToken = $paypalEcOrderSetupRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalDoRefTransactionRequestID()
    {
        return $this->paypalDoRefTransactionRequestID;
    }

    /**
     * @param string $paypalDoRefTransactionRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService
     */
    public function setPaypalDoRefTransactionRequestID($paypalDoRefTransactionRequestID)
    {
        $this->paypalDoRefTransactionRequestID = $paypalDoRefTransactionRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalDoRefTransactionRequestToken()
    {
        return $this->paypalDoRefTransactionRequestToken;
    }

    /**
     * @param string $paypalDoRefTransactionRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService
     */
    public function setPaypalDoRefTransactionRequestToken($paypalDoRefTransactionRequestToken)
    {
        $this->paypalDoRefTransactionRequestToken = $paypalDoRefTransactionRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalCustomerEmail()
    {
        return $this->paypalCustomerEmail;
    }

    /**
     * @param string $paypalCustomerEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService
     */
    public function setPaypalCustomerEmail($paypalCustomerEmail)
    {
        $this->paypalCustomerEmail = $paypalCustomerEmail;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
