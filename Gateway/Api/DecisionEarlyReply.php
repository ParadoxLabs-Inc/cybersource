<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DecisionEarlyReply
{
    /**
     * @var int $casePriority
     */
    protected $casePriority;

    /**
     * @var string $decision
     */
    protected $decision;

    /**
     * @var string $action
     */
    protected $action;

    /**
     * @var string $applicableOrderModifications
     */
    protected $applicableOrderModifications;

    /**
     * @var string $appliedOrderModifications
     */
    protected $appliedOrderModifications;

    /**
     * @var ProfileReplyEarly $activeProfileReply
     */
    protected $activeProfileReply;

    /**
     * @var VelocityCountsEarly $velocityCounts
     */
    protected $velocityCounts;

    /**
     * @return int
     */
    public function getCasePriority()
    {
        return $this->casePriority;
    }

    /**
     * @param int $casePriority
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setCasePriority($casePriority)
    {
        $this->casePriority = $casePriority;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * @param string $decision
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;

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
     * @return string
     */
    public function getApplicableOrderModifications()
    {
        return $this->applicableOrderModifications;
    }

    /**
     * @param string $applicableOrderModifications
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setApplicableOrderModifications($applicableOrderModifications)
    {
        $this->applicableOrderModifications = $applicableOrderModifications;

        return $this;
    }

    /**
     * @return string
     */
    public function getAppliedOrderModifications()
    {
        return $this->appliedOrderModifications;
    }

    /**
     * @param string $appliedOrderModifications
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setAppliedOrderModifications($appliedOrderModifications)
    {
        $this->appliedOrderModifications = $appliedOrderModifications;

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

    /**
     * @return VelocityCountsEarly
     */
    public function getVelocityCounts()
    {
        return $this->velocityCounts;
    }

    /**
     * @param VelocityCountsEarly $velocityCounts
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply
     */
    public function setVelocityCounts($velocityCounts)
    {
        $this->velocityCounts = $velocityCounts;

        return $this;
    }
}
