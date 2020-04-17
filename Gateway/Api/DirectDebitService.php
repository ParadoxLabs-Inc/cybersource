<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DirectDebitService
{
    /**
     * @var string $dateCollect
     */
    protected $dateCollect;

    /**
     * @var string $directDebitText
     */
    protected $directDebitText;

    /**
     * @var string $authorizationID
     */
    protected $authorizationID;

    /**
     * @var string $transactionType
     */
    protected $transactionType;

    /**
     * @var string $directDebitType
     */
    protected $directDebitType;

    /**
     * @var string $validateRequestID
     */
    protected $validateRequestID;

    /**
     * @var string $recurringType
     */
    protected $recurringType;

    /**
     * @var string $mandateID
     */
    protected $mandateID;

    /**
     * @var string $validateRequestToken
     */
    protected $validateRequestToken;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

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
    public function getDateCollect()
    {
        return $this->dateCollect;
    }

    /**
     * @param string $dateCollect
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setDateCollect($dateCollect)
    {
        $this->dateCollect = $dateCollect;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectDebitText()
    {
        return $this->directDebitText;
    }

    /**
     * @param string $directDebitText
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setDirectDebitText($directDebitText)
    {
        $this->directDebitText = $directDebitText;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationID()
    {
        return $this->authorizationID;
    }

    /**
     * @param string $authorizationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setAuthorizationID($authorizationID)
    {
        $this->authorizationID = $authorizationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setDirectDebitType($directDebitType)
    {
        $this->directDebitType = $directDebitType;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidateRequestID()
    {
        return $this->validateRequestID;
    }

    /**
     * @param string $validateRequestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setValidateRequestID($validateRequestID)
    {
        $this->validateRequestID = $validateRequestID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setMandateID($mandateID)
    {
        $this->mandateID = $mandateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidateRequestToken()
    {
        return $this->validateRequestToken;
    }

    /**
     * @param string $validateRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setValidateRequestToken($validateRequestToken)
    {
        $this->validateRequestToken = $validateRequestToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
