<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Batch
{
    /**
     * @var string $batchID
     */
    protected $batchID;

    /**
     * @var string $recordID
     */
    protected $recordID;

    /**
     * @return string
     */
    public function getBatchID()
    {
        return $this->batchID;
    }

    /**
     * @param string $batchID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Batch
     */
    public function setBatchID($batchID)
    {
        $this->batchID = $batchID;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecordID()
    {
        return $this->recordID;
    }

    /**
     * @param string $recordID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Batch
     */
    public function setRecordID($recordID)
    {
        $this->recordID = $recordID;

        return $this;
    }
}
