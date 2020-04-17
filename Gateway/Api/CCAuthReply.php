<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class CCAuthReply
{
    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var float $amount
     */
    protected $amount;

    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $avsCode
     */
    protected $avsCode;

    /**
     * @var string $avsCodeRaw
     */
    protected $avsCodeRaw;

    /**
     * @var string $cvCode
     */
    protected $cvCode;

    /**
     * @var string $cvCodeRaw
     */
    protected $cvCodeRaw;

    /**
     * @var string $personalIDCode
     */
    protected $personalIDCode;

    /**
     * @var \DateTime $authorizedDateTime
     */
    protected $authorizedDateTime;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var string $bmlAccountNumber
     */
    protected $bmlAccountNumber;

    /**
     * @var string $authFactorCode
     */
    protected $authFactorCode;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var FundingTotals $fundingTotals
     */
    protected $fundingTotals;

    /**
     * @var string $fxQuoteID
     */
    protected $fxQuoteID;

    /**
     * @var \DateTime $fxQuoteRate
     */
    protected $fxQuoteRate;

    /**
     * @var string $fxQuoteType
     */
    protected $fxQuoteType;

    /**
     * @var \DateTime $fxQuoteExpirationDateTime
     */
    protected $fxQuoteExpirationDateTime;

    /**
     * @var string $authRecord
     */
    protected $authRecord;

    /**
     * @var string $merchantAdviceCode
     */
    protected $merchantAdviceCode;

    /**
     * @var string $merchantAdviceCodeRaw
     */
    protected $merchantAdviceCodeRaw;

    /**
     * @var string $cavvResponseCode
     */
    protected $cavvResponseCode;

    /**
     * @var string $cavvResponseCodeRaw
     */
    protected $cavvResponseCodeRaw;

    /**
     * @var string $authenticationXID
     */
    protected $authenticationXID;

    /**
     * @var string $authorizationXID
     */
    protected $authorizationXID;

    /**
     * @var string $processorCardType
     */
    protected $processorCardType;

    /**
     * @var float $accountBalance
     */
    protected $accountBalance;

    /**
     * @var string $forwardCode
     */
    protected $forwardCode;

    /**
     * @var string $enhancedDataEnabled
     */
    protected $enhancedDataEnabled;

    /**
     * @var string $referralResponseNumber
     */
    protected $referralResponseNumber;

    /**
     * @var string $subResponseCode
     */
    protected $subResponseCode;

    /**
     * @var string $approvedAmount
     */
    protected $approvedAmount;

    /**
     * @var string $creditLine
     */
    protected $creditLine;

    /**
     * @var string $approvedTerms
     */
    protected $approvedTerms;

    /**
     * @var string $paymentNetworkTransactionID
     */
    protected $paymentNetworkTransactionID;

    /**
     * @var string $cardCategory
     */
    protected $cardCategory;

    /**
     * @var string $ownerMerchantID
     */
    protected $ownerMerchantID;

    /**
     * @var float $requestAmount
     */
    protected $requestAmount;

    /**
     * @var string $requestCurrency
     */
    protected $requestCurrency;

    /**
     * @var string $accountBalanceCurrency
     */
    protected $accountBalanceCurrency;

    /**
     * @var string $accountBalanceSign
     */
    protected $accountBalanceSign;

    /**
     * @var string $amountType
     */
    protected $amountType;

    /**
     * @var string $accountType
     */
    protected $accountType;

    /**
     * @var string $affluenceIndicator
     */
    protected $affluenceIndicator;

    /**
     * @var string $evEmail
     */
    protected $evEmail;

    /**
     * @var string $evPhoneNumber
     */
    protected $evPhoneNumber;

    /**
     * @var string $evPostalCode
     */
    protected $evPostalCode;

    /**
     * @var string $evName
     */
    protected $evName;

    /**
     * @var string $evStreet
     */
    protected $evStreet;

    /**
     * @var string $evEmailRaw
     */
    protected $evEmailRaw;

    /**
     * @var string $evPhoneNumberRaw
     */
    protected $evPhoneNumberRaw;

    /**
     * @var string $evPostalCodeRaw
     */
    protected $evPostalCodeRaw;

    /**
     * @var string $evNameRaw
     */
    protected $evNameRaw;

    /**
     * @var string $evStreetRaw
     */
    protected $evStreetRaw;

    /**
     * @var string $cardGroup
     */
    protected $cardGroup;

    /**
     * @var string $posData
     */
    protected $posData;

    /**
     * @var string $transactionID
     */
    protected $transactionID;

    /**
     * @var string $cardIssuerCountry
     */
    protected $cardIssuerCountry;

    /**
     * @var string $cardRegulated
     */
    protected $cardRegulated;

    /**
     * @var string $cardCommercial
     */
    protected $cardCommercial;

    /**
     * @var string $cardPrepaid
     */
    protected $cardPrepaid;

    /**
     * @var string $cardPayroll
     */
    protected $cardPayroll;

    /**
     * @var string $cardHealthcare
     */
    protected $cardHealthcare;

    /**
     * @var string $cardSignatureDebit
     */
    protected $cardSignatureDebit;

    /**
     * @var string $cardPINlessDebit
     */
    protected $cardPINlessDebit;

    /**
     * @var string $cardLevel3Eligible
     */
    protected $cardLevel3Eligible;

    /**
     * @var string $processorTransactionID
     */
    protected $processorTransactionID;

    /**
     * @var string $providerReasonCode
     */
    protected $providerReasonCode;

    /**
     * @var string $providerReasonDescription
     */
    protected $providerReasonDescription;

    /**
     * @var string $providerPassThroughData
     */
    protected $providerPassThroughData;

    /**
     * @var string $providerCVNResponseCode
     */
    protected $providerCVNResponseCode;

    /**
     * @var string $providerAVSResponseCode
     */
    protected $providerAVSResponseCode;

    /**
     * @var string $providerAcquirerBankCode
     */
    protected $providerAcquirerBankCode;

    /**
     * @var string $paymentCardService
     */
    protected $paymentCardService;

    /**
     * @var string $paymentCardServiceResult
     */
    protected $paymentCardServiceResult;

    /**
     * @var string $transactionQualification
     */
    protected $transactionQualification;

    /**
     * @var string $transactionIntegrity
     */
    protected $transactionIntegrity;

    /**
     * @var string $emsTransactionRiskScore
     */
    protected $emsTransactionRiskScore;

    /**
     * @var string $reconciliationReferenceNumber
     */
    protected $reconciliationReferenceNumber;

    /**
     * @var string $cardReferenceData
     */
    protected $cardReferenceData;

    /**
     * @var string $partialPANandIBAN
     */
    protected $partialPANandIBAN;

    /**
     * @var string $issuerPINrequest
     */
    protected $issuerPINrequest;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * @param string $authorizationCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCode()
    {
        return $this->avsCode;
    }

    /**
     * @param string $avsCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAvsCode($avsCode)
    {
        $this->avsCode = $avsCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvsCodeRaw()
    {
        return $this->avsCodeRaw;
    }

    /**
     * @param string $avsCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAvsCodeRaw($avsCodeRaw)
    {
        $this->avsCodeRaw = $avsCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvCode()
    {
        return $this->cvCode;
    }

    /**
     * @param string $cvCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCvCode($cvCode)
    {
        $this->cvCode = $cvCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvCodeRaw()
    {
        return $this->cvCodeRaw;
    }

    /**
     * @param string $cvCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCvCodeRaw($cvCodeRaw)
    {
        $this->cvCodeRaw = $cvCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonalIDCode()
    {
        return $this->personalIDCode;
    }

    /**
     * @param string $personalIDCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setPersonalIDCode($personalIDCode)
    {
        $this->personalIDCode = $personalIDCode;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAuthorizedDateTime()
    {
        if ($this->authorizedDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->authorizedDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $authorizedDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAuthorizedDateTime(DateTime $authorizedDateTime = null)
    {
        if ($authorizedDateTime == null) {
            $this->authorizedDateTime = null;
        } else {
            $this->authorizedDateTime = $authorizedDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorResponse()
    {
        return $this->processorResponse;
    }

    /**
     * @param string $processorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getBmlAccountNumber()
    {
        return $this->bmlAccountNumber;
    }

    /**
     * @param string $bmlAccountNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setBmlAccountNumber($bmlAccountNumber)
    {
        $this->bmlAccountNumber = $bmlAccountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthFactorCode()
    {
        return $this->authFactorCode;
    }

    /**
     * @param string $authFactorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAuthFactorCode($authFactorCode)
    {
        $this->authFactorCode = $authFactorCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return FundingTotals
     */
    public function getFundingTotals()
    {
        return $this->fundingTotals;
    }

    /**
     * @param FundingTotals $fundingTotals
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setFundingTotals($fundingTotals)
    {
        $this->fundingTotals = $fundingTotals;

        return $this;
    }

    /**
     * @return string
     */
    public function getFxQuoteID()
    {
        return $this->fxQuoteID;
    }

    /**
     * @param string $fxQuoteID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setFxQuoteID($fxQuoteID)
    {
        $this->fxQuoteID = $fxQuoteID;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFxQuoteRate()
    {
        if ($this->fxQuoteRate == null) {
            return null;
        } else {
            try {
                return new DateTime($this->fxQuoteRate);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $fxQuoteRate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setFxQuoteRate(DateTime $fxQuoteRate = null)
    {
        if ($fxQuoteRate == null) {
            $this->fxQuoteRate = null;
        } else {
            $this->fxQuoteRate = $fxQuoteRate->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFxQuoteType()
    {
        return $this->fxQuoteType;
    }

    /**
     * @param string $fxQuoteType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setFxQuoteType($fxQuoteType)
    {
        $this->fxQuoteType = $fxQuoteType;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFxQuoteExpirationDateTime()
    {
        if ($this->fxQuoteExpirationDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->fxQuoteExpirationDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $fxQuoteExpirationDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setFxQuoteExpirationDateTime(DateTime $fxQuoteExpirationDateTime = null)
    {
        if ($fxQuoteExpirationDateTime == null) {
            $this->fxQuoteExpirationDateTime = null;
        } else {
            $this->fxQuoteExpirationDateTime = $fxQuoteExpirationDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthRecord()
    {
        return $this->authRecord;
    }

    /**
     * @param string $authRecord
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAuthRecord($authRecord)
    {
        $this->authRecord = $authRecord;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantAdviceCode()
    {
        return $this->merchantAdviceCode;
    }

    /**
     * @param string $merchantAdviceCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setMerchantAdviceCode($merchantAdviceCode)
    {
        $this->merchantAdviceCode = $merchantAdviceCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantAdviceCodeRaw()
    {
        return $this->merchantAdviceCodeRaw;
    }

    /**
     * @param string $merchantAdviceCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setMerchantAdviceCodeRaw($merchantAdviceCodeRaw)
    {
        $this->merchantAdviceCodeRaw = $merchantAdviceCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCavvResponseCode()
    {
        return $this->cavvResponseCode;
    }

    /**
     * @param string $cavvResponseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCavvResponseCode($cavvResponseCode)
    {
        $this->cavvResponseCode = $cavvResponseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCavvResponseCodeRaw()
    {
        return $this->cavvResponseCodeRaw;
    }

    /**
     * @param string $cavvResponseCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCavvResponseCodeRaw($cavvResponseCodeRaw)
    {
        $this->cavvResponseCodeRaw = $cavvResponseCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationXID()
    {
        return $this->authenticationXID;
    }

    /**
     * @param string $authenticationXID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAuthenticationXID($authenticationXID)
    {
        $this->authenticationXID = $authenticationXID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationXID()
    {
        return $this->authorizationXID;
    }

    /**
     * @param string $authorizationXID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAuthorizationXID($authorizationXID)
    {
        $this->authorizationXID = $authorizationXID;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorCardType()
    {
        return $this->processorCardType;
    }

    /**
     * @param string $processorCardType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProcessorCardType($processorCardType)
    {
        $this->processorCardType = $processorCardType;

        return $this;
    }

    /**
     * @return float
     */
    public function getAccountBalance()
    {
        return $this->accountBalance;
    }

    /**
     * @param float $accountBalance
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAccountBalance($accountBalance)
    {
        $this->accountBalance = $accountBalance;

        return $this;
    }

    /**
     * @return string
     */
    public function getForwardCode()
    {
        return $this->forwardCode;
    }

    /**
     * @param string $forwardCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setForwardCode($forwardCode)
    {
        $this->forwardCode = $forwardCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnhancedDataEnabled()
    {
        return $this->enhancedDataEnabled;
    }

    /**
     * @param string $enhancedDataEnabled
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEnhancedDataEnabled($enhancedDataEnabled)
    {
        $this->enhancedDataEnabled = $enhancedDataEnabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferralResponseNumber()
    {
        return $this->referralResponseNumber;
    }

    /**
     * @param string $referralResponseNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setReferralResponseNumber($referralResponseNumber)
    {
        $this->referralResponseNumber = $referralResponseNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubResponseCode()
    {
        return $this->subResponseCode;
    }

    /**
     * @param string $subResponseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setSubResponseCode($subResponseCode)
    {
        $this->subResponseCode = $subResponseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getApprovedAmount()
    {
        return $this->approvedAmount;
    }

    /**
     * @param string $approvedAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setApprovedAmount($approvedAmount)
    {
        $this->approvedAmount = $approvedAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreditLine()
    {
        return $this->creditLine;
    }

    /**
     * @param string $creditLine
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCreditLine($creditLine)
    {
        $this->creditLine = $creditLine;

        return $this;
    }

    /**
     * @return string
     */
    public function getApprovedTerms()
    {
        return $this->approvedTerms;
    }

    /**
     * @param string $approvedTerms
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setApprovedTerms($approvedTerms)
    {
        $this->approvedTerms = $approvedTerms;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentNetworkTransactionID()
    {
        return $this->paymentNetworkTransactionID;
    }

    /**
     * @param string $paymentNetworkTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setPaymentNetworkTransactionID($paymentNetworkTransactionID)
    {
        $this->paymentNetworkTransactionID = $paymentNetworkTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardCategory()
    {
        return $this->cardCategory;
    }

    /**
     * @param string $cardCategory
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardCategory($cardCategory)
    {
        $this->cardCategory = $cardCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwnerMerchantID()
    {
        return $this->ownerMerchantID;
    }

    /**
     * @param string $ownerMerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setOwnerMerchantID($ownerMerchantID)
    {
        $this->ownerMerchantID = $ownerMerchantID;

        return $this;
    }

    /**
     * @return float
     */
    public function getRequestAmount()
    {
        return $this->requestAmount;
    }

    /**
     * @param float $requestAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setRequestAmount($requestAmount)
    {
        $this->requestAmount = $requestAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestCurrency()
    {
        return $this->requestCurrency;
    }

    /**
     * @param string $requestCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setRequestCurrency($requestCurrency)
    {
        $this->requestCurrency = $requestCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountBalanceCurrency()
    {
        return $this->accountBalanceCurrency;
    }

    /**
     * @param string $accountBalanceCurrency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAccountBalanceCurrency($accountBalanceCurrency)
    {
        $this->accountBalanceCurrency = $accountBalanceCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountBalanceSign()
    {
        return $this->accountBalanceSign;
    }

    /**
     * @param string $accountBalanceSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAccountBalanceSign($accountBalanceSign)
    {
        $this->accountBalanceSign = $accountBalanceSign;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmountType()
    {
        return $this->amountType;
    }

    /**
     * @param string $amountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAmountType($amountType)
    {
        $this->amountType = $amountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAffluenceIndicator()
    {
        return $this->affluenceIndicator;
    }

    /**
     * @param string $affluenceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setAffluenceIndicator($affluenceIndicator)
    {
        $this->affluenceIndicator = $affluenceIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvEmail()
    {
        return $this->evEmail;
    }

    /**
     * @param string $evEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvEmail($evEmail)
    {
        $this->evEmail = $evEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvPhoneNumber()
    {
        return $this->evPhoneNumber;
    }

    /**
     * @param string $evPhoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvPhoneNumber($evPhoneNumber)
    {
        $this->evPhoneNumber = $evPhoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvPostalCode()
    {
        return $this->evPostalCode;
    }

    /**
     * @param string $evPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvPostalCode($evPostalCode)
    {
        $this->evPostalCode = $evPostalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvName()
    {
        return $this->evName;
    }

    /**
     * @param string $evName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvName($evName)
    {
        $this->evName = $evName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvStreet()
    {
        return $this->evStreet;
    }

    /**
     * @param string $evStreet
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvStreet($evStreet)
    {
        $this->evStreet = $evStreet;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvEmailRaw()
    {
        return $this->evEmailRaw;
    }

    /**
     * @param string $evEmailRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvEmailRaw($evEmailRaw)
    {
        $this->evEmailRaw = $evEmailRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvPhoneNumberRaw()
    {
        return $this->evPhoneNumberRaw;
    }

    /**
     * @param string $evPhoneNumberRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvPhoneNumberRaw($evPhoneNumberRaw)
    {
        $this->evPhoneNumberRaw = $evPhoneNumberRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvPostalCodeRaw()
    {
        return $this->evPostalCodeRaw;
    }

    /**
     * @param string $evPostalCodeRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvPostalCodeRaw($evPostalCodeRaw)
    {
        $this->evPostalCodeRaw = $evPostalCodeRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvNameRaw()
    {
        return $this->evNameRaw;
    }

    /**
     * @param string $evNameRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvNameRaw($evNameRaw)
    {
        $this->evNameRaw = $evNameRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvStreetRaw()
    {
        return $this->evStreetRaw;
    }

    /**
     * @param string $evStreetRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEvStreetRaw($evStreetRaw)
    {
        $this->evStreetRaw = $evStreetRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardGroup()
    {
        return $this->cardGroup;
    }

    /**
     * @param string $cardGroup
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardGroup($cardGroup)
    {
        $this->cardGroup = $cardGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosData()
    {
        return $this->posData;
    }

    /**
     * @param string $posData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setPosData($posData)
    {
        $this->posData = $posData;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @param string $transactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardIssuerCountry()
    {
        return $this->cardIssuerCountry;
    }

    /**
     * @param string $cardIssuerCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardIssuerCountry($cardIssuerCountry)
    {
        $this->cardIssuerCountry = $cardIssuerCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardRegulated()
    {
        return $this->cardRegulated;
    }

    /**
     * @param string $cardRegulated
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardRegulated($cardRegulated)
    {
        $this->cardRegulated = $cardRegulated;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardCommercial()
    {
        return $this->cardCommercial;
    }

    /**
     * @param string $cardCommercial
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardCommercial($cardCommercial)
    {
        $this->cardCommercial = $cardCommercial;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardPrepaid()
    {
        return $this->cardPrepaid;
    }

    /**
     * @param string $cardPrepaid
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardPrepaid($cardPrepaid)
    {
        $this->cardPrepaid = $cardPrepaid;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardPayroll()
    {
        return $this->cardPayroll;
    }

    /**
     * @param string $cardPayroll
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardPayroll($cardPayroll)
    {
        $this->cardPayroll = $cardPayroll;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardHealthcare()
    {
        return $this->cardHealthcare;
    }

    /**
     * @param string $cardHealthcare
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardHealthcare($cardHealthcare)
    {
        $this->cardHealthcare = $cardHealthcare;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardSignatureDebit()
    {
        return $this->cardSignatureDebit;
    }

    /**
     * @param string $cardSignatureDebit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardSignatureDebit($cardSignatureDebit)
    {
        $this->cardSignatureDebit = $cardSignatureDebit;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardPINlessDebit()
    {
        return $this->cardPINlessDebit;
    }

    /**
     * @param string $cardPINlessDebit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardPINlessDebit($cardPINlessDebit)
    {
        $this->cardPINlessDebit = $cardPINlessDebit;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardLevel3Eligible()
    {
        return $this->cardLevel3Eligible;
    }

    /**
     * @param string $cardLevel3Eligible
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardLevel3Eligible($cardLevel3Eligible)
    {
        $this->cardLevel3Eligible = $cardLevel3Eligible;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorTransactionID()
    {
        return $this->processorTransactionID;
    }

    /**
     * @param string $processorTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProcessorTransactionID($processorTransactionID)
    {
        $this->processorTransactionID = $processorTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderReasonCode()
    {
        return $this->providerReasonCode;
    }

    /**
     * @param string $providerReasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProviderReasonCode($providerReasonCode)
    {
        $this->providerReasonCode = $providerReasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderReasonDescription()
    {
        return $this->providerReasonDescription;
    }

    /**
     * @param string $providerReasonDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProviderReasonDescription($providerReasonDescription)
    {
        $this->providerReasonDescription = $providerReasonDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderPassThroughData()
    {
        return $this->providerPassThroughData;
    }

    /**
     * @param string $providerPassThroughData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProviderPassThroughData($providerPassThroughData)
    {
        $this->providerPassThroughData = $providerPassThroughData;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderCVNResponseCode()
    {
        return $this->providerCVNResponseCode;
    }

    /**
     * @param string $providerCVNResponseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProviderCVNResponseCode($providerCVNResponseCode)
    {
        $this->providerCVNResponseCode = $providerCVNResponseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderAVSResponseCode()
    {
        return $this->providerAVSResponseCode;
    }

    /**
     * @param string $providerAVSResponseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProviderAVSResponseCode($providerAVSResponseCode)
    {
        $this->providerAVSResponseCode = $providerAVSResponseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderAcquirerBankCode()
    {
        return $this->providerAcquirerBankCode;
    }

    /**
     * @param string $providerAcquirerBankCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setProviderAcquirerBankCode($providerAcquirerBankCode)
    {
        $this->providerAcquirerBankCode = $providerAcquirerBankCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentCardService()
    {
        return $this->paymentCardService;
    }

    /**
     * @param string $paymentCardService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setPaymentCardService($paymentCardService)
    {
        $this->paymentCardService = $paymentCardService;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentCardServiceResult()
    {
        return $this->paymentCardServiceResult;
    }

    /**
     * @param string $paymentCardServiceResult
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setPaymentCardServiceResult($paymentCardServiceResult)
    {
        $this->paymentCardServiceResult = $paymentCardServiceResult;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionQualification()
    {
        return $this->transactionQualification;
    }

    /**
     * @param string $transactionQualification
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setTransactionQualification($transactionQualification)
    {
        $this->transactionQualification = $transactionQualification;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionIntegrity()
    {
        return $this->transactionIntegrity;
    }

    /**
     * @param string $transactionIntegrity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setTransactionIntegrity($transactionIntegrity)
    {
        $this->transactionIntegrity = $transactionIntegrity;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmsTransactionRiskScore()
    {
        return $this->emsTransactionRiskScore;
    }

    /**
     * @param string $emsTransactionRiskScore
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setEmsTransactionRiskScore($emsTransactionRiskScore)
    {
        $this->emsTransactionRiskScore = $emsTransactionRiskScore;

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationReferenceNumber()
    {
        return $this->reconciliationReferenceNumber;
    }

    /**
     * @param string $reconciliationReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setReconciliationReferenceNumber($reconciliationReferenceNumber)
    {
        $this->reconciliationReferenceNumber = $reconciliationReferenceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardReferenceData()
    {
        return $this->cardReferenceData;
    }

    /**
     * @param string $cardReferenceData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setCardReferenceData($cardReferenceData)
    {
        $this->cardReferenceData = $cardReferenceData;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartialPANandIBAN()
    {
        return $this->partialPANandIBAN;
    }

    /**
     * @param string $partialPANandIBAN
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setPartialPANandIBAN($partialPANandIBAN)
    {
        $this->partialPANandIBAN = $partialPANandIBAN;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuerPINrequest()
    {
        return $this->issuerPINrequest;
    }

    /**
     * @param string $issuerPINrequest
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply
     */
    public function setIssuerPINrequest($issuerPINrequest)
    {
        $this->issuerPINrequest = $issuerPINrequest;

        return $this;
    }
}
