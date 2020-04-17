<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class HostedDataCreateService
{
    /**
     * @var string $profileID
     */
    protected $profileID;

    /**
     * @var string $paymentMethod
     */
    protected $paymentMethod;

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
    public function getProfileID()
    {
        return $this->profileID;
    }

    /**
     * @param string $profileID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateService
     */
    public function setProfileID($profileID)
    {
        $this->profileID = $profileID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateService
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
