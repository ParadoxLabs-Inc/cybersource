<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class BankTransferRefundService
{
    /**
     * @var string $bankTransferRequestID
     */
    protected $bankTransferRequestID;

    /**
     * @var string $bankTransferRealTimeRequestID
     */
    protected $bankTransferRealTimeRequestID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $bankTransferRealTimeReconciliationID
     */
    protected $bankTransferRealTimeReconciliationID;

    /**
     * @var string $bankTransferRequestToken
     */
    protected $bankTransferRequestToken;

    /**
     * @var string $bankTransferRealTimeRequestToken
     */
    protected $bankTransferRealTimeRequestToken;

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
    public function getBankTransferRequestID()
    {
        return $this->bankTransferRequestID;
    }

    /**
     * @param string $bankTransferRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService
     */
    public function setBankTransferRequestID($bankTransferRequestID)
    {
        $this->bankTransferRequestID = $bankTransferRequestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankTransferRealTimeRequestID()
    {
        return $this->bankTransferRealTimeRequestID;
    }

    /**
     * @param string $bankTransferRealTimeRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService
     */
    public function setBankTransferRealTimeRequestID($bankTransferRealTimeRequestID)
    {
        $this->bankTransferRealTimeRequestID = $bankTransferRealTimeRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankTransferRealTimeReconciliationID()
    {
        return $this->bankTransferRealTimeReconciliationID;
    }

    /**
     * @param string $bankTransferRealTimeReconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService
     */
    public function setBankTransferRealTimeReconciliationID($bankTransferRealTimeReconciliationID)
    {
        $this->bankTransferRealTimeReconciliationID = $bankTransferRealTimeReconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankTransferRequestToken()
    {
        return $this->bankTransferRequestToken;
    }

    /**
     * @param string $bankTransferRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService
     */
    public function setBankTransferRequestToken($bankTransferRequestToken)
    {
        $this->bankTransferRequestToken = $bankTransferRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankTransferRealTimeRequestToken()
    {
        return $this->bankTransferRealTimeRequestToken;
    }

    /**
     * @param string $bankTransferRealTimeRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService
     */
    public function setBankTransferRealTimeRequestToken($bankTransferRealTimeRequestToken)
    {
        $this->bankTransferRealTimeRequestToken = $bankTransferRealTimeRequestToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
