<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TransactionMetadataService
{
    /**
     * @var string $authRequestID
     */
    protected $authRequestID;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
    }

    /**
     * @return string
     */
    public function getAuthRequestID()
    {
        return $this->authRequestID;
    }

    /**
     * @param string $authRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TransactionMetadataService
     */
    public function setAuthRequestID($authRequestID)
    {
        $this->authRequestID = $authRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TransactionMetadataService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
