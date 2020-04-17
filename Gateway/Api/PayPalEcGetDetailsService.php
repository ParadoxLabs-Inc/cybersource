<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalEcGetDetailsService
{
    /**
     * @var string $paypalToken
     */
    protected $paypalToken;

    /**
     * @var string $paypalEcSetRequestID
     */
    protected $paypalEcSetRequestID;

    /**
     * @var string $paypalEcSetRequestToken
     */
    protected $paypalEcSetRequestToken;

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
    public function getPaypalToken()
    {
        return $this->paypalToken;
    }

    /**
     * @param string $paypalToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsService
     */
    public function setPaypalToken($paypalToken)
    {
        $this->paypalToken = $paypalToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalEcSetRequestID()
    {
        return $this->paypalEcSetRequestID;
    }

    /**
     * @param string $paypalEcSetRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsService
     */
    public function setPaypalEcSetRequestID($paypalEcSetRequestID)
    {
        $this->paypalEcSetRequestID = $paypalEcSetRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalEcSetRequestToken()
    {
        return $this->paypalEcSetRequestToken;
    }

    /**
     * @param string $paypalEcSetRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsService
     */
    public function setPaypalEcSetRequestToken($paypalEcSetRequestToken)
    {
        $this->paypalEcSetRequestToken = $paypalEcSetRequestToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
