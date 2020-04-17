<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APOptionsReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $responseCode
     */
    protected $responseCode;

    /**
     * @var string $offset
     */
    protected $offset;

    /**
     * @var string $count
     */
    protected $count;

    /**
     * @var string $totalCount
     */
    protected $totalCount;

    /**
     * @var APOptionsOption[] $option
     */
    protected $option;

    /**
     * @param int $reasonCode
     */
    public function __construct($reasonCode)
    {
        $this->reasonCode = $reasonCode;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string $responseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsReply
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param string $offset
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsReply
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return string
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param string $count
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsReply
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param string $totalCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsReply
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return APOptionsOption[]
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param APOptionsOption[] $option
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsReply
     */
    public function setOption(array $option = null)
    {
        $this->option = $option;

        return $this;
    }
}
