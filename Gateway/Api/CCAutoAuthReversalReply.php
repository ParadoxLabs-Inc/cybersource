<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CCAutoAuthReversalReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $result
     */
    protected $result;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorResponse()
    {
        return $this->processorResponse;
    }

    /**
     * @param string $processorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param string $result
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalReply
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
}
