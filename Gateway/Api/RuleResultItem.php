<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class RuleResultItem
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $decision
     */
    protected $decision;

    /**
     * @var string $evaluation
     */
    protected $evaluation;

    /**
     * @var int $ruleID
     */
    protected $ruleID;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RuleResultItem
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RuleResultItem
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * @param string $evaluation
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RuleResultItem
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * @return int
     */
    public function getRuleID()
    {
        return $this->ruleID;
    }

    /**
     * @param int $ruleID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RuleResultItem
     */
    public function setRuleID($ruleID)
    {
        $this->ruleID = $ruleID;

        return $this;
    }
}
