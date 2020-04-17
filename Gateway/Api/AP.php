<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AP
{
    /**
     * @var string $orderID
     */
    protected $orderID;

    /**
     * @var string $pspBarcodeID
     */
    protected $pspBarcodeID;

    /**
     * @var string $customerRepresentativeID
     */
    protected $customerRepresentativeID;

    /**
     * @var string $productDescription
     */
    protected $productDescription;

    /**
     * @var string $settlementCurrency
     */
    protected $settlementCurrency;

    /**
     * @var string $subtotalAmount
     */
    protected $subtotalAmount;

    /**
     * @var string $shippingAmount
     */
    protected $shippingAmount;

    /**
     * @var string $handlingAmount
     */
    protected $handlingAmount;

    /**
     * @var string $shippingHandlingAmount
     */
    protected $shippingHandlingAmount;

    /**
     * @var string $additionalAmount
     */
    protected $additionalAmount;

    /**
     * @var string $taxAmount
     */
    protected $taxAmount;

    /**
     * @var string $giftWrapAmount
     */
    protected $giftWrapAmount;

    /**
     * @var string $discountAmount
     */
    protected $discountAmount;

    /**
     * @var string $purchaseID
     */
    protected $purchaseID;

    /**
     * @var string $productID
     */
    protected $productID;

    /**
     * @var APDevice $device
     */
    protected $device;

    /**
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @var string $insuranceAmount
     */
    protected $insuranceAmount;

    /**
     * @var boolean $billingAgreementIndicator
     */
    protected $billingAgreementIndicator;

    /**
     * @var string $billingAgreementID
     */
    protected $billingAgreementID;

    /**
     * @var string $billingAgreementDescription
     */
    protected $billingAgreementDescription;

    /**
     * @var string $payerID
     */
    protected $payerID;

    /**
     * @var string $fundingSource
     */
    protected $fundingSource;

    /**
     * @var string $shippingAddressImmutable
     */
    protected $shippingAddressImmutable;

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @param string $orderID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPspBarcodeID()
    {
        return $this->pspBarcodeID;
    }

    /**
     * @param string $pspBarcodeID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setPspBarcodeID($pspBarcodeID)
    {
        $this->pspBarcodeID = $pspBarcodeID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerRepresentativeID()
    {
        return $this->customerRepresentativeID;
    }

    /**
     * @param string $customerRepresentativeID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setCustomerRepresentativeID($customerRepresentativeID)
    {
        $this->customerRepresentativeID = $customerRepresentativeID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getSettlementCurrency()
    {
        return $this->settlementCurrency;
    }

    /**
     * @param string $settlementCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setSettlementCurrency($settlementCurrency)
    {
        $this->settlementCurrency = $settlementCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtotalAmount()
    {
        return $this->subtotalAmount;
    }

    /**
     * @param string $subtotalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setSubtotalAmount($subtotalAmount)
    {
        $this->subtotalAmount = $subtotalAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }

    /**
     * @param string $shippingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setShippingAmount($shippingAmount)
    {
        $this->shippingAmount = $shippingAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getHandlingAmount()
    {
        return $this->handlingAmount;
    }

    /**
     * @param string $handlingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setHandlingAmount($handlingAmount)
    {
        $this->handlingAmount = $handlingAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingHandlingAmount()
    {
        return $this->shippingHandlingAmount;
    }

    /**
     * @param string $shippingHandlingAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setShippingHandlingAmount($shippingHandlingAmount)
    {
        $this->shippingHandlingAmount = $shippingHandlingAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalAmount()
    {
        return $this->additionalAmount;
    }

    /**
     * @param string $additionalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setAdditionalAmount($additionalAmount)
    {
        $this->additionalAmount = $additionalAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param string $taxAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getGiftWrapAmount()
    {
        return $this->giftWrapAmount;
    }

    /**
     * @param string $giftWrapAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setGiftWrapAmount($giftWrapAmount)
    {
        $this->giftWrapAmount = $giftWrapAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param string $discountAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPurchaseID()
    {
        return $this->purchaseID;
    }

    /**
     * @param string $purchaseID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setPurchaseID($purchaseID)
    {
        $this->purchaseID = $purchaseID;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductID()
    {
        return $this->productID;
    }

    /**
     * @param string $productID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setProductID($productID)
    {
        $this->productID = $productID;

        return $this;
    }

    /**
     * @return APDevice
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param APDevice $device
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getInsuranceAmount()
    {
        return $this->insuranceAmount;
    }

    /**
     * @param string $insuranceAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setInsuranceAmount($insuranceAmount)
    {
        $this->insuranceAmount = $insuranceAmount;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getBillingAgreementIndicator()
    {
        return $this->billingAgreementIndicator;
    }

    /**
     * @param boolean $billingAgreementIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setBillingAgreementIndicator($billingAgreementIndicator)
    {
        $this->billingAgreementIndicator = $billingAgreementIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAgreementID()
    {
        return $this->billingAgreementID;
    }

    /**
     * @param string $billingAgreementID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setBillingAgreementID($billingAgreementID)
    {
        $this->billingAgreementID = $billingAgreementID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAgreementDescription()
    {
        return $this->billingAgreementDescription;
    }

    /**
     * @param string $billingAgreementDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setBillingAgreementDescription($billingAgreementDescription)
    {
        $this->billingAgreementDescription = $billingAgreementDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayerID()
    {
        return $this->payerID;
    }

    /**
     * @param string $payerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setPayerID($payerID)
    {
        $this->payerID = $payerID;

        return $this;
    }

    /**
     * @return string
     */
    public function getFundingSource()
    {
        return $this->fundingSource;
    }

    /**
     * @param string $fundingSource
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setFundingSource($fundingSource)
    {
        $this->fundingSource = $fundingSource;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAddressImmutable()
    {
        return $this->shippingAddressImmutable;
    }

    /**
     * @param string $shippingAddressImmutable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AP
     */
    public function setShippingAddressImmutable($shippingAddressImmutable)
    {
        $this->shippingAddressImmutable = $shippingAddressImmutable;

        return $this;
    }
}
