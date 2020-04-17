<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APReply
{
    /**
     * @var string $orderID
     */
    protected $orderID;

    /**
     * @var string $cardGroup
     */
    protected $cardGroup;

    /**
     * @var string $cardType
     */
    protected $cardType;

    /**
     * @var string $cardNumberSuffix
     */
    protected $cardNumberSuffix;

    /**
     * @var string $cardExpirationMonth
     */
    protected $cardExpirationMonth;

    /**
     * @var string $cardExpirationYear
     */
    protected $cardExpirationYear;

    /**
     * @var string $avsCodeRaw
     */
    protected $avsCodeRaw;

    /**
     * @var string $purchaseID
     */
    protected $purchaseID;

    /**
     * @var string $productID
     */
    protected $productID;

    /**
     * @var string $productDescription
     */
    protected $productDescription;

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
     * @var string $subtotalAmount
     */
    protected $subtotalAmount;

    /**
     * @var string $totalPurchaseAmount
     */
    protected $totalPurchaseAmount;

    /**
     * @var string $giftWrapAmount
     */
    protected $giftWrapAmount;

    /**
     * @var string $discountAmount
     */
    protected $discountAmount;

    /**
     * @var string $cardNumberPrefix
     */
    protected $cardNumberPrefix;

    /**
     * @var string $riskIndicator
     */
    protected $riskIndicator;

    /**
     * @var string $merchantUUID
     */
    protected $merchantUUID;

    /**
     * @var string $merchantSiteID
     */
    protected $merchantSiteID;

    /**
     * @var string $transactionExpirationDate
     */
    protected $transactionExpirationDate;

    /**
     * @var SellerProtection $sellerProtection
     */
    protected $sellerProtection;

    /**
     * @var string $processorFraudDecision
     */
    protected $processorFraudDecision;

    /**
     * @var string $processorFraudDecisionReason
     */
    protected $processorFraudDecisionReason;

    /**
     * @var string $customerID
     */
    protected $customerID;

    /**
     * @var string $billingAgreementID
     */
    protected $billingAgreementID;

    /**
     * @var string $payerID
     */
    protected $payerID;

    /**
     * @var string $fundingSource
     */
    protected $fundingSource;

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @param string $orderID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardGroup()
    {
        return $this->cardGroup;
    }

    /**
     * @param string $cardGroup
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setCardGroup($cardGroup)
    {
        $this->cardGroup = $cardGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param string $cardType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardNumberSuffix()
    {
        return $this->cardNumberSuffix;
    }

    /**
     * @param string $cardNumberSuffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setCardNumberSuffix($cardNumberSuffix)
    {
        $this->cardNumberSuffix = $cardNumberSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardExpirationMonth()
    {
        return $this->cardExpirationMonth;
    }

    /**
     * @param string $cardExpirationMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setCardExpirationMonth($cardExpirationMonth)
    {
        $this->cardExpirationMonth = $cardExpirationMonth;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardExpirationYear()
    {
        return $this->cardExpirationYear;
    }

    /**
     * @param string $cardExpirationYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setCardExpirationYear($cardExpirationYear)
    {
        $this->cardExpirationYear = $cardExpirationYear;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCodeRaw()
    {
        return $this->avsCodeRaw;
    }

    /**
     * @param string $avsCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setAvsCodeRaw($avsCodeRaw)
    {
        $this->avsCodeRaw = $avsCodeRaw;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setProductID($productID)
    {
        $this->productID = $productID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setSubtotalAmount($subtotalAmount)
    {
        $this->subtotalAmount = $subtotalAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotalPurchaseAmount()
    {
        return $this->totalPurchaseAmount;
    }

    /**
     * @param string $totalPurchaseAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setTotalPurchaseAmount($totalPurchaseAmount)
    {
        $this->totalPurchaseAmount = $totalPurchaseAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardNumberPrefix()
    {
        return $this->cardNumberPrefix;
    }

    /**
     * @param string $cardNumberPrefix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setCardNumberPrefix($cardNumberPrefix)
    {
        $this->cardNumberPrefix = $cardNumberPrefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskIndicator()
    {
        return $this->riskIndicator;
    }

    /**
     * @param string $riskIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setRiskIndicator($riskIndicator)
    {
        $this->riskIndicator = $riskIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantUUID()
    {
        return $this->merchantUUID;
    }

    /**
     * @param string $merchantUUID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setMerchantUUID($merchantUUID)
    {
        $this->merchantUUID = $merchantUUID;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantSiteID()
    {
        return $this->merchantSiteID;
    }

    /**
     * @param string $merchantSiteID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setMerchantSiteID($merchantSiteID)
    {
        $this->merchantSiteID = $merchantSiteID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionExpirationDate()
    {
        return $this->transactionExpirationDate;
    }

    /**
     * @param string $transactionExpirationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setTransactionExpirationDate($transactionExpirationDate)
    {
        $this->transactionExpirationDate = $transactionExpirationDate;

        return $this;
    }

    /**
     * @return SellerProtection
     */
    public function getSellerProtection()
    {
        return $this->sellerProtection;
    }

    /**
     * @param SellerProtection $sellerProtection
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setSellerProtection($sellerProtection)
    {
        $this->sellerProtection = $sellerProtection;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorFraudDecision()
    {
        return $this->processorFraudDecision;
    }

    /**
     * @param string $processorFraudDecision
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setProcessorFraudDecision($processorFraudDecision)
    {
        $this->processorFraudDecision = $processorFraudDecision;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorFraudDecisionReason()
    {
        return $this->processorFraudDecisionReason;
    }

    /**
     * @param string $processorFraudDecisionReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setProcessorFraudDecisionReason($processorFraudDecisionReason)
    {
        $this->processorFraudDecisionReason = $processorFraudDecisionReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * @param string $customerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setBillingAgreementID($billingAgreementID)
    {
        $this->billingAgreementID = $billingAgreementID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APReply
     */
    public function setFundingSource($fundingSource)
    {
        $this->fundingSource = $fundingSource;

        return $this;
    }
}
