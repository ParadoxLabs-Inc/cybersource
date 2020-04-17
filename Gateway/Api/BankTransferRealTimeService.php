<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BankTransferRealTimeService
{
    /**
     * @var string $bankTransferRealTimeType
     */
    protected $bankTransferRealTimeType;

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
    public function getBankTransferRealTimeType()
    {
        return $this->bankTransferRealTimeType;
    }

    /**
     * @param string $bankTransferRealTimeType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRealTimeService
     */
    public function setBankTransferRealTimeType($bankTransferRealTimeType)
    {
        $this->bankTransferRealTimeType = $bankTransferRealTimeType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRealTimeService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
