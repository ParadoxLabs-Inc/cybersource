<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APBillingAgreementService
{
    /**
     * @var string $sessionsRequestID
     */
    protected $sessionsRequestID;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
    }

    /**
     * @return string
     */
    public function getSessionsRequestID()
    {
        return $this->sessionsRequestID;
    }

    /**
     * @param string $sessionsRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementService
     */
    public function setSessionsRequestID($sessionsRequestID)
    {
        $this->sessionsRequestID = $sessionsRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
