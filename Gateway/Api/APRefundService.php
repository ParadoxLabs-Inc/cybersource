<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APRefundService
{
    /**
     * @var string $captureRequestID
     */
    protected $captureRequestID;

    /**
     * @var string $refundRequestID
     */
    protected $refundRequestID;

    /**
     * @var string $reason
     */
    protected $reason;

    /**
     * @var string $instant
     */
    protected $instant;

    /**
     * @var string $note
     */
    protected $note;

    /**
     * @var string $apInitiateRequestID
     */
    protected $apInitiateRequestID;

    /**
     * @var string $returnRef
     */
    protected $returnRef;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $saleRequestID
     */
    protected $saleRequestID;

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
    public function getCaptureRequestID()
    {
        return $this->captureRequestID;
    }

    /**
     * @param string $captureRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setCaptureRequestID($captureRequestID)
    {
        $this->captureRequestID = $captureRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefundRequestID()
    {
        return $this->refundRequestID;
    }

    /**
     * @param string $refundRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setRefundRequestID($refundRequestID)
    {
        $this->refundRequestID = $refundRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstant()
    {
        return $this->instant;
    }

    /**
     * @param string $instant
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setInstant($instant)
    {
        $this->instant = $instant;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return string
     */
    public function getApInitiateRequestID()
    {
        return $this->apInitiateRequestID;
    }

    /**
     * @param string $apInitiateRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setApInitiateRequestID($apInitiateRequestID)
    {
        $this->apInitiateRequestID = $apInitiateRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnRef()
    {
        return $this->returnRef;
    }

    /**
     * @param string $returnRef
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setReturnRef($returnRef)
    {
        $this->returnRef = $returnRef;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationID()
    {
        return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaleRequestID()
    {
        return $this->saleRequestID;
    }

    /**
     * @param string $saleRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setSaleRequestID($saleRequestID)
    {
        $this->saleRequestID = $saleRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APRefundService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
