<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalRefundService
{
    /**
     * @var string $paypalDoCaptureRequestID
     */
    protected $paypalDoCaptureRequestID;

    /**
     * @var string $paypalDoCaptureRequestToken
     */
    protected $paypalDoCaptureRequestToken;

    /**
     * @var string $paypalCaptureId
     */
    protected $paypalCaptureId;

    /**
     * @var string $paypalNote
     */
    protected $paypalNote;

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
    public function getPaypalDoCaptureRequestID()
    {
        return $this->paypalDoCaptureRequestID;
    }

    /**
     * @param string $paypalDoCaptureRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundService
     */
    public function setPaypalDoCaptureRequestID($paypalDoCaptureRequestID)
    {
        $this->paypalDoCaptureRequestID = $paypalDoCaptureRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalDoCaptureRequestToken()
    {
        return $this->paypalDoCaptureRequestToken;
    }

    /**
     * @param string $paypalDoCaptureRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundService
     */
    public function setPaypalDoCaptureRequestToken($paypalDoCaptureRequestToken)
    {
        $this->paypalDoCaptureRequestToken = $paypalDoCaptureRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalCaptureId()
    {
        return $this->paypalCaptureId;
    }

    /**
     * @param string $paypalCaptureId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundService
     */
    public function setPaypalCaptureId($paypalCaptureId)
    {
        $this->paypalCaptureId = $paypalCaptureId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalNote()
    {
        return $this->paypalNote;
    }

    /**
     * @param string $paypalNote
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundService
     */
    public function setPaypalNote($paypalNote)
    {
        $this->paypalNote = $paypalNote;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
