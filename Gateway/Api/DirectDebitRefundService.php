<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DirectDebitRefundService
{
    /**
     * @var string $directDebitRequestID
     */
    protected $directDebitRequestID;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $directDebitRequestToken
     */
    protected $directDebitRequestToken;

    /**
     * @var string $directDebitType
     */
    protected $directDebitType;

    /**
     * @var string $recurringType
     */
    protected $recurringType;

    /**
     * @var string $mandateID
     */
    protected $mandateID;

    /**
     * @var string $mandateAuthenticationDate
     */
    protected $mandateAuthenticationDate;

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
    public function getDirectDebitRequestID()
    {
        return $this->directDebitRequestID;
    }

    /**
     * @param string $directDebitRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setDirectDebitRequestID($directDebitRequestID)
    {
        $this->directDebitRequestID = $directDebitRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectDebitRequestToken()
    {
        return $this->directDebitRequestToken;
    }

    /**
     * @param string $directDebitRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setDirectDebitRequestToken($directDebitRequestToken)
    {
        $this->directDebitRequestToken = $directDebitRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectDebitType()
    {
        return $this->directDebitType;
    }

    /**
     * @param string $directDebitType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setDirectDebitType($directDebitType)
    {
        $this->directDebitType = $directDebitType;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecurringType()
    {
        return $this->recurringType;
    }

    /**
     * @param string $recurringType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setRecurringType($recurringType)
    {
        $this->recurringType = $recurringType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandateID()
    {
        return $this->mandateID;
    }

    /**
     * @param string $mandateID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setMandateID($mandateID)
    {
        $this->mandateID = $mandateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandateAuthenticationDate()
    {
        return $this->mandateAuthenticationDate;
    }

    /**
     * @param string $mandateAuthenticationDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setMandateAuthenticationDate($mandateAuthenticationDate)
    {
        $this->mandateAuthenticationDate = $mandateAuthenticationDate;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
