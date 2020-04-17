<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ChinaPaymentService
{
    /**
     * @var string $paymentMode
     */
    protected $paymentMode;

    /**
     * @var string $returnURL
     */
    protected $returnURL;

    /**
     * @var string $pickUpAddress
     */
    protected $pickUpAddress;

    /**
     * @var string $pickUpPhoneNumber
     */
    protected $pickUpPhoneNumber;

    /**
     * @var string $pickUpPostalCode
     */
    protected $pickUpPostalCode;

    /**
     * @var string $pickUpName
     */
    protected $pickUpName;

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
    public function getPaymentMode()
    {
        return $this->paymentMode;
    }

    /**
     * @param string $paymentMode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService
     */
    public function setPaymentMode($paymentMode)
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnURL()
    {
        return $this->returnURL;
    }

    /**
     * @param string $returnURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService
     */
    public function setReturnURL($returnURL)
    {
        $this->returnURL = $returnURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpAddress()
    {
        return $this->pickUpAddress;
    }

    /**
     * @param string $pickUpAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService
     */
    public function setPickUpAddress($pickUpAddress)
    {
        $this->pickUpAddress = $pickUpAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpPhoneNumber()
    {
        return $this->pickUpPhoneNumber;
    }

    /**
     * @param string $pickUpPhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService
     */
    public function setPickUpPhoneNumber($pickUpPhoneNumber)
    {
        $this->pickUpPhoneNumber = $pickUpPhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpPostalCode()
    {
        return $this->pickUpPostalCode;
    }

    /**
     * @param string $pickUpPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService
     */
    public function setPickUpPostalCode($pickUpPostalCode)
    {
        $this->pickUpPostalCode = $pickUpPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpName()
    {
        return $this->pickUpName;
    }

    /**
     * @param string $pickUpName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService
     */
    public function setPickUpName($pickUpName)
    {
        $this->pickUpName = $pickUpName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
