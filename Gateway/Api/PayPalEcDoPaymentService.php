<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalEcDoPaymentService
{

    /**
     * @var string $paypalToken
     */
    protected $paypalToken = null;

    /**
     * @var string $paypalPayerId
     */
    protected $paypalPayerId = null;

    /**
     * @var string $paypalCustomerEmail
     */
    protected $paypalCustomerEmail = null;

    /**
     * @var string $paypalDesc
     */
    protected $paypalDesc = null;

    /**
     * @var string $paypalEcSetRequestID
     */
    protected $paypalEcSetRequestID = null;

    /**
     * @var string $paypalEcSetRequestToken
     */
    protected $paypalEcSetRequestToken = null;

    /**
     * @var string $promoCode0
     */
    protected $promoCode0 = null;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber = null;

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
    public function getPaypalToken()
    {
        return $this->paypalToken;
    }

    /**
     * @param string $paypalToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
     */
    public function setPaypalToken($paypalToken)
    {
        $this->paypalToken = $paypalToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPayerId()
    {
        return $this->paypalPayerId;
    }

    /**
     * @param string $paypalPayerId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
     */
    public function setPaypalPayerId($paypalPayerId)
    {
        $this->paypalPayerId = $paypalPayerId;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
     */
    public function setPaypalCustomerEmail($paypalCustomerEmail)
    {
        $this->paypalCustomerEmail = $paypalCustomerEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalDesc()
    {
        return $this->paypalDesc;
    }

    /**
     * @param string $paypalDesc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
     */
    public function setPaypalDesc($paypalDesc)
    {
        $this->paypalDesc = $paypalDesc;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
     */
    public function setPaypalEcSetRequestToken($paypalEcSetRequestToken)
    {
        $this->paypalEcSetRequestToken = $paypalEcSetRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromoCode0()
    {
        return $this->promoCode0;
    }

    /**
     * @param string $promoCode0
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
     */
    public function setPromoCode0($promoCode0)
    {
        $this->promoCode0 = $promoCode0;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }

}
