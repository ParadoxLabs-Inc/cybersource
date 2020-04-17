<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AFSService
{
    /**
     * @var string $avsCode
     */
    protected $avsCode;

    /**
     * @var string $cvCode
     */
    protected $cvCode;

    /**
     * @var boolean $disableAVSScoring
     */
    protected $disableAVSScoring;

    /**
     * @var string $customRiskModel
     */
    protected $customRiskModel;

    /**
     * @var boolean $run
     */
    protected $run;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
        $this->run = $run;
    }

    /**
     * @return string
     */
    public function getAvsCode()
    {
        return $this->avsCode;
    }

    /**
     * @param string $avsCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSService
     */
    public function setAvsCode($avsCode)
    {
        $this->avsCode = $avsCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvCode()
    {
        return $this->cvCode;
    }

    /**
     * @param string $cvCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSService
     */
    public function setCvCode($cvCode)
    {
        $this->cvCode = $cvCode;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDisableAVSScoring()
    {
        return $this->disableAVSScoring;
    }

    /**
     * @param boolean $disableAVSScoring
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSService
     */
    public function setDisableAVSScoring($disableAVSScoring)
    {
        $this->disableAVSScoring = $disableAVSScoring;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomRiskModel()
    {
        return $this->customRiskModel;
    }

    /**
     * @param string $customRiskModel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSService
     */
    public function setCustomRiskModel($customRiskModel)
    {
        $this->customRiskModel = $customRiskModel;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AFSService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
