<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ChinaRefundService
{
    /**
     * @var string $chinaPaymentRequestID
     */
    protected $chinaPaymentRequestID;

    /**
     * @var string $chinaPaymentRequestToken
     */
    protected $chinaPaymentRequestToken;

    /**
     * @var string $refundReason
     */
    protected $refundReason;

    /**
     * @var boolean $run
     */
    protected $run;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
        $this->run = $run;
    }

    /**
     * @return string
     */
    public function getChinaPaymentRequestID()
    {
        return $this->chinaPaymentRequestID;
    }

    /**
     * @param string $chinaPaymentRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundService
     */
    public function setChinaPaymentRequestID($chinaPaymentRequestID)
    {
        $this->chinaPaymentRequestID = $chinaPaymentRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getChinaPaymentRequestToken()
    {
        return $this->chinaPaymentRequestToken;
    }

    /**
     * @param string $chinaPaymentRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundService
     */
    public function setChinaPaymentRequestToken($chinaPaymentRequestToken)
    {
        $this->chinaPaymentRequestToken = $chinaPaymentRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefundReason()
    {
        return $this->refundReason;
    }

    /**
     * @param string $refundReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundService
     */
    public function setRefundReason($refundReason)
    {
        $this->refundReason = $refundReason;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
