<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BusinessRules
{

    /**
     * @var boolean $ignoreAVSResult
     */
    protected $ignoreAVSResult = null;

    /**
     * @var boolean $ignoreCVResult
     */
    protected $ignoreCVResult = null;

    /**
     * @var boolean $ignoreDAVResult
     */
    protected $ignoreDAVResult = null;

    /**
     * @var boolean $ignoreExportResult
     */
    protected $ignoreExportResult = null;

    /**
     * @var boolean $ignoreValidateResult
     */
    protected $ignoreValidateResult = null;

    /**
     * @var string $declineAVSFlags
     */
    protected $declineAVSFlags = null;

    /**
     * @var int $scoreThreshold
     */
    protected $scoreThreshold = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return boolean
     */
    public function getIgnoreAVSResult()
    {
      return $this->ignoreAVSResult;
    }

    /**
     * @param boolean $ignoreAVSResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function setIgnoreAVSResult($ignoreAVSResult)
    {
      $this->ignoreAVSResult = $ignoreAVSResult;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIgnoreCVResult()
    {
      return $this->ignoreCVResult;
    }

    /**
     * @param boolean $ignoreCVResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function setIgnoreCVResult($ignoreCVResult)
    {
      $this->ignoreCVResult = $ignoreCVResult;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIgnoreDAVResult()
    {
      return $this->ignoreDAVResult;
    }

    /**
     * @param boolean $ignoreDAVResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function setIgnoreDAVResult($ignoreDAVResult)
    {
      $this->ignoreDAVResult = $ignoreDAVResult;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIgnoreExportResult()
    {
      return $this->ignoreExportResult;
    }

    /**
     * @param boolean $ignoreExportResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function setIgnoreExportResult($ignoreExportResult)
    {
      $this->ignoreExportResult = $ignoreExportResult;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIgnoreValidateResult()
    {
      return $this->ignoreValidateResult;
    }

    /**
     * @param boolean $ignoreValidateResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function setIgnoreValidateResult($ignoreValidateResult)
    {
      $this->ignoreValidateResult = $ignoreValidateResult;
      return $this;
    }

    /**
     * @return string
     */
    public function getDeclineAVSFlags()
    {
      return $this->declineAVSFlags;
    }

    /**
     * @param string $declineAVSFlags
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function setDeclineAVSFlags($declineAVSFlags)
    {
      $this->declineAVSFlags = $declineAVSFlags;
      return $this;
    }

    /**
     * @return int
     */
    public function getScoreThreshold()
    {
      return $this->scoreThreshold;
    }

    /**
     * @param int $scoreThreshold
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function setScoreThreshold($scoreThreshold)
    {
      $this->scoreThreshold = $scoreThreshold;
      return $this;
    }

}
