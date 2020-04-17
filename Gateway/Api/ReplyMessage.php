<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class ReplyMessage
{
    /**
     * @var string $merchantReferenceCode
     */
    protected $merchantReferenceCode;

    /**
     * @var string $requestID
     */
    protected $requestID;

    /**
     * @var string $decision
     */
    protected $decision;

    /**
     * @var int $reasonCode
     */
    protected $reasonCode;

    /**
     * @var string[] $missingField
     */
    protected $missingField;

    /**
     * @var string[] $invalidField
     */
    protected $invalidField;

    /**
     * @var string $requestToken
     */
    protected $requestToken;

    /**
     * @var PurchaseTotals $purchaseTotals
     */
    protected $purchaseTotals;

    /**
     * @var DeniedPartiesMatch[] $deniedPartiesMatch
     */
    protected $deniedPartiesMatch;

    /**
     * @var CCAuthReply $ccAuthReply
     */
    protected $ccAuthReply;

    /**
     * @var OCTReply $octReply
     */
    protected $octReply;

    /**
     * @var VerificationReply $verificationReply
     */
    protected $verificationReply;

    /**
     * @var CCSaleReply $ccSaleReply
     */
    protected $ccSaleReply;

    /**
     * @var CCSaleCreditReply $ccSaleCreditReply
     */
    protected $ccSaleCreditReply;

    /**
     * @var CCSaleReversalReply $ccSaleReversalReply
     */
    protected $ccSaleReversalReply;

    /**
     * @var CCIncrementalAuthReply $ccIncrementalAuthReply
     */
    protected $ccIncrementalAuthReply;

    /**
     * @var ServiceFeeCalculateReply $serviceFeeCalculateReply
     */
    protected $serviceFeeCalculateReply;

    /**
     * @var CCCaptureReply $ccCaptureReply
     */
    protected $ccCaptureReply;

    /**
     * @var CCCreditReply $ccCreditReply
     */
    protected $ccCreditReply;

    /**
     * @var CCAuthReversalReply $ccAuthReversalReply
     */
    protected $ccAuthReversalReply;

    /**
     * @var CCAutoAuthReversalReply $ccAutoAuthReversalReply
     */
    protected $ccAutoAuthReversalReply;

    /**
     * @var CCDCCReply $ccDCCReply
     */
    protected $ccDCCReply;

    /**
     * @var CCDCCUpdateReply $ccDCCUpdateReply
     */
    protected $ccDCCUpdateReply;

    /**
     * @var ECDebitReply $ecDebitReply
     */
    protected $ecDebitReply;

    /**
     * @var ECCreditReply $ecCreditReply
     */
    protected $ecCreditReply;

    /**
     * @var ECAuthenticateReply $ecAuthenticateReply
     */
    protected $ecAuthenticateReply;

    /**
     * @var PayerAuthEnrollReply $payerAuthEnrollReply
     */
    protected $payerAuthEnrollReply;

    /**
     * @var PayerAuthValidateReply $payerAuthValidateReply
     */
    protected $payerAuthValidateReply;

    /**
     * @var TaxReply $taxReply
     */
    protected $taxReply;

    /**
     * @var EncryptedPayment $encryptedPayment
     */
    protected $encryptedPayment;

    /**
     * @var EncryptPaymentDataReply $encryptPaymentDataReply
     */
    protected $encryptPaymentDataReply;

    /**
     * @var DMEReply $dmeReply
     */
    protected $dmeReply;

    /**
     * @var AFSReply $afsReply
     */
    protected $afsReply;

    /**
     * @var DAVReply $davReply
     */
    protected $davReply;

    /**
     * @var ExportReply $exportReply
     */
    protected $exportReply;

    /**
     * @var FXRatesReply $fxRatesReply
     */
    protected $fxRatesReply;

    /**
     * @var BankTransferReply $bankTransferReply
     */
    protected $bankTransferReply;

    /**
     * @var BankTransferRefundReply $bankTransferRefundReply
     */
    protected $bankTransferRefundReply;

    /**
     * @var BankTransferRealTimeReply $bankTransferRealTimeReply
     */
    protected $bankTransferRealTimeReply;

    /**
     * @var DirectDebitMandateReply $directDebitMandateReply
     */
    protected $directDebitMandateReply;

    /**
     * @var DirectDebitReply $directDebitReply
     */
    protected $directDebitReply;

    /**
     * @var DirectDebitValidateReply $directDebitValidateReply
     */
    protected $directDebitValidateReply;

    /**
     * @var DirectDebitRefundReply $directDebitRefundReply
     */
    protected $directDebitRefundReply;

    /**
     * @var PaySubscriptionCreateReply $paySubscriptionCreateReply
     */
    protected $paySubscriptionCreateReply;

    /**
     * @var PaySubscriptionUpdateReply $paySubscriptionUpdateReply
     */
    protected $paySubscriptionUpdateReply;

    /**
     * @var PaySubscriptionEventUpdateReply $paySubscriptionEventUpdateReply
     */
    protected $paySubscriptionEventUpdateReply;

    /**
     * @var PaySubscriptionRetrieveReply $paySubscriptionRetrieveReply
     */
    protected $paySubscriptionRetrieveReply;

    /**
     * @var PaySubscriptionDeleteReply $paySubscriptionDeleteReply
     */
    protected $paySubscriptionDeleteReply;

    /**
     * @var PayPalPaymentReply $payPalPaymentReply
     */
    protected $payPalPaymentReply;

    /**
     * @var PayPalCreditReply $payPalCreditReply
     */
    protected $payPalCreditReply;

    /**
     * @var VoidReply $voidReply
     */
    protected $voidReply;

    /**
     * @var PinlessDebitReply $pinlessDebitReply
     */
    protected $pinlessDebitReply;

    /**
     * @var PinlessDebitValidateReply $pinlessDebitValidateReply
     */
    protected $pinlessDebitValidateReply;

    /**
     * @var PinlessDebitReversalReply $pinlessDebitReversalReply
     */
    protected $pinlessDebitReversalReply;

    /**
     * @var PayPalButtonCreateReply $payPalButtonCreateReply
     */
    protected $payPalButtonCreateReply;

    /**
     * @var PayPalPreapprovedPaymentReply $payPalPreapprovedPaymentReply
     */
    protected $payPalPreapprovedPaymentReply;

    /**
     * @var PayPalPreapprovedUpdateReply $payPalPreapprovedUpdateReply
     */
    protected $payPalPreapprovedUpdateReply;

    /**
     * @var RiskUpdateReply $riskUpdateReply
     */
    protected $riskUpdateReply;

    /**
     * @var FraudUpdateReply $fraudUpdateReply
     */
    protected $fraudUpdateReply;

    /**
     * @var CaseManagementActionReply $caseManagementActionReply
     */
    protected $caseManagementActionReply;

    /**
     * @var DecisionEarlyReply $decisionEarlyReply
     */
    protected $decisionEarlyReply;

    /**
     * @var DecisionReply $decisionReply
     */
    protected $decisionReply;

    /**
     * @var PayPalRefundReply $payPalRefundReply
     */
    protected $payPalRefundReply;

    /**
     * @var PayPalAuthReversalReply $payPalAuthReversalReply
     */
    protected $payPalAuthReversalReply;

    /**
     * @var PayPalDoCaptureReply $payPalDoCaptureReply
     */
    protected $payPalDoCaptureReply;

    /**
     * @var PayPalEcDoPaymentReply $payPalEcDoPaymentReply
     */
    protected $payPalEcDoPaymentReply;

    /**
     * @var PayPalEcGetDetailsReply $payPalEcGetDetailsReply
     */
    protected $payPalEcGetDetailsReply;

    /**
     * @var PayPalEcSetReply $payPalEcSetReply
     */
    protected $payPalEcSetReply;

    /**
     * @var PayPalAuthorizationReply $payPalAuthorizationReply
     */
    protected $payPalAuthorizationReply;

    /**
     * @var PayPalEcOrderSetupReply $payPalEcOrderSetupReply
     */
    protected $payPalEcOrderSetupReply;

    /**
     * @var PayPalUpdateAgreementReply $payPalUpdateAgreementReply
     */
    protected $payPalUpdateAgreementReply;

    /**
     * @var PayPalCreateAgreementReply $payPalCreateAgreementReply
     */
    protected $payPalCreateAgreementReply;

    /**
     * @var PayPalDoRefTransactionReply $payPalDoRefTransactionReply
     */
    protected $payPalDoRefTransactionReply;

    /**
     * @var ChinaPaymentReply $chinaPaymentReply
     */
    protected $chinaPaymentReply;

    /**
     * @var ChinaRefundReply $chinaRefundReply
     */
    protected $chinaRefundReply;

    /**
     * @var BoletoPaymentReply $boletoPaymentReply
     */
    protected $boletoPaymentReply;

    /**
     * @var PinDebitPurchaseReply $pinDebitPurchaseReply
     */
    protected $pinDebitPurchaseReply;

    /**
     * @var PinDebitCreditReply $pinDebitCreditReply
     */
    protected $pinDebitCreditReply;

    /**
     * @var PinDebitReversalReply $pinDebitReversalReply
     */
    protected $pinDebitReversalReply;

    /**
     * @var APInitiateReply $apInitiateReply
     */
    protected $apInitiateReply;

    /**
     * @var APCheckStatusReply $apCheckStatusReply
     */
    protected $apCheckStatusReply;

    /**
     * @var string $receiptNumber
     */
    protected $receiptNumber;

    /**
     * @var string $additionalData
     */
    protected $additionalData;

    /**
     * @var string $solutionProviderTransactionID
     */
    protected $solutionProviderTransactionID;

    /**
     * @var APReply $apReply
     */
    protected $apReply;

    /**
     * @var ShipTo $shipTo
     */
    protected $shipTo;

    /**
     * @var BillTo $billTo
     */
    protected $billTo;

    /**
     * @var APAuthReply $apAuthReply
     */
    protected $apAuthReply;

    /**
     * @var APSessionsReply $apSessionsReply
     */
    protected $apSessionsReply;

    /**
     * @var APAuthReversalReply $apAuthReversalReply
     */
    protected $apAuthReversalReply;

    /**
     * @var APCaptureReply $apCaptureReply
     */
    protected $apCaptureReply;

    /**
     * @var APOptionsReply $apOptionsReply
     */
    protected $apOptionsReply;

    /**
     * @var APRefundReply $apRefundReply
     */
    protected $apRefundReply;

    /**
     * @var APSaleReply $apSaleReply
     */
    protected $apSaleReply;

    /**
     * @var APCheckOutDetailsReply $apCheckoutDetailsReply
     */
    protected $apCheckoutDetailsReply;

    /**
     * @var APTransactionDetailsReply $apTransactionDetailsReply
     */
    protected $apTransactionDetailsReply;

    /**
     * @var APConfirmPurchaseReply $apConfirmPurchaseReply
     */
    protected $apConfirmPurchaseReply;

    /**
     * @var Promotion $promotion
     */
    protected $promotion;

    /**
     * @var PromotionGroupReply[] $promotionGroup
     */
    protected $promotionGroup;

    /**
     * @var PayPalGetTxnDetailsReply $payPalGetTxnDetailsReply
     */
    protected $payPalGetTxnDetailsReply;

    /**
     * @var PayPalTransactionSearchReply $payPalTransactionSearchReply
     */
    protected $payPalTransactionSearchReply;

    /**
     * @var EmvReply $emvReply
     */
    protected $emvReply;

    /**
     * @var OriginalTransaction $originalTransaction
     */
    protected $originalTransaction;

    /**
     * @var HostedDataCreateReply $hostedDataCreateReply
     */
    protected $hostedDataCreateReply;

    /**
     * @var HostedDataRetrieveReply $hostedDataRetrieveReply
     */
    protected $hostedDataRetrieveReply;

    /**
     * @var string $salesSlipNumber
     */
    protected $salesSlipNumber;

    /**
     * @var string $additionalProcessorResponse
     */
    protected $additionalProcessorResponse;

    /**
     * @var JPO $jpo
     */
    protected $jpo;

    /**
     * @var Card $card
     */
    protected $card;

    /**
     * @var PaymentNetworkToken $paymentNetworkToken
     */
    protected $paymentNetworkToken;

    /**
     * @var VCReply $vcReply
     */
    protected $vcReply;

    /**
     * @var DecryptVisaCheckoutDataReply $decryptVisaCheckoutDataReply
     */
    protected $decryptVisaCheckoutDataReply;

    /**
     * @var GetVisaCheckoutDataReply $getVisaCheckoutDataReply
     */
    protected $getVisaCheckoutDataReply;

    /**
     * @var BinLookupReply $binLookupReply
     */
    protected $binLookupReply;

    /**
     * @var string $issuerMessage
     */
    protected $issuerMessage;

    /**
     * @var string $token
     */
    protected $token;

    /**
     * @var issuer $issuer
     */
    protected $issuer;

    /**
     * @var Recipient $recipient
     */
    protected $recipient;

    /**
     * @var string $feeProgramIndicator
     */
    protected $feeProgramIndicator;

    /**
     * @var Installment $installment
     */
    protected $installment;

    /**
     * @var string $paymentAccountReference
     */
    protected $paymentAccountReference;

    /**
     * @var string $authIndicator
     */
    protected $authIndicator;

    /**
     * @var UCAF $ucaf
     */
    protected $ucaf;

    /**
     * @var Network[] $network
     */
    protected $network;

    /**
     * @var InvoiceHeader $invoiceHeader
     */
    protected $invoiceHeader;

    /**
     * @var APOrderReply $apOrderReply
     */
    protected $apOrderReply;

    /**
     * @var APCancelReply $apCancelReply
     */
    protected $apCancelReply;

    /**
     * @var APBillingAgreementReply $apBillingAgreementReply
     */
    protected $apBillingAgreementReply;

    /**
     * @var string $customerVerificationStatus
     */
    protected $customerVerificationStatus;

    /**
     * @var PersonalID $personalID
     */
    protected $personalID;

    /**
     * @var string $acquirerMerchantNumber
     */
    protected $acquirerMerchantNumber;

    /**
     * @var Pos $pos
     */
    protected $pos;

    /**
     * @var string $issuerMessageAction
     */
    protected $issuerMessageAction;

    /**
     * @var string $customerID
     */
    protected $customerID;

    /**
     * @var Routing $routing
     */
    protected $routing;

    /**
     * @var \DateTime $transactionLocalDateTime
     */
    protected $transactionLocalDateTime;

    /**
     * @var APCreateMandateReply $apCreateMandateReply
     */
    protected $apCreateMandateReply;

    /**
     * @var APMandateStatusReply $apMandateStatusReply
     */
    protected $apMandateStatusReply;

    /**
     * @var APUpdateMandateReply $apUpdateMandateReply
     */
    protected $apUpdateMandateReply;

    /**
     * @var APImportMandateReply $apImportMandateReply
     */
    protected $apImportMandateReply;

    /**
     * @var APRevokeMandateReply $apRevokeMandateReply
     */
    protected $apRevokeMandateReply;

    /**
     * @var GetMasterpassDataReply $getMasterpassDataReply
     */
    protected $getMasterpassDataReply;

    /**
     * @var string $paymentNetworkMerchantID
     */
    protected $paymentNetworkMerchantID;

    /**
     * @var Wallet $wallet
     */
    protected $wallet;

    /**
     * @var float $cashbackAmount
     */
    protected $cashbackAmount;

    /**
     * @var GiftCard $giftCard
     */
    protected $giftCard;

    /**
     * @var GiftCardActivationReply $giftCardActivationReply
     */
    protected $giftCardActivationReply;

    /**
     * @var GiftCardBalanceInquiryReply $giftCardBalanceInquiryReply
     */
    protected $giftCardBalanceInquiryReply;

    /**
     * @var GiftCardRedemptionReply $giftCardRedemptionReply
     */
    protected $giftCardRedemptionReply;

    /**
     * @var GiftCardVoidReply $giftCardVoidReply
     */
    protected $giftCardVoidReply;

    /**
     * @var GiftCardReversalReply $giftCardReversalReply
     */
    protected $giftCardReversalReply;

    /**
     * @var CCCheckStatusReply $ccCheckStatusReply
     */
    protected $ccCheckStatusReply;

    /**
     * @var ECAVSReply $ecAVSReply
     */
    protected $ecAVSReply;

    /**
     * @var AbortReply $abortReply
     */
    protected $abortReply;

    /**
     * @var ReplyReserved $reserved
     */
    protected $reserved;

    /**
     * @param string $requestID
     * @param string $decision
     * @param int $reasonCode
     * @param string $requestToken
     */
    public function __construct($requestID, $decision, $reasonCode, $requestToken)
    {
        $this->requestID    = $requestID;
        $this->decision     = $decision;
        $this->reasonCode   = $reasonCode;
        $this->requestToken = $requestToken;
    }

    /**
     * @return string
     */
    public function getMerchantReferenceCode()
    {
        return $this->merchantReferenceCode;
    }

    /**
     * @param string $merchantReferenceCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setMerchantReferenceCode($merchantReferenceCode)
    {
        $this->merchantReferenceCode = $merchantReferenceCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestID()
    {
        return $this->requestID;
    }

    /**
     * @param string $requestID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setRequestID($requestID)
    {
        $this->requestID = $requestID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * @param string $decision
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return (int)$this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMissingField()
    {
        return $this->missingField;
    }

    /**
     * @param string[] $missingField
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setMissingField(array $missingField = null)
    {
        $this->missingField = $missingField;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getInvalidField()
    {
        return $this->invalidField;
    }

    /**
     * @param string[] $invalidField
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setInvalidField(array $invalidField = null)
    {
        $this->invalidField = $invalidField;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestToken()
    {
        return $this->requestToken;
    }

    /**
     * @param string $requestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setRequestToken($requestToken)
    {
        $this->requestToken = $requestToken;

        return $this;
    }

    /**
     * @return PurchaseTotals
     */
    public function getPurchaseTotals()
    {
        return $this->purchaseTotals;
    }

    /**
     * @param PurchaseTotals $purchaseTotals
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPurchaseTotals($purchaseTotals)
    {
        $this->purchaseTotals = $purchaseTotals;

        return $this;
    }

    /**
     * @return DeniedPartiesMatch[]
     */
    public function getDeniedPartiesMatch()
    {
        return $this->deniedPartiesMatch;
    }

    /**
     * @param DeniedPartiesMatch[] $deniedPartiesMatch
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDeniedPartiesMatch(array $deniedPartiesMatch = null)
    {
        $this->deniedPartiesMatch = $deniedPartiesMatch;

        return $this;
    }

    /**
     * @return CCAuthReply
     */
    public function getCcAuthReply()
    {
        return $this->ccAuthReply;
    }

    /**
     * @param CCAuthReply $ccAuthReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcAuthReply($ccAuthReply)
    {
        $this->ccAuthReply = $ccAuthReply;

        return $this;
    }

    /**
     * @return OCTReply
     */
    public function getOctReply()
    {
        return $this->octReply;
    }

    /**
     * @param OCTReply $octReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setOctReply($octReply)
    {
        $this->octReply = $octReply;

        return $this;
    }

    /**
     * @return VerificationReply
     */
    public function getVerificationReply()
    {
        return $this->verificationReply;
    }

    /**
     * @param VerificationReply $verificationReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setVerificationReply($verificationReply)
    {
        $this->verificationReply = $verificationReply;

        return $this;
    }

    /**
     * @return CCSaleReply
     */
    public function getCcSaleReply()
    {
        return $this->ccSaleReply;
    }

    /**
     * @param CCSaleReply $ccSaleReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcSaleReply($ccSaleReply)
    {
        $this->ccSaleReply = $ccSaleReply;

        return $this;
    }

    /**
     * @return CCSaleCreditReply
     */
    public function getCcSaleCreditReply()
    {
        return $this->ccSaleCreditReply;
    }

    /**
     * @param CCSaleCreditReply $ccSaleCreditReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcSaleCreditReply($ccSaleCreditReply)
    {
        $this->ccSaleCreditReply = $ccSaleCreditReply;

        return $this;
    }

    /**
     * @return CCSaleReversalReply
     */
    public function getCcSaleReversalReply()
    {
        return $this->ccSaleReversalReply;
    }

    /**
     * @param CCSaleReversalReply $ccSaleReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcSaleReversalReply($ccSaleReversalReply)
    {
        $this->ccSaleReversalReply = $ccSaleReversalReply;

        return $this;
    }

    /**
     * @return CCIncrementalAuthReply
     */
    public function getCcIncrementalAuthReply()
    {
        return $this->ccIncrementalAuthReply;
    }

    /**
     * @param CCIncrementalAuthReply $ccIncrementalAuthReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcIncrementalAuthReply($ccIncrementalAuthReply)
    {
        $this->ccIncrementalAuthReply = $ccIncrementalAuthReply;

        return $this;
    }

    /**
     * @return ServiceFeeCalculateReply
     */
    public function getServiceFeeCalculateReply()
    {
        return $this->serviceFeeCalculateReply;
    }

    /**
     * @param ServiceFeeCalculateReply $serviceFeeCalculateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setServiceFeeCalculateReply($serviceFeeCalculateReply)
    {
        $this->serviceFeeCalculateReply = $serviceFeeCalculateReply;

        return $this;
    }

    /**
     * @return CCCaptureReply
     */
    public function getCcCaptureReply()
    {
        return $this->ccCaptureReply;
    }

    /**
     * @param CCCaptureReply $ccCaptureReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcCaptureReply($ccCaptureReply)
    {
        $this->ccCaptureReply = $ccCaptureReply;

        return $this;
    }

    /**
     * @return CCCreditReply
     */
    public function getCcCreditReply()
    {
        return $this->ccCreditReply;
    }

    /**
     * @param CCCreditReply $ccCreditReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcCreditReply($ccCreditReply)
    {
        $this->ccCreditReply = $ccCreditReply;

        return $this;
    }

    /**
     * @return CCAuthReversalReply
     */
    public function getCcAuthReversalReply()
    {
        return $this->ccAuthReversalReply;
    }

    /**
     * @param CCAuthReversalReply $ccAuthReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcAuthReversalReply($ccAuthReversalReply)
    {
        $this->ccAuthReversalReply = $ccAuthReversalReply;

        return $this;
    }

    /**
     * @return CCAutoAuthReversalReply
     */
    public function getCcAutoAuthReversalReply()
    {
        return $this->ccAutoAuthReversalReply;
    }

    /**
     * @param CCAutoAuthReversalReply $ccAutoAuthReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcAutoAuthReversalReply($ccAutoAuthReversalReply)
    {
        $this->ccAutoAuthReversalReply = $ccAutoAuthReversalReply;

        return $this;
    }

    /**
     * @return CCDCCReply
     */
    public function getCcDCCReply()
    {
        return $this->ccDCCReply;
    }

    /**
     * @param CCDCCReply $ccDCCReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcDCCReply($ccDCCReply)
    {
        $this->ccDCCReply = $ccDCCReply;

        return $this;
    }

    /**
     * @return CCDCCUpdateReply
     */
    public function getCcDCCUpdateReply()
    {
        return $this->ccDCCUpdateReply;
    }

    /**
     * @param CCDCCUpdateReply $ccDCCUpdateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcDCCUpdateReply($ccDCCUpdateReply)
    {
        $this->ccDCCUpdateReply = $ccDCCUpdateReply;

        return $this;
    }

    /**
     * @return ECDebitReply
     */
    public function getEcDebitReply()
    {
        return $this->ecDebitReply;
    }

    /**
     * @param ECDebitReply $ecDebitReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setEcDebitReply($ecDebitReply)
    {
        $this->ecDebitReply = $ecDebitReply;

        return $this;
    }

    /**
     * @return ECCreditReply
     */
    public function getEcCreditReply()
    {
        return $this->ecCreditReply;
    }

    /**
     * @param ECCreditReply $ecCreditReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setEcCreditReply($ecCreditReply)
    {
        $this->ecCreditReply = $ecCreditReply;

        return $this;
    }

    /**
     * @return ECAuthenticateReply
     */
    public function getEcAuthenticateReply()
    {
        return $this->ecAuthenticateReply;
    }

    /**
     * @param ECAuthenticateReply $ecAuthenticateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setEcAuthenticateReply($ecAuthenticateReply)
    {
        $this->ecAuthenticateReply = $ecAuthenticateReply;

        return $this;
    }

    /**
     * @return PayerAuthEnrollReply
     */
    public function getPayerAuthEnrollReply()
    {
        return $this->payerAuthEnrollReply;
    }

    /**
     * @param PayerAuthEnrollReply $payerAuthEnrollReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayerAuthEnrollReply($payerAuthEnrollReply)
    {
        $this->payerAuthEnrollReply = $payerAuthEnrollReply;

        return $this;
    }

    /**
     * @return PayerAuthValidateReply
     */
    public function getPayerAuthValidateReply()
    {
        return $this->payerAuthValidateReply;
    }

    /**
     * @param PayerAuthValidateReply $payerAuthValidateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayerAuthValidateReply($payerAuthValidateReply)
    {
        $this->payerAuthValidateReply = $payerAuthValidateReply;

        return $this;
    }

    /**
     * @return TaxReply
     */
    public function getTaxReply()
    {
        return $this->taxReply;
    }

    /**
     * @param TaxReply $taxReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setTaxReply($taxReply)
    {
        $this->taxReply = $taxReply;

        return $this;
    }

    /**
     * @return EncryptedPayment
     */
    public function getEncryptedPayment()
    {
        return $this->encryptedPayment;
    }

    /**
     * @param EncryptedPayment $encryptedPayment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setEncryptedPayment($encryptedPayment)
    {
        $this->encryptedPayment = $encryptedPayment;

        return $this;
    }

    /**
     * @return EncryptPaymentDataReply
     */
    public function getEncryptPaymentDataReply()
    {
        return $this->encryptPaymentDataReply;
    }

    /**
     * @param EncryptPaymentDataReply $encryptPaymentDataReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setEncryptPaymentDataReply($encryptPaymentDataReply)
    {
        $this->encryptPaymentDataReply = $encryptPaymentDataReply;

        return $this;
    }

    /**
     * @return DMEReply
     */
    public function getDmeReply()
    {
        return $this->dmeReply;
    }

    /**
     * @param DMEReply $dmeReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDmeReply($dmeReply)
    {
        $this->dmeReply = $dmeReply;

        return $this;
    }

    /**
     * @return AFSReply
     */
    public function getAfsReply()
    {
        return $this->afsReply;
    }

    /**
     * @param AFSReply $afsReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setAfsReply($afsReply)
    {
        $this->afsReply = $afsReply;

        return $this;
    }

    /**
     * @return DAVReply
     */
    public function getDavReply()
    {
        return $this->davReply;
    }

    /**
     * @param DAVReply $davReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDavReply($davReply)
    {
        $this->davReply = $davReply;

        return $this;
    }

    /**
     * @return ExportReply
     */
    public function getExportReply()
    {
        return $this->exportReply;
    }

    /**
     * @param ExportReply $exportReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setExportReply($exportReply)
    {
        $this->exportReply = $exportReply;

        return $this;
    }

    /**
     * @return FXRatesReply
     */
    public function getFxRatesReply()
    {
        return $this->fxRatesReply;
    }

    /**
     * @param FXRatesReply $fxRatesReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setFxRatesReply($fxRatesReply)
    {
        $this->fxRatesReply = $fxRatesReply;

        return $this;
    }

    /**
     * @return BankTransferReply
     */
    public function getBankTransferReply()
    {
        return $this->bankTransferReply;
    }

    /**
     * @param BankTransferReply $bankTransferReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setBankTransferReply($bankTransferReply)
    {
        $this->bankTransferReply = $bankTransferReply;

        return $this;
    }

    /**
     * @return BankTransferRefundReply
     */
    public function getBankTransferRefundReply()
    {
        return $this->bankTransferRefundReply;
    }

    /**
     * @param BankTransferRefundReply $bankTransferRefundReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setBankTransferRefundReply($bankTransferRefundReply)
    {
        $this->bankTransferRefundReply = $bankTransferRefundReply;

        return $this;
    }

    /**
     * @return BankTransferRealTimeReply
     */
    public function getBankTransferRealTimeReply()
    {
        return $this->bankTransferRealTimeReply;
    }

    /**
     * @param BankTransferRealTimeReply $bankTransferRealTimeReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setBankTransferRealTimeReply($bankTransferRealTimeReply)
    {
        $this->bankTransferRealTimeReply = $bankTransferRealTimeReply;

        return $this;
    }

    /**
     * @return DirectDebitMandateReply
     */
    public function getDirectDebitMandateReply()
    {
        return $this->directDebitMandateReply;
    }

    /**
     * @param DirectDebitMandateReply $directDebitMandateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDirectDebitMandateReply($directDebitMandateReply)
    {
        $this->directDebitMandateReply = $directDebitMandateReply;

        return $this;
    }

    /**
     * @return DirectDebitReply
     */
    public function getDirectDebitReply()
    {
        return $this->directDebitReply;
    }

    /**
     * @param DirectDebitReply $directDebitReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDirectDebitReply($directDebitReply)
    {
        $this->directDebitReply = $directDebitReply;

        return $this;
    }

    /**
     * @return DirectDebitValidateReply
     */
    public function getDirectDebitValidateReply()
    {
        return $this->directDebitValidateReply;
    }

    /**
     * @param DirectDebitValidateReply $directDebitValidateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDirectDebitValidateReply($directDebitValidateReply)
    {
        $this->directDebitValidateReply = $directDebitValidateReply;

        return $this;
    }

    /**
     * @return DirectDebitRefundReply
     */
    public function getDirectDebitRefundReply()
    {
        return $this->directDebitRefundReply;
    }

    /**
     * @param DirectDebitRefundReply $directDebitRefundReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDirectDebitRefundReply($directDebitRefundReply)
    {
        $this->directDebitRefundReply = $directDebitRefundReply;

        return $this;
    }

    /**
     * @return PaySubscriptionCreateReply
     */
    public function getPaySubscriptionCreateReply()
    {
        return $this->paySubscriptionCreateReply;
    }

    /**
     * @param PaySubscriptionCreateReply $paySubscriptionCreateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaySubscriptionCreateReply($paySubscriptionCreateReply)
    {
        $this->paySubscriptionCreateReply = $paySubscriptionCreateReply;

        return $this;
    }

    /**
     * @return PaySubscriptionUpdateReply
     */
    public function getPaySubscriptionUpdateReply()
    {
        return $this->paySubscriptionUpdateReply;
    }

    /**
     * @param PaySubscriptionUpdateReply $paySubscriptionUpdateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaySubscriptionUpdateReply($paySubscriptionUpdateReply)
    {
        $this->paySubscriptionUpdateReply = $paySubscriptionUpdateReply;

        return $this;
    }

    /**
     * @return PaySubscriptionEventUpdateReply
     */
    public function getPaySubscriptionEventUpdateReply()
    {
        return $this->paySubscriptionEventUpdateReply;
    }

    /**
     * @param PaySubscriptionEventUpdateReply $paySubscriptionEventUpdateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaySubscriptionEventUpdateReply($paySubscriptionEventUpdateReply)
    {
        $this->paySubscriptionEventUpdateReply = $paySubscriptionEventUpdateReply;

        return $this;
    }

    /**
     * @return PaySubscriptionRetrieveReply
     */
    public function getPaySubscriptionRetrieveReply()
    {
        return $this->paySubscriptionRetrieveReply;
    }

    /**
     * @param PaySubscriptionRetrieveReply $paySubscriptionRetrieveReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaySubscriptionRetrieveReply($paySubscriptionRetrieveReply)
    {
        $this->paySubscriptionRetrieveReply = $paySubscriptionRetrieveReply;

        return $this;
    }

    /**
     * @return PaySubscriptionDeleteReply
     */
    public function getPaySubscriptionDeleteReply()
    {
        return $this->paySubscriptionDeleteReply;
    }

    /**
     * @param PaySubscriptionDeleteReply $paySubscriptionDeleteReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaySubscriptionDeleteReply($paySubscriptionDeleteReply)
    {
        $this->paySubscriptionDeleteReply = $paySubscriptionDeleteReply;

        return $this;
    }

    /**
     * @return PayPalPaymentReply
     */
    public function getPayPalPaymentReply()
    {
        return $this->payPalPaymentReply;
    }

    /**
     * @param PayPalPaymentReply $payPalPaymentReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalPaymentReply($payPalPaymentReply)
    {
        $this->payPalPaymentReply = $payPalPaymentReply;

        return $this;
    }

    /**
     * @return PayPalCreditReply
     */
    public function getPayPalCreditReply()
    {
        return $this->payPalCreditReply;
    }

    /**
     * @param PayPalCreditReply $payPalCreditReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalCreditReply($payPalCreditReply)
    {
        $this->payPalCreditReply = $payPalCreditReply;

        return $this;
    }

    /**
     * @return VoidReply
     */
    public function getVoidReply()
    {
        return $this->voidReply;
    }

    /**
     * @param VoidReply $voidReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setVoidReply($voidReply)
    {
        $this->voidReply = $voidReply;

        return $this;
    }

    /**
     * @return PinlessDebitReply
     */
    public function getPinlessDebitReply()
    {
        return $this->pinlessDebitReply;
    }

    /**
     * @param PinlessDebitReply $pinlessDebitReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPinlessDebitReply($pinlessDebitReply)
    {
        $this->pinlessDebitReply = $pinlessDebitReply;

        return $this;
    }

    /**
     * @return PinlessDebitValidateReply
     */
    public function getPinlessDebitValidateReply()
    {
        return $this->pinlessDebitValidateReply;
    }

    /**
     * @param PinlessDebitValidateReply $pinlessDebitValidateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPinlessDebitValidateReply($pinlessDebitValidateReply)
    {
        $this->pinlessDebitValidateReply = $pinlessDebitValidateReply;

        return $this;
    }

    /**
     * @return PinlessDebitReversalReply
     */
    public function getPinlessDebitReversalReply()
    {
        return $this->pinlessDebitReversalReply;
    }

    /**
     * @param PinlessDebitReversalReply $pinlessDebitReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPinlessDebitReversalReply($pinlessDebitReversalReply)
    {
        $this->pinlessDebitReversalReply = $pinlessDebitReversalReply;

        return $this;
    }

    /**
     * @return PayPalButtonCreateReply
     */
    public function getPayPalButtonCreateReply()
    {
        return $this->payPalButtonCreateReply;
    }

    /**
     * @param PayPalButtonCreateReply $payPalButtonCreateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalButtonCreateReply($payPalButtonCreateReply)
    {
        $this->payPalButtonCreateReply = $payPalButtonCreateReply;

        return $this;
    }

    /**
     * @return PayPalPreapprovedPaymentReply
     */
    public function getPayPalPreapprovedPaymentReply()
    {
        return $this->payPalPreapprovedPaymentReply;
    }

    /**
     * @param PayPalPreapprovedPaymentReply $payPalPreapprovedPaymentReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalPreapprovedPaymentReply($payPalPreapprovedPaymentReply)
    {
        $this->payPalPreapprovedPaymentReply = $payPalPreapprovedPaymentReply;

        return $this;
    }

    /**
     * @return PayPalPreapprovedUpdateReply
     */
    public function getPayPalPreapprovedUpdateReply()
    {
        return $this->payPalPreapprovedUpdateReply;
    }

    /**
     * @param PayPalPreapprovedUpdateReply $payPalPreapprovedUpdateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalPreapprovedUpdateReply($payPalPreapprovedUpdateReply)
    {
        $this->payPalPreapprovedUpdateReply = $payPalPreapprovedUpdateReply;

        return $this;
    }

    /**
     * @return RiskUpdateReply
     */
    public function getRiskUpdateReply()
    {
        return $this->riskUpdateReply;
    }

    /**
     * @param RiskUpdateReply $riskUpdateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setRiskUpdateReply($riskUpdateReply)
    {
        $this->riskUpdateReply = $riskUpdateReply;

        return $this;
    }

    /**
     * @return FraudUpdateReply
     */
    public function getFraudUpdateReply()
    {
        return $this->fraudUpdateReply;
    }

    /**
     * @param FraudUpdateReply $fraudUpdateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setFraudUpdateReply($fraudUpdateReply)
    {
        $this->fraudUpdateReply = $fraudUpdateReply;

        return $this;
    }

    /**
     * @return CaseManagementActionReply
     */
    public function getCaseManagementActionReply()
    {
        return $this->caseManagementActionReply;
    }

    /**
     * @param CaseManagementActionReply $caseManagementActionReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCaseManagementActionReply($caseManagementActionReply)
    {
        $this->caseManagementActionReply = $caseManagementActionReply;

        return $this;
    }

    /**
     * @return DecisionEarlyReply
     */
    public function getDecisionEarlyReply()
    {
        return $this->decisionEarlyReply;
    }

    /**
     * @param DecisionEarlyReply $decisionEarlyReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDecisionEarlyReply($decisionEarlyReply)
    {
        $this->decisionEarlyReply = $decisionEarlyReply;

        return $this;
    }

    /**
     * @return DecisionReply
     */
    public function getDecisionReply()
    {
        return $this->decisionReply;
    }

    /**
     * @param DecisionReply $decisionReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDecisionReply($decisionReply)
    {
        $this->decisionReply = $decisionReply;

        return $this;
    }

    /**
     * @return PayPalRefundReply
     */
    public function getPayPalRefundReply()
    {
        return $this->payPalRefundReply;
    }

    /**
     * @param PayPalRefundReply $payPalRefundReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalRefundReply($payPalRefundReply)
    {
        $this->payPalRefundReply = $payPalRefundReply;

        return $this;
    }

    /**
     * @return PayPalAuthReversalReply
     */
    public function getPayPalAuthReversalReply()
    {
        return $this->payPalAuthReversalReply;
    }

    /**
     * @param PayPalAuthReversalReply $payPalAuthReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalAuthReversalReply($payPalAuthReversalReply)
    {
        $this->payPalAuthReversalReply = $payPalAuthReversalReply;

        return $this;
    }

    /**
     * @return PayPalDoCaptureReply
     */
    public function getPayPalDoCaptureReply()
    {
        return $this->payPalDoCaptureReply;
    }

    /**
     * @param PayPalDoCaptureReply $payPalDoCaptureReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalDoCaptureReply($payPalDoCaptureReply)
    {
        $this->payPalDoCaptureReply = $payPalDoCaptureReply;

        return $this;
    }

    /**
     * @return PayPalEcDoPaymentReply
     */
    public function getPayPalEcDoPaymentReply()
    {
        return $this->payPalEcDoPaymentReply;
    }

    /**
     * @param PayPalEcDoPaymentReply $payPalEcDoPaymentReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalEcDoPaymentReply($payPalEcDoPaymentReply)
    {
        $this->payPalEcDoPaymentReply = $payPalEcDoPaymentReply;

        return $this;
    }

    /**
     * @return PayPalEcGetDetailsReply
     */
    public function getPayPalEcGetDetailsReply()
    {
        return $this->payPalEcGetDetailsReply;
    }

    /**
     * @param PayPalEcGetDetailsReply $payPalEcGetDetailsReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalEcGetDetailsReply($payPalEcGetDetailsReply)
    {
        $this->payPalEcGetDetailsReply = $payPalEcGetDetailsReply;

        return $this;
    }

    /**
     * @return PayPalEcSetReply
     */
    public function getPayPalEcSetReply()
    {
        return $this->payPalEcSetReply;
    }

    /**
     * @param PayPalEcSetReply $payPalEcSetReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalEcSetReply($payPalEcSetReply)
    {
        $this->payPalEcSetReply = $payPalEcSetReply;

        return $this;
    }

    /**
     * @return PayPalAuthorizationReply
     */
    public function getPayPalAuthorizationReply()
    {
        return $this->payPalAuthorizationReply;
    }

    /**
     * @param PayPalAuthorizationReply $payPalAuthorizationReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalAuthorizationReply($payPalAuthorizationReply)
    {
        $this->payPalAuthorizationReply = $payPalAuthorizationReply;

        return $this;
    }

    /**
     * @return PayPalEcOrderSetupReply
     */
    public function getPayPalEcOrderSetupReply()
    {
        return $this->payPalEcOrderSetupReply;
    }

    /**
     * @param PayPalEcOrderSetupReply $payPalEcOrderSetupReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalEcOrderSetupReply($payPalEcOrderSetupReply)
    {
        $this->payPalEcOrderSetupReply = $payPalEcOrderSetupReply;

        return $this;
    }

    /**
     * @return PayPalUpdateAgreementReply
     */
    public function getPayPalUpdateAgreementReply()
    {
        return $this->payPalUpdateAgreementReply;
    }

    /**
     * @param PayPalUpdateAgreementReply $payPalUpdateAgreementReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalUpdateAgreementReply($payPalUpdateAgreementReply)
    {
        $this->payPalUpdateAgreementReply = $payPalUpdateAgreementReply;

        return $this;
    }

    /**
     * @return PayPalCreateAgreementReply
     */
    public function getPayPalCreateAgreementReply()
    {
        return $this->payPalCreateAgreementReply;
    }

    /**
     * @param PayPalCreateAgreementReply $payPalCreateAgreementReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalCreateAgreementReply($payPalCreateAgreementReply)
    {
        $this->payPalCreateAgreementReply = $payPalCreateAgreementReply;

        return $this;
    }

    /**
     * @return PayPalDoRefTransactionReply
     */
    public function getPayPalDoRefTransactionReply()
    {
        return $this->payPalDoRefTransactionReply;
    }

    /**
     * @param PayPalDoRefTransactionReply $payPalDoRefTransactionReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalDoRefTransactionReply($payPalDoRefTransactionReply)
    {
        $this->payPalDoRefTransactionReply = $payPalDoRefTransactionReply;

        return $this;
    }

    /**
     * @return ChinaPaymentReply
     */
    public function getChinaPaymentReply()
    {
        return $this->chinaPaymentReply;
    }

    /**
     * @param ChinaPaymentReply $chinaPaymentReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setChinaPaymentReply($chinaPaymentReply)
    {
        $this->chinaPaymentReply = $chinaPaymentReply;

        return $this;
    }

    /**
     * @return ChinaRefundReply
     */
    public function getChinaRefundReply()
    {
        return $this->chinaRefundReply;
    }

    /**
     * @param ChinaRefundReply $chinaRefundReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setChinaRefundReply($chinaRefundReply)
    {
        $this->chinaRefundReply = $chinaRefundReply;

        return $this;
    }

    /**
     * @return BoletoPaymentReply
     */
    public function getBoletoPaymentReply()
    {
        return $this->boletoPaymentReply;
    }

    /**
     * @param BoletoPaymentReply $boletoPaymentReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setBoletoPaymentReply($boletoPaymentReply)
    {
        $this->boletoPaymentReply = $boletoPaymentReply;

        return $this;
    }

    /**
     * @return PinDebitPurchaseReply
     */
    public function getPinDebitPurchaseReply()
    {
        return $this->pinDebitPurchaseReply;
    }

    /**
     * @param PinDebitPurchaseReply $pinDebitPurchaseReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPinDebitPurchaseReply($pinDebitPurchaseReply)
    {
        $this->pinDebitPurchaseReply = $pinDebitPurchaseReply;

        return $this;
    }

    /**
     * @return PinDebitCreditReply
     */
    public function getPinDebitCreditReply()
    {
        return $this->pinDebitCreditReply;
    }

    /**
     * @param PinDebitCreditReply $pinDebitCreditReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPinDebitCreditReply($pinDebitCreditReply)
    {
        $this->pinDebitCreditReply = $pinDebitCreditReply;

        return $this;
    }

    /**
     * @return PinDebitReversalReply
     */
    public function getPinDebitReversalReply()
    {
        return $this->pinDebitReversalReply;
    }

    /**
     * @param PinDebitReversalReply $pinDebitReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPinDebitReversalReply($pinDebitReversalReply)
    {
        $this->pinDebitReversalReply = $pinDebitReversalReply;

        return $this;
    }

    /**
     * @return APInitiateReply
     */
    public function getApInitiateReply()
    {
        return $this->apInitiateReply;
    }

    /**
     * @param APInitiateReply $apInitiateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApInitiateReply($apInitiateReply)
    {
        $this->apInitiateReply = $apInitiateReply;

        return $this;
    }

    /**
     * @return APCheckStatusReply
     */
    public function getApCheckStatusReply()
    {
        return $this->apCheckStatusReply;
    }

    /**
     * @param APCheckStatusReply $apCheckStatusReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApCheckStatusReply($apCheckStatusReply)
    {
        $this->apCheckStatusReply = $apCheckStatusReply;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceiptNumber()
    {
        return $this->receiptNumber;
    }

    /**
     * @param string $receiptNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setReceiptNumber($receiptNumber)
    {
        $this->receiptNumber = $receiptNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @param string $additionalData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    /**
     * @return string
     */
    public function getSolutionProviderTransactionID()
    {
        return $this->solutionProviderTransactionID;
    }

    /**
     * @param string $solutionProviderTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setSolutionProviderTransactionID($solutionProviderTransactionID)
    {
        $this->solutionProviderTransactionID = $solutionProviderTransactionID;

        return $this;
    }

    /**
     * @return APReply
     */
    public function getApReply()
    {
        return $this->apReply;
    }

    /**
     * @param APReply $apReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApReply($apReply)
    {
        $this->apReply = $apReply;

        return $this;
    }

    /**
     * @return ShipTo
     */
    public function getShipTo()
    {
        return $this->shipTo;
    }

    /**
     * @param ShipTo $shipTo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setShipTo($shipTo)
    {
        $this->shipTo = $shipTo;

        return $this;
    }

    /**
     * @return BillTo
     */
    public function getBillTo()
    {
        return $this->billTo;
    }

    /**
     * @param BillTo $billTo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setBillTo($billTo)
    {
        $this->billTo = $billTo;

        return $this;
    }

    /**
     * @return APAuthReply
     */
    public function getApAuthReply()
    {
        return $this->apAuthReply;
    }

    /**
     * @param APAuthReply $apAuthReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApAuthReply($apAuthReply)
    {
        $this->apAuthReply = $apAuthReply;

        return $this;
    }

    /**
     * @return APSessionsReply
     */
    public function getApSessionsReply()
    {
        return $this->apSessionsReply;
    }

    /**
     * @param APSessionsReply $apSessionsReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApSessionsReply($apSessionsReply)
    {
        $this->apSessionsReply = $apSessionsReply;

        return $this;
    }

    /**
     * @return APAuthReversalReply
     */
    public function getApAuthReversalReply()
    {
        return $this->apAuthReversalReply;
    }

    /**
     * @param APAuthReversalReply $apAuthReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApAuthReversalReply($apAuthReversalReply)
    {
        $this->apAuthReversalReply = $apAuthReversalReply;

        return $this;
    }

    /**
     * @return APCaptureReply
     */
    public function getApCaptureReply()
    {
        return $this->apCaptureReply;
    }

    /**
     * @param APCaptureReply $apCaptureReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApCaptureReply($apCaptureReply)
    {
        $this->apCaptureReply = $apCaptureReply;

        return $this;
    }

    /**
     * @return APOptionsReply
     */
    public function getApOptionsReply()
    {
        return $this->apOptionsReply;
    }

    /**
     * @param APOptionsReply $apOptionsReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApOptionsReply($apOptionsReply)
    {
        $this->apOptionsReply = $apOptionsReply;

        return $this;
    }

    /**
     * @return APRefundReply
     */
    public function getApRefundReply()
    {
        return $this->apRefundReply;
    }

    /**
     * @param APRefundReply $apRefundReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApRefundReply($apRefundReply)
    {
        $this->apRefundReply = $apRefundReply;

        return $this;
    }

    /**
     * @return APSaleReply
     */
    public function getApSaleReply()
    {
        return $this->apSaleReply;
    }

    /**
     * @param APSaleReply $apSaleReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApSaleReply($apSaleReply)
    {
        $this->apSaleReply = $apSaleReply;

        return $this;
    }

    /**
     * @return APCheckOutDetailsReply
     */
    public function getApCheckoutDetailsReply()
    {
        return $this->apCheckoutDetailsReply;
    }

    /**
     * @param APCheckOutDetailsReply $apCheckoutDetailsReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApCheckoutDetailsReply($apCheckoutDetailsReply)
    {
        $this->apCheckoutDetailsReply = $apCheckoutDetailsReply;

        return $this;
    }

    /**
     * @return APTransactionDetailsReply
     */
    public function getApTransactionDetailsReply()
    {
        return $this->apTransactionDetailsReply;
    }

    /**
     * @param APTransactionDetailsReply $apTransactionDetailsReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApTransactionDetailsReply($apTransactionDetailsReply)
    {
        $this->apTransactionDetailsReply = $apTransactionDetailsReply;

        return $this;
    }

    /**
     * @return APConfirmPurchaseReply
     */
    public function getApConfirmPurchaseReply()
    {
        return $this->apConfirmPurchaseReply;
    }

    /**
     * @param APConfirmPurchaseReply $apConfirmPurchaseReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApConfirmPurchaseReply($apConfirmPurchaseReply)
    {
        $this->apConfirmPurchaseReply = $apConfirmPurchaseReply;

        return $this;
    }

    /**
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param Promotion $promotion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * @return PromotionGroupReply[]
     */
    public function getPromotionGroup()
    {
        return $this->promotionGroup;
    }

    /**
     * @param PromotionGroupReply[] $promotionGroup
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPromotionGroup(array $promotionGroup = null)
    {
        $this->promotionGroup = $promotionGroup;

        return $this;
    }

    /**
     * @return PayPalGetTxnDetailsReply
     */
    public function getPayPalGetTxnDetailsReply()
    {
        return $this->payPalGetTxnDetailsReply;
    }

    /**
     * @param PayPalGetTxnDetailsReply $payPalGetTxnDetailsReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalGetTxnDetailsReply($payPalGetTxnDetailsReply)
    {
        $this->payPalGetTxnDetailsReply = $payPalGetTxnDetailsReply;

        return $this;
    }

    /**
     * @return PayPalTransactionSearchReply
     */
    public function getPayPalTransactionSearchReply()
    {
        return $this->payPalTransactionSearchReply;
    }

    /**
     * @param PayPalTransactionSearchReply $payPalTransactionSearchReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPayPalTransactionSearchReply($payPalTransactionSearchReply)
    {
        $this->payPalTransactionSearchReply = $payPalTransactionSearchReply;

        return $this;
    }

    /**
     * @return EmvReply
     */
    public function getEmvReply()
    {
        return $this->emvReply;
    }

    /**
     * @param EmvReply $emvReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setEmvReply($emvReply)
    {
        $this->emvReply = $emvReply;

        return $this;
    }

    /**
     * @return OriginalTransaction
     */
    public function getOriginalTransaction()
    {
        return $this->originalTransaction;
    }

    /**
     * @param OriginalTransaction $originalTransaction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setOriginalTransaction($originalTransaction)
    {
        $this->originalTransaction = $originalTransaction;

        return $this;
    }

    /**
     * @return HostedDataCreateReply
     */
    public function getHostedDataCreateReply()
    {
        return $this->hostedDataCreateReply;
    }

    /**
     * @param HostedDataCreateReply $hostedDataCreateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setHostedDataCreateReply($hostedDataCreateReply)
    {
        $this->hostedDataCreateReply = $hostedDataCreateReply;

        return $this;
    }

    /**
     * @return HostedDataRetrieveReply
     */
    public function getHostedDataRetrieveReply()
    {
        return $this->hostedDataRetrieveReply;
    }

    /**
     * @param HostedDataRetrieveReply $hostedDataRetrieveReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setHostedDataRetrieveReply($hostedDataRetrieveReply)
    {
        $this->hostedDataRetrieveReply = $hostedDataRetrieveReply;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalesSlipNumber()
    {
        return $this->salesSlipNumber;
    }

    /**
     * @param string $salesSlipNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setSalesSlipNumber($salesSlipNumber)
    {
        $this->salesSlipNumber = $salesSlipNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalProcessorResponse()
    {
        return $this->additionalProcessorResponse;
    }

    /**
     * @param string $additionalProcessorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setAdditionalProcessorResponse($additionalProcessorResponse)
    {
        $this->additionalProcessorResponse = $additionalProcessorResponse;

        return $this;
    }

    /**
     * @return JPO
     */
    public function getJpo()
    {
        return $this->jpo;
    }

    /**
     * @param JPO $jpo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setJpo($jpo)
    {
        $this->jpo = $jpo;

        return $this;
    }

    /**
     * @return Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @param Card $card
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCard($card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * @return PaymentNetworkToken
     */
    public function getPaymentNetworkToken()
    {
        return $this->paymentNetworkToken;
    }

    /**
     * @param PaymentNetworkToken $paymentNetworkToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaymentNetworkToken($paymentNetworkToken)
    {
        $this->paymentNetworkToken = $paymentNetworkToken;

        return $this;
    }

    /**
     * @return VCReply
     */
    public function getVcReply()
    {
        return $this->vcReply;
    }

    /**
     * @param VCReply $vcReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setVcReply($vcReply)
    {
        $this->vcReply = $vcReply;

        return $this;
    }

    /**
     * @return DecryptVisaCheckoutDataReply
     */
    public function getDecryptVisaCheckoutDataReply()
    {
        return $this->decryptVisaCheckoutDataReply;
    }

    /**
     * @param DecryptVisaCheckoutDataReply $decryptVisaCheckoutDataReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setDecryptVisaCheckoutDataReply($decryptVisaCheckoutDataReply)
    {
        $this->decryptVisaCheckoutDataReply = $decryptVisaCheckoutDataReply;

        return $this;
    }

    /**
     * @return GetVisaCheckoutDataReply
     */
    public function getGetVisaCheckoutDataReply()
    {
        return $this->getVisaCheckoutDataReply;
    }

    /**
     * @param GetVisaCheckoutDataReply $getVisaCheckoutDataReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGetVisaCheckoutDataReply($getVisaCheckoutDataReply)
    {
        $this->getVisaCheckoutDataReply = $getVisaCheckoutDataReply;

        return $this;
    }

    /**
     * @return BinLookupReply
     */
    public function getBinLookupReply()
    {
        return $this->binLookupReply;
    }

    /**
     * @param BinLookupReply $binLookupReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setBinLookupReply($binLookupReply)
    {
        $this->binLookupReply = $binLookupReply;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuerMessage()
    {
        return $this->issuerMessage;
    }

    /**
     * @param string $issuerMessage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setIssuerMessage($issuerMessage)
    {
        $this->issuerMessage = $issuerMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return issuer
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @param issuer $issuer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * @return Recipient
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param Recipient $recipient
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeeProgramIndicator()
    {
        return $this->feeProgramIndicator;
    }

    /**
     * @param string $feeProgramIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setFeeProgramIndicator($feeProgramIndicator)
    {
        $this->feeProgramIndicator = $feeProgramIndicator;

        return $this;
    }

    /**
     * @return Installment
     */
    public function getInstallment()
    {
        return $this->installment;
    }

    /**
     * @param Installment $installment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setInstallment($installment)
    {
        $this->installment = $installment;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentAccountReference()
    {
        return $this->paymentAccountReference;
    }

    /**
     * @param string $paymentAccountReference
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaymentAccountReference($paymentAccountReference)
    {
        $this->paymentAccountReference = $paymentAccountReference;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthIndicator()
    {
        return $this->authIndicator;
    }

    /**
     * @param string $authIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setAuthIndicator($authIndicator)
    {
        $this->authIndicator = $authIndicator;

        return $this;
    }

    /**
     * @return UCAF
     */
    public function getUcaf()
    {
        return $this->ucaf;
    }

    /**
     * @param UCAF $ucaf
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setUcaf($ucaf)
    {
        $this->ucaf = $ucaf;

        return $this;
    }

    /**
     * @return Network[]
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * @param Network[] $network
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setNetwork(array $network = null)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * @return InvoiceHeader
     */
    public function getInvoiceHeader()
    {
        return $this->invoiceHeader;
    }

    /**
     * @param InvoiceHeader $invoiceHeader
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setInvoiceHeader($invoiceHeader)
    {
        $this->invoiceHeader = $invoiceHeader;

        return $this;
    }

    /**
     * @return APOrderReply
     */
    public function getApOrderReply()
    {
        return $this->apOrderReply;
    }

    /**
     * @param APOrderReply $apOrderReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApOrderReply($apOrderReply)
    {
        $this->apOrderReply = $apOrderReply;

        return $this;
    }

    /**
     * @return APCancelReply
     */
    public function getApCancelReply()
    {
        return $this->apCancelReply;
    }

    /**
     * @param APCancelReply $apCancelReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApCancelReply($apCancelReply)
    {
        $this->apCancelReply = $apCancelReply;

        return $this;
    }

    /**
     * @return APBillingAgreementReply
     */
    public function getApBillingAgreementReply()
    {
        return $this->apBillingAgreementReply;
    }

    /**
     * @param APBillingAgreementReply $apBillingAgreementReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApBillingAgreementReply($apBillingAgreementReply)
    {
        $this->apBillingAgreementReply = $apBillingAgreementReply;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerVerificationStatus()
    {
        return $this->customerVerificationStatus;
    }

    /**
     * @param string $customerVerificationStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCustomerVerificationStatus($customerVerificationStatus)
    {
        $this->customerVerificationStatus = $customerVerificationStatus;

        return $this;
    }

    /**
     * @return PersonalID
     */
    public function getPersonalID()
    {
        return $this->personalID;
    }

    /**
     * @param PersonalID $personalID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPersonalID($personalID)
    {
        $this->personalID = $personalID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcquirerMerchantNumber()
    {
        return $this->acquirerMerchantNumber;
    }

    /**
     * @param string $acquirerMerchantNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setAcquirerMerchantNumber($acquirerMerchantNumber)
    {
        $this->acquirerMerchantNumber = $acquirerMerchantNumber;

        return $this;
    }

    /**
     * @return Pos
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @param Pos $pos
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuerMessageAction()
    {
        return $this->issuerMessageAction;
    }

    /**
     * @param string $issuerMessageAction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setIssuerMessageAction($issuerMessageAction)
    {
        $this->issuerMessageAction = $issuerMessageAction;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * @param string $customerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;

        return $this;
    }

    /**
     * @return Routing
     */
    public function getRouting()
    {
        return $this->routing;
    }

    /**
     * @param Routing $routing
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setRouting($routing)
    {
        $this->routing = $routing;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTransactionLocalDateTime()
    {
        if ($this->transactionLocalDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->transactionLocalDateTime);
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime $transactionLocalDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setTransactionLocalDateTime(DateTime $transactionLocalDateTime = null)
    {
        if ($transactionLocalDateTime == null) {
            $this->transactionLocalDateTime = null;
        } else {
            $this->transactionLocalDateTime = $transactionLocalDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return APCreateMandateReply
     */
    public function getApCreateMandateReply()
    {
        return $this->apCreateMandateReply;
    }

    /**
     * @param APCreateMandateReply $apCreateMandateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApCreateMandateReply($apCreateMandateReply)
    {
        $this->apCreateMandateReply = $apCreateMandateReply;

        return $this;
    }

    /**
     * @return APMandateStatusReply
     */
    public function getApMandateStatusReply()
    {
        return $this->apMandateStatusReply;
    }

    /**
     * @param APMandateStatusReply $apMandateStatusReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApMandateStatusReply($apMandateStatusReply)
    {
        $this->apMandateStatusReply = $apMandateStatusReply;

        return $this;
    }

    /**
     * @return APUpdateMandateReply
     */
    public function getApUpdateMandateReply()
    {
        return $this->apUpdateMandateReply;
    }

    /**
     * @param APUpdateMandateReply $apUpdateMandateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApUpdateMandateReply($apUpdateMandateReply)
    {
        $this->apUpdateMandateReply = $apUpdateMandateReply;

        return $this;
    }

    /**
     * @return APImportMandateReply
     */
    public function getApImportMandateReply()
    {
        return $this->apImportMandateReply;
    }

    /**
     * @param APImportMandateReply $apImportMandateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApImportMandateReply($apImportMandateReply)
    {
        $this->apImportMandateReply = $apImportMandateReply;

        return $this;
    }

    /**
     * @return APRevokeMandateReply
     */
    public function getApRevokeMandateReply()
    {
        return $this->apRevokeMandateReply;
    }

    /**
     * @param APRevokeMandateReply $apRevokeMandateReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setApRevokeMandateReply($apRevokeMandateReply)
    {
        $this->apRevokeMandateReply = $apRevokeMandateReply;

        return $this;
    }

    /**
     * @return GetMasterpassDataReply
     */
    public function getGetMasterpassDataReply()
    {
        return $this->getMasterpassDataReply;
    }

    /**
     * @param GetMasterpassDataReply $getMasterpassDataReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGetMasterpassDataReply($getMasterpassDataReply)
    {
        $this->getMasterpassDataReply = $getMasterpassDataReply;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentNetworkMerchantID()
    {
        return $this->paymentNetworkMerchantID;
    }

    /**
     * @param string $paymentNetworkMerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setPaymentNetworkMerchantID($paymentNetworkMerchantID)
    {
        $this->paymentNetworkMerchantID = $paymentNetworkMerchantID;

        return $this;
    }

    /**
     * @return Wallet
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * @param Wallet $wallet
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return float
     */
    public function getCashbackAmount()
    {
        return $this->cashbackAmount;
    }

    /**
     * @param float $cashbackAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCashbackAmount($cashbackAmount)
    {
        $this->cashbackAmount = $cashbackAmount;

        return $this;
    }

    /**
     * @return GiftCard
     */
    public function getGiftCard()
    {
        return $this->giftCard;
    }

    /**
     * @param GiftCard $giftCard
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGiftCard($giftCard)
    {
        $this->giftCard = $giftCard;

        return $this;
    }

    /**
     * @return GiftCardActivationReply
     */
    public function getGiftCardActivationReply()
    {
        return $this->giftCardActivationReply;
    }

    /**
     * @param GiftCardActivationReply $giftCardActivationReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGiftCardActivationReply($giftCardActivationReply)
    {
        $this->giftCardActivationReply = $giftCardActivationReply;

        return $this;
    }

    /**
     * @return GiftCardBalanceInquiryReply
     */
    public function getGiftCardBalanceInquiryReply()
    {
        return $this->giftCardBalanceInquiryReply;
    }

    /**
     * @param GiftCardBalanceInquiryReply $giftCardBalanceInquiryReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGiftCardBalanceInquiryReply($giftCardBalanceInquiryReply)
    {
        $this->giftCardBalanceInquiryReply = $giftCardBalanceInquiryReply;

        return $this;
    }

    /**
     * @return GiftCardRedemptionReply
     */
    public function getGiftCardRedemptionReply()
    {
        return $this->giftCardRedemptionReply;
    }

    /**
     * @param GiftCardRedemptionReply $giftCardRedemptionReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGiftCardRedemptionReply($giftCardRedemptionReply)
    {
        $this->giftCardRedemptionReply = $giftCardRedemptionReply;

        return $this;
    }

    /**
     * @return GiftCardVoidReply
     */
    public function getGiftCardVoidReply()
    {
        return $this->giftCardVoidReply;
    }

    /**
     * @param GiftCardVoidReply $giftCardVoidReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGiftCardVoidReply($giftCardVoidReply)
    {
        $this->giftCardVoidReply = $giftCardVoidReply;

        return $this;
    }

    /**
     * @return GiftCardReversalReply
     */
    public function getGiftCardReversalReply()
    {
        return $this->giftCardReversalReply;
    }

    /**
     * @param GiftCardReversalReply $giftCardReversalReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setGiftCardReversalReply($giftCardReversalReply)
    {
        $this->giftCardReversalReply = $giftCardReversalReply;

        return $this;
    }

    /**
     * @return CCCheckStatusReply
     */
    public function getCcCheckStatusReply()
    {
        return $this->ccCheckStatusReply;
    }

    /**
     * @param CCCheckStatusReply $ccCheckStatusReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setCcCheckStatusReply($ccCheckStatusReply)
    {
        $this->ccCheckStatusReply = $ccCheckStatusReply;

        return $this;
    }

    /**
     * @return ECAVSReply
     */
    public function getEcAVSReply()
    {
        return $this->ecAVSReply;
    }

    /**
     * @param ECAVSReply $ecAVSReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setEcAVSReply($ecAVSReply)
    {
        $this->ecAVSReply = $ecAVSReply;

        return $this;
    }

    /**
     * @return AbortReply
     */
    public function getAbortReply()
    {
        return $this->abortReply;
    }

    /**
     * @param AbortReply $abortReply
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setAbortReply($abortReply)
    {
        $this->abortReply = $abortReply;

        return $this;
    }

    /**
     * @return ReplyReserved
     */
    public function getReserved()
    {
        return $this->reserved;
    }

    /**
     * @param ReplyReserved $reserved
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage
     */
    public function setReserved($reserved)
    {
        $this->reserved = $reserved;

        return $this;
    }
}
