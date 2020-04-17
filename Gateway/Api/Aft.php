<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Aft
{
    /**
     * @var string $indicator
     */
    protected $indicator;

    /**
     * @var string $serviceFee
     */
    protected $serviceFee;

    /**
     * @var string $foreignExchangeFee
     */
    protected $foreignExchangeFee;

    /**
     * @return string
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * @param string $indicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Aft
     */
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceFee()
    {
        return $this->serviceFee;
    }

    /**
     * @param string $serviceFee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Aft
     */
    public function setServiceFee($serviceFee)
    {
        $this->serviceFee = $serviceFee;

        return $this;
    }

    /**
     * @return string
     */
    public function getForeignExchangeFee()
    {
        return $this->foreignExchangeFee;
    }

    /**
     * @param string $foreignExchangeFee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Aft
     */
    public function setForeignExchangeFee($foreignExchangeFee)
    {
        $this->foreignExchangeFee = $foreignExchangeFee;

        return $this;
    }
}
