<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PurchaseTotals
{

    /**
     * @var string $currency
     */
    protected $currency = null;

    /**
     * @var float $discountAmount
     */
    protected $discountAmount = null;

    /**
     * @var string $discountAmountSign
     */
    protected $discountAmountSign = null;

    /**
     * @var string $discountManagementIndicator
     */
    protected $discountManagementIndicator = null;

    /**
     * @var float $taxAmount
     */
    protected $taxAmount = null;

    /**
     * @var float $dutyAmount
     */
    protected $dutyAmount = null;

    /**
     * @var string $dutyAmountSign
     */
    protected $dutyAmountSign = null;

    /**
     * @var float $grandTotalAmount
     */
    protected $grandTotalAmount = null;

    /**
     * @var float $freightAmount
     */
    protected $freightAmount = null;

    /**
     * @var string $freightAmountSign
     */
    protected $freightAmountSign = null;

    /**
     * @var float $foreignAmount
     */
    protected $foreignAmount = null;

    /**
     * @var string $foreignCurrency
     */
    protected $foreignCurrency = null;

    /**
     * @var float $originalAmount
     */
    protected $originalAmount = null;

    /**
     * @var string $originalCurrency
     */
    protected $originalCurrency = null;

    /**
     * @var float $exchangeRate
     */
    protected $exchangeRate = null;

    /**
     * @var string $exchangeRateTimeStamp
     */
    protected $exchangeRateTimeStamp = null;

    /**
     * @var string $additionalAmountType0
     */
    protected $additionalAmountType0 = null;

    /**
     * @var string $additionalAmount0
     */
    protected $additionalAmount0 = null;

    /**
     * @var string $additionalAmountType1
     */
    protected $additionalAmountType1 = null;

    /**
     * @var string $additionalAmount1
     */
    protected $additionalAmount1 = null;

    /**
     * @var string $additionalAmountType2
     */
    protected $additionalAmountType2 = null;

    /**
     * @var string $additionalAmount2
     */
    protected $additionalAmount2 = null;

    /**
     * @var string $additionalAmountType3
     */
    protected $additionalAmountType3 = null;

    /**
     * @var string $additionalAmount3
     */
    protected $additionalAmount3 = null;

    /**
     * @var string $additionalAmountType4
     */
    protected $additionalAmountType4 = null;

    /**
     * @var string $additionalAmount4
     */
    protected $additionalAmount4 = null;

    /**
     * @var float $serviceFeeAmount
     */
    protected $serviceFeeAmount = null;

    /**
     * @var float $subtotalAmount
     */
    protected $subtotalAmount = null;

    /**
     * @var float $shippingAmount
     */
    protected $shippingAmount = null;

    /**
     * @var float $handlingAmount
     */
    protected $handlingAmount = null;

    /**
     * @var float $shippingHandlingAmount
     */
    protected $shippingHandlingAmount = null;

    /**
     * @var float $shippingDiscountAmount
     */
    protected $shippingDiscountAmount = null;

    /**
     * @var float $giftWrapAmount
     */
    protected $giftWrapAmount = null;

    /**
     * @var float $insuranceAmount
     */
    protected $insuranceAmount = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
      return $this->currency;
    }

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setCurrency($currency)
    {
      $this->currency = $currency;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setDiscountAmount($discountAmount)
    {
      $this->discountAmount = $discountAmount;
      return $this;
    }

    /**
     * @return string
     */
    public function getDiscountAmountSign()
    {
      return $this->discountAmountSign;
    }

    /**
     * @param string $discountAmountSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setDiscountAmountSign($discountAmountSign)
    {
      $this->discountAmountSign = $discountAmountSign;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setDiscountManagementIndicator($discountManagementIndicator)
    {
      $this->discountManagementIndicator = $discountManagementIndicator;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setTaxAmount($taxAmount)
    {
      $this->taxAmount = $taxAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getDutyAmount()
    {
      return $this->dutyAmount;
    }

    /**
     * @param float $dutyAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setDutyAmount($dutyAmount)
    {
      $this->dutyAmount = $dutyAmount;
      return $this;
    }

    /**
     * @return string
     */
    public function getDutyAmountSign()
    {
      return $this->dutyAmountSign;
    }

    /**
     * @param string $dutyAmountSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setDutyAmountSign($dutyAmountSign)
    {
      $this->dutyAmountSign = $dutyAmountSign;
      return $this;
    }

    /**
     * @return float
     */
    public function getGrandTotalAmount()
    {
      return $this->grandTotalAmount;
    }

    /**
     * @param float $grandTotalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setGrandTotalAmount($grandTotalAmount)
    {
      $this->grandTotalAmount = $grandTotalAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getFreightAmount()
    {
      return $this->freightAmount;
    }

    /**
     * @param float $freightAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setFreightAmount($freightAmount)
    {
      $this->freightAmount = $freightAmount;
      return $this;
    }

    /**
     * @return string
     */
    public function getFreightAmountSign()
    {
      return $this->freightAmountSign;
    }

    /**
     * @param string $freightAmountSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setFreightAmountSign($freightAmountSign)
    {
      $this->freightAmountSign = $freightAmountSign;
      return $this;
    }

    /**
     * @return float
     */
    public function getForeignAmount()
    {
      return $this->foreignAmount;
    }

    /**
     * @param float $foreignAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setForeignAmount($foreignAmount)
    {
      $this->foreignAmount = $foreignAmount;
      return $this;
    }

    /**
     * @return string
     */
    public function getForeignCurrency()
    {
      return $this->foreignCurrency;
    }

    /**
     * @param string $foreignCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setForeignCurrency($foreignCurrency)
    {
      $this->foreignCurrency = $foreignCurrency;
      return $this;
    }

    /**
     * @return float
     */
    public function getOriginalAmount()
    {
      return $this->originalAmount;
    }

    /**
     * @param float $originalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setOriginalAmount($originalAmount)
    {
      $this->originalAmount = $originalAmount;
      return $this;
    }

    /**
     * @return string
     */
    public function getOriginalCurrency()
    {
      return $this->originalCurrency;
    }

    /**
     * @param string $originalCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setOriginalCurrency($originalCurrency)
    {
      $this->originalCurrency = $originalCurrency;
      return $this;
    }

    /**
     * @return float
     */
    public function getExchangeRate()
    {
      return $this->exchangeRate;
    }

    /**
     * @param float $exchangeRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setExchangeRate($exchangeRate)
    {
      $this->exchangeRate = $exchangeRate;
      return $this;
    }

    /**
     * @return string
     */
    public function getExchangeRateTimeStamp()
    {
      return $this->exchangeRateTimeStamp;
    }

    /**
     * @param string $exchangeRateTimeStamp
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setExchangeRateTimeStamp($exchangeRateTimeStamp)
    {
      $this->exchangeRateTimeStamp = $exchangeRateTimeStamp;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmountType0()
    {
      return $this->additionalAmountType0;
    }

    /**
     * @param string $additionalAmountType0
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmountType0($additionalAmountType0)
    {
      $this->additionalAmountType0 = $additionalAmountType0;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmount0()
    {
      return $this->additionalAmount0;
    }

    /**
     * @param string $additionalAmount0
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmount0($additionalAmount0)
    {
      $this->additionalAmount0 = $additionalAmount0;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmountType1()
    {
      return $this->additionalAmountType1;
    }

    /**
     * @param string $additionalAmountType1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmountType1($additionalAmountType1)
    {
      $this->additionalAmountType1 = $additionalAmountType1;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmount1()
    {
      return $this->additionalAmount1;
    }

    /**
     * @param string $additionalAmount1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmount1($additionalAmount1)
    {
      $this->additionalAmount1 = $additionalAmount1;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmountType2()
    {
      return $this->additionalAmountType2;
    }

    /**
     * @param string $additionalAmountType2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmountType2($additionalAmountType2)
    {
      $this->additionalAmountType2 = $additionalAmountType2;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmount2()
    {
      return $this->additionalAmount2;
    }

    /**
     * @param string $additionalAmount2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmount2($additionalAmount2)
    {
      $this->additionalAmount2 = $additionalAmount2;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmountType3()
    {
      return $this->additionalAmountType3;
    }

    /**
     * @param string $additionalAmountType3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmountType3($additionalAmountType3)
    {
      $this->additionalAmountType3 = $additionalAmountType3;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmount3()
    {
      return $this->additionalAmount3;
    }

    /**
     * @param string $additionalAmount3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmount3($additionalAmount3)
    {
      $this->additionalAmount3 = $additionalAmount3;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmountType4()
    {
      return $this->additionalAmountType4;
    }

    /**
     * @param string $additionalAmountType4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmountType4($additionalAmountType4)
    {
      $this->additionalAmountType4 = $additionalAmountType4;
      return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmount4()
    {
      return $this->additionalAmount4;
    }

    /**
     * @param string $additionalAmount4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setAdditionalAmount4($additionalAmount4)
    {
      $this->additionalAmount4 = $additionalAmount4;
      return $this;
    }

    /**
     * @return float
     */
    public function getServiceFeeAmount()
    {
      return $this->serviceFeeAmount;
    }

    /**
     * @param float $serviceFeeAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setServiceFeeAmount($serviceFeeAmount)
    {
      $this->serviceFeeAmount = $serviceFeeAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getSubtotalAmount()
    {
      return $this->subtotalAmount;
    }

    /**
     * @param float $subtotalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setSubtotalAmount($subtotalAmount)
    {
      $this->subtotalAmount = $subtotalAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getShippingAmount()
    {
      return $this->shippingAmount;
    }

    /**
     * @param float $shippingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setShippingAmount($shippingAmount)
    {
      $this->shippingAmount = $shippingAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getHandlingAmount()
    {
      return $this->handlingAmount;
    }

    /**
     * @param float $handlingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setHandlingAmount($handlingAmount)
    {
      $this->handlingAmount = $handlingAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getShippingHandlingAmount()
    {
      return $this->shippingHandlingAmount;
    }

    /**
     * @param float $shippingHandlingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setShippingHandlingAmount($shippingHandlingAmount)
    {
      $this->shippingHandlingAmount = $shippingHandlingAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getShippingDiscountAmount()
    {
      return $this->shippingDiscountAmount;
    }

    /**
     * @param float $shippingDiscountAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setShippingDiscountAmount($shippingDiscountAmount)
    {
      $this->shippingDiscountAmount = $shippingDiscountAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getGiftWrapAmount()
    {
      return $this->giftWrapAmount;
    }

    /**
     * @param float $giftWrapAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setGiftWrapAmount($giftWrapAmount)
    {
      $this->giftWrapAmount = $giftWrapAmount;
      return $this;
    }

    /**
     * @return float
     */
    public function getInsuranceAmount()
    {
      return $this->insuranceAmount;
    }

    /**
     * @param float $insuranceAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function setInsuranceAmount($insuranceAmount)
    {
      $this->insuranceAmount = $insuranceAmount;
      return $this;
    }

}
