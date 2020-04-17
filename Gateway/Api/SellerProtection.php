<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class SellerProtection
{
    /**
     * @var string $eligibility
     */
    protected $eligibility;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @return string
     */
    public function getEligibility()
    {
        return $this->eligibility;
    }

    /**
     * @param string $eligibility
     * @return \ParadoxLabs\CyberSource\Gateway\Api\SellerProtection
     */
    public function setEligibility($eligibility)
    {
        $this->eligibility = $eligibility;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\SellerProtection
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
