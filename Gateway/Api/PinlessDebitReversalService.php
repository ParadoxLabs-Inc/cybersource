<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PinlessDebitReversalService
{
    /**
     * @var string $pinlessDebitRequestID
     */
    protected $pinlessDebitRequestID;

    /**
     * @var string $pinlessDebitRequestToken
     */
    protected $pinlessDebitRequestToken;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
    }

    /**
     * @return string
     */
    public function getPinlessDebitRequestID()
    {
        return $this->pinlessDebitRequestID;
    }

    /**
     * @param string $pinlessDebitRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitReversalService
     */
    public function setPinlessDebitRequestID($pinlessDebitRequestID)
    {
        $this->pinlessDebitRequestID = $pinlessDebitRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPinlessDebitRequestToken()
    {
        return $this->pinlessDebitRequestToken;
    }

    /**
     * @param string $pinlessDebitRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitReversalService
     */
    public function setPinlessDebitRequestToken($pinlessDebitRequestToken)
    {
        $this->pinlessDebitRequestToken = $pinlessDebitRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationID()
    {
        return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitReversalService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitReversalService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
