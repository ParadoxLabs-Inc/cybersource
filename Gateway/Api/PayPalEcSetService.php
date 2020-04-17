<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalEcSetService
{
    /**
     * @var string $paypalReturn
     */
    protected $paypalReturn;

    /**
     * @var string $paypalCancelReturn
     */
    protected $paypalCancelReturn;

    /**
     * @var string $paypalMaxamt
     */
    protected $paypalMaxamt;

    /**
     * @var string $paypalCustomerEmail
     */
    protected $paypalCustomerEmail;

    /**
     * @var string $paypalDesc
     */
    protected $paypalDesc;

    /**
     * @var string $paypalReqconfirmshipping
     */
    protected $paypalReqconfirmshipping;

    /**
     * @var string $paypalNoshipping
     */
    protected $paypalNoshipping;

    /**
     * @var string $paypalAddressOverride
     */
    protected $paypalAddressOverride;

    /**
     * @var string $paypalToken
     */
    protected $paypalToken;

    /**
     * @var string $paypalLc
     */
    protected $paypalLc;

    /**
     * @var string $paypalPagestyle
     */
    protected $paypalPagestyle;

    /**
     * @var string $paypalHdrimg
     */
    protected $paypalHdrimg;

    /**
     * @var string $paypalHdrbordercolor
     */
    protected $paypalHdrbordercolor;

    /**
     * @var string $paypalHdrbackcolor
     */
    protected $paypalHdrbackcolor;

    /**
     * @var string $paypalPayflowcolor
     */
    protected $paypalPayflowcolor;

    /**
     * @var string $paypalEcSetRequestID
     */
    protected $paypalEcSetRequestID;

    /**
     * @var string $paypalEcSetRequestToken
     */
    protected $paypalEcSetRequestToken;

    /**
     * @var string $promoCode0
     */
    protected $promoCode0;

    /**
     * @var string $requestBillingAddress
     */
    protected $requestBillingAddress;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber;

    /**
     * @var string $paypalBillingType
     */
    protected $paypalBillingType;

    /**
     * @var string $paypalBillingAgreementDesc
     */
    protected $paypalBillingAgreementDesc;

    /**
     * @var string $paypalPaymentType
     */
    protected $paypalPaymentType;

    /**
     * @var string $paypalBillingAgreementCustom
     */
    protected $paypalBillingAgreementCustom;

    /**
     * @var string $paypalLogoimg
     */
    protected $paypalLogoimg;

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
    public function getPaypalReturn()
    {
        return $this->paypalReturn;
    }

    /**
     * @param string $paypalReturn
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalReturn($paypalReturn)
    {
        $this->paypalReturn = $paypalReturn;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalCancelReturn()
    {
        return $this->paypalCancelReturn;
    }

    /**
     * @param string $paypalCancelReturn
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalCancelReturn($paypalCancelReturn)
    {
        $this->paypalCancelReturn = $paypalCancelReturn;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalMaxamt()
    {
        return $this->paypalMaxamt;
    }

    /**
     * @param string $paypalMaxamt
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalMaxamt($paypalMaxamt)
    {
        $this->paypalMaxamt = $paypalMaxamt;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalDesc($paypalDesc)
    {
        $this->paypalDesc = $paypalDesc;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalReqconfirmshipping()
    {
        return $this->paypalReqconfirmshipping;
    }

    /**
     * @param string $paypalReqconfirmshipping
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalReqconfirmshipping($paypalReqconfirmshipping)
    {
        $this->paypalReqconfirmshipping = $paypalReqconfirmshipping;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalNoshipping()
    {
        return $this->paypalNoshipping;
    }

    /**
     * @param string $paypalNoshipping
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalNoshipping($paypalNoshipping)
    {
        $this->paypalNoshipping = $paypalNoshipping;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalAddressOverride()
    {
        return $this->paypalAddressOverride;
    }

    /**
     * @param string $paypalAddressOverride
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalAddressOverride($paypalAddressOverride)
    {
        $this->paypalAddressOverride = $paypalAddressOverride;

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalToken($paypalToken)
    {
        $this->paypalToken = $paypalToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalLc()
    {
        return $this->paypalLc;
    }

    /**
     * @param string $paypalLc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalLc($paypalLc)
    {
        $this->paypalLc = $paypalLc;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPagestyle()
    {
        return $this->paypalPagestyle;
    }

    /**
     * @param string $paypalPagestyle
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalPagestyle($paypalPagestyle)
    {
        $this->paypalPagestyle = $paypalPagestyle;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalHdrimg()
    {
        return $this->paypalHdrimg;
    }

    /**
     * @param string $paypalHdrimg
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalHdrimg($paypalHdrimg)
    {
        $this->paypalHdrimg = $paypalHdrimg;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalHdrbordercolor()
    {
        return $this->paypalHdrbordercolor;
    }

    /**
     * @param string $paypalHdrbordercolor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalHdrbordercolor($paypalHdrbordercolor)
    {
        $this->paypalHdrbordercolor = $paypalHdrbordercolor;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalHdrbackcolor()
    {
        return $this->paypalHdrbackcolor;
    }

    /**
     * @param string $paypalHdrbackcolor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalHdrbackcolor($paypalHdrbackcolor)
    {
        $this->paypalHdrbackcolor = $paypalHdrbackcolor;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPayflowcolor()
    {
        return $this->paypalPayflowcolor;
    }

    /**
     * @param string $paypalPayflowcolor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalPayflowcolor($paypalPayflowcolor)
    {
        $this->paypalPayflowcolor = $paypalPayflowcolor;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPromoCode0($promoCode0)
    {
        $this->promoCode0 = $promoCode0;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestBillingAddress()
    {
        return $this->requestBillingAddress;
    }

    /**
     * @param string $requestBillingAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setRequestBillingAddress($requestBillingAddress)
    {
        $this->requestBillingAddress = $requestBillingAddress;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingType()
    {
        return $this->paypalBillingType;
    }

    /**
     * @param string $paypalBillingType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalBillingType($paypalBillingType)
    {
        $this->paypalBillingType = $paypalBillingType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementDesc()
    {
        return $this->paypalBillingAgreementDesc;
    }

    /**
     * @param string $paypalBillingAgreementDesc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalBillingAgreementDesc($paypalBillingAgreementDesc)
    {
        $this->paypalBillingAgreementDesc = $paypalBillingAgreementDesc;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalPaymentType()
    {
        return $this->paypalPaymentType;
    }

    /**
     * @param string $paypalPaymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalPaymentType($paypalPaymentType)
    {
        $this->paypalPaymentType = $paypalPaymentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalBillingAgreementCustom()
    {
        return $this->paypalBillingAgreementCustom;
    }

    /**
     * @param string $paypalBillingAgreementCustom
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalBillingAgreementCustom($paypalBillingAgreementCustom)
    {
        $this->paypalBillingAgreementCustom = $paypalBillingAgreementCustom;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalLogoimg()
    {
        return $this->paypalLogoimg;
    }

    /**
     * @param string $paypalLogoimg
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setPaypalLogoimg($paypalLogoimg)
    {
        $this->paypalLogoimg = $paypalLogoimg;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
