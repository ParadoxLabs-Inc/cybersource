<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Category
{

    /**
     * @var string $affiliate
     */
    protected $affiliate = null;

    /**
     * @var string $campaign
     */
    protected $campaign = null;

    /**
     * @var string $group
     */
    protected $group = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }

    /**
     * @param string $affiliate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Category
     */
    public function setAffiliate($affiliate)
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param string $campaign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Category
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Category
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

}
