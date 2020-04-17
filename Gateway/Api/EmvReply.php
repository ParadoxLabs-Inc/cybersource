<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class EmvReply
{
    /**
     * @var string $combinedTags
     */
    protected $combinedTags;

    /**
     * @var string $decryptedRequestTags
     */
    protected $decryptedRequestTags;

    /**
     * @var string $chipValidationResults
     */
    protected $chipValidationResults;

    /**
     * @var string $chipValidationType
     */
    protected $chipValidationType;

    /**
     * @return string
     */
    public function getCombinedTags()
    {
        return $this->combinedTags;
    }

    /**
     * @param string $combinedTags
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvReply
     */
    public function setCombinedTags($combinedTags)
    {
        $this->combinedTags = $combinedTags;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecryptedRequestTags()
    {
        return $this->decryptedRequestTags;
    }

    /**
     * @param string $decryptedRequestTags
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvReply
     */
    public function setDecryptedRequestTags($decryptedRequestTags)
    {
        $this->decryptedRequestTags = $decryptedRequestTags;

        return $this;
    }

    /**
     * @return string
     */
    public function getChipValidationResults()
    {
        return $this->chipValidationResults;
    }

    /**
     * @param string $chipValidationResults
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvReply
     */
    public function setChipValidationResults($chipValidationResults)
    {
        $this->chipValidationResults = $chipValidationResults;

        return $this;
    }

    /**
     * @return string
     */
    public function getChipValidationType()
    {
        return $this->chipValidationType;
    }

    /**
     * @param string $chipValidationType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvReply
     */
    public function setChipValidationType($chipValidationType)
    {
        $this->chipValidationType = $chipValidationType;

        return $this;
    }
}
