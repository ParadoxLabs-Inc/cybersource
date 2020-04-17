<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ProfileReplyEarly
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $selectedBy
     */
    protected $selectedBy;

    /**
     * @var PauseRuleResultItems $pauseRulesTriggered
     */
    protected $pauseRulesTriggered;

    /**
     * @var RuleResultItems $rulesTriggered
     */
    protected $rulesTriggered;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReplyEarly
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedBy()
    {
        return $this->selectedBy;
    }

    /**
     * @param string $selectedBy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReplyEarly
     */
    public function setSelectedBy($selectedBy)
    {
        $this->selectedBy = $selectedBy;

        return $this;
    }

    /**
     * @return PauseRuleResultItems
     */
    public function getPauseRulesTriggered()
    {
        return $this->pauseRulesTriggered;
    }

    /**
     * @param PauseRuleResultItems $pauseRulesTriggered
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReplyEarly
     */
    public function setPauseRulesTriggered($pauseRulesTriggered)
    {
        $this->pauseRulesTriggered = $pauseRulesTriggered;

        return $this;
    }

    /**
     * @return RuleResultItems
     */
    public function getRulesTriggered()
    {
        return $this->rulesTriggered;
    }

    /**
     * @param RuleResultItems $rulesTriggered
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReplyEarly
     */
    public function setRulesTriggered($rulesTriggered)
    {
        $this->rulesTriggered = $rulesTriggered;

        return $this;
    }
}
