<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class EmvRequest
{
    /**
     * @var string $combinedTags
     */
    protected $combinedTags;

    /**
     * @var string $repeat
     */
    protected $repeat;

    /**
     * @var string $cardSequenceNumber
     */
    protected $cardSequenceNumber;

    /**
     * @var string $aidAndDFname
     */
    protected $aidAndDFname;

    /**
     * @var string $fallback
     */
    protected $fallback;

    /**
     * @var string $fallbackCondition
     */
    protected $fallbackCondition;

    /**
     * @return string
     */
    public function getCombinedTags()
    {
        return $this->combinedTags;
    }

    /**
     * @param string $combinedTags
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvRequest
     */
    public function setCombinedTags($combinedTags)
    {
        $this->combinedTags = $combinedTags;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * @param string $repeat
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvRequest
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardSequenceNumber()
    {
        return $this->cardSequenceNumber;
    }

    /**
     * @param string $cardSequenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvRequest
     */
    public function setCardSequenceNumber($cardSequenceNumber)
    {
        $this->cardSequenceNumber = $cardSequenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAidAndDFname()
    {
        return $this->aidAndDFname;
    }

    /**
     * @param string $aidAndDFname
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvRequest
     */
    public function setAidAndDFname($aidAndDFname)
    {
        $this->aidAndDFname = $aidAndDFname;

        return $this;
    }

    /**
     * @return string
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * @param string $fallback
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvRequest
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackCondition()
    {
        return $this->fallbackCondition;
    }

    /**
     * @param string $fallbackCondition
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmvRequest
     */
    public function setFallbackCondition($fallbackCondition)
    {
        $this->fallbackCondition = $fallbackCondition;

        return $this;
    }
}
