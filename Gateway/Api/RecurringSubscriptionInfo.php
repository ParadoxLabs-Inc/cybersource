<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class RecurringSubscriptionInfo
{
    /**
     * @var string $subscriptionID
     */
    protected $subscriptionID;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var int $numberOfPayments
     */
    protected $numberOfPayments;

    /**
     * @var int $numberOfPaymentsToAdd
     */
    protected $numberOfPaymentsToAdd;

    /**
     * @var boolean $automaticRenew
     */
    protected $automaticRenew;

    /**
     * @var string $frequency
     */
    protected $frequency;

    /**
     * @var string $startDate
     */
    protected $startDate;

    /**
     * @var string $endDate
     */
    protected $endDate;

    /**
     * @var boolean $approvalRequired
     */
    protected $approvalRequired;

    /**
     * @var PaySubscriptionEvent $event
     */
    protected $event;

    /**
     * @var boolean $billPayment
     */
    protected $billPayment;

    /**
     * @return string
     */
    public function getSubscriptionID()
    {
        return $this->subscriptionID;
    }

    /**
     * @param string $subscriptionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setSubscriptionID($subscriptionID)
    {
        $this->subscriptionID = $subscriptionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfPayments()
    {
        return $this->numberOfPayments;
    }

    /**
     * @param int $numberOfPayments
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setNumberOfPayments($numberOfPayments)
    {
        $this->numberOfPayments = $numberOfPayments;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfPaymentsToAdd()
    {
        return $this->numberOfPaymentsToAdd;
    }

    /**
     * @param int $numberOfPaymentsToAdd
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setNumberOfPaymentsToAdd($numberOfPaymentsToAdd)
    {
        $this->numberOfPaymentsToAdd = $numberOfPaymentsToAdd;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAutomaticRenew()
    {
        return $this->automaticRenew;
    }

    /**
     * @param boolean $automaticRenew
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setAutomaticRenew($automaticRenew)
    {
        $this->automaticRenew = $automaticRenew;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param string $frequency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getApprovalRequired()
    {
        return $this->approvalRequired;
    }

    /**
     * @param boolean $approvalRequired
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setApprovalRequired($approvalRequired)
    {
        $this->approvalRequired = $approvalRequired;

        return $this;
    }

    /**
     * @return PaySubscriptionEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param PaySubscriptionEvent $event
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getBillPayment()
    {
        return $this->billPayment;
    }

    /**
     * @param boolean $billPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function setBillPayment($billPayment)
    {
        $this->billPayment = $billPayment;

        return $this;
    }
}
