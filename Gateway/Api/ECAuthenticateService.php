<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ECAuthenticateService
{
    /**
     * @var string $referenceNumber
     */
    protected $referenceNumber;

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
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $referenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateService
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
