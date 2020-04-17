<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Promotion
{
    /**
     * @var float $discountedAmount
     */
    protected $discountedAmount;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var string $receiptData
     */
    protected $receiptData;

    /**
     * @var float $discountApplied
     */
    protected $discountApplied;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @return float
     */
    public function getDiscountedAmount()
    {
        return $this->discountedAmount;
    }

    /**
     * @param float $discountedAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Promotion
     */
    public function setDiscountedAmount($discountedAmount)
    {
        $this->discountedAmount = $discountedAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Promotion
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Promotion
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiptData()
    {
        return $this->receiptData;
    }

    /**
     * @param string $receiptData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Promotion
     */
    public function setReceiptData($receiptData)
    {
        $this->receiptData = $receiptData;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountApplied()
    {
        return $this->discountApplied;
    }

    /**
     * @param float $discountApplied
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Promotion
     */
    public function setDiscountApplied($discountApplied)
    {
        $this->discountApplied = $discountApplied;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Promotion
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
