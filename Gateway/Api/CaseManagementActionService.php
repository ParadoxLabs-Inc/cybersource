<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CaseManagementActionService
{
    /**
     * @var string $actionCode
     */
    protected $actionCode;

    /**
     * @var string $requestID
     */
    protected $requestID;

    /**
     * @var string $comments
     */
    protected $comments;

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
    public function getActionCode()
    {
        return $this->actionCode;
    }

    /**
     * @param string $actionCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CaseManagementActionService
     */
    public function setActionCode($actionCode)
    {
        $this->actionCode = $actionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestID()
    {
        return $this->requestID;
    }

    /**
     * @param string $requestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CaseManagementActionService
     */
    public function setRequestID($requestID)
    {
        $this->requestID = $requestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CaseManagementActionService
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CaseManagementActionService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
