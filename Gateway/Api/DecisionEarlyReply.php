<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DecisionEarlyReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $rcode
     */
    protected $rcode;

    /**
     * @var string $rflag
     */
    protected $rflag;

    /**
     * @var string $rmsg
     */
    protected $rmsg;

    /**
     * @var string $action
     */
    protected $action;

    /**
     * @var ProfileReplyEarly $activeProfileReply
     */
    protected $activeProfileReply;

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRcode()
    {
        return $this->rcode;
    }

    /**
     * @param string $rcode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setRcode($rcode)
    {
        $this->rcode = $rcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRflag()
    {
        return $this->rflag;
    }

    /**
     * @param string $rflag
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setRflag($rflag)
    {
        $this->rflag = $rflag;

        return $this;
    }

    /**
     * @return string
     */
    public function getRmsg()
    {
        return $this->rmsg;
    }

    /**
     * @param string $rmsg
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setRmsg($rmsg)
    {
        $this->rmsg = $rmsg;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return ProfileReplyEarly
     */
    public function getActiveProfileReply()
    {
        return $this->activeProfileReply;
    }

    /**
     * @param ProfileReplyEarly $activeProfileReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setActiveProfileReply($activeProfileReply)
    {
        $this->activeProfileReply = $activeProfileReply;

        return $this;
    }
}
