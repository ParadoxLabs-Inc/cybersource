<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PaymentInsightsInformation
{
    /**
     * @var string $responseInsightsCategory
     */
    protected $responseInsightsCategory;

    /**
     * @var string $responseInsightsCategoryCode
     */
    protected $responseInsightsCategoryCode;

    /**
     * @var string $processorRawName
     */
    protected $processorRawName;

    /**
     * @var string $orchestration_infoCodes
     */
    protected $orchestration_infoCodes;

    /**
     * @return string
     */
    public function getResponseInsightsCategory()
    {
        return $this->responseInsightsCategory;
    }

    /**
     * @param string $responseInsightsCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentInsightsInformation
     */
    public function setResponseInsightsCategory($responseInsightsCategory)
    {
        $this->responseInsightsCategory = $responseInsightsCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseInsightsCategoryCode()
    {
        return $this->responseInsightsCategoryCode;
    }

    /**
     * @param string $responseInsightsCategoryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentInsightsInformation
     */
    public function setResponseInsightsCategoryCode($responseInsightsCategoryCode)
    {
        $this->responseInsightsCategoryCode = $responseInsightsCategoryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorRawName()
    {
        return $this->processorRawName;
    }

    /**
     * @param string $processorRawName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentInsightsInformation
     */
    public function setProcessorRawName($processorRawName)
    {
        $this->processorRawName = $processorRawName;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrchestration_infoCodes()
    {
        return $this->orchestration_infoCodes;
    }

    /**
     * @param string $orchestration_infoCodes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaymentInsightsInformation
     */
    public function setOrchestration_infoCodes($orchestration_infoCodes)
    {
        $this->orchestration_infoCodes = $orchestration_infoCodes;

        return $this;
    }
}
