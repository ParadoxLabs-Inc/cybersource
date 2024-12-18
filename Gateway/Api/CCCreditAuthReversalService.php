<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCCreditAuthReversalService
{
    /**
     * @var string $creditAuthRequestID
     */
    protected $creditAuthRequestID;

    /**
     * @var string $requestToken
     */
    protected $requestToken;

    /**
     * @var string $reversalReason
     */
    protected $reversalReason;

    /**
     * @var string $returnReversalRecord
     */
    protected $returnReversalRecord;

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
    public function getCreditAuthRequestID()
    {
        return $this->creditAuthRequestID;
    }

    /**
     * @param string $creditAuthRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReversalService
     */
    public function setCreditAuthRequestID($creditAuthRequestID)
    {
        $this->creditAuthRequestID = $creditAuthRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestToken()
    {
        return $this->requestToken;
    }

    /**
     * @param string $requestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReversalService
     */
    public function setRequestToken($requestToken)
    {
        $this->requestToken = $requestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     * @param string $reversalReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReversalService
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnReversalRecord()
    {
        return $this->returnReversalRecord;
    }

    /**
     * @param string $returnReversalRecord
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReversalService
     */
    public function setReturnReversalRecord($returnReversalRecord)
    {
        $this->returnReversalRecord = $returnReversalRecord;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReversalService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
