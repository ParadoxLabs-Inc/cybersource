<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PauseRuleResultItems
{

    /**
     * @var PauseRuleResultItem[] $ruleResultItem
     */
    protected $ruleResultItem = null;

    public function __construct()
    {
    }

    /**
     * @return PauseRuleResultItem[]
     */
    public function getRuleResultItem()
    {
        return $this->ruleResultItem;
    }

    /**
     * @param PauseRuleResultItem[] $ruleResultItem
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PauseRuleResultItems
     */
    public function setRuleResultItem(array $ruleResultItem = null)
    {
        $this->ruleResultItem = $ruleResultItem;

        return $this;
    }

}
