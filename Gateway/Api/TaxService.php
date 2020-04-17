<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TaxService
{
    /**
     * @var string $nexus
     */
    protected $nexus;

    /**
     * @var string $noNexus
     */
    protected $noNexus;

    /**
     * @var string $orderAcceptanceCity
     */
    protected $orderAcceptanceCity;

    /**
     * @var string $orderAcceptanceCounty
     */
    protected $orderAcceptanceCounty;

    /**
     * @var string $orderAcceptanceCountry
     */
    protected $orderAcceptanceCountry;

    /**
     * @var string $orderAcceptanceState
     */
    protected $orderAcceptanceState;

    /**
     * @var string $orderAcceptancePostalCode
     */
    protected $orderAcceptancePostalCode;

    /**
     * @var string $orderOriginCity
     */
    protected $orderOriginCity;

    /**
     * @var string $orderOriginCounty
     */
    protected $orderOriginCounty;

    /**
     * @var string $orderOriginCountry
     */
    protected $orderOriginCountry;

    /**
     * @var string $orderOriginState
     */
    protected $orderOriginState;

    /**
     * @var string $orderOriginPostalCode
     */
    protected $orderOriginPostalCode;

    /**
     * @var string $sellerRegistration
     */
    protected $sellerRegistration;

    /**
     * @var string $sellerRegistration0
     */
    protected $sellerRegistration0;

    /**
     * @var string $sellerRegistration1
     */
    protected $sellerRegistration1;

    /**
     * @var string $sellerRegistration2
     */
    protected $sellerRegistration2;

    /**
     * @var string $sellerRegistration3
     */
    protected $sellerRegistration3;

    /**
     * @var string $sellerRegistration4
     */
    protected $sellerRegistration4;

    /**
     * @var string $sellerRegistration5
     */
    protected $sellerRegistration5;

    /**
     * @var string $sellerRegistration6
     */
    protected $sellerRegistration6;

    /**
     * @var string $sellerRegistration7
     */
    protected $sellerRegistration7;

    /**
     * @var string $sellerRegistration8
     */
    protected $sellerRegistration8;

    /**
     * @var string $sellerRegistration9
     */
    protected $sellerRegistration9;

    /**
     * @var string $buyerRegistration
     */
    protected $buyerRegistration;

    /**
     * @var string $middlemanRegistration
     */
    protected $middlemanRegistration;

    /**
     * @var string $pointOfTitleTransfer
     */
    protected $pointOfTitleTransfer;

    /**
     * @var string $commitIndicator
     */
    protected $commitIndicator;

    /**
     * @var string $refundIndicator
     */
    protected $refundIndicator;

    /**
     * @var string $dateOverrideReason
     */
    protected $dateOverrideReason;

    /**
     * @var string $reportingDate
     */
    protected $reportingDate;

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
    public function getNexus()
    {
        return $this->nexus;
    }

    /**
     * @param string $nexus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setNexus($nexus)
    {
        $this->nexus = $nexus;

        return $this;
    }

    /**
     * @return string
     */
    public function getNoNexus()
    {
        return $this->noNexus;
    }

    /**
     * @param string $noNexus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setNoNexus($noNexus)
    {
        $this->noNexus = $noNexus;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderAcceptanceCity()
    {
        return $this->orderAcceptanceCity;
    }

    /**
     * @param string $orderAcceptanceCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderAcceptanceCity($orderAcceptanceCity)
    {
        $this->orderAcceptanceCity = $orderAcceptanceCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderAcceptanceCounty()
    {
        return $this->orderAcceptanceCounty;
    }

    /**
     * @param string $orderAcceptanceCounty
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderAcceptanceCounty($orderAcceptanceCounty)
    {
        $this->orderAcceptanceCounty = $orderAcceptanceCounty;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderAcceptanceCountry()
    {
        return $this->orderAcceptanceCountry;
    }

    /**
     * @param string $orderAcceptanceCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderAcceptanceCountry($orderAcceptanceCountry)
    {
        $this->orderAcceptanceCountry = $orderAcceptanceCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderAcceptanceState()
    {
        return $this->orderAcceptanceState;
    }

    /**
     * @param string $orderAcceptanceState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderAcceptanceState($orderAcceptanceState)
    {
        $this->orderAcceptanceState = $orderAcceptanceState;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderAcceptancePostalCode()
    {
        return $this->orderAcceptancePostalCode;
    }

    /**
     * @param string $orderAcceptancePostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderAcceptancePostalCode($orderAcceptancePostalCode)
    {
        $this->orderAcceptancePostalCode = $orderAcceptancePostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderOriginCity()
    {
        return $this->orderOriginCity;
    }

    /**
     * @param string $orderOriginCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderOriginCity($orderOriginCity)
    {
        $this->orderOriginCity = $orderOriginCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderOriginCounty()
    {
        return $this->orderOriginCounty;
    }

    /**
     * @param string $orderOriginCounty
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderOriginCounty($orderOriginCounty)
    {
        $this->orderOriginCounty = $orderOriginCounty;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderOriginCountry()
    {
        return $this->orderOriginCountry;
    }

    /**
     * @param string $orderOriginCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderOriginCountry($orderOriginCountry)
    {
        $this->orderOriginCountry = $orderOriginCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderOriginState()
    {
        return $this->orderOriginState;
    }

    /**
     * @param string $orderOriginState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderOriginState($orderOriginState)
    {
        $this->orderOriginState = $orderOriginState;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderOriginPostalCode()
    {
        return $this->orderOriginPostalCode;
    }

    /**
     * @param string $orderOriginPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setOrderOriginPostalCode($orderOriginPostalCode)
    {
        $this->orderOriginPostalCode = $orderOriginPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration()
    {
        return $this->sellerRegistration;
    }

    /**
     * @param string $sellerRegistration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration($sellerRegistration)
    {
        $this->sellerRegistration = $sellerRegistration;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration0()
    {
        return $this->sellerRegistration0;
    }

    /**
     * @param string $sellerRegistration0
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration0($sellerRegistration0)
    {
        $this->sellerRegistration0 = $sellerRegistration0;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration1()
    {
        return $this->sellerRegistration1;
    }

    /**
     * @param string $sellerRegistration1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration1($sellerRegistration1)
    {
        $this->sellerRegistration1 = $sellerRegistration1;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration2()
    {
        return $this->sellerRegistration2;
    }

    /**
     * @param string $sellerRegistration2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration2($sellerRegistration2)
    {
        $this->sellerRegistration2 = $sellerRegistration2;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration3()
    {
        return $this->sellerRegistration3;
    }

    /**
     * @param string $sellerRegistration3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration3($sellerRegistration3)
    {
        $this->sellerRegistration3 = $sellerRegistration3;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration4()
    {
        return $this->sellerRegistration4;
    }

    /**
     * @param string $sellerRegistration4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration4($sellerRegistration4)
    {
        $this->sellerRegistration4 = $sellerRegistration4;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration5()
    {
        return $this->sellerRegistration5;
    }

    /**
     * @param string $sellerRegistration5
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration5($sellerRegistration5)
    {
        $this->sellerRegistration5 = $sellerRegistration5;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration6()
    {
        return $this->sellerRegistration6;
    }

    /**
     * @param string $sellerRegistration6
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration6($sellerRegistration6)
    {
        $this->sellerRegistration6 = $sellerRegistration6;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration7()
    {
        return $this->sellerRegistration7;
    }

    /**
     * @param string $sellerRegistration7
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration7($sellerRegistration7)
    {
        $this->sellerRegistration7 = $sellerRegistration7;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration8()
    {
        return $this->sellerRegistration8;
    }

    /**
     * @param string $sellerRegistration8
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration8($sellerRegistration8)
    {
        $this->sellerRegistration8 = $sellerRegistration8;

        return $this;
    }

    /**
     * @return string
     */
    public function getSellerRegistration9()
    {
        return $this->sellerRegistration9;
    }

    /**
     * @param string $sellerRegistration9
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setSellerRegistration9($sellerRegistration9)
    {
        $this->sellerRegistration9 = $sellerRegistration9;

        return $this;
    }

    /**
     * @return string
     */
    public function getBuyerRegistration()
    {
        return $this->buyerRegistration;
    }

    /**
     * @param string $buyerRegistration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setBuyerRegistration($buyerRegistration)
    {
        $this->buyerRegistration = $buyerRegistration;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddlemanRegistration()
    {
        return $this->middlemanRegistration;
    }

    /**
     * @param string $middlemanRegistration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setMiddlemanRegistration($middlemanRegistration)
    {
        $this->middlemanRegistration = $middlemanRegistration;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointOfTitleTransfer()
    {
        return $this->pointOfTitleTransfer;
    }

    /**
     * @param string $pointOfTitleTransfer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setPointOfTitleTransfer($pointOfTitleTransfer)
    {
        $this->pointOfTitleTransfer = $pointOfTitleTransfer;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommitIndicator()
    {
        return $this->commitIndicator;
    }

    /**
     * @param string $commitIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setCommitIndicator($commitIndicator)
    {
        $this->commitIndicator = $commitIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefundIndicator()
    {
        return $this->refundIndicator;
    }

    /**
     * @param string $refundIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setRefundIndicator($refundIndicator)
    {
        $this->refundIndicator = $refundIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateOverrideReason()
    {
        return $this->dateOverrideReason;
    }

    /**
     * @param string $dateOverrideReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setDateOverrideReason($dateOverrideReason)
    {
        $this->dateOverrideReason = $dateOverrideReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getReportingDate()
    {
        return $this->reportingDate;
    }

    /**
     * @param string $reportingDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setReportingDate($reportingDate)
    {
        $this->reportingDate = $reportingDate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TaxService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
