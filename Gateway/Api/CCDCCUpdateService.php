<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCDCCUpdateService
{
    /**
     * @var string $reason
     */
    protected $reason;

    /**
     * @var string $action
     */
    protected $action;

    /**
     * @var string $dccRequestID
     */
    protected $dccRequestID;

    /**
     * @var string $captureRequestID
     */
    protected $captureRequestID;

    /**
     * @var string $creditRequestID
     */
    protected $creditRequestID;

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
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateService
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateService
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getDccRequestID()
    {
        return $this->dccRequestID;
    }

    /**
     * @param string $dccRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateService
     */
    public function setDccRequestID($dccRequestID)
    {
        $this->dccRequestID = $dccRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptureRequestID()
    {
        return $this->captureRequestID;
    }

    /**
     * @param string $captureRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateService
     */
    public function setCaptureRequestID($captureRequestID)
    {
        $this->captureRequestID = $captureRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreditRequestID()
    {
        return $this->creditRequestID;
    }

    /**
     * @param string $creditRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateService
     */
    public function setCreditRequestID($creditRequestID)
    {
        $this->creditRequestID = $creditRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
