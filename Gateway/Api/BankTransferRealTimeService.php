<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BankTransferRealTimeService
{
    /**
     * @var string $bankTransferRealTimeType
     */
    protected $bankTransferRealTimeType;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
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
