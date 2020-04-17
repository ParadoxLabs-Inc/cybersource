<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class RuleResultItems
{
    /**
     * @var RuleResultItem[] $ruleResultItem
     */
    protected $ruleResultItem;

    /**
     * @return RuleResultItem[]
     */
    public function getRuleResultItem()
    {
        return $this->ruleResultItem;
    }

    /**
     * @param RuleResultItem[] $ruleResultItem
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RuleResultItems
     */
    public function setRuleResultItem(array $ruleResultItem = null)
    {
        $this->ruleResultItem = $ruleResultItem;

        return $this;
    }
}
