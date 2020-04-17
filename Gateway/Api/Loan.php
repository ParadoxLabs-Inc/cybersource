<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Loan
{
    /**
     * @var string $assetType
     */
    protected $assetType;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @return string
     */
    public function getAssetType()
    {
        return $this->assetType;
    }

    /**
     * @param string $assetType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Loan
     */
    public function setAssetType($assetType)
    {
        $this->assetType = $assetType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Loan
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
