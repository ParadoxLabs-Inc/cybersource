<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCSaleReversalService
{
    /**
     * @var string $saleRequestID
     */
    protected $saleRequestID;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
    }

    /**
     * @return string
     */
    public function getSaleRequestID()
    {
        return $this->saleRequestID;
    }

    /**
     * @param string $saleRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalService
     */
    public function setSaleRequestID($saleRequestID)
    {
        $this->saleRequestID = $saleRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
