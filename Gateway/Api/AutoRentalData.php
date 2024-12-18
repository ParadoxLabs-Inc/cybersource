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
     * @var boolean $noShowIndicator
     */
    protected $noShowIndicator;

    /**
     * @var string $timePeriod
     */
    protected $timePeriod;

    /**
     * @var float $weeklyRentalRate
     */
    protected $weeklyRentalRate;

    /**
     * @var string $distanceUnit
     */
    protected $distanceUnit;

    /**
     * @var string $rentalLocationID
     */
    protected $rentalLocationID;

    /**
     * @var boolean $vehicleInsuranceIndicator
     */
    protected $vehicleInsuranceIndicator;

    /**
     * @var string $programCode
     */
    protected $programCode;

    /**
     * @var float $otherCharges
     */
    protected $otherCharges;

    /**
     * @var float $taxRate
     */
    protected $taxRate;

    /**
     * @var boolean $taxIndicator
     */
    protected $taxIndicator;

    /**
     * @var string $taxStatusIndicator
     */
    protected $taxStatusIndicator;

    /**
     * @var float $taxAmount
     */
    protected $taxAmount;

    /**
     * @var string $taxType
     */
    protected $taxType;

    /**
     * @var string $taxSummary
     */
    protected $taxSummary;

    /**
     * @var string $returnLocation
     */
    protected $returnLocation;

    /**
     * @var int $odometerReading
     */
    protected $odometerReading;

    /**
     * @var string $vehicleIdentificationNumber
     */
    protected $vehicleIdentificationNumber;

    /**
     * @var string $commodityCode
     */
    protected $commodityCode;

    /**
     * @var string $companyId
     */
    protected $companyId;

    /**
     * @var float $regularMileageCost
     */
    protected $regularMileageCost;

    /**
     * @var float $towingCharge
     */
    protected $towingCharge;

    /**
     * @var float $extraCharge
     */
    protected $extraCharge;

    /**
     * @var int $additionalDrivers
     */
    protected $additionalDrivers;

    /**
     * @var string $rentalAddress
     */
    protected $rentalAddress;

    /**
     * @var int $driverAge
     */
    protected $driverAge;

    /**
     * @var string $vehicleMake
     */
    protected $vehicleMake;

    /**
     * @var string $vehicleModel
     */
    protected $vehicleModel;

    /**
     * @var string $corporateClientCode
     */
    protected $corporateClientCode;

    /**
     * @var float $phoneCharge
     */
    protected $phoneCharge;

    /**
     * @var float $gpsCharge
     */
    protected $gpsCharge;

    /**
     * @var string $pickupLocation
     */
    protected $pickupLocation;

    /**
     * @var string $taxAmountSign
     */
    protected $taxAmountSign;

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

    /**
     * @return boolean
     */
    public function getNoShowIndicator()
    {
        return $this->noShowIndicator;
    }

    /**
     * @param boolean $noShowIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setNoShowIndicator($noShowIndicator)
    {
        $this->noShowIndicator = $noShowIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimePeriod()
    {
        return $this->timePeriod;
    }

    /**
     * @param string $timePeriod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTimePeriod($timePeriod)
    {
        $this->timePeriod = $timePeriod;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeeklyRentalRate()
    {
        return $this->weeklyRentalRate;
    }

    /**
     * @param float $weeklyRentalRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setWeeklyRentalRate($weeklyRentalRate)
    {
        $this->weeklyRentalRate = $weeklyRentalRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getDistanceUnit()
    {
        return $this->distanceUnit;
    }

    /**
     * @param string $distanceUnit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setDistanceUnit($distanceUnit)
    {
        $this->distanceUnit = $distanceUnit;

        return $this;
    }

    /**
     * @return string
     */
    public function getRentalLocationID()
    {
        return $this->rentalLocationID;
    }

    /**
     * @param string $rentalLocationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setRentalLocationID($rentalLocationID)
    {
        $this->rentalLocationID = $rentalLocationID;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getVehicleInsuranceIndicator()
    {
        return $this->vehicleInsuranceIndicator;
    }

    /**
     * @param boolean $vehicleInsuranceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setVehicleInsuranceIndicator($vehicleInsuranceIndicator)
    {
        $this->vehicleInsuranceIndicator = $vehicleInsuranceIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getProgramCode()
    {
        return $this->programCode;
    }

    /**
     * @param string $programCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setProgramCode($programCode)
    {
        $this->programCode = $programCode;

        return $this;
    }

    /**
     * @return float
     */
    public function getOtherCharges()
    {
        return $this->otherCharges;
    }

    /**
     * @param float $otherCharges
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setOtherCharges($otherCharges)
    {
        $this->otherCharges = $otherCharges;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getTaxIndicator()
    {
        return $this->taxIndicator;
    }

    /**
     * @param boolean $taxIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTaxIndicator($taxIndicator)
    {
        $this->taxIndicator = $taxIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTaxStatusIndicator($taxStatusIndicator)
    {
        $this->taxStatusIndicator = $taxStatusIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxType()
    {
        return $this->taxType;
    }

    /**
     * @param string $taxType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTaxType($taxType)
    {
        $this->taxType = $taxType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxSummary()
    {
        return $this->taxSummary;
    }

    /**
     * @param string $taxSummary
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTaxSummary($taxSummary)
    {
        $this->taxSummary = $taxSummary;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnLocation()
    {
        return $this->returnLocation;
    }

    /**
     * @param string $returnLocation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setReturnLocation($returnLocation)
    {
        $this->returnLocation = $returnLocation;

        return $this;
    }

    /**
     * @return int
     */
    public function getOdometerReading()
    {
        return $this->odometerReading;
    }

    /**
     * @param int $odometerReading
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setOdometerReading($odometerReading)
    {
        $this->odometerReading = $odometerReading;

        return $this;
    }

    /**
     * @return string
     */
    public function getVehicleIdentificationNumber()
    {
        return $this->vehicleIdentificationNumber;
    }

    /**
     * @param string $vehicleIdentificationNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setVehicleIdentificationNumber($vehicleIdentificationNumber)
    {
        $this->vehicleIdentificationNumber = $vehicleIdentificationNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setCommodityCode($commodityCode)
    {
        $this->commodityCode = $commodityCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * @return float
     */
    public function getRegularMileageCost()
    {
        return $this->regularMileageCost;
    }

    /**
     * @param float $regularMileageCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setRegularMileageCost($regularMileageCost)
    {
        $this->regularMileageCost = $regularMileageCost;

        return $this;
    }

    /**
     * @return float
     */
    public function getTowingCharge()
    {
        return $this->towingCharge;
    }

    /**
     * @param float $towingCharge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTowingCharge($towingCharge)
    {
        $this->towingCharge = $towingCharge;

        return $this;
    }

    /**
     * @return float
     */
    public function getExtraCharge()
    {
        return $this->extraCharge;
    }

    /**
     * @param float $extraCharge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setExtraCharge($extraCharge)
    {
        $this->extraCharge = $extraCharge;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdditionalDrivers()
    {
        return $this->additionalDrivers;
    }

    /**
     * @param int $additionalDrivers
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setAdditionalDrivers($additionalDrivers)
    {
        $this->additionalDrivers = $additionalDrivers;

        return $this;
    }

    /**
     * @return string
     */
    public function getRentalAddress()
    {
        return $this->rentalAddress;
    }

    /**
     * @param string $rentalAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setRentalAddress($rentalAddress)
    {
        $this->rentalAddress = $rentalAddress;

        return $this;
    }

    /**
     * @return int
     */
    public function getDriverAge()
    {
        return $this->driverAge;
    }

    /**
     * @param int $driverAge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setDriverAge($driverAge)
    {
        $this->driverAge = $driverAge;

        return $this;
    }

    /**
     * @return string
     */
    public function getVehicleMake()
    {
        return $this->vehicleMake;
    }

    /**
     * @param string $vehicleMake
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setVehicleMake($vehicleMake)
    {
        $this->vehicleMake = $vehicleMake;

        return $this;
    }

    /**
     * @return string
     */
    public function getVehicleModel()
    {
        return $this->vehicleModel;
    }

    /**
     * @param string $vehicleModel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setVehicleModel($vehicleModel)
    {
        $this->vehicleModel = $vehicleModel;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setCorporateClientCode($corporateClientCode)
    {
        $this->corporateClientCode = $corporateClientCode;

        return $this;
    }

    /**
     * @return float
     */
    public function getPhoneCharge()
    {
        return $this->phoneCharge;
    }

    /**
     * @param float $phoneCharge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setPhoneCharge($phoneCharge)
    {
        $this->phoneCharge = $phoneCharge;

        return $this;
    }

    /**
     * @return float
     */
    public function getGpsCharge()
    {
        return $this->gpsCharge;
    }

    /**
     * @param float $gpsCharge
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setGpsCharge($gpsCharge)
    {
        $this->gpsCharge = $gpsCharge;

        return $this;
    }

    /**
     * @return string
     */
    public function getPickupLocation()
    {
        return $this->pickupLocation;
    }

    /**
     * @param string $pickupLocation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setPickupLocation($pickupLocation)
    {
        $this->pickupLocation = $pickupLocation;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxAmountSign()
    {
        return $this->taxAmountSign;
    }

    /**
     * @param string $taxAmountSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData
     */
    public function setTaxAmountSign($taxAmountSign)
    {
        $this->taxAmountSign = $taxAmountSign;

        return $this;
    }
}
