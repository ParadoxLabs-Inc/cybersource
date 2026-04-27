<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class FXRatesReply
{
    /**
     * @var FXQuote[] $quote
     */
    protected $quote;

    /**
     * @param int $reasonCode
     */
    public function __construct(protected $reasonCode)
    {
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXRatesReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return FXQuote[]
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param FXQuote[] $quote
     * @return \ParadoxLabs\CyberSource\Gateway\Api\FXRatesReply
     */
    public function setQuote(?array $quote = null)
    {
        $this->quote = $quote;

        return $this;
    }
}
