<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ECAuthenticateService
{
    /**
     * @var string $referenceNumber
     */
    protected $referenceNumber;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
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
