<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayPalTransactionSearchReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var PaypalTransaction[] $transaction
     */
    protected $transaction;

    /**
     * @var string $errorCode
     */
    protected $errorCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return PaypalTransaction[]
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param PaypalTransaction[] $transaction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchReply
     */
    public function setTransaction(array $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchReply
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
