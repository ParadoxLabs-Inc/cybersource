<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Recurring
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $validationIndicator
     */
    protected $validationIndicator;

    /**
     * @var string[] $maximumAmount
     */
    protected $maximumAmount;

    /**
     * @var string[] $referenceNumber
     */
    protected $referenceNumber;

    /**
     * @var string[] $occurrence
     */
    protected $occurrence;

    /**
     * @var string[] $numberOfPayments
     */
    protected $numberOfPayments;

    /**
     * @var string $amountType
     */
    protected $amountType;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recurring
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidationIndicator()
    {
        return $this->validationIndicator;
    }

    /**
     * @param string $validationIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recurring
     */
    public function setValidationIndicator($validationIndicator)
    {
        $this->validationIndicator = $validationIndicator;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMaximumAmount()
    {
        return $this->maximumAmount;
    }

    /**
     * @param string[] $maximumAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recurring
     */
    public function setMaximumAmount(?array $maximumAmount = null)
    {
        $this->maximumAmount = $maximumAmount;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @param string[] $referenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recurring
     */
    public function setReferenceNumber(?array $referenceNumber = null)
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOccurrence()
    {
        return $this->occurrence;
    }

    /**
     * @param string[] $occurrence
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recurring
     */
    public function setOccurrence(?array $occurrence = null)
    {
        $this->occurrence = $occurrence;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getNumberOfPayments()
    {
        return $this->numberOfPayments;
    }

    /**
     * @param string[] $numberOfPayments
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recurring
     */
    public function setNumberOfPayments(?array $numberOfPayments = null)
    {
        $this->numberOfPayments = $numberOfPayments;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * @param string $amountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Recurring
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;

        return $this;
    }
}
