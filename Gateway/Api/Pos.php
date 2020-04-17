<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Pos
{
    /**
     * @var string $entryMode
     */
    protected $entryMode;

    /**
     * @var string $cardPresent
     */
    protected $cardPresent;

    /**
     * @var string $terminalCapability
     */
    protected $terminalCapability;

    /**
     * @var string $trackData
     */
    protected $trackData;

    /**
     * @var string $terminalID
     */
    protected $terminalID;

    /**
     * @var string $terminalType
     */
    protected $terminalType;

    /**
     * @var string $terminalLocation
     */
    protected $terminalLocation;

    /**
     * @var string $transactionSecurity
     */
    protected $transactionSecurity;

    /**
     * @var string $catLevel
     */
    protected $catLevel;

    /**
     * @var string $conditionCode
     */
    protected $conditionCode;

    /**
     * @var string $environment
     */
    protected $environment;

    /**
     * @var string $paymentData
     */
    protected $paymentData;

    /**
     * @var string $deviceReaderData
     */
    protected $deviceReaderData;

    /**
     * @var string $encryptionAlgorithm
     */
    protected $encryptionAlgorithm;

    /**
     * @var string $encodingMethod
     */
    protected $encodingMethod;

    /**
     * @var string $deviceID
     */
    protected $deviceID;

    /**
     * @var string $serviceCode
     */
    protected $serviceCode;

    /**
     * @var string $terminalIDAlternate
     */
    protected $terminalIDAlternate;

    /**
     * @var int $terminalCompliance
     */
    protected $terminalCompliance;

    /**
     * @var string $terminalCardCaptureCapability
     */
    protected $terminalCardCaptureCapability;

    /**
     * @var string $terminalOutputCapability
     */
    protected $terminalOutputCapability;

    /**
     * @var string $terminalPINcapability
     */
    protected $terminalPINcapability;

    /**
     * @var string $terminalCVMcapabilities_0
     */
    protected $terminalCVMcapabilities_0;

    /**
     * @var string $terminalCVMcapabilities_1
     */
    protected $terminalCVMcapabilities_1;

    /**
     * @var string $terminalCVMcapabilities_2
     */
    protected $terminalCVMcapabilities_2;

    /**
     * @var string $terminalInputCapabilities_0
     */
    protected $terminalInputCapabilities_0;

    /**
     * @var string $terminalInputCapabilities_1
     */
    protected $terminalInputCapabilities_1;

    /**
     * @var string $terminalInputCapabilities_2
     */
    protected $terminalInputCapabilities_2;

    /**
     * @var string $terminalInputCapabilities_3
     */
    protected $terminalInputCapabilities_3;

    /**
     * @var string $terminalInputCapabilities_4
     */
    protected $terminalInputCapabilities_4;

    /**
     * @var string $terminalInputCapabilities_5
     */
    protected $terminalInputCapabilities_5;

    /**
     * @var string $terminalInputCapabilities_6
     */
    protected $terminalInputCapabilities_6;

    /**
     * @var string $terminalSerialNumber
     */
    protected $terminalSerialNumber;

    /**
     * @var string $storeAndForwardIndicator
     */
    protected $storeAndForwardIndicator;

    /**
     * @var string $panEntryMode
     */
    protected $panEntryMode;

    /**
     * @var boolean $endlessAisleTransactionIndicator
     */
    protected $endlessAisleTransactionIndicator;

    /**
     * @var string $terminalModel
     */
    protected $terminalModel;

    /**
     * @return string
     */
    public function getEntryMode()
    {
        return $this->entryMode;
    }

    /**
     * @param string $entryMode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setEntryMode($entryMode)
    {
        $this->entryMode = $entryMode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardPresent()
    {
        return $this->cardPresent;
    }

    /**
     * @param string $cardPresent
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setCardPresent($cardPresent)
    {
        $this->cardPresent = $cardPresent;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalCapability()
    {
        return $this->terminalCapability;
    }

    /**
     * @param string $terminalCapability
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalCapability($terminalCapability)
    {
        $this->terminalCapability = $terminalCapability;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrackData()
    {
        return $this->trackData;
    }

    /**
     * @param string $trackData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTrackData($trackData)
    {
        $this->trackData = $trackData;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalID()
    {
        return $this->terminalID;
    }

    /**
     * @param string $terminalID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalID($terminalID)
    {
        $this->terminalID = $terminalID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalType()
    {
        return $this->terminalType;
    }

    /**
     * @param string $terminalType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalType($terminalType)
    {
        $this->terminalType = $terminalType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalLocation()
    {
        return $this->terminalLocation;
    }

    /**
     * @param string $terminalLocation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalLocation($terminalLocation)
    {
        $this->terminalLocation = $terminalLocation;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionSecurity()
    {
        return $this->transactionSecurity;
    }

    /**
     * @param string $transactionSecurity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTransactionSecurity($transactionSecurity)
    {
        $this->transactionSecurity = $transactionSecurity;

        return $this;
    }

    /**
     * @return string
     */
    public function getCatLevel()
    {
        return $this->catLevel;
    }

    /**
     * @param string $catLevel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setCatLevel($catLevel)
    {
        $this->catLevel = $catLevel;

        return $this;
    }

    /**
     * @return string
     */
    public function getConditionCode()
    {
        return $this->conditionCode;
    }

    /**
     * @param string $conditionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setConditionCode($conditionCode)
    {
        $this->conditionCode = $conditionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentData()
    {
        return $this->paymentData;
    }

    /**
     * @param string $paymentData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setPaymentData($paymentData)
    {
        $this->paymentData = $paymentData;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceReaderData()
    {
        return $this->deviceReaderData;
    }

    /**
     * @param string $deviceReaderData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setDeviceReaderData($deviceReaderData)
    {
        $this->deviceReaderData = $deviceReaderData;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptionAlgorithm()
    {
        return $this->encryptionAlgorithm;
    }

    /**
     * @param string $encryptionAlgorithm
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setEncryptionAlgorithm($encryptionAlgorithm)
    {
        $this->encryptionAlgorithm = $encryptionAlgorithm;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncodingMethod()
    {
        return $this->encodingMethod;
    }

    /**
     * @param string $encodingMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setEncodingMethod($encodingMethod)
    {
        $this->encodingMethod = $encodingMethod;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setDeviceID($deviceID)
    {
        $this->deviceID = $deviceID;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceCode()
    {
        return $this->serviceCode;
    }

    /**
     * @param string $serviceCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setServiceCode($serviceCode)
    {
        $this->serviceCode = $serviceCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalIDAlternate()
    {
        return $this->terminalIDAlternate;
    }

    /**
     * @param string $terminalIDAlternate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalIDAlternate($terminalIDAlternate)
    {
        $this->terminalIDAlternate = $terminalIDAlternate;

        return $this;
    }

    /**
     * @return int
     */
    public function getTerminalCompliance()
    {
        return $this->terminalCompliance;
    }

    /**
     * @param int $terminalCompliance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalCompliance($terminalCompliance)
    {
        $this->terminalCompliance = $terminalCompliance;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalCardCaptureCapability()
    {
        return $this->terminalCardCaptureCapability;
    }

    /**
     * @param string $terminalCardCaptureCapability
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalCardCaptureCapability($terminalCardCaptureCapability)
    {
        $this->terminalCardCaptureCapability = $terminalCardCaptureCapability;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalOutputCapability()
    {
        return $this->terminalOutputCapability;
    }

    /**
     * @param string $terminalOutputCapability
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalOutputCapability($terminalOutputCapability)
    {
        $this->terminalOutputCapability = $terminalOutputCapability;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalPINcapability()
    {
        return $this->terminalPINcapability;
    }

    /**
     * @param string $terminalPINcapability
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalPINcapability($terminalPINcapability)
    {
        $this->terminalPINcapability = $terminalPINcapability;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalCVMcapabilities_0()
    {
        return $this->terminalCVMcapabilities_0;
    }

    /**
     * @param string $terminalCVMcapabilities_0
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalCVMcapabilities_0($terminalCVMcapabilities_0)
    {
        $this->terminalCVMcapabilities_0 = $terminalCVMcapabilities_0;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalCVMcapabilities_1()
    {
        return $this->terminalCVMcapabilities_1;
    }

    /**
     * @param string $terminalCVMcapabilities_1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalCVMcapabilities_1($terminalCVMcapabilities_1)
    {
        $this->terminalCVMcapabilities_1 = $terminalCVMcapabilities_1;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalCVMcapabilities_2()
    {
        return $this->terminalCVMcapabilities_2;
    }

    /**
     * @param string $terminalCVMcapabilities_2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalCVMcapabilities_2($terminalCVMcapabilities_2)
    {
        $this->terminalCVMcapabilities_2 = $terminalCVMcapabilities_2;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalInputCapabilities_0()
    {
        return $this->terminalInputCapabilities_0;
    }

    /**
     * @param string $terminalInputCapabilities_0
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalInputCapabilities_0($terminalInputCapabilities_0)
    {
        $this->terminalInputCapabilities_0 = $terminalInputCapabilities_0;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalInputCapabilities_1()
    {
        return $this->terminalInputCapabilities_1;
    }

    /**
     * @param string $terminalInputCapabilities_1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalInputCapabilities_1($terminalInputCapabilities_1)
    {
        $this->terminalInputCapabilities_1 = $terminalInputCapabilities_1;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalInputCapabilities_2()
    {
        return $this->terminalInputCapabilities_2;
    }

    /**
     * @param string $terminalInputCapabilities_2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalInputCapabilities_2($terminalInputCapabilities_2)
    {
        $this->terminalInputCapabilities_2 = $terminalInputCapabilities_2;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalInputCapabilities_3()
    {
        return $this->terminalInputCapabilities_3;
    }

    /**
     * @param string $terminalInputCapabilities_3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalInputCapabilities_3($terminalInputCapabilities_3)
    {
        $this->terminalInputCapabilities_3 = $terminalInputCapabilities_3;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalInputCapabilities_4()
    {
        return $this->terminalInputCapabilities_4;
    }

    /**
     * @param string $terminalInputCapabilities_4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalInputCapabilities_4($terminalInputCapabilities_4)
    {
        $this->terminalInputCapabilities_4 = $terminalInputCapabilities_4;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalInputCapabilities_5()
    {
        return $this->terminalInputCapabilities_5;
    }

    /**
     * @param string $terminalInputCapabilities_5
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalInputCapabilities_5($terminalInputCapabilities_5)
    {
        $this->terminalInputCapabilities_5 = $terminalInputCapabilities_5;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalInputCapabilities_6()
    {
        return $this->terminalInputCapabilities_6;
    }

    /**
     * @param string $terminalInputCapabilities_6
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalInputCapabilities_6($terminalInputCapabilities_6)
    {
        $this->terminalInputCapabilities_6 = $terminalInputCapabilities_6;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalSerialNumber()
    {
        return $this->terminalSerialNumber;
    }

    /**
     * @param string $terminalSerialNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalSerialNumber($terminalSerialNumber)
    {
        $this->terminalSerialNumber = $terminalSerialNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreAndForwardIndicator()
    {
        return $this->storeAndForwardIndicator;
    }

    /**
     * @param string $storeAndForwardIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setStoreAndForwardIndicator($storeAndForwardIndicator)
    {
        $this->storeAndForwardIndicator = $storeAndForwardIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getPanEntryMode()
    {
        return $this->panEntryMode;
    }

    /**
     * @param string $panEntryMode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setPanEntryMode($panEntryMode)
    {
        $this->panEntryMode = $panEntryMode;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEndlessAisleTransactionIndicator()
    {
        return $this->endlessAisleTransactionIndicator;
    }

    /**
     * @param boolean $endlessAisleTransactionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setEndlessAisleTransactionIndicator($endlessAisleTransactionIndicator)
    {
        $this->endlessAisleTransactionIndicator = $endlessAisleTransactionIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerminalModel()
    {
        return $this->terminalModel;
    }

    /**
     * @param string $terminalModel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Pos
     */
    public function setTerminalModel($terminalModel)
    {
        $this->terminalModel = $terminalModel;

        return $this;
    }
}
