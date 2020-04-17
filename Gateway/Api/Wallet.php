<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Wallet
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $orderID
     */
    protected $orderID;

    /**
     * @var string $merchantReferenceID
     */
    protected $merchantReferenceID;

    /**
     * @var string $userPhone
     */
    protected $userPhone;

    /**
     * @var string $avv
     */
    protected $avv;

    /**
     * @var string $eciRaw
     */
    protected $eciRaw;

    /**
     * @var string $authenticatonMethod
     */
    protected $authenticatonMethod;

    /**
     * @var string $cardEnrollmentMethod
     */
    protected $cardEnrollmentMethod;

    /**
     * @var string $paresStatus
     */
    protected $paresStatus;

    /**
     * @var string $veresEnrolled
     */
    protected $veresEnrolled;

    /**
     * @var string $xid
     */
    protected $xid;

    /**
     * @var string $totalPurchaseAmount
     */
    protected $totalPurchaseAmount;

    /**
     * @var string $subtotalAmount
     */
    protected $subtotalAmount;

    /**
     * @var string $discountAmount
     */
    protected $discountAmount;

    /**
     * @var string $giftWrapAmount
     */
    protected $giftWrapAmount;

    /**
     * @var string $eventType
     */
    protected $eventType;

    /**
     * @var string $promotionCode
     */
    protected $promotionCode;

    /**
     * @var string $enrollmentID
     */
    protected $enrollmentID;

    /**
     * @var string $staySignedInIndicator
     */
    protected $staySignedInIndicator;

    /**
     * @var string $authenticationData
     */
    protected $authenticationData;

    /**
     * @var string $deviceID
     */
    protected $deviceID;

    /**
     * @var string $httpResponse
     */
    protected $httpResponse;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

    /**
     * @var string $errorDescription
     */
    protected $errorDescription;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }

    /**
     * @param string $orderID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setMerchantReferenceID($merchantReferenceID)
    {
        $this->merchantReferenceID = $merchantReferenceID;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserPhone()
    {
        return $this->userPhone;
    }

    /**
     * @param string $userPhone
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setUserPhone($userPhone)
    {
        $this->userPhone = $userPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvv()
    {
        return $this->avv;
    }

    /**
     * @param string $avv
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setAvv($avv)
    {
        $this->avv = $avv;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setEciRaw($eciRaw)
    {
        $this->eciRaw = $eciRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticatonMethod()
    {
        return $this->authenticatonMethod;
    }

    /**
     * @param string $authenticatonMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setAuthenticatonMethod($authenticatonMethod)
    {
        $this->authenticatonMethod = $authenticatonMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardEnrollmentMethod()
    {
        return $this->cardEnrollmentMethod;
    }

    /**
     * @param string $cardEnrollmentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setCardEnrollmentMethod($cardEnrollmentMethod)
    {
        $this->cardEnrollmentMethod = $cardEnrollmentMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setParesStatus($paresStatus)
    {
        $this->paresStatus = $paresStatus;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setVeresEnrolled($veresEnrolled)
    {
        $this->veresEnrolled = $veresEnrolled;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setXid($xid)
    {
        $this->xid = $xid;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setTotalPurchaseAmount($totalPurchaseAmount)
    {
        $this->totalPurchaseAmount = $totalPurchaseAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setSubtotalAmount($subtotalAmount)
    {
        $this->subtotalAmount = $subtotalAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setGiftWrapAmount($giftWrapAmount)
    {
        $this->giftWrapAmount = $giftWrapAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setPromotionCode($promotionCode)
    {
        $this->promotionCode = $promotionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnrollmentID()
    {
        return $this->enrollmentID;
    }

    /**
     * @param string $enrollmentID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setEnrollmentID($enrollmentID)
    {
        $this->enrollmentID = $enrollmentID;

        return $this;
    }

    /**
     * @return string
     */
    public function getStaySignedInIndicator()
    {
        return $this->staySignedInIndicator;
    }

    /**
     * @param string $staySignedInIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setStaySignedInIndicator($staySignedInIndicator)
    {
        $this->staySignedInIndicator = $staySignedInIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationData()
    {
        return $this->authenticationData;
    }

    /**
     * @param string $authenticationData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setAuthenticationData($authenticationData)
    {
        $this->authenticationData = $authenticationData;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceID()
    {
        return $this->deviceID;
    }

    /**
     * @param string $deviceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setDeviceID($deviceID)
    {
        $this->deviceID = $deviceID;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @param string $httpResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setHttpResponse($httpResponse)
    {
        $this->httpResponse = $httpResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * @param string $errorDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Wallet
     */
    public function setErrorDescription($errorDescription)
    {
        $this->errorDescription = $errorDescription;

        return $this;
    }
}
