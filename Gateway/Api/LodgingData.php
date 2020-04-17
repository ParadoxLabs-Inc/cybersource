<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class LodgingData
{
    /**
     * @var string $checkInDate
     */
    protected $checkInDate;

    /**
     * @var string $checkOutDate
     */
    protected $checkOutDate;

    /**
     * @var float $dailyRoomRate1
     */
    protected $dailyRoomRate1;

    /**
     * @var float $dailyRoomRate2
     */
    protected $dailyRoomRate2;

    /**
     * @var float $dailyRoomRate3
     */
    protected $dailyRoomRate3;

    /**
     * @var int $roomNights1
     */
    protected $roomNights1;

    /**
     * @var int $roomNights2
     */
    protected $roomNights2;

    /**
     * @var int $roomNights3
     */
    protected $roomNights3;

    /**
     * @var string $guestSmokingPreference
     */
    protected $guestSmokingPreference;

    /**
     * @var int $numberOfRoomsBooked
     */
    protected $numberOfRoomsBooked;

    /**
     * @var int $numberOfGuests
     */
    protected $numberOfGuests;

    /**
     * @var string $roomBedType
     */
    protected $roomBedType;

    /**
     * @var string $roomTaxElements
     */
    protected $roomTaxElements;

    /**
     * @var string $roomRateType
     */
    protected $roomRateType;

    /**
     * @var string $guestName
     */
    protected $guestName;

    /**
     * @var string $customerServicePhoneNumber
     */
    protected $customerServicePhoneNumber;

    /**
     * @var string $corporateClientCode
     */
    protected $corporateClientCode;

    /**
     * @var string $promotionalCode
     */
    protected $promotionalCode;

    /**
     * @var string $additionalCoupon
     */
    protected $additionalCoupon;

    /**
     * @var string $roomLocation
     */
    protected $roomLocation;

    /**
     * @var string $specialProgramCode
     */
    protected $specialProgramCode;

    /**
     * @var float $tax
     */
    protected $tax;

    /**
     * @var float $prepaidCost
     */
    protected $prepaidCost;

    /**
     * @var float $foodAndBeverageCost
     */
    protected $foodAndBeverageCost;

    /**
     * @var float $roomTax
     */
    protected $roomTax;

    /**
     * @var float $adjustmentAmount
     */
    protected $adjustmentAmount;

    /**
     * @var float $phoneCost
     */
    protected $phoneCost;

    /**
     * @var float $restaurantCost
     */
    protected $restaurantCost;

    /**
     * @var float $roomServiceCost
     */
    protected $roomServiceCost;

    /**
     * @var float $miniBarCost
     */
    protected $miniBarCost;

    /**
     * @var float $laundryCost
     */
    protected $laundryCost;

    /**
     * @var float $miscellaneousCost
     */
    protected $miscellaneousCost;

    /**
     * @var float $giftShopCost
     */
    protected $giftShopCost;

    /**
     * @var float $movieCost
     */
    protected $movieCost;

    /**
     * @var float $healthClubCost
     */
    protected $healthClubCost;

    /**
     * @var float $valetParkingCost
     */
    protected $valetParkingCost;

    /**
     * @var float $cashDisbursementCost
     */
    protected $cashDisbursementCost;

    /**
     * @var float $nonRoomCost
     */
    protected $nonRoomCost;

    /**
     * @var float $businessCenterCost
     */
    protected $businessCenterCost;

    /**
     * @var float $loungeBarCost
     */
    protected $loungeBarCost;

    /**
     * @var float $transportationCost
     */
    protected $transportationCost;

    /**
     * @var float $gratuityCost
     */
    protected $gratuityCost;

    /**
     * @var float $conferenceRoomCost
     */
    protected $conferenceRoomCost;

    /**
     * @var float $audioVisualCost
     */
    protected $audioVisualCost;

    /**
     * @var float $banquetCost
     */
    protected $banquetCost;

    /**
     * @var float $internetAccessCost
     */
    protected $internetAccessCost;

    /**
     * @var float $earlyCheckOutCost
     */
    protected $earlyCheckOutCost;

    /**
     * @var float $nonRoomTax
     */
    protected $nonRoomTax;

    /**
     * @var string $travelAgencyCode
     */
    protected $travelAgencyCode;

    /**
     * @var string $travelAgencyName
     */
    protected $travelAgencyName;

    /**
     * @return string
     */
    public function getCheckInDate()
    {
        return $this->checkInDate;
    }

    /**
     * @param string $checkInDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setCheckInDate($checkInDate)
    {
        $this->checkInDate = $checkInDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckOutDate()
    {
        return $this->checkOutDate;
    }

    /**
     * @param string $checkOutDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setCheckOutDate($checkOutDate)
    {
        $this->checkOutDate = $checkOutDate;

        return $this;
    }

    /**
     * @return float
     */
    public function getDailyRoomRate1()
    {
        return $this->dailyRoomRate1;
    }

    /**
     * @param float $dailyRoomRate1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setDailyRoomRate1($dailyRoomRate1)
    {
        $this->dailyRoomRate1 = $dailyRoomRate1;

        return $this;
    }

    /**
     * @return float
     */
    public function getDailyRoomRate2()
    {
        return $this->dailyRoomRate2;
    }

    /**
     * @param float $dailyRoomRate2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setDailyRoomRate2($dailyRoomRate2)
    {
        $this->dailyRoomRate2 = $dailyRoomRate2;

        return $this;
    }

    /**
     * @return float
     */
    public function getDailyRoomRate3()
    {
        return $this->dailyRoomRate3;
    }

    /**
     * @param float $dailyRoomRate3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setDailyRoomRate3($dailyRoomRate3)
    {
        $this->dailyRoomRate3 = $dailyRoomRate3;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoomNights1()
    {
        return $this->roomNights1;
    }

    /**
     * @param int $roomNights1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomNights1($roomNights1)
    {
        $this->roomNights1 = $roomNights1;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoomNights2()
    {
        return $this->roomNights2;
    }

    /**
     * @param int $roomNights2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomNights2($roomNights2)
    {
        $this->roomNights2 = $roomNights2;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoomNights3()
    {
        return $this->roomNights3;
    }

    /**
     * @param int $roomNights3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomNights3($roomNights3)
    {
        $this->roomNights3 = $roomNights3;

        return $this;
    }

    /**
     * @return string
     */
    public function getGuestSmokingPreference()
    {
        return $this->guestSmokingPreference;
    }

    /**
     * @param string $guestSmokingPreference
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setGuestSmokingPreference($guestSmokingPreference)
    {
        $this->guestSmokingPreference = $guestSmokingPreference;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfRoomsBooked()
    {
        return $this->numberOfRoomsBooked;
    }

    /**
     * @param int $numberOfRoomsBooked
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setNumberOfRoomsBooked($numberOfRoomsBooked)
    {
        $this->numberOfRoomsBooked = $numberOfRoomsBooked;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfGuests()
    {
        return $this->numberOfGuests;
    }

    /**
     * @param int $numberOfGuests
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setNumberOfGuests($numberOfGuests)
    {
        $this->numberOfGuests = $numberOfGuests;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoomBedType()
    {
        return $this->roomBedType;
    }

    /**
     * @param string $roomBedType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomBedType($roomBedType)
    {
        $this->roomBedType = $roomBedType;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoomTaxElements()
    {
        return $this->roomTaxElements;
    }

    /**
     * @param string $roomTaxElements
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomTaxElements($roomTaxElements)
    {
        $this->roomTaxElements = $roomTaxElements;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoomRateType()
    {
        return $this->roomRateType;
    }

    /**
     * @param string $roomRateType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomRateType($roomRateType)
    {
        $this->roomRateType = $roomRateType;

        return $this;
    }

    /**
     * @return string
     */
    public function getGuestName()
    {
        return $this->guestName;
    }

    /**
     * @param string $guestName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setGuestName($guestName)
    {
        $this->guestName = $guestName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerServicePhoneNumber()
    {
        return $this->customerServicePhoneNumber;
    }

    /**
     * @param string $customerServicePhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setCustomerServicePhoneNumber($customerServicePhoneNumber)
    {
        $this->customerServicePhoneNumber = $customerServicePhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorporateClientCode()
    {
        return $this->corporateClientCode;
    }

    /**
     * @param string $corporateClientCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setCorporateClientCode($corporateClientCode)
    {
        $this->corporateClientCode = $corporateClientCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPromotionalCode()
    {
        return $this->promotionalCode;
    }

    /**
     * @param string $promotionalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setPromotionalCode($promotionalCode)
    {
        $this->promotionalCode = $promotionalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalCoupon()
    {
        return $this->additionalCoupon;
    }

    /**
     * @param string $additionalCoupon
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setAdditionalCoupon($additionalCoupon)
    {
        $this->additionalCoupon = $additionalCoupon;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoomLocation()
    {
        return $this->roomLocation;
    }

    /**
     * @param string $roomLocation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomLocation($roomLocation)
    {
        $this->roomLocation = $roomLocation;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpecialProgramCode()
    {
        return $this->specialProgramCode;
    }

    /**
     * @param string $specialProgramCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setSpecialProgramCode($specialProgramCode)
    {
        $this->specialProgramCode = $specialProgramCode;

        return $this;
    }

    /**
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrepaidCost()
    {
        return $this->prepaidCost;
    }

    /**
     * @param float $prepaidCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setPrepaidCost($prepaidCost)
    {
        $this->prepaidCost = $prepaidCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getFoodAndBeverageCost()
    {
        return $this->foodAndBeverageCost;
    }

    /**
     * @param float $foodAndBeverageCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setFoodAndBeverageCost($foodAndBeverageCost)
    {
        $this->foodAndBeverageCost = $foodAndBeverageCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getRoomTax()
    {
        return $this->roomTax;
    }

    /**
     * @param float $roomTax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomTax($roomTax)
    {
        $this->roomTax = $roomTax;

        return $this;
    }

    /**
     * @return float
     */
    public function getAdjustmentAmount()
    {
        return $this->adjustmentAmount;
    }

    /**
     * @param float $adjustmentAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setAdjustmentAmount($adjustmentAmount)
    {
        $this->adjustmentAmount = $adjustmentAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getPhoneCost()
    {
        return $this->phoneCost;
    }

    /**
     * @param float $phoneCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setPhoneCost($phoneCost)
    {
        $this->phoneCost = $phoneCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getRestaurantCost()
    {
        return $this->restaurantCost;
    }

    /**
     * @param float $restaurantCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRestaurantCost($restaurantCost)
    {
        $this->restaurantCost = $restaurantCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getRoomServiceCost()
    {
        return $this->roomServiceCost;
    }

    /**
     * @param float $roomServiceCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setRoomServiceCost($roomServiceCost)
    {
        $this->roomServiceCost = $roomServiceCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getMiniBarCost()
    {
        return $this->miniBarCost;
    }

    /**
     * @param float $miniBarCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setMiniBarCost($miniBarCost)
    {
        $this->miniBarCost = $miniBarCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getLaundryCost()
    {
        return $this->laundryCost;
    }

    /**
     * @param float $laundryCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setLaundryCost($laundryCost)
    {
        $this->laundryCost = $laundryCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getMiscellaneousCost()
    {
        return $this->miscellaneousCost;
    }

    /**
     * @param float $miscellaneousCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setMiscellaneousCost($miscellaneousCost)
    {
        $this->miscellaneousCost = $miscellaneousCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getGiftShopCost()
    {
        return $this->giftShopCost;
    }

    /**
     * @param float $giftShopCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setGiftShopCost($giftShopCost)
    {
        $this->giftShopCost = $giftShopCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getMovieCost()
    {
        return $this->movieCost;
    }

    /**
     * @param float $movieCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setMovieCost($movieCost)
    {
        $this->movieCost = $movieCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getHealthClubCost()
    {
        return $this->healthClubCost;
    }

    /**
     * @param float $healthClubCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setHealthClubCost($healthClubCost)
    {
        $this->healthClubCost = $healthClubCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getValetParkingCost()
    {
        return $this->valetParkingCost;
    }

    /**
     * @param float $valetParkingCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setValetParkingCost($valetParkingCost)
    {
        $this->valetParkingCost = $valetParkingCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getCashDisbursementCost()
    {
        return $this->cashDisbursementCost;
    }

    /**
     * @param float $cashDisbursementCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setCashDisbursementCost($cashDisbursementCost)
    {
        $this->cashDisbursementCost = $cashDisbursementCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getNonRoomCost()
    {
        return $this->nonRoomCost;
    }

    /**
     * @param float $nonRoomCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setNonRoomCost($nonRoomCost)
    {
        $this->nonRoomCost = $nonRoomCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getBusinessCenterCost()
    {
        return $this->businessCenterCost;
    }

    /**
     * @param float $businessCenterCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setBusinessCenterCost($businessCenterCost)
    {
        $this->businessCenterCost = $businessCenterCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getLoungeBarCost()
    {
        return $this->loungeBarCost;
    }

    /**
     * @param float $loungeBarCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setLoungeBarCost($loungeBarCost)
    {
        $this->loungeBarCost = $loungeBarCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getTransportationCost()
    {
        return $this->transportationCost;
    }

    /**
     * @param float $transportationCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setTransportationCost($transportationCost)
    {
        $this->transportationCost = $transportationCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getGratuityCost()
    {
        return $this->gratuityCost;
    }

    /**
     * @param float $gratuityCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setGratuityCost($gratuityCost)
    {
        $this->gratuityCost = $gratuityCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getConferenceRoomCost()
    {
        return $this->conferenceRoomCost;
    }

    /**
     * @param float $conferenceRoomCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setConferenceRoomCost($conferenceRoomCost)
    {
        $this->conferenceRoomCost = $conferenceRoomCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getAudioVisualCost()
    {
        return $this->audioVisualCost;
    }

    /**
     * @param float $audioVisualCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setAudioVisualCost($audioVisualCost)
    {
        $this->audioVisualCost = $audioVisualCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getBanquetCost()
    {
        return $this->banquetCost;
    }

    /**
     * @param float $banquetCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setBanquetCost($banquetCost)
    {
        $this->banquetCost = $banquetCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getInternetAccessCost()
    {
        return $this->internetAccessCost;
    }

    /**
     * @param float $internetAccessCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setInternetAccessCost($internetAccessCost)
    {
        $this->internetAccessCost = $internetAccessCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getEarlyCheckOutCost()
    {
        return $this->earlyCheckOutCost;
    }

    /**
     * @param float $earlyCheckOutCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setEarlyCheckOutCost($earlyCheckOutCost)
    {
        $this->earlyCheckOutCost = $earlyCheckOutCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getNonRoomTax()
    {
        return $this->nonRoomTax;
    }

    /**
     * @param float $nonRoomTax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setNonRoomTax($nonRoomTax)
    {
        $this->nonRoomTax = $nonRoomTax;

        return $this;
    }

    /**
     * @return string
     */
    public function getTravelAgencyCode()
    {
        return $this->travelAgencyCode;
    }

    /**
     * @param string $travelAgencyCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setTravelAgencyCode($travelAgencyCode)
    {
        $this->travelAgencyCode = $travelAgencyCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getTravelAgencyName()
    {
        return $this->travelAgencyName;
    }

    /**
     * @param string $travelAgencyName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\LodgingData
     */
    public function setTravelAgencyName($travelAgencyName)
    {
        $this->travelAgencyName = $travelAgencyName;

        return $this;
    }
}
