<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BinLookupService
{
    /**
     * @var string $mode
     */
    protected $mode;

    /**
     * @var string $networkOrder
     */
    protected $networkOrder;

    /**
     * @var string $retrievalReferenceNumber
     */
    protected $retrievalReferenceNumber;

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
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BinLookupService
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkOrder()
    {
        return $this->networkOrder;
    }

    /**
     * @param string $networkOrder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BinLookupService
     */
    public function setNetworkOrder($networkOrder)
    {
        $this->networkOrder = $networkOrder;

        return $this;
    }

    /**
     * @return string
     */
    public function getRetrievalReferenceNumber()
    {
        return $this->retrievalReferenceNumber;
    }

    /**
     * @param string $retrievalReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BinLookupService
     */
    public function setRetrievalReferenceNumber($retrievalReferenceNumber)
    {
        $this->retrievalReferenceNumber = $retrievalReferenceNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BinLookupService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
