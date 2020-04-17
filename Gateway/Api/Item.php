<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Item
{
    /**
     * @var float $unitPrice
     */
    protected $unitPrice;

    /**
     * @var float $quantity
     */
    protected $quantity;

    /**
     * @var string $productCode
     */
    protected $productCode;

    /**
     * @var string $productName
     */
    protected $productName;

    /**
     * @var string $productSKU
     */
    protected $productSKU;

    /**
     * @var string $productRisk
     */
    protected $productRisk;

    /**
     * @var float $taxAmount
     */
    protected $taxAmount;

    /**
     * @var float $cityOverrideAmount
     */
    protected $cityOverrideAmount;

    /**
     * @var float $cityOverrideRate
     */
    protected $cityOverrideRate;

    /**
     * @var float $countyOverrideAmount
     */
    protected $countyOverrideAmount;

    /**
     * @var float $countyOverrideRate
     */
    protected $countyOverrideRate;

    /**
     * @var float $districtOverrideAmount
     */
    protected $districtOverrideAmount;

    /**
     * @var float $districtOverrideRate
     */
    protected $districtOverrideRate;

    /**
     * @var float $stateOverrideAmount
     */
    protected $stateOverrideAmount;

    /**
     * @var float $stateOverrideRate
     */
    protected $stateOverrideRate;

    /**
     * @var float $countryOverrideAmount
     */
    protected $countryOverrideAmount;

    /**
     * @var float $countryOverrideRate
     */
    protected $countryOverrideRate;

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
     * @var string $shipFromCity
     */
    protected $shipFromCity;

    /**
     * @var string $shipFromCounty
     */
    protected $shipFromCounty;

    /**
     * @var string $shipFromCountry
     */
    protected $shipFromCountry;

    /**
     * @var string $shipFromState
     */
    protected $shipFromState;

    /**
     * @var string $shipFromPostalCode
     */
    protected $shipFromPostalCode;

    /**
     * @var string $export
     */
    protected $export;

    /**
     * @var string $noExport
     */
    protected $noExport;

    /**
     * @var float $nationalTax
     */
    protected $nationalTax;

    /**
     * @var float $vatRate
     */
    protected $vatRate;

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
     * @var boolean $giftCategory
     */
    protected $giftCategory;

    /**
     * @var string $timeCategory
     */
    protected $timeCategory;

    /**
     * @var string $hostHedge
     */
    protected $hostHedge;

    /**
     * @var string $timeHedge
     */
    protected $timeHedge;

    /**
     * @var string $velocityHedge
     */
    protected $velocityHedge;

    /**
     * @var string $nonsensicalHedge
     */
    protected $nonsensicalHedge;

    /**
     * @var string $phoneHedge
     */
    protected $phoneHedge;

    /**
     * @var string $obscenitiesHedge
     */
    protected $obscenitiesHedge;

    /**
     * @var string $unitOfMeasure
     */
    protected $unitOfMeasure;

    /**
     * @var float $taxRate
     */
    protected $taxRate;

    /**
     * @var float $totalAmount
     */
    protected $totalAmount;

    /**
     * @var float $discountAmount
     */
    protected $discountAmount;

    /**
     * @var float $discountRate
     */
    protected $discountRate;

    /**
     * @var string $commodityCode
     */
    protected $commodityCode;

    /**
     * @var string $grossNetIndicator
     */
    protected $grossNetIndicator;

    /**
     * @var string $taxTypeApplied
     */
    protected $taxTypeApplied;

    /**
     * @var string $discountIndicator
     */
    protected $discountIndicator;

    /**
     * @var string $alternateTaxID
     */
    protected $alternateTaxID;

    /**
     * @var float $alternateTaxAmount
     */
    protected $alternateTaxAmount;

    /**
     * @var string $alternateTaxTypeApplied
     */
    protected $alternateTaxTypeApplied;

    /**
     * @var float $alternateTaxRate
     */
    protected $alternateTaxRate;

    /**
     * @var string $alternateTaxType
     */
    protected $alternateTaxType;

    /**
     * @var float $localTax
     */
    protected $localTax;

    /**
     * @var string $zeroCostToCustomerIndicator
     */
    protected $zeroCostToCustomerIndicator;

    /**
     * @var string $passengerFirstName
     */
    protected $passengerFirstName;

    /**
     * @var string $passengerLastName
     */
    protected $passengerLastName;

    /**
     * @var string $passengerID
     */
    protected $passengerID;

    /**
     * @var string $passengerStatus
     */
    protected $passengerStatus;

    /**
     * @var string $passengerType
     */
    protected $passengerType;

    /**
     * @var string $passengerEmail
     */
    protected $passengerEmail;

    /**
     * @var string $passengerPhone
     */
    protected $passengerPhone;

    /**
     * @var string $passengerNationality
     */
    protected $passengerNationality;

    /**
     * @var string $invoiceNumber
     */
    protected $invoiceNumber;

    /**
     * @var string $productDescription
     */
    protected $productDescription;

    /**
     * @var string $taxStatusIndicator
     */
    protected $taxStatusIndicator;

    /**
     * @var string $discountManagementIndicator
     */
    protected $discountManagementIndicator;

    /**
     * @var string $typeOfSupply
     */
    protected $typeOfSupply;

    /**
     * @var string $sign
     */
    protected $sign;

    /**
     * @var string $unitTaxAmount
     */
    protected $unitTaxAmount;

    /**
     * @var string $weightAmount
     */
    protected $weightAmount;

    /**
     * @var string $weightID
     */
    protected $weightID;

    /**
     * @var string $weightUnitMeasurement
     */
    protected $weightUnitMeasurement;

    /**
     * @var string $otherTax_1_type
     */
    protected $otherTax_1_type;

    /**
     * @var float $otherTax_1_amount
     */
    protected $otherTax_1_amount;

    /**
     * @var float $otherTax_1_rate
     */
    protected $otherTax_1_rate;

    /**
     * @var string $otherTax_1_statusIndicator
     */
    protected $otherTax_1_statusIndicator;

    /**
     * @var string $otherTax_2_type
     */
    protected $otherTax_2_type;

    /**
     * @var float $otherTax_2_amount
     */
    protected $otherTax_2_amount;

    /**
     * @var float $otherTax_2_rate
     */
    protected $otherTax_2_rate;

    /**
     * @var string $otherTax_2_statusIndicator
     */
    protected $otherTax_2_statusIndicator;

    /**
     * @var string $otherTax_3_type
     */
    protected $otherTax_3_type;

    /**
     * @var float $otherTax_3_amount
     */
    protected $otherTax_3_amount;

    /**
     * @var float $otherTax_3_rate
     */
    protected $otherTax_3_rate;

    /**
     * @var string $otherTax_3_statusIndicator
     */
    protected $otherTax_3_statusIndicator;

    /**
     * @var string $otherTax_4_type
     */
    protected $otherTax_4_type;

    /**
     * @var float $otherTax_4_amount
     */
    protected $otherTax_4_amount;

    /**
     * @var float $otherTax_4_rate
     */
    protected $otherTax_4_rate;

    /**
     * @var string $otherTax_4_statusIndicator
     */
    protected $otherTax_4_statusIndicator;

    /**
     * @var string $otherTax_5_type
     */
    protected $otherTax_5_type;

    /**
     * @var float $otherTax_5_amount
     */
    protected $otherTax_5_amount;

    /**
     * @var float $otherTax_5_rate
     */
    protected $otherTax_5_rate;

    /**
     * @var string $otherTax_5_statusIndicator
     */
    protected $otherTax_5_statusIndicator;

    /**
     * @var string $otherTax_6_type
     */
    protected $otherTax_6_type;

    /**
     * @var float $otherTax_6_amount
     */
    protected $otherTax_6_amount;

    /**
     * @var float $otherTax_6_rate
     */
    protected $otherTax_6_rate;

    /**
     * @var string $otherTax_6_statusIndicator
     */
    protected $otherTax_6_statusIndicator;

    /**
     * @var string $otherTax_7_type
     */
    protected $otherTax_7_type;

    /**
     * @var float $otherTax_7_amount
     */
    protected $otherTax_7_amount;

    /**
     * @var float $otherTax_7_rate
     */
    protected $otherTax_7_rate;

    /**
     * @var string $otherTax_7_statusIndicator
     */
    protected $otherTax_7_statusIndicator;

    /**
     * @var string $referenceData_1_number
     */
    protected $referenceData_1_number;

    /**
     * @var string $referenceData_1_code
     */
    protected $referenceData_1_code;

    /**
     * @var string $referenceData_2_number
     */
    protected $referenceData_2_number;

    /**
     * @var string $referenceData_2_code
     */
    protected $referenceData_2_code;

    /**
     * @var string $referenceData_3_number
     */
    protected $referenceData_3_number;

    /**
     * @var string $referenceData_3_code
     */
    protected $referenceData_3_code;

    /**
     * @var string $referenceData_4_number
     */
    protected $referenceData_4_number;

    /**
     * @var string $referenceData_4_code
     */
    protected $referenceData_4_code;

    /**
     * @var string $referenceData_5_number
     */
    protected $referenceData_5_number;

    /**
     * @var string $referenceData_5_code
     */
    protected $referenceData_5_code;

    /**
     * @var string $referenceData_6_number
     */
    protected $referenceData_6_number;

    /**
     * @var string $referenceData_6_code
     */
    protected $referenceData_6_code;

    /**
     * @var string $referenceData_7_number
     */
    protected $referenceData_7_number;

    /**
     * @var string $referenceData_7_code
     */
    protected $referenceData_7_code;

    /**
     * @var string $shippingDestinationTypes
     */
    protected $shippingDestinationTypes;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = (float)$unitPrice;

        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int)$quantity;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setProductCode($productCode)
    {
        $this->productCode = substr(str_replace(['^', ':'], '', $productCode), 0, 255);

        return $this;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setProductName($productName)
    {
        $this->productName = substr(str_replace(['^', ':'], '', $productName), 0, 255);

        return $this;
    }

    /**
     * @return string
     */
    public function getProductSKU()
    {
        return $this->productSKU;
    }

    /**
     * @param string $productSKU
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setProductSKU($productSKU)
    {
        $this->productSKU = substr(str_replace(['^', ':'], '', $productSKU), 0, 255);

        return $this;
    }

    /**
     * @return string
     */
    public function getProductRisk()
    {
        return $this->productRisk;
    }

    /**
     * @param string $productRisk
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setProductRisk($productRisk)
    {
        $this->productRisk = $productRisk;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param float $taxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = max((float)$taxAmount, 0);

        return $this;
    }

    /**
     * @return float
     */
    public function getCityOverrideAmount()
    {
        return $this->cityOverrideAmount;
    }

    /**
     * @param float $cityOverrideAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setCityOverrideAmount($cityOverrideAmount)
    {
        $this->cityOverrideAmount = $cityOverrideAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCityOverrideRate()
    {
        return $this->cityOverrideRate;
    }

    /**
     * @param float $cityOverrideRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setCityOverrideRate($cityOverrideRate)
    {
        $this->cityOverrideRate = $cityOverrideRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getCountyOverrideAmount()
    {
        return $this->countyOverrideAmount;
    }

    /**
     * @param float $countyOverrideAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setCountyOverrideAmount($countyOverrideAmount)
    {
        $this->countyOverrideAmount = $countyOverrideAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCountyOverrideRate()
    {
        return $this->countyOverrideRate;
    }

    /**
     * @param float $countyOverrideRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setCountyOverrideRate($countyOverrideRate)
    {
        $this->countyOverrideRate = $countyOverrideRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getDistrictOverrideAmount()
    {
        return $this->districtOverrideAmount;
    }

    /**
     * @param float $districtOverrideAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setDistrictOverrideAmount($districtOverrideAmount)
    {
        $this->districtOverrideAmount = $districtOverrideAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getDistrictOverrideRate()
    {
        return $this->districtOverrideRate;
    }

    /**
     * @param float $districtOverrideRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setDistrictOverrideRate($districtOverrideRate)
    {
        $this->districtOverrideRate = $districtOverrideRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getStateOverrideAmount()
    {
        return $this->stateOverrideAmount;
    }

    /**
     * @param float $stateOverrideAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setStateOverrideAmount($stateOverrideAmount)
    {
        $this->stateOverrideAmount = $stateOverrideAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getStateOverrideRate()
    {
        return $this->stateOverrideRate;
    }

    /**
     * @param float $stateOverrideRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setStateOverrideRate($stateOverrideRate)
    {
        $this->stateOverrideRate = $stateOverrideRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getCountryOverrideAmount()
    {
        return $this->countryOverrideAmount;
    }

    /**
     * @param float $countryOverrideAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setCountryOverrideAmount($countryOverrideAmount)
    {
        $this->countryOverrideAmount = $countryOverrideAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCountryOverrideRate()
    {
        return $this->countryOverrideRate;
    }

    /**
     * @param float $countryOverrideRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setCountryOverrideRate($countryOverrideRate)
    {
        $this->countryOverrideRate = $countryOverrideRate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOrderOriginPostalCode($orderOriginPostalCode)
    {
        $this->orderOriginPostalCode = $orderOriginPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipFromCity()
    {
        return $this->shipFromCity;
    }

    /**
     * @param string $shipFromCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setShipFromCity($shipFromCity)
    {
        $this->shipFromCity = $shipFromCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipFromCounty()
    {
        return $this->shipFromCounty;
    }

    /**
     * @param string $shipFromCounty
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setShipFromCounty($shipFromCounty)
    {
        $this->shipFromCounty = $shipFromCounty;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipFromCountry()
    {
        return $this->shipFromCountry;
    }

    /**
     * @param string $shipFromCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setShipFromCountry($shipFromCountry)
    {
        $this->shipFromCountry = $shipFromCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipFromState()
    {
        return $this->shipFromState;
    }

    /**
     * @param string $shipFromState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setShipFromState($shipFromState)
    {
        $this->shipFromState = $shipFromState;

        return $this;
    }

    /**
     * @return string
     */
    public function getShipFromPostalCode()
    {
        return $this->shipFromPostalCode;
    }

    /**
     * @param string $shipFromPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setShipFromPostalCode($shipFromPostalCode)
    {
        $this->shipFromPostalCode = $shipFromPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * @param string $export
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setExport($export)
    {
        $this->export = $export;

        return $this;
    }

    /**
     * @return string
     */
    public function getNoExport()
    {
        return $this->noExport;
    }

    /**
     * @param string $noExport
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setNoExport($noExport)
    {
        $this->noExport = $noExport;

        return $this;
    }

    /**
     * @return float
     */
    public function getNationalTax()
    {
        return $this->nationalTax;
    }

    /**
     * @param float $nationalTax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setNationalTax($nationalTax)
    {
        $this->nationalTax = $nationalTax;

        return $this;
    }

    /**
     * @return float
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * @param float $vatRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPointOfTitleTransfer($pointOfTitleTransfer)
    {
        $this->pointOfTitleTransfer = $pointOfTitleTransfer;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getGiftCategory()
    {
        return $this->giftCategory;
    }

    /**
     * @param boolean $giftCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setGiftCategory($giftCategory)
    {
        $this->giftCategory = $giftCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeCategory()
    {
        return $this->timeCategory;
    }

    /**
     * @param string $timeCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTimeCategory($timeCategory)
    {
        $this->timeCategory = $timeCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getHostHedge()
    {
        return $this->hostHedge;
    }

    /**
     * @param string $hostHedge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setHostHedge($hostHedge)
    {
        $this->hostHedge = $hostHedge;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeHedge()
    {
        return $this->timeHedge;
    }

    /**
     * @param string $timeHedge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTimeHedge($timeHedge)
    {
        $this->timeHedge = $timeHedge;

        return $this;
    }

    /**
     * @return string
     */
    public function getVelocityHedge()
    {
        return $this->velocityHedge;
    }

    /**
     * @param string $velocityHedge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setVelocityHedge($velocityHedge)
    {
        $this->velocityHedge = $velocityHedge;

        return $this;
    }

    /**
     * @return string
     */
    public function getNonsensicalHedge()
    {
        return $this->nonsensicalHedge;
    }

    /**
     * @param string $nonsensicalHedge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setNonsensicalHedge($nonsensicalHedge)
    {
        $this->nonsensicalHedge = $nonsensicalHedge;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneHedge()
    {
        return $this->phoneHedge;
    }

    /**
     * @param string $phoneHedge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPhoneHedge($phoneHedge)
    {
        $this->phoneHedge = $phoneHedge;

        return $this;
    }

    /**
     * @return string
     */
    public function getObscenitiesHedge()
    {
        return $this->obscenitiesHedge;
    }

    /**
     * @param string $obscenitiesHedge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setObscenitiesHedge($obscenitiesHedge)
    {
        $this->obscenitiesHedge = $obscenitiesHedge;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnitOfMeasure()
    {
        return $this->unitOfMeasure;
    }

    /**
     * @param string $unitOfMeasure
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setUnitOfMeasure($unitOfMeasure)
    {
        $this->unitOfMeasure = $unitOfMeasure;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param float $taxRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = (float)$totalAmount ?: null;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param float $discountAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = (float)$discountAmount ?: null;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * @param float $discountRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommodityCode()
    {
        return $this->commodityCode;
    }

    /**
     * @param string $commodityCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setCommodityCode($commodityCode)
    {
        $this->commodityCode = $commodityCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getGrossNetIndicator()
    {
        return $this->grossNetIndicator;
    }

    /**
     * @param string $grossNetIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setGrossNetIndicator($grossNetIndicator)
    {
        $this->grossNetIndicator = $grossNetIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxTypeApplied()
    {
        return $this->taxTypeApplied;
    }

    /**
     * @param string $taxTypeApplied
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTaxTypeApplied($taxTypeApplied)
    {
        $this->taxTypeApplied = $taxTypeApplied;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountIndicator()
    {
        return $this->discountIndicator;
    }

    /**
     * @param string $discountIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setDiscountIndicator($discountIndicator)
    {
        $this->discountIndicator = $discountIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateTaxID()
    {
        return $this->alternateTaxID;
    }

    /**
     * @param string $alternateTaxID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setAlternateTaxID($alternateTaxID)
    {
        $this->alternateTaxID = $alternateTaxID;

        return $this;
    }

    /**
     * @return float
     */
    public function getAlternateTaxAmount()
    {
        return $this->alternateTaxAmount;
    }

    /**
     * @param float $alternateTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setAlternateTaxAmount($alternateTaxAmount)
    {
        $this->alternateTaxAmount = $alternateTaxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateTaxTypeApplied()
    {
        return $this->alternateTaxTypeApplied;
    }

    /**
     * @param string $alternateTaxTypeApplied
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setAlternateTaxTypeApplied($alternateTaxTypeApplied)
    {
        $this->alternateTaxTypeApplied = $alternateTaxTypeApplied;

        return $this;
    }

    /**
     * @return float
     */
    public function getAlternateTaxRate()
    {
        return $this->alternateTaxRate;
    }

    /**
     * @param float $alternateTaxRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setAlternateTaxRate($alternateTaxRate)
    {
        $this->alternateTaxRate = $alternateTaxRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateTaxType()
    {
        return $this->alternateTaxType;
    }

    /**
     * @param string $alternateTaxType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setAlternateTaxType($alternateTaxType)
    {
        $this->alternateTaxType = $alternateTaxType;

        return $this;
    }

    /**
     * @return float
     */
    public function getLocalTax()
    {
        return $this->localTax;
    }

    /**
     * @param float $localTax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setLocalTax($localTax)
    {
        $this->localTax = $localTax;

        return $this;
    }

    /**
     * @return string
     */
    public function getZeroCostToCustomerIndicator()
    {
        return $this->zeroCostToCustomerIndicator;
    }

    /**
     * @param string $zeroCostToCustomerIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setZeroCostToCustomerIndicator($zeroCostToCustomerIndicator)
    {
        $this->zeroCostToCustomerIndicator = $zeroCostToCustomerIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerFirstName()
    {
        return $this->passengerFirstName;
    }

    /**
     * @param string $passengerFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerFirstName($passengerFirstName)
    {
        $this->passengerFirstName = $passengerFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerLastName()
    {
        return $this->passengerLastName;
    }

    /**
     * @param string $passengerLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerLastName($passengerLastName)
    {
        $this->passengerLastName = $passengerLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerID()
    {
        return $this->passengerID;
    }

    /**
     * @param string $passengerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerID($passengerID)
    {
        $this->passengerID = $passengerID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerStatus()
    {
        return $this->passengerStatus;
    }

    /**
     * @param string $passengerStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerStatus($passengerStatus)
    {
        $this->passengerStatus = $passengerStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerType()
    {
        return $this->passengerType;
    }

    /**
     * @param string $passengerType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerType($passengerType)
    {
        $this->passengerType = $passengerType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerEmail()
    {
        return $this->passengerEmail;
    }

    /**
     * @param string $passengerEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerEmail($passengerEmail)
    {
        $this->passengerEmail = $passengerEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerPhone()
    {
        return $this->passengerPhone;
    }

    /**
     * @param string $passengerPhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerPhone($passengerPhone)
    {
        $this->passengerPhone = $passengerPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassengerNationality()
    {
        return $this->passengerNationality;
    }

    /**
     * @param string $passengerNationality
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setPassengerNationality($passengerNationality)
    {
        $this->passengerNationality = $passengerNationality;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductDescription()
    {
        return $this->productDescription;
    }

    /**
     * @param string $productDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = substr(str_replace(['^', ':'], '', $productDescription), 0, 255);

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxStatusIndicator()
    {
        return $this->taxStatusIndicator;
    }

    /**
     * @param string $taxStatusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTaxStatusIndicator($taxStatusIndicator)
    {
        $this->taxStatusIndicator = $taxStatusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountManagementIndicator()
    {
        return $this->discountManagementIndicator;
    }

    /**
     * @param string $discountManagementIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setDiscountManagementIndicator($discountManagementIndicator)
    {
        $this->discountManagementIndicator = $discountManagementIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeOfSupply()
    {
        return $this->typeOfSupply;
    }

    /**
     * @param string $typeOfSupply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setTypeOfSupply($typeOfSupply)
    {
        $this->typeOfSupply = $typeOfSupply;

        return $this;
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnitTaxAmount()
    {
        return $this->unitTaxAmount;
    }

    /**
     * @param string $unitTaxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setUnitTaxAmount($unitTaxAmount)
    {
        $this->unitTaxAmount = $unitTaxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getWeightAmount()
    {
        return $this->weightAmount;
    }

    /**
     * @param string $weightAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setWeightAmount($weightAmount)
    {
        $this->weightAmount = $weightAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getWeightID()
    {
        return $this->weightID;
    }

    /**
     * @param string $weightID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setWeightID($weightID)
    {
        $this->weightID = $weightID;

        return $this;
    }

    /**
     * @return string
     */
    public function getWeightUnitMeasurement()
    {
        return $this->weightUnitMeasurement;
    }

    /**
     * @param string $weightUnitMeasurement
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setWeightUnitMeasurement($weightUnitMeasurement)
    {
        $this->weightUnitMeasurement = $weightUnitMeasurement;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_1_type()
    {
        return $this->otherTax_1_type;
    }

    /**
     * @param string $otherTax_1_type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_1_type($otherTax_1_type)
    {
        $this->otherTax_1_type = $otherTax_1_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_1_amount()
    {
        return $this->otherTax_1_amount;
    }

    /**
     * @param float $otherTax_1_amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_1_amount($otherTax_1_amount)
    {
        $this->otherTax_1_amount = $otherTax_1_amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_1_rate()
    {
        return $this->otherTax_1_rate;
    }

    /**
     * @param float $otherTax_1_rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_1_rate($otherTax_1_rate)
    {
        $this->otherTax_1_rate = $otherTax_1_rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_1_statusIndicator()
    {
        return $this->otherTax_1_statusIndicator;
    }

    /**
     * @param string $otherTax_1_statusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_1_statusIndicator($otherTax_1_statusIndicator)
    {
        $this->otherTax_1_statusIndicator = $otherTax_1_statusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_2_type()
    {
        return $this->otherTax_2_type;
    }

    /**
     * @param string $otherTax_2_type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_2_type($otherTax_2_type)
    {
        $this->otherTax_2_type = $otherTax_2_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_2_amount()
    {
        return $this->otherTax_2_amount;
    }

    /**
     * @param float $otherTax_2_amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_2_amount($otherTax_2_amount)
    {
        $this->otherTax_2_amount = $otherTax_2_amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_2_rate()
    {
        return $this->otherTax_2_rate;
    }

    /**
     * @param float $otherTax_2_rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_2_rate($otherTax_2_rate)
    {
        $this->otherTax_2_rate = $otherTax_2_rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_2_statusIndicator()
    {
        return $this->otherTax_2_statusIndicator;
    }

    /**
     * @param string $otherTax_2_statusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_2_statusIndicator($otherTax_2_statusIndicator)
    {
        $this->otherTax_2_statusIndicator = $otherTax_2_statusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_3_type()
    {
        return $this->otherTax_3_type;
    }

    /**
     * @param string $otherTax_3_type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_3_type($otherTax_3_type)
    {
        $this->otherTax_3_type = $otherTax_3_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_3_amount()
    {
        return $this->otherTax_3_amount;
    }

    /**
     * @param float $otherTax_3_amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_3_amount($otherTax_3_amount)
    {
        $this->otherTax_3_amount = $otherTax_3_amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_3_rate()
    {
        return $this->otherTax_3_rate;
    }

    /**
     * @param float $otherTax_3_rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_3_rate($otherTax_3_rate)
    {
        $this->otherTax_3_rate = $otherTax_3_rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_3_statusIndicator()
    {
        return $this->otherTax_3_statusIndicator;
    }

    /**
     * @param string $otherTax_3_statusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_3_statusIndicator($otherTax_3_statusIndicator)
    {
        $this->otherTax_3_statusIndicator = $otherTax_3_statusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_4_type()
    {
        return $this->otherTax_4_type;
    }

    /**
     * @param string $otherTax_4_type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_4_type($otherTax_4_type)
    {
        $this->otherTax_4_type = $otherTax_4_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_4_amount()
    {
        return $this->otherTax_4_amount;
    }

    /**
     * @param float $otherTax_4_amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_4_amount($otherTax_4_amount)
    {
        $this->otherTax_4_amount = $otherTax_4_amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_4_rate()
    {
        return $this->otherTax_4_rate;
    }

    /**
     * @param float $otherTax_4_rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_4_rate($otherTax_4_rate)
    {
        $this->otherTax_4_rate = $otherTax_4_rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_4_statusIndicator()
    {
        return $this->otherTax_4_statusIndicator;
    }

    /**
     * @param string $otherTax_4_statusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_4_statusIndicator($otherTax_4_statusIndicator)
    {
        $this->otherTax_4_statusIndicator = $otherTax_4_statusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_5_type()
    {
        return $this->otherTax_5_type;
    }

    /**
     * @param string $otherTax_5_type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_5_type($otherTax_5_type)
    {
        $this->otherTax_5_type = $otherTax_5_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_5_amount()
    {
        return $this->otherTax_5_amount;
    }

    /**
     * @param float $otherTax_5_amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_5_amount($otherTax_5_amount)
    {
        $this->otherTax_5_amount = $otherTax_5_amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_5_rate()
    {
        return $this->otherTax_5_rate;
    }

    /**
     * @param float $otherTax_5_rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_5_rate($otherTax_5_rate)
    {
        $this->otherTax_5_rate = $otherTax_5_rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_5_statusIndicator()
    {
        return $this->otherTax_5_statusIndicator;
    }

    /**
     * @param string $otherTax_5_statusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_5_statusIndicator($otherTax_5_statusIndicator)
    {
        $this->otherTax_5_statusIndicator = $otherTax_5_statusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_6_type()
    {
        return $this->otherTax_6_type;
    }

    /**
     * @param string $otherTax_6_type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_6_type($otherTax_6_type)
    {
        $this->otherTax_6_type = $otherTax_6_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_6_amount()
    {
        return $this->otherTax_6_amount;
    }

    /**
     * @param float $otherTax_6_amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_6_amount($otherTax_6_amount)
    {
        $this->otherTax_6_amount = $otherTax_6_amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_6_rate()
    {
        return $this->otherTax_6_rate;
    }

    /**
     * @param float $otherTax_6_rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_6_rate($otherTax_6_rate)
    {
        $this->otherTax_6_rate = $otherTax_6_rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_6_statusIndicator()
    {
        return $this->otherTax_6_statusIndicator;
    }

    /**
     * @param string $otherTax_6_statusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_6_statusIndicator($otherTax_6_statusIndicator)
    {
        $this->otherTax_6_statusIndicator = $otherTax_6_statusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_7_type()
    {
        return $this->otherTax_7_type;
    }

    /**
     * @param string $otherTax_7_type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_7_type($otherTax_7_type)
    {
        $this->otherTax_7_type = $otherTax_7_type;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_7_amount()
    {
        return $this->otherTax_7_amount;
    }

    /**
     * @param float $otherTax_7_amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_7_amount($otherTax_7_amount)
    {
        $this->otherTax_7_amount = $otherTax_7_amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherTax_7_rate()
    {
        return $this->otherTax_7_rate;
    }

    /**
     * @param float $otherTax_7_rate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_7_rate($otherTax_7_rate)
    {
        $this->otherTax_7_rate = $otherTax_7_rate;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherTax_7_statusIndicator()
    {
        return $this->otherTax_7_statusIndicator;
    }

    /**
     * @param string $otherTax_7_statusIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setOtherTax_7_statusIndicator($otherTax_7_statusIndicator)
    {
        $this->otherTax_7_statusIndicator = $otherTax_7_statusIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_1_number()
    {
        return $this->referenceData_1_number;
    }

    /**
     * @param string $referenceData_1_number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_1_number($referenceData_1_number)
    {
        $this->referenceData_1_number = $referenceData_1_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_1_code()
    {
        return $this->referenceData_1_code;
    }

    /**
     * @param string $referenceData_1_code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_1_code($referenceData_1_code)
    {
        $this->referenceData_1_code = $referenceData_1_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_2_number()
    {
        return $this->referenceData_2_number;
    }

    /**
     * @param string $referenceData_2_number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_2_number($referenceData_2_number)
    {
        $this->referenceData_2_number = $referenceData_2_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_2_code()
    {
        return $this->referenceData_2_code;
    }

    /**
     * @param string $referenceData_2_code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_2_code($referenceData_2_code)
    {
        $this->referenceData_2_code = $referenceData_2_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_3_number()
    {
        return $this->referenceData_3_number;
    }

    /**
     * @param string $referenceData_3_number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_3_number($referenceData_3_number)
    {
        $this->referenceData_3_number = $referenceData_3_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_3_code()
    {
        return $this->referenceData_3_code;
    }

    /**
     * @param string $referenceData_3_code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_3_code($referenceData_3_code)
    {
        $this->referenceData_3_code = $referenceData_3_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_4_number()
    {
        return $this->referenceData_4_number;
    }

    /**
     * @param string $referenceData_4_number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_4_number($referenceData_4_number)
    {
        $this->referenceData_4_number = $referenceData_4_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_4_code()
    {
        return $this->referenceData_4_code;
    }

    /**
     * @param string $referenceData_4_code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_4_code($referenceData_4_code)
    {
        $this->referenceData_4_code = $referenceData_4_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_5_number()
    {
        return $this->referenceData_5_number;
    }

    /**
     * @param string $referenceData_5_number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_5_number($referenceData_5_number)
    {
        $this->referenceData_5_number = $referenceData_5_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_5_code()
    {
        return $this->referenceData_5_code;
    }

    /**
     * @param string $referenceData_5_code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_5_code($referenceData_5_code)
    {
        $this->referenceData_5_code = $referenceData_5_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_6_number()
    {
        return $this->referenceData_6_number;
    }

    /**
     * @param string $referenceData_6_number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_6_number($referenceData_6_number)
    {
        $this->referenceData_6_number = $referenceData_6_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_6_code()
    {
        return $this->referenceData_6_code;
    }

    /**
     * @param string $referenceData_6_code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_6_code($referenceData_6_code)
    {
        $this->referenceData_6_code = $referenceData_6_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_7_number()
    {
        return $this->referenceData_7_number;
    }

    /**
     * @param string $referenceData_7_number
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_7_number($referenceData_7_number)
    {
        $this->referenceData_7_number = $referenceData_7_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceData_7_code()
    {
        return $this->referenceData_7_code;
    }

    /**
     * @param string $referenceData_7_code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setReferenceData_7_code($referenceData_7_code)
    {
        $this->referenceData_7_code = $referenceData_7_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingDestinationTypes()
    {
        return $this->shippingDestinationTypes;
    }

    /**
     * @param string $shippingDestinationTypes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setShippingDestinationTypes($shippingDestinationTypes)
    {
        $this->shippingDestinationTypes = $shippingDestinationTypes;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
