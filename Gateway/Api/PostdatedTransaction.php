<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PostdatedTransaction
{
    /**
     * @var string $guaranteeIndicator
     */
    protected $guaranteeIndicator;

    /**
     * @var string $guaranteeAmount
     */
    protected $guaranteeAmount;

    /**
     * @var int $settlementDate
     */
    protected $settlementDate;

    /**
     * @return string
     */
    public function getGuaranteeIndicator()
    {
        return $this->guaranteeIndicator;
    }

    /**
     * @param string $guaranteeIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PostdatedTransaction
     */
    public function setGuaranteeIndicator($guaranteeIndicator)
    {
        $this->guaranteeIndicator = $guaranteeIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getGuaranteeAmount()
    {
        return $this->guaranteeAmount;
    }

    /**
     * @param string $guaranteeAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PostdatedTransaction
     */
    public function setGuaranteeAmount($guaranteeAmount)
    {
        $this->guaranteeAmount = $guaranteeAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getSettlementDate()
    {
        return $this->settlementDate;
    }

    /**
     * @param int $settlementDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PostdatedTransaction
     */
    public function setSettlementDate($settlementDate)
    {
        $this->settlementDate = $settlementDate;

        return $this;
    }
}
