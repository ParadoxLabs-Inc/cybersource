<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AutoRentalData
{
    /**
     * @var float $adjustmentCost
     */
    protected $adjustmentCost;

    /**
     * @var string $adjustmentCode
     */
    protected $adjustmentCode;

    /**
     * @var string $agreementNumber
     */
    protected $agreementNumber;

    /**
     * @var string $classCode
     */
    protected $classCode;

    /**
     * @var string $customerServicePhoneNumber
     */
    protected $customerServicePhoneNumber;

    /**
     * @var float $dailyRate
     */
    protected $dailyRate;

    /**
     * @var float $mileageCost
     */
    protected $mileageCost;

    /**
     * @var float $gasCost
     */
    protected $gasCost;

    /**
     * @var float $insuranceCost
     */
    protected $insuranceCost;

    /**
     * @var float $lateReturnCost
     */
    protected $lateReturnCost;

    /**
     * @var int $maximumFreeMiles
     */
    protected $maximumFreeMiles;

    /**
     * @var int $milesTraveled
     */
    protected $milesTraveled;

    /**
     * @var float $oneWayCost
     */
    protected $oneWayCost;

    /**
     * @var float $parkingViolationCost
     */
    protected $parkingViolationCost;

    /**
     * @var string $pickUpCity
     */
    protected $pickUpCity;

    /**
     * @var string $pickUpCountry
     */
    protected $pickUpCountry;

    /**
     * @var string $pickUpDate
     */
    protected $pickUpDate;

    /**
     * @var string $pickUpState
     */
    protected $pickUpState;

    /**
     * @var int $pickUpTime
     */
    protected $pickUpTime;

    /**
     * @var float $ratePerMile
     */
    protected $ratePerMile;

    /**
     * @var string $renterName
     */
    protected $renterName;

    /**
     * @var string $returnCity
     */
    protected $returnCity;

    /**
     * @var string $returnCountry
     */
    protected $returnCountry;

    /**
     * @var string $returnDate
     */
    protected $returnDate;

    /**
     * @var string $returnLocationID
     */
    protected $returnLocationID;

    /**
     * @var string $returnState
     */
    protected $returnState;

    /**
     * @var int $returnTime
     */
    protected $returnTime;

    /**
     * @var string $specialProgramCode
     */
    protected $specialProgramCode;

    /**
     * @return float
     */
    public function getAdjustmentCost()
    {
        return $this->adjustmentCost;
    }

    /**
     * @param float $adjustmentCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setAdjustmentCost($adjustmentCost)
    {
        $this->adjustmentCost = $adjustmentCost;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdjustmentCode()
    {
        return $this->adjustmentCode;
    }

    /**
     * @param string $adjustmentCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setAdjustmentCode($adjustmentCode)
    {
        $this->adjustmentCode = $adjustmentCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAgreementNumber()
    {
        return $this->agreementNumber;
    }

    /**
     * @param string $agreementNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setAgreementNumber($agreementNumber)
    {
        $this->agreementNumber = $agreementNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassCode()
    {
        return $this->classCode;
    }

    /**
     * @param string $classCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setClassCode($classCode)
    {
        $this->classCode = $classCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setCustomerServicePhoneNumber($customerServicePhoneNumber)
    {
        $this->customerServicePhoneNumber = $customerServicePhoneNumber;

        return $this;
    }

    /**
     * @return float
     */
    public function getDailyRate()
    {
        return $this->dailyRate;
    }

    /**
     * @param float $dailyRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setDailyRate($dailyRate)
    {
        $this->dailyRate = $dailyRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getMileageCost()
    {
        return $this->mileageCost;
    }

    /**
     * @param float $mileageCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setMileageCost($mileageCost)
    {
        $this->mileageCost = $mileageCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getGasCost()
    {
        return $this->gasCost;
    }

    /**
     * @param float $gasCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setGasCost($gasCost)
    {
        $this->gasCost = $gasCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getInsuranceCost()
    {
        return $this->insuranceCost;
    }

    /**
     * @param float $insuranceCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setInsuranceCost($insuranceCost)
    {
        $this->insuranceCost = $insuranceCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getLateReturnCost()
    {
        return $this->lateReturnCost;
    }

    /**
     * @param float $lateReturnCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setLateReturnCost($lateReturnCost)
    {
        $this->lateReturnCost = $lateReturnCost;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaximumFreeMiles()
    {
        return $this->maximumFreeMiles;
    }

    /**
     * @param int $maximumFreeMiles
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setMaximumFreeMiles($maximumFreeMiles)
    {
        $this->maximumFreeMiles = $maximumFreeMiles;

        return $this;
    }

    /**
     * @return int
     */
    public function getMilesTraveled()
    {
        return $this->milesTraveled;
    }

    /**
     * @param int $milesTraveled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setMilesTraveled($milesTraveled)
    {
        $this->milesTraveled = $milesTraveled;

        return $this;
    }

    /**
     * @return float
     */
    public function getOneWayCost()
    {
        return $this->oneWayCost;
    }

    /**
     * @param float $oneWayCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setOneWayCost($oneWayCost)
    {
        $this->oneWayCost = $oneWayCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getParkingViolationCost()
    {
        return $this->parkingViolationCost;
    }

    /**
     * @param float $parkingViolationCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setParkingViolationCost($parkingViolationCost)
    {
        $this->parkingViolationCost = $parkingViolationCost;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpCity()
    {
        return $this->pickUpCity;
    }

    /**
     * @param string $pickUpCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setPickUpCity($pickUpCity)
    {
        $this->pickUpCity = $pickUpCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpCountry()
    {
        return $this->pickUpCountry;
    }

    /**
     * @param string $pickUpCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setPickUpCountry($pickUpCountry)
    {
        $this->pickUpCountry = $pickUpCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpDate()
    {
        return $this->pickUpDate;
    }

    /**
     * @param string $pickUpDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setPickUpDate($pickUpDate)
    {
        $this->pickUpDate = $pickUpDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickUpState()
    {
        return $this->pickUpState;
    }

    /**
     * @param string $pickUpState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setPickUpState($pickUpState)
    {
        $this->pickUpState = $pickUpState;

        return $this;
    }

    /**
     * @return int
     */
    public function getPickUpTime()
    {
        return $this->pickUpTime;
    }

    /**
     * @param int $pickUpTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setPickUpTime($pickUpTime)
    {
        $this->pickUpTime = $pickUpTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getRatePerMile()
    {
        return $this->ratePerMile;
    }

    /**
     * @param float $ratePerMile
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setRatePerMile($ratePerMile)
    {
        $this->ratePerMile = $ratePerMile;

        return $this;
    }

    /**
     * @return string
     */
    public function getRenterName()
    {
        return $this->renterName;
    }

    /**
     * @param string $renterName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setRenterName($renterName)
    {
        $this->renterName = $renterName;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnCity()
    {
        return $this->returnCity;
    }

    /**
     * @param string $returnCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setReturnCity($returnCity)
    {
        $this->returnCity = $returnCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnCountry()
    {
        return $this->returnCountry;
    }

    /**
     * @param string $returnCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setReturnCountry($returnCountry)
    {
        $this->returnCountry = $returnCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @param string $returnDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setReturnDate($returnDate)
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnLocationID()
    {
        return $this->returnLocationID;
    }

    /**
     * @param string $returnLocationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setReturnLocationID($returnLocationID)
    {
        $this->returnLocationID = $returnLocationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnState()
    {
        return $this->returnState;
    }

    /**
     * @param string $returnState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setReturnState($returnState)
    {
        $this->returnState = $returnState;

        return $this;
    }

    /**
     * @return int
     */
    public function getReturnTime()
    {
        return $this->returnTime;
    }

    /**
     * @param int $returnTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setReturnTime($returnTime)
    {
        $this->returnTime = $returnTime;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setSpecialProgramCode($specialProgramCode)
    {
        $this->specialProgramCode = $specialProgramCode;

        return $this;
    }
}
