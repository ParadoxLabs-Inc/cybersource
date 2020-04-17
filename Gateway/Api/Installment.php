<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Installment
{
    /**
     * @var string $sequence
     */
    protected $sequence;

    /**
     * @var string $totalCount
     */
    protected $totalCount;

    /**
     * @var string $totalAmount
     */
    protected $totalAmount;

    /**
     * @var string $frequency
     */
    protected $frequency;

    /**
     * @var string $amount
     */
    protected $amount;

    /**
     * @var string $planType
     */
    protected $planType;

    /**
     * @var string $firstInstallmentDate
     */
    protected $firstInstallmentDate;

    /**
     * @var string $amountFunded
     */
    protected $amountFunded;

    /**
     * @var string $amountRequestedPercentage
     */
    protected $amountRequestedPercentage;

    /**
     * @var string $expenses
     */
    protected $expenses;

    /**
     * @var string $expensesPercentage
     */
    protected $expensesPercentage;

    /**
     * @var string $fees
     */
    protected $fees;

    /**
     * @var string $feesPercentage
     */
    protected $feesPercentage;

    /**
     * @var string $taxes
     */
    protected $taxes;

    /**
     * @var string $taxesPercentage
     */
    protected $taxesPercentage;

    /**
     * @var string $insurance
     */
    protected $insurance;

    /**
     * @var string $insurancePercentage
     */
    protected $insurancePercentage;

    /**
     * @var string $additionalCosts
     */
    protected $additionalCosts;

    /**
     * @var string $additionalCostsPercentage
     */
    protected $additionalCostsPercentage;

    /**
     * @var string $monthlyInterestRate
     */
    protected $monthlyInterestRate;

    /**
     * @var string $annualInterestRate
     */
    protected $annualInterestRate;

    /**
     * @var string $annualFinancingCost
     */
    protected $annualFinancingCost;

    /**
     * @var string $paymentType
     */
    protected $paymentType;

    /**
     * @var string $invoiceData
     */
    protected $invoiceData;

    /**
     * @var string $downPayment
     */
    protected $downPayment;

    /**
     * @var string $firstInstallmentAmount
     */
    protected $firstInstallmentAmount;

    /**
     * @var string $minimumTotalCount
     */
    protected $minimumTotalCount;

    /**
     * @var string $maximumTotalCount
     */
    protected $maximumTotalCount;

    /**
     * @var string $gracePeriodDuration
     */
    protected $gracePeriodDuration;

    /**
     * @var string $gracePeriodDurationType
     */
    protected $gracePeriodDurationType;

    /**
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param string $sequence
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param string $totalCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param string $totalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlanType()
    {
        return $this->planType;
    }

    /**
     * @param string $planType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setPlanType($planType)
    {
        $this->planType = $planType;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstInstallmentDate()
    {
        return $this->firstInstallmentDate;
    }

    /**
     * @param string $firstInstallmentDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setFirstInstallmentDate($firstInstallmentDate)
    {
        $this->firstInstallmentDate = $firstInstallmentDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmountFunded()
    {
        return $this->amountFunded;
    }

    /**
     * @param string $amountFunded
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setAmountFunded($amountFunded)
    {
        $this->amountFunded = $amountFunded;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmountRequestedPercentage()
    {
        return $this->amountRequestedPercentage;
    }

    /**
     * @param string $amountRequestedPercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setAmountRequestedPercentage($amountRequestedPercentage)
    {
        $this->amountRequestedPercentage = $amountRequestedPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpenses()
    {
        return $this->expenses;
    }

    /**
     * @param string $expenses
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setExpenses($expenses)
    {
        $this->expenses = $expenses;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpensesPercentage()
    {
        return $this->expensesPercentage;
    }

    /**
     * @param string $expensesPercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setExpensesPercentage($expensesPercentage)
    {
        $this->expensesPercentage = $expensesPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @param string $fees
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setFees($fees)
    {
        $this->fees = $fees;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeesPercentage()
    {
        return $this->feesPercentage;
    }

    /**
     * @param string $feesPercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setFeesPercentage($feesPercentage)
    {
        $this->feesPercentage = $feesPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param string $taxes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxesPercentage()
    {
        return $this->taxesPercentage;
    }

    /**
     * @param string $taxesPercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setTaxesPercentage($taxesPercentage)
    {
        $this->taxesPercentage = $taxesPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getInsurance()
    {
        return $this->insurance;
    }

    /**
     * @param string $insurance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setInsurance($insurance)
    {
        $this->insurance = $insurance;

        return $this;
    }

    /**
     * @return string
     */
    public function getInsurancePercentage()
    {
        return $this->insurancePercentage;
    }

    /**
     * @param string $insurancePercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setInsurancePercentage($insurancePercentage)
    {
        $this->insurancePercentage = $insurancePercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalCosts()
    {
        return $this->additionalCosts;
    }

    /**
     * @param string $additionalCosts
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setAdditionalCosts($additionalCosts)
    {
        $this->additionalCosts = $additionalCosts;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalCostsPercentage()
    {
        return $this->additionalCostsPercentage;
    }

    /**
     * @param string $additionalCostsPercentage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setAdditionalCostsPercentage($additionalCostsPercentage)
    {
        $this->additionalCostsPercentage = $additionalCostsPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getMonthlyInterestRate()
    {
        return $this->monthlyInterestRate;
    }

    /**
     * @param string $monthlyInterestRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setMonthlyInterestRate($monthlyInterestRate)
    {
        $this->monthlyInterestRate = $monthlyInterestRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAnnualInterestRate()
    {
        return $this->annualInterestRate;
    }

    /**
     * @param string $annualInterestRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setAnnualInterestRate($annualInterestRate)
    {
        $this->annualInterestRate = $annualInterestRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAnnualFinancingCost()
    {
        return $this->annualFinancingCost;
    }

    /**
     * @param string $annualFinancingCost
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setAnnualFinancingCost($annualFinancingCost)
    {
        $this->annualFinancingCost = $annualFinancingCost;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceData()
    {
        return $this->invoiceData;
    }

    /**
     * @param string $invoiceData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setInvoiceData($invoiceData)
    {
        $this->invoiceData = $invoiceData;

        return $this;
    }

    /**
     * @return string
     */
    public function getDownPayment()
    {
        return $this->downPayment;
    }

    /**
     * @param string $downPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setDownPayment($downPayment)
    {
        $this->downPayment = $downPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstInstallmentAmount()
    {
        return $this->firstInstallmentAmount;
    }

    /**
     * @param string $firstInstallmentAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setFirstInstallmentAmount($firstInstallmentAmount)
    {
        $this->firstInstallmentAmount = $firstInstallmentAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getMinimumTotalCount()
    {
        return $this->minimumTotalCount;
    }

    /**
     * @param string $minimumTotalCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setMinimumTotalCount($minimumTotalCount)
    {
        $this->minimumTotalCount = $minimumTotalCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getMaximumTotalCount()
    {
        return $this->maximumTotalCount;
    }

    /**
     * @param string $maximumTotalCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setMaximumTotalCount($maximumTotalCount)
    {
        $this->maximumTotalCount = $maximumTotalCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getGracePeriodDuration()
    {
        return $this->gracePeriodDuration;
    }

    /**
     * @param string $gracePeriodDuration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setGracePeriodDuration($gracePeriodDuration)
    {
        $this->gracePeriodDuration = $gracePeriodDuration;

        return $this;
    }

    /**
     * @return string
     */
    public function getGracePeriodDurationType()
    {
        return $this->gracePeriodDurationType;
    }

    /**
     * @param string $gracePeriodDurationType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Installment
     */
    public function setGracePeriodDurationType($gracePeriodDurationType)
    {
        $this->gracePeriodDurationType = $gracePeriodDurationType;

        return $this;
    }
}
