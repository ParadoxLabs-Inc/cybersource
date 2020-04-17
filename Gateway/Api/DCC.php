<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DCC
{
    /**
     * @var int $dccIndicator
     */
    protected $dccIndicator;

    /**
     * @var string $referenceNumber
     */
    protected $referenceNumber;

    /**
     * @return int
     */
    public function getDccIndicator()
    {
        return $this->dccIndicator;
    }

    /**
     * @param int $dccIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DCC
     */
    public function setDccIndicator($dccIndicator)
    {
        $this->dccIndicator = $dccIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @param string $referenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DCC
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }
}
