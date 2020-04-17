<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ECAVSReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $validationType
     */
    protected $validationType;

    /**
     * @var string $primaryStatusCode
     */
    protected $primaryStatusCode;

    /**
     * @var string $secondaryStatusCode
     */
    protected $secondaryStatusCode;

    /**
     * @var string $additionalStatusCode
     */
    protected $additionalStatusCode;

    /**
     * @var string $numberOfReturns
     */
    protected $numberOfReturns;

    /**
     * @var string $lastReturnDate
     */
    protected $lastReturnDate;

    /**
     * @var string $lastReturnProcessorResponse
     */
    protected $lastReturnProcessorResponse;

    /**
     * @var string $lastUpdateDate
     */
    protected $lastUpdateDate;

    /**
     * @var string $addedOrClosedDate
     */
    protected $addedOrClosedDate;

    /**
     * @var string $previousStatusCode
     */
    protected $previousStatusCode;

    /**
     * @var string $fcraDisputeCode
     */
    protected $fcraDisputeCode;

    /**
     * @var string $scoredAccountProcessorResponse1
     */
    protected $scoredAccountProcessorResponse1;

    /**
     * @var string $scoredAccountProcessorResponse2
     */
    protected $scoredAccountProcessorResponse2;

    /**
     * @var string $scoredAccountProcessorResponse3
     */
    protected $scoredAccountProcessorResponse3;

    /**
     * @var string $scoredAccountProcessorResponse5
     */
    protected $scoredAccountProcessorResponse5;

    /**
     * @var string $customerDataConditionCode
     */
    protected $customerDataConditionCode;

    /**
     * @var string $matchBillToFullName
     */
    protected $matchBillToFullName;

    /**
     * @var string $matchBillToPrefix
     */
    protected $matchBillToPrefix;

    /**
     * @var string $matchBillToFirstName
     */
    protected $matchBillToFirstName;

    /**
     * @var string $matchBillToMiddleName
     */
    protected $matchBillToMiddleName;

    /**
     * @var string $matchBillToLastName
     */
    protected $matchBillToLastName;

    /**
     * @var string $matchBillToSuffix
     */
    protected $matchBillToSuffix;

    /**
     * @var string $matchBillToCompany
     */
    protected $matchBillToCompany;

    /**
     * @var string $matchBillToAddress
     */
    protected $matchBillToAddress;

    /**
     * @var string $matchBillToCity
     */
    protected $matchBillToCity;

    /**
     * @var string $matchBillToState
     */
    protected $matchBillToState;

    /**
     * @var string $matchBillToPostalCode
     */
    protected $matchBillToPostalCode;

    /**
     * @var string $matchBillToPhoneNumber
     */
    protected $matchBillToPhoneNumber;

    /**
     * @var string $matchBillToCompanyPhoneNumber
     */
    protected $matchBillToCompanyPhoneNumber;

    /**
     * @var string $matchBillToCompanyTaxID
     */
    protected $matchBillToCompanyTaxID;

    /**
     * @var string $matchBillToSSN
     */
    protected $matchBillToSSN;

    /**
     * @var string $matchBillToDateOfBirth
     */
    protected $matchBillToDateOfBirth;

    /**
     * @var string $matchPersonalIDType
     */
    protected $matchPersonalIDType;

    /**
     * @var string $matchPersonalID
     */
    protected $matchPersonalID;

    /**
     * @var string $matchPersonalIDIssuedBy
     */
    protected $matchPersonalIDIssuedBy;

    /**
     * @var int $overallMatchScore
     */
    protected $overallMatchScore;

    /**
     * @var string $calculatedResponse
     */
    protected $calculatedResponse;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidationType()
    {
        return $this->validationType;
    }

    /**
     * @param string $validationType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setValidationType($validationType)
    {
        $this->validationType = $validationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryStatusCode()
    {
        return $this->primaryStatusCode;
    }

    /**
     * @param string $primaryStatusCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setPrimaryStatusCode($primaryStatusCode)
    {
        $this->primaryStatusCode = $primaryStatusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecondaryStatusCode()
    {
        return $this->secondaryStatusCode;
    }

    /**
     * @param string $secondaryStatusCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setSecondaryStatusCode($secondaryStatusCode)
    {
        $this->secondaryStatusCode = $secondaryStatusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalStatusCode()
    {
        return $this->additionalStatusCode;
    }

    /**
     * @param string $additionalStatusCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setAdditionalStatusCode($additionalStatusCode)
    {
        $this->additionalStatusCode = $additionalStatusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumberOfReturns()
    {
        return $this->numberOfReturns;
    }

    /**
     * @param string $numberOfReturns
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setNumberOfReturns($numberOfReturns)
    {
        $this->numberOfReturns = $numberOfReturns;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastReturnDate()
    {
        return $this->lastReturnDate;
    }

    /**
     * @param string $lastReturnDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setLastReturnDate($lastReturnDate)
    {
        $this->lastReturnDate = $lastReturnDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastReturnProcessorResponse()
    {
        return $this->lastReturnProcessorResponse;
    }

    /**
     * @param string $lastReturnProcessorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setLastReturnProcessorResponse($lastReturnProcessorResponse)
    {
        $this->lastReturnProcessorResponse = $lastReturnProcessorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdateDate()
    {
        return $this->lastUpdateDate;
    }

    /**
     * @param string $lastUpdateDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setLastUpdateDate($lastUpdateDate)
    {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddedOrClosedDate()
    {
        return $this->addedOrClosedDate;
    }

    /**
     * @param string $addedOrClosedDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setAddedOrClosedDate($addedOrClosedDate)
    {
        $this->addedOrClosedDate = $addedOrClosedDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPreviousStatusCode()
    {
        return $this->previousStatusCode;
    }

    /**
     * @param string $previousStatusCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setPreviousStatusCode($previousStatusCode)
    {
        $this->previousStatusCode = $previousStatusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getFcraDisputeCode()
    {
        return $this->fcraDisputeCode;
    }

    /**
     * @param string $fcraDisputeCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setFcraDisputeCode($fcraDisputeCode)
    {
        $this->fcraDisputeCode = $fcraDisputeCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getScoredAccountProcessorResponse1()
    {
        return $this->scoredAccountProcessorResponse1;
    }

    /**
     * @param string $scoredAccountProcessorResponse1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setScoredAccountProcessorResponse1($scoredAccountProcessorResponse1)
    {
        $this->scoredAccountProcessorResponse1 = $scoredAccountProcessorResponse1;

        return $this;
    }

    /**
     * @return string
     */
    public function getScoredAccountProcessorResponse2()
    {
        return $this->scoredAccountProcessorResponse2;
    }

    /**
     * @param string $scoredAccountProcessorResponse2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setScoredAccountProcessorResponse2($scoredAccountProcessorResponse2)
    {
        $this->scoredAccountProcessorResponse2 = $scoredAccountProcessorResponse2;

        return $this;
    }

    /**
     * @return string
     */
    public function getScoredAccountProcessorResponse3()
    {
        return $this->scoredAccountProcessorResponse3;
    }

    /**
     * @param string $scoredAccountProcessorResponse3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setScoredAccountProcessorResponse3($scoredAccountProcessorResponse3)
    {
        $this->scoredAccountProcessorResponse3 = $scoredAccountProcessorResponse3;

        return $this;
    }

    /**
     * @return string
     */
    public function getScoredAccountProcessorResponse5()
    {
        return $this->scoredAccountProcessorResponse5;
    }

    /**
     * @param string $scoredAccountProcessorResponse5
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setScoredAccountProcessorResponse5($scoredAccountProcessorResponse5)
    {
        $this->scoredAccountProcessorResponse5 = $scoredAccountProcessorResponse5;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerDataConditionCode()
    {
        return $this->customerDataConditionCode;
    }

    /**
     * @param string $customerDataConditionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setCustomerDataConditionCode($customerDataConditionCode)
    {
        $this->customerDataConditionCode = $customerDataConditionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToFullName()
    {
        return $this->matchBillToFullName;
    }

    /**
     * @param string $matchBillToFullName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToFullName($matchBillToFullName)
    {
        $this->matchBillToFullName = $matchBillToFullName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToPrefix()
    {
        return $this->matchBillToPrefix;
    }

    /**
     * @param string $matchBillToPrefix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToPrefix($matchBillToPrefix)
    {
        $this->matchBillToPrefix = $matchBillToPrefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToFirstName()
    {
        return $this->matchBillToFirstName;
    }

    /**
     * @param string $matchBillToFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToFirstName($matchBillToFirstName)
    {
        $this->matchBillToFirstName = $matchBillToFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToMiddleName()
    {
        return $this->matchBillToMiddleName;
    }

    /**
     * @param string $matchBillToMiddleName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToMiddleName($matchBillToMiddleName)
    {
        $this->matchBillToMiddleName = $matchBillToMiddleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToLastName()
    {
        return $this->matchBillToLastName;
    }

    /**
     * @param string $matchBillToLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToLastName($matchBillToLastName)
    {
        $this->matchBillToLastName = $matchBillToLastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToSuffix()
    {
        return $this->matchBillToSuffix;
    }

    /**
     * @param string $matchBillToSuffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToSuffix($matchBillToSuffix)
    {
        $this->matchBillToSuffix = $matchBillToSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToCompany()
    {
        return $this->matchBillToCompany;
    }

    /**
     * @param string $matchBillToCompany
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToCompany($matchBillToCompany)
    {
        $this->matchBillToCompany = $matchBillToCompany;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToAddress()
    {
        return $this->matchBillToAddress;
    }

    /**
     * @param string $matchBillToAddress
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToAddress($matchBillToAddress)
    {
        $this->matchBillToAddress = $matchBillToAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToCity()
    {
        return $this->matchBillToCity;
    }

    /**
     * @param string $matchBillToCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToCity($matchBillToCity)
    {
        $this->matchBillToCity = $matchBillToCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToState()
    {
        return $this->matchBillToState;
    }

    /**
     * @param string $matchBillToState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToState($matchBillToState)
    {
        $this->matchBillToState = $matchBillToState;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToPostalCode()
    {
        return $this->matchBillToPostalCode;
    }

    /**
     * @param string $matchBillToPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToPostalCode($matchBillToPostalCode)
    {
        $this->matchBillToPostalCode = $matchBillToPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToPhoneNumber()
    {
        return $this->matchBillToPhoneNumber;
    }

    /**
     * @param string $matchBillToPhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToPhoneNumber($matchBillToPhoneNumber)
    {
        $this->matchBillToPhoneNumber = $matchBillToPhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToCompanyPhoneNumber()
    {
        return $this->matchBillToCompanyPhoneNumber;
    }

    /**
     * @param string $matchBillToCompanyPhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToCompanyPhoneNumber($matchBillToCompanyPhoneNumber)
    {
        $this->matchBillToCompanyPhoneNumber = $matchBillToCompanyPhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToCompanyTaxID()
    {
        return $this->matchBillToCompanyTaxID;
    }

    /**
     * @param string $matchBillToCompanyTaxID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToCompanyTaxID($matchBillToCompanyTaxID)
    {
        $this->matchBillToCompanyTaxID = $matchBillToCompanyTaxID;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToSSN()
    {
        return $this->matchBillToSSN;
    }

    /**
     * @param string $matchBillToSSN
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToSSN($matchBillToSSN)
    {
        $this->matchBillToSSN = $matchBillToSSN;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchBillToDateOfBirth()
    {
        return $this->matchBillToDateOfBirth;
    }

    /**
     * @param string $matchBillToDateOfBirth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchBillToDateOfBirth($matchBillToDateOfBirth)
    {
        $this->matchBillToDateOfBirth = $matchBillToDateOfBirth;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchPersonalIDType()
    {
        return $this->matchPersonalIDType;
    }

    /**
     * @param string $matchPersonalIDType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchPersonalIDType($matchPersonalIDType)
    {
        $this->matchPersonalIDType = $matchPersonalIDType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchPersonalID()
    {
        return $this->matchPersonalID;
    }

    /**
     * @param string $matchPersonalID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchPersonalID($matchPersonalID)
    {
        $this->matchPersonalID = $matchPersonalID;

        return $this;
    }

    /**
     * @return string
     */
    public function getMatchPersonalIDIssuedBy()
    {
        return $this->matchPersonalIDIssuedBy;
    }

    /**
     * @param string $matchPersonalIDIssuedBy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setMatchPersonalIDIssuedBy($matchPersonalIDIssuedBy)
    {
        $this->matchPersonalIDIssuedBy = $matchPersonalIDIssuedBy;

        return $this;
    }

    /**
     * @return int
     */
    public function getOverallMatchScore()
    {
        return $this->overallMatchScore;
    }

    /**
     * @param int $overallMatchScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setOverallMatchScore($overallMatchScore)
    {
        $this->overallMatchScore = $overallMatchScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getCalculatedResponse()
    {
        return $this->calculatedResponse;
    }

    /**
     * @param string $calculatedResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply
     */
    public function setCalculatedResponse($calculatedResponse)
    {
        $this->calculatedResponse = $calculatedResponse;

        return $this;
    }
}
