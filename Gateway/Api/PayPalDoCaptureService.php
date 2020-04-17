<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalDoCaptureService
{
    /**
     * @var string $paypalAuthorizationId
     */
    protected $paypalAuthorizationId;

    /**
     * @var string $completeType
     */
    protected $completeType;

    /**
     * @var string $paypalEcDoPaymentRequestID
     */
    protected $paypalEcDoPaymentRequestID;

    /**
     * @var string $paypalEcDoPaymentRequestToken
     */
    protected $paypalEcDoPaymentRequestToken;

    /**
     * @var string $paypalAuthorizationRequestID
     */
    protected $paypalAuthorizationRequestID;

    /**
     * @var string $paypalAuthorizationRequestToken
     */
    protected $paypalAuthorizationRequestToken;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber;

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
    public function getPaypalAuthorizationId()
    {
        return $this->paypalAuthorizationId;
    }

    /**
     * @param string $paypalAuthorizationId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setPaypalAuthorizationId($paypalAuthorizationId)
    {
        $this->paypalAuthorizationId = $paypalAuthorizationId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompleteType()
    {
        return $this->completeType;
    }

    /**
     * @param string $completeType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setCompleteType($completeType)
    {
        $this->completeType = $completeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalEcDoPaymentRequestID()
    {
        return $this->paypalEcDoPaymentRequestID;
    }

    /**
     * @param string $paypalEcDoPaymentRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setPaypalEcDoPaymentRequestID($paypalEcDoPaymentRequestID)
    {
        $this->paypalEcDoPaymentRequestID = $paypalEcDoPaymentRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalEcDoPaymentRequestToken()
    {
        return $this->paypalEcDoPaymentRequestToken;
    }

    /**
     * @param string $paypalEcDoPaymentRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setPaypalEcDoPaymentRequestToken($paypalEcDoPaymentRequestToken)
    {
        $this->paypalEcDoPaymentRequestToken = $paypalEcDoPaymentRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalAuthorizationRequestID()
    {
        return $this->paypalAuthorizationRequestID;
    }

    /**
     * @param string $paypalAuthorizationRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setPaypalAuthorizationRequestID($paypalAuthorizationRequestID)
    {
        $this->paypalAuthorizationRequestID = $paypalAuthorizationRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalAuthorizationRequestToken()
    {
        return $this->paypalAuthorizationRequestToken;
    }

    /**
     * @param string $paypalAuthorizationRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setPaypalAuthorizationRequestToken($paypalAuthorizationRequestToken)
    {
        $this->paypalAuthorizationRequestToken = $paypalAuthorizationRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
