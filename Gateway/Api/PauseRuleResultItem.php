<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PauseRuleResultItem
{
    /**
     * @var int $ruleID
     */
    protected $ruleID;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $action
     */
    protected $action;

    /**
     * @var string $evaluation
     */
    protected $evaluation;

    /**
     * @return int
     */
    public function getRuleID()
    {
        return $this->ruleID;
    }

    /**
     * @param int $ruleID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PauseRuleResultItem
     */
    public function setRuleID($ruleID)
    {
        $this->ruleID = $ruleID;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PauseRuleResultItem
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PauseRuleResultItem
     */
    public function setAction($action)
    {
        $this->action = $action;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PauseRuleResultItem
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;

        return $this;
    }
}
