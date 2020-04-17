<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VCReply
{
    /**
     * @var string $creationTimeStamp
     */
    protected $creationTimeStamp;

    /**
     * @var string $alternateShippingAddressCountryCode
     */
    protected $alternateShippingAddressCountryCode;

    /**
     * @var string $alternateShippingAddressPostalCode
     */
    protected $alternateShippingAddressPostalCode;

    /**
     * @var string $vcAccountLoginName
     */
    protected $vcAccountLoginName;

    /**
     * @var string $vcAccountFirstName
     */
    protected $vcAccountFirstName;

    /**
     * @var string $vcAccountLastName
     */
    protected $vcAccountLastName;

    /**
     * @var string $vcAccountEncryptedID
     */
    protected $vcAccountEncryptedID;

    /**
     * @var string $vcAccountEmail
     */
    protected $vcAccountEmail;

    /**
     * @var string $vcAccountMobilePhoneNumber
     */
    protected $vcAccountMobilePhoneNumber;

    /**
     * @var string $merchantReferenceID
     */
    protected $merchantReferenceID;

    /**
     * @var string $subtotalAmount
     */
    protected $subtotalAmount;

    /**
     * @var string $shippingHandlingAmount
     */
    protected $shippingHandlingAmount;

    /**
     * @var string $taxAmount
     */
    protected $taxAmount;

    /**
     * @var string $discountAmount
     */
    protected $discountAmount;

    /**
     * @var string $giftWrapAmount
     */
    protected $giftWrapAmount;

    /**
     * @var string $uncategorizedAmount
     */
    protected $uncategorizedAmount;

    /**
     * @var string $totalPurchaseAmount
     */
    protected $totalPurchaseAmount;

    /**
     * @var string $walletReferenceID
     */
    protected $walletReferenceID;

    /**
     * @var string $promotionCode
     */
    protected $promotionCode;

    /**
     * @var string $paymentInstrumentID
     */
    protected $paymentInstrumentID;

    /**
     * @var string $cardVerificationStatus
     */
    protected $cardVerificationStatus;

    /**
     * @var string $issuerID
     */
    protected $issuerID;

    /**
     * @var string $paymentInstrumentNickName
     */
    protected $paymentInstrumentNickName;

    /**
     * @var string $nameOnCard
     */
    protected $nameOnCard;

    /**
     * @var string $cardType
     */
    protected $cardType;

    /**
     * @var string $cardGroup
     */
    protected $cardGroup;

    /**
     * @var VCCardArt $cardArt
     */
    protected $cardArt;

    /**
     * @var string $riskAdvice
     */
    protected $riskAdvice;

    /**
     * @var string $riskScore
     */
    protected $riskScore;

    /**
     * @var string $riskAdditionalData
     */
    protected $riskAdditionalData;

    /**
     * @var string $avsCodeRaw
     */
    protected $avsCodeRaw;

    /**
     * @var string $cvnCodeRaw
     */
    protected $cvnCodeRaw;

    /**
     * @var string $eciRaw
     */
    protected $eciRaw;

    /**
     * @var string $eci
     */
    protected $eci;

    /**
     * @var string $cavv
     */
    protected $cavv;

    /**
     * @var string $veresEnrolled
     */
    protected $veresEnrolled;

    /**
     * @var string $veresTimeStamp
     */
    protected $veresTimeStamp;

    /**
     * @var string $paresStatus
     */
    protected $paresStatus;

    /**
     * @var string $paresTimeStamp
     */
    protected $paresTimeStamp;

    /**
     * @var string $xid
     */
    protected $xid;

    /**
     * @var VCCustomData $customData
     */
    protected $customData;

    /**
     * @var string $vcAccountFullName
     */
    protected $vcAccountFullName;

    /**
     * @var string $paymentDescription
     */
    protected $paymentDescription;

    /**
     * @var string $billingAddressStreetName
     */
    protected $billingAddressStreetName;

    /**
     * @var string $billingAddressAdditionalLocation
     */
    protected $billingAddressAdditionalLocation;

    /**
     * @var string $billingAddressStreetNumber
     */
    protected $billingAddressStreetNumber;

    /**
     * @var string $expiredCard
     */
    protected $expiredCard;

    /**
     * @var string $cardFirstName
     */
    protected $cardFirstName;

    /**
     * @var string $cardLastName
     */
    protected $cardLastName;

    /**
     * @var string $shippingAddressStreetName
     */
    protected $shippingAddressStreetName;

    /**
     * @var string $shippingAddressAdditionalLocation
     */
    protected $shippingAddressAdditionalLocation;

    /**
     * @var string $shippingAddressStreetNumber
     */
    protected $shippingAddressStreetNumber;

    /**
     * @var string $ageOfAccount
     */
    protected $ageOfAccount;

    /**
     * @var string $newUser
     */
    protected $newUser;

    /**
     * @return string
     */
    public function getCreationTimeStamp()
    {
        return $this->creationTimeStamp;
    }

    /**
     * @param string $creationTimeStamp
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCreationTimeStamp($creationTimeStamp)
    {
        $this->creationTimeStamp = $creationTimeStamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateShippingAddressCountryCode()
    {
        return $this->alternateShippingAddressCountryCode;
    }

    /**
     * @param string $alternateShippingAddressCountryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setAlternateShippingAddressCountryCode($alternateShippingAddressCountryCode)
    {
        $this->alternateShippingAddressCountryCode = $alternateShippingAddressCountryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateShippingAddressPostalCode()
    {
        return $this->alternateShippingAddressPostalCode;
    }

    /**
     * @param string $alternateShippingAddressPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setAlternateShippingAddressPostalCode($alternateShippingAddressPostalCode)
    {
        $this->alternateShippingAddressPostalCode = $alternateShippingAddressPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getVcAccountLoginName()
    {
        return $this->vcAccountLoginName;
    }

    /**
     * @param string $vcAccountLoginName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVcAccountLoginName($vcAccountLoginName)
    {
        $this->vcAccountLoginName = $vcAccountLoginName;

        return $this;
    }

    /**
     * @return string
     */
    public function getVcAccountFirstName()
    {
        return $this->vcAccountFirstName;
    }

    /**
     * @param string $vcAccountFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVcAccountFirstName($vcAccountFirstName)
    {
        $this->vcAccountFirstName = $vcAccountFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getVcAccountLastName()
    {
        return $this->vcAccountLastName;
    }

    /**
     * @param string $vcAccountLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVcAccountLastName($vcAccountLastName)
    {
        $this->vcAccountLastName = $vcAccountLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getVcAccountEncryptedID()
    {
        return $this->vcAccountEncryptedID;
    }

    /**
     * @param string $vcAccountEncryptedID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVcAccountEncryptedID($vcAccountEncryptedID)
    {
        $this->vcAccountEncryptedID = $vcAccountEncryptedID;

        return $this;
    }

    /**
     * @return string
     */
    public function getVcAccountEmail()
    {
        return $this->vcAccountEmail;
    }

    /**
     * @param string $vcAccountEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVcAccountEmail($vcAccountEmail)
    {
        $this->vcAccountEmail = $vcAccountEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getVcAccountMobilePhoneNumber()
    {
        return $this->vcAccountMobilePhoneNumber;
    }

    /**
     * @param string $vcAccountMobilePhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVcAccountMobilePhoneNumber($vcAccountMobilePhoneNumber)
    {
        $this->vcAccountMobilePhoneNumber = $vcAccountMobilePhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantReferenceID()
    {
        return $this->merchantReferenceID;
    }

    /**
     * @param string $merchantReferenceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setMerchantReferenceID($merchantReferenceID)
    {
        $this->merchantReferenceID = $merchantReferenceID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setSubtotalAmount($subtotalAmount)
    {
        $this->subtotalAmount = $subtotalAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setShippingHandlingAmount($shippingHandlingAmount)
    {
        $this->shippingHandlingAmount = $shippingHandlingAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setGiftWrapAmount($giftWrapAmount)
    {
        $this->giftWrapAmount = $giftWrapAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getUncategorizedAmount()
    {
        return $this->uncategorizedAmount;
    }

    /**
     * @param string $uncategorizedAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setUncategorizedAmount($uncategorizedAmount)
    {
        $this->uncategorizedAmount = $uncategorizedAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setTotalPurchaseAmount($totalPurchaseAmount)
    {
        $this->totalPurchaseAmount = $totalPurchaseAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getWalletReferenceID()
    {
        return $this->walletReferenceID;
    }

    /**
     * @param string $walletReferenceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setWalletReferenceID($walletReferenceID)
    {
        $this->walletReferenceID = $walletReferenceID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromotionCode()
    {
        return $this->promotionCode;
    }

    /**
     * @param string $promotionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setPromotionCode($promotionCode)
    {
        $this->promotionCode = $promotionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentInstrumentID()
    {
        return $this->paymentInstrumentID;
    }

    /**
     * @param string $paymentInstrumentID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setPaymentInstrumentID($paymentInstrumentID)
    {
        $this->paymentInstrumentID = $paymentInstrumentID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardVerificationStatus()
    {
        return $this->cardVerificationStatus;
    }

    /**
     * @param string $cardVerificationStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCardVerificationStatus($cardVerificationStatus)
    {
        $this->cardVerificationStatus = $cardVerificationStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuerID()
    {
        return $this->issuerID;
    }

    /**
     * @param string $issuerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setIssuerID($issuerID)
    {
        $this->issuerID = $issuerID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentInstrumentNickName()
    {
        return $this->paymentInstrumentNickName;
    }

    /**
     * @param string $paymentInstrumentNickName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setPaymentInstrumentNickName($paymentInstrumentNickName)
    {
        $this->paymentInstrumentNickName = $paymentInstrumentNickName;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameOnCard()
    {
        return $this->nameOnCard;
    }

    /**
     * @param string $nameOnCard
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setNameOnCard($nameOnCard)
    {
        $this->nameOnCard = $nameOnCard;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCardGroup($cardGroup)
    {
        $this->cardGroup = $cardGroup;

        return $this;
    }

    /**
     * @return VCCardArt
     */
    public function getCardArt()
    {
        return $this->cardArt;
    }

    /**
     * @param VCCardArt $cardArt
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCardArt($cardArt)
    {
        $this->cardArt = $cardArt;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskAdvice()
    {
        return $this->riskAdvice;
    }

    /**
     * @param string $riskAdvice
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setRiskAdvice($riskAdvice)
    {
        $this->riskAdvice = $riskAdvice;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskScore()
    {
        return $this->riskScore;
    }

    /**
     * @param string $riskScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setRiskScore($riskScore)
    {
        $this->riskScore = $riskScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getRiskAdditionalData()
    {
        return $this->riskAdditionalData;
    }

    /**
     * @param string $riskAdditionalData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setRiskAdditionalData($riskAdditionalData)
    {
        $this->riskAdditionalData = $riskAdditionalData;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setAvsCodeRaw($avsCodeRaw)
    {
        $this->avsCodeRaw = $avsCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvnCodeRaw()
    {
        return $this->cvnCodeRaw;
    }

    /**
     * @param string $cvnCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCvnCodeRaw($cvnCodeRaw)
    {
        $this->cvnCodeRaw = $cvnCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getEciRaw()
    {
        return $this->eciRaw;
    }

    /**
     * @param string $eciRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setEciRaw($eciRaw)
    {
        $this->eciRaw = $eciRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getEci()
    {
        return $this->eci;
    }

    /**
     * @param string $eci
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setEci($eci)
    {
        $this->eci = $eci;

        return $this;
    }

    /**
     * @return string
     */
    public function getCavv()
    {
        return $this->cavv;
    }

    /**
     * @param string $cavv
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCavv($cavv)
    {
        $this->cavv = $cavv;

        return $this;
    }

    /**
     * @return string
     */
    public function getVeresEnrolled()
    {
        return $this->veresEnrolled;
    }

    /**
     * @param string $veresEnrolled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVeresEnrolled($veresEnrolled)
    {
        $this->veresEnrolled = $veresEnrolled;

        return $this;
    }

    /**
     * @return string
     */
    public function getVeresTimeStamp()
    {
        return $this->veresTimeStamp;
    }

    /**
     * @param string $veresTimeStamp
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVeresTimeStamp($veresTimeStamp)
    {
        $this->veresTimeStamp = $veresTimeStamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getParesStatus()
    {
        return $this->paresStatus;
    }

    /**
     * @param string $paresStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setParesStatus($paresStatus)
    {
        $this->paresStatus = $paresStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getParesTimeStamp()
    {
        return $this->paresTimeStamp;
    }

    /**
     * @param string $paresTimeStamp
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setParesTimeStamp($paresTimeStamp)
    {
        $this->paresTimeStamp = $paresTimeStamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getXid()
    {
        return $this->xid;
    }

    /**
     * @param string $xid
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setXid($xid)
    {
        $this->xid = $xid;

        return $this;
    }

    /**
     * @return VCCustomData
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * @param VCCustomData $customData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCustomData($customData)
    {
        $this->customData = $customData;

        return $this;
    }

    /**
     * @return string
     */
    public function getVcAccountFullName()
    {
        return $this->vcAccountFullName;
    }

    /**
     * @param string $vcAccountFullName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setVcAccountFullName($vcAccountFullName)
    {
        $this->vcAccountFullName = $vcAccountFullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDescription()
    {
        return $this->paymentDescription;
    }

    /**
     * @param string $paymentDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setPaymentDescription($paymentDescription)
    {
        $this->paymentDescription = $paymentDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAddressStreetName()
    {
        return $this->billingAddressStreetName;
    }

    /**
     * @param string $billingAddressStreetName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setBillingAddressStreetName($billingAddressStreetName)
    {
        $this->billingAddressStreetName = $billingAddressStreetName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAddressAdditionalLocation()
    {
        return $this->billingAddressAdditionalLocation;
    }

    /**
     * @param string $billingAddressAdditionalLocation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setBillingAddressAdditionalLocation($billingAddressAdditionalLocation)
    {
        $this->billingAddressAdditionalLocation = $billingAddressAdditionalLocation;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAddressStreetNumber()
    {
        return $this->billingAddressStreetNumber;
    }

    /**
     * @param string $billingAddressStreetNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setBillingAddressStreetNumber($billingAddressStreetNumber)
    {
        $this->billingAddressStreetNumber = $billingAddressStreetNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpiredCard()
    {
        return $this->expiredCard;
    }

    /**
     * @param string $expiredCard
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setExpiredCard($expiredCard)
    {
        $this->expiredCard = $expiredCard;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardFirstName()
    {
        return $this->cardFirstName;
    }

    /**
     * @param string $cardFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCardFirstName($cardFirstName)
    {
        $this->cardFirstName = $cardFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardLastName()
    {
        return $this->cardLastName;
    }

    /**
     * @param string $cardLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setCardLastName($cardLastName)
    {
        $this->cardLastName = $cardLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAddressStreetName()
    {
        return $this->shippingAddressStreetName;
    }

    /**
     * @param string $shippingAddressStreetName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setShippingAddressStreetName($shippingAddressStreetName)
    {
        $this->shippingAddressStreetName = $shippingAddressStreetName;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAddressAdditionalLocation()
    {
        return $this->shippingAddressAdditionalLocation;
    }

    /**
     * @param string $shippingAddressAdditionalLocation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setShippingAddressAdditionalLocation($shippingAddressAdditionalLocation)
    {
        $this->shippingAddressAdditionalLocation = $shippingAddressAdditionalLocation;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingAddressStreetNumber()
    {
        return $this->shippingAddressStreetNumber;
    }

    /**
     * @param string $shippingAddressStreetNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setShippingAddressStreetNumber($shippingAddressStreetNumber)
    {
        $this->shippingAddressStreetNumber = $shippingAddressStreetNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAgeOfAccount()
    {
        return $this->ageOfAccount;
    }

    /**
     * @param string $ageOfAccount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setAgeOfAccount($ageOfAccount)
    {
        $this->ageOfAccount = $ageOfAccount;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewUser()
    {
        return $this->newUser;
    }

    /**
     * @param string $newUser
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCReply
     */
    public function setNewUser($newUser)
    {
        $this->newUser = $newUser;

        return $this;
    }
}
