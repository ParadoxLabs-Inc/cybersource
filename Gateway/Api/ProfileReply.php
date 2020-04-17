<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ProfileReply
{
    /**
     * @var string $selectedBy
     */
    protected $selectedBy;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $destinationQueue
     */
    protected $destinationQueue;

    /**
     * @var string $profileScore
     */
    protected $profileScore;

    /**
     * @var RuleResultItems $rulesTriggered
     */
    protected $rulesTriggered;

    /**
     * @return string
     */
    public function getSelectedBy()
    {
        return $this->selectedBy;
    }

    /**
     * @param string $selectedBy
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReply
     */
    public function setSelectedBy($selectedBy)
    {
        $this->selectedBy = $selectedBy;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReply
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationQueue()
    {
        return $this->destinationQueue;
    }

    /**
     * @param string $destinationQueue
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReply
     */
    public function setDestinationQueue($destinationQueue)
    {
        $this->destinationQueue = $destinationQueue;

        return $this;
    }

    /**
     * @return string
     */
    public function getProfileScore()
    {
        return $this->profileScore;
    }

    /**
     * @param string $profileScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReply
     */
    public function setProfileScore($profileScore)
    {
        $this->profileScore = $profileScore;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProfileReply
     */
    public function setRulesTriggered($rulesTriggered)
    {
        $this->rulesTriggered = $rulesTriggered;

        return $this;
    }
}
