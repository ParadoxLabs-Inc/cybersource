<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCAuthReversalService
{
    /**
     * @var string $authRequestID
     */
    protected $authRequestID;

    /**
     * @var string $authRequestToken
     */
    protected $authRequestToken;

    /**
     * @var string $reversalReason
     */
    protected $reversalReason;

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
    public function getAuthRequestID()
    {
        return $this->authRequestID;
    }

    /**
     * @param string $authRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalService
     */
    public function setAuthRequestID($authRequestID)
    {
        $this->authRequestID = $authRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthRequestToken()
    {
        return $this->authRequestToken;
    }

    /**
     * @param string $authRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalService
     */
    public function setAuthRequestToken($authRequestToken)
    {
        $this->authRequestToken = $authRequestToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalService
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
