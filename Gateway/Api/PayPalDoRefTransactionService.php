<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalDoRefTransactionService
{
    /**
     * @var string $paypalBillingAgreementId
     */
    protected $paypalBillingAgreementId;

    /**
     * @var string $paypalPaymentType
     */
    protected $paypalPaymentType;

    /**
     * @var string $paypalReqconfirmshipping
     */
    protected $paypalReqconfirmshipping;

    /**
     * @var string $paypalReturnFmfDetails
     */
    protected $paypalReturnFmfDetails;

    /**
     * @var string $paypalSoftDescriptor
     */
    protected $paypalSoftDescriptor;

    /**
     * @var string $paypalShippingdiscount
     */
    protected $paypalShippingdiscount;

    /**
     * @var string $paypalDesc
     */
    protected $paypalDesc;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber;

    /**
     * @var string $paypalEcNotifyUrl
     */
    protected $paypalEcNotifyUrl;

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
    public function getPaypalBillingAgreementId()
    {
        return $this->paypalBillingAgreementId;
    }

    /**
     * @param string $paypalBillingAgreementId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalBillingAgreementId($paypalBillingAgreementId)
    {
        $this->paypalBillingAgreementId = $paypalBillingAgreementId;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalPaymentType($paypalPaymentType)
    {
        $this->paypalPaymentType = $paypalPaymentType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalReqconfirmshipping($paypalReqconfirmshipping)
    {
        $this->paypalReqconfirmshipping = $paypalReqconfirmshipping;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalReturnFmfDetails()
    {
        return $this->paypalReturnFmfDetails;
    }

    /**
     * @param string $paypalReturnFmfDetails
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalReturnFmfDetails($paypalReturnFmfDetails)
    {
        $this->paypalReturnFmfDetails = $paypalReturnFmfDetails;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalSoftDescriptor()
    {
        return $this->paypalSoftDescriptor;
    }

    /**
     * @param string $paypalSoftDescriptor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalSoftDescriptor($paypalSoftDescriptor)
    {
        $this->paypalSoftDescriptor = $paypalSoftDescriptor;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalShippingdiscount()
    {
        return $this->paypalShippingdiscount;
    }

    /**
     * @param string $paypalShippingdiscount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalShippingdiscount($paypalShippingdiscount)
    {
        $this->paypalShippingdiscount = $paypalShippingdiscount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalDesc($paypalDesc)
    {
        $this->paypalDesc = $paypalDesc;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaypalEcNotifyUrl()
    {
        return $this->paypalEcNotifyUrl;
    }

    /**
     * @param string $paypalEcNotifyUrl
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setPaypalEcNotifyUrl($paypalEcNotifyUrl)
    {
        $this->paypalEcNotifyUrl = $paypalEcNotifyUrl;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
