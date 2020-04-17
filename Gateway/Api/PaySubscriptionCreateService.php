<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaySubscriptionCreateService
{
    /**
     * @var string $paymentRequestID
     */
    protected $paymentRequestID;

    /**
     * @var string $paymentRequestToken
     */
    protected $paymentRequestToken;

    /**
     * @var boolean $disableAutoAuth
     */
    protected $disableAutoAuth;

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
    public function getPaymentRequestID()
    {
        return $this->paymentRequestID;
    }

    /**
     * @param string $paymentRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateService
     */
    public function setPaymentRequestID($paymentRequestID)
    {
        $this->paymentRequestID = $paymentRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentRequestToken()
    {
        return $this->paymentRequestToken;
    }

    /**
     * @param string $paymentRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateService
     */
    public function setPaymentRequestToken($paymentRequestToken)
    {
        $this->paymentRequestToken = $paymentRequestToken;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDisableAutoAuth()
    {
        return $this->disableAutoAuth;
    }

    /**
     * @param boolean $disableAutoAuth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateService
     */
    public function setDisableAutoAuth($disableAutoAuth)
    {
        $this->disableAutoAuth = $disableAutoAuth;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
