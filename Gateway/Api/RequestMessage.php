<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Exception;

class RequestMessage
{
    /**
     * @var string $merchantID
     */
    protected $merchantID;

    /**
     * @var string $merchantReferenceCode
     */
    protected $merchantReferenceCode;

    /**
     * @var boolean $debtIndicator
     */
    protected $debtIndicator;

    /**
     * @var string $clientLibrary
     */
    protected $clientLibrary;

    /**
     * @var string $clientLibraryVersion
     */
    protected $clientLibraryVersion;

    /**
     * @var string $clientEnvironment
     */
    protected $clientEnvironment;

    /**
     * @var string $clientSecurityLibraryVersion
     */
    protected $clientSecurityLibraryVersion;

    /**
     * @var string $clientApplication
     */
    protected $clientApplication;

    /**
     * @var string $clientApplicationVersion
     */
    protected $clientApplicationVersion;

    /**
     * @var string $clientApplicationUser
     */
    protected $clientApplicationUser;

    /**
     * @var string $routingCode
     */
    protected $routingCode;

    /**
     * @var string $comments
     */
    protected $comments;

    /**
     * @var string $returnURL
     */
    protected $returnURL;

    /**
     * @var InvoiceHeader $invoiceHeader
     */
    protected $invoiceHeader;

    /**
     * @var string $paymentScheme
     */
    protected $paymentScheme;

    /**
     * @var string $mandateID
     */
    protected $mandateID;

    /**
     * @var string $aggregatorMerchantIdentifier
     */
    protected $aggregatorMerchantIdentifier;

    /**
     * @var string $customerID
     */
    protected $customerID;

    /**
     * @var string $customerFirstName
     */
    protected $customerFirstName;

    /**
     * @var string $customerLastName
     */
    protected $customerLastName;

    /**
     * @var BillTo $billTo
     */
    protected $billTo;

    /**
     * @var ShipTo $shipTo
     */
    protected $shipTo;

    /**
     * @var PersonalID $personalID
     */
    protected $personalID;

    /**
     * @var ShipFrom $shipFrom
     */
    protected $shipFrom;

    /**
     * @var Item[] $item
     */
    protected $item;

    /**
     * @var PurchaseTotals $purchaseTotals
     */
    protected $purchaseTotals;

    /**
     * @var FundingTotals $fundingTotals
     */
    protected $fundingTotals;

    /**
     * @var DCC $dcc
     */
    protected $dcc;

    /**
     * @var Pos $pos
     */
    protected $pos;

    /**
     * @var Pin $pin
     */
    protected $pin;

    /**
     * @var EncryptedPayment $encryptedPayment
     */
    protected $encryptedPayment;

    /**
     * @var Installment $installment
     */
    protected $installment;

    /**
     * @var Card $card
     */
    protected $card;

    /**
     * @var Category $category
     */
    protected $category;

    /**
     * @var Check $check
     */
    protected $check;

    /**
     * @var BML $bml
     */
    protected $bml;

    /**
     * @var GECC $gecc
     */
    protected $gecc;

    /**
     * @var UCAF $ucaf
     */
    protected $ucaf;

    /**
     * @var FundTransfer $fundTransfer
     */
    protected $fundTransfer;

    /**
     * @var BankInfo $bankInfo
     */
    protected $bankInfo;

    /**
     * @var Subscription $subscription
     */
    protected $subscription;

    /**
     * @var RecurringSubscriptionInfo $recurringSubscriptionInfo
     */
    protected $recurringSubscriptionInfo;

    /**
     * @var TokenSource $tokenSource
     */
    protected $tokenSource;

    /**
     * @var DecisionManager $decisionManager
     */
    protected $decisionManager;

    /**
     * @var OtherTax $otherTax
     */
    protected $otherTax;

    /**
     * @var PayPal $paypal
     */
    protected $paypal;

    /**
     * @var MerchantDefinedData $merchantDefinedData
     */
    protected $merchantDefinedData;

    /**
     * @var MerchantSecureData $merchantSecureData
     */
    protected $merchantSecureData;

    /**
     * @var JPO $jpo
     */
    protected $jpo;

    /**
     * @var string $orderRequestToken
     */
    protected $orderRequestToken;

    /**
     * @var string $linkToRequest
     */
    protected $linkToRequest;

    /**
     * @var ServiceFee $serviceFee
     */
    protected $serviceFee;

    /**
     * @var GiftCard $giftCard
     */
    protected $giftCard;

    /**
     * @var CCAuthService $ccAuthService
     */
    protected $ccAuthService;

    /**
     * @var OCTService $octService
     */
    protected $octService;

    /**
     * @var ECAVSService $ecAVSService
     */
    protected $ecAVSService;

    /**
     * @var GiftCardActivationService $giftCardActivationService
     */
    protected $giftCardActivationService;

    /**
     * @var GiftCardBalanceInquiryService $giftCardBalanceInquiryService
     */
    protected $giftCardBalanceInquiryService;

    /**
     * @var GiftCardRedemptionService $giftCardRedemptionService
     */
    protected $giftCardRedemptionService;

    /**
     * @var GiftCardVoidService $giftCardVoidService
     */
    protected $giftCardVoidService;

    /**
     * @var GiftCardReversalService $giftCardReversalService
     */
    protected $giftCardReversalService;

    /**
     * @var VerificationService $verificationService
     */
    protected $verificationService;

    /**
     * @var CCSaleService $ccSaleService
     */
    protected $ccSaleService;

    /**
     * @var CCSaleCreditService $ccSaleCreditService
     */
    protected $ccSaleCreditService;

    /**
     * @var CCSaleReversalService $ccSaleReversalService
     */
    protected $ccSaleReversalService;

    /**
     * @var CCIncrementalAuthService $ccIncrementalAuthService
     */
    protected $ccIncrementalAuthService;

    /**
     * @var CCCaptureService $ccCaptureService
     */
    protected $ccCaptureService;

    /**
     * @var CCCreditService $ccCreditService
     */
    protected $ccCreditService;

    /**
     * @var CCAuthReversalService $ccAuthReversalService
     */
    protected $ccAuthReversalService;

    /**
     * @var CCAutoAuthReversalService $ccAutoAuthReversalService
     */
    protected $ccAutoAuthReversalService;

    /**
     * @var CCDCCService $ccDCCService
     */
    protected $ccDCCService;

    /**
     * @var ServiceFeeCalculateService $serviceFeeCalculateService
     */
    protected $serviceFeeCalculateService;

    /**
     * @var ECDebitService $ecDebitService
     */
    protected $ecDebitService;

    /**
     * @var ECCreditService $ecCreditService
     */
    protected $ecCreditService;

    /**
     * @var ECAuthenticateService $ecAuthenticateService
     */
    protected $ecAuthenticateService;

    /**
     * @var PayerAuthEnrollService $payerAuthEnrollService
     */
    protected $payerAuthEnrollService;

    /**
     * @var PayerAuthValidateService $payerAuthValidateService
     */
    protected $payerAuthValidateService;

    /**
     * @var TaxService $taxService
     */
    protected $taxService;

    /**
     * @var DMEService $dmeService
     */
    protected $dmeService;

    /**
     * @var AFSService $afsService
     */
    protected $afsService;

    /**
     * @var DAVService $davService
     */
    protected $davService;

    /**
     * @var ExportService $exportService
     */
    protected $exportService;

    /**
     * @var FXRatesService $fxRatesService
     */
    protected $fxRatesService;

    /**
     * @var BankTransferService $bankTransferService
     */
    protected $bankTransferService;

    /**
     * @var BankTransferRefundService $bankTransferRefundService
     */
    protected $bankTransferRefundService;

    /**
     * @var BankTransferRealTimeService $bankTransferRealTimeService
     */
    protected $bankTransferRealTimeService;

    /**
     * @var DirectDebitMandateService $directDebitMandateService
     */
    protected $directDebitMandateService;

    /**
     * @var DirectDebitService $directDebitService
     */
    protected $directDebitService;

    /**
     * @var DirectDebitRefundService $directDebitRefundService
     */
    protected $directDebitRefundService;

    /**
     * @var DirectDebitValidateService $directDebitValidateService
     */
    protected $directDebitValidateService;

    /**
     * @var DeviceFingerprintData[] $deviceFingerprintData
     */
    protected $deviceFingerprintData;

    /**
     * @var PaySubscriptionCreateService $paySubscriptionCreateService
     */
    protected $paySubscriptionCreateService;

    /**
     * @var PaySubscriptionUpdateService $paySubscriptionUpdateService
     */
    protected $paySubscriptionUpdateService;

    /**
     * @var PaySubscriptionEventUpdateService $paySubscriptionEventUpdateService
     */
    protected $paySubscriptionEventUpdateService;

    /**
     * @var PaySubscriptionRetrieveService $paySubscriptionRetrieveService
     */
    protected $paySubscriptionRetrieveService;

    /**
     * @var PaySubscriptionDeleteService $paySubscriptionDeleteService
     */
    protected $paySubscriptionDeleteService;

    /**
     * @var PayPalPaymentService $payPalPaymentService
     */
    protected $payPalPaymentService;

    /**
     * @var PayPalCreditService $payPalCreditService
     */
    protected $payPalCreditService;

    /**
     * @var VoidService $voidService
     */
    protected $voidService;

    /**
     * @var BusinessRules $businessRules
     */
    protected $businessRules;

    /**
     * @var PinlessDebitService $pinlessDebitService
     */
    protected $pinlessDebitService;

    /**
     * @var PinlessDebitValidateService $pinlessDebitValidateService
     */
    protected $pinlessDebitValidateService;

    /**
     * @var PinlessDebitReversalService $pinlessDebitReversalService
     */
    protected $pinlessDebitReversalService;

    /**
     * @var Batch $batch
     */
    protected $batch;

    /**
     * @var AirlineData $airlineData
     */
    protected $airlineData;

    /**
     * @var AncillaryData $ancillaryData
     */
    protected $ancillaryData;

    /**
     * @var LodgingData $lodgingData
     */
    protected $lodgingData;

    /**
     * @var PayPalButtonCreateService $payPalButtonCreateService
     */
    protected $payPalButtonCreateService;

    /**
     * @var PayPalPreapprovedPaymentService $payPalPreapprovedPaymentService
     */
    protected $payPalPreapprovedPaymentService;

    /**
     * @var PayPalPreapprovedUpdateService $payPalPreapprovedUpdateService
     */
    protected $payPalPreapprovedUpdateService;

    /**
     * @var RiskUpdateService $riskUpdateService
     */
    protected $riskUpdateService;

    /**
     * @var FraudUpdateService $fraudUpdateService
     */
    protected $fraudUpdateService;

    /**
     * @var CaseManagementActionService $caseManagementActionService
     */
    protected $caseManagementActionService;

    /**
     * @var RequestReserved[] $reserved
     */
    protected $reserved;

    /**
     * @var string $deviceFingerprintID
     */
    protected $deviceFingerprintID;

    /**
     * @var boolean $deviceFingerprintRaw
     */
    protected $deviceFingerprintRaw;

    /**
     * @var string $deviceFingerprintHash
     */
    protected $deviceFingerprintHash;

    /**
     * @var PayPalRefundService $payPalRefundService
     */
    protected $payPalRefundService;

    /**
     * @var PayPalAuthReversalService $payPalAuthReversalService
     */
    protected $payPalAuthReversalService;

    /**
     * @var PayPalDoCaptureService $payPalDoCaptureService
     */
    protected $payPalDoCaptureService;

    /**
     * @var PayPalEcDoPaymentService $payPalEcDoPaymentService
     */
    protected $payPalEcDoPaymentService;

    /**
     * @var PayPalEcGetDetailsService $payPalEcGetDetailsService
     */
    protected $payPalEcGetDetailsService;

    /**
     * @var PayPalEcSetService $payPalEcSetService
     */
    protected $payPalEcSetService;

    /**
     * @var PayPalEcOrderSetupService $payPalEcOrderSetupService
     */
    protected $payPalEcOrderSetupService;

    /**
     * @var PayPalAuthorizationService $payPalAuthorizationService
     */
    protected $payPalAuthorizationService;

    /**
     * @var PayPalUpdateAgreementService $payPalUpdateAgreementService
     */
    protected $payPalUpdateAgreementService;

    /**
     * @var PayPalCreateAgreementService $payPalCreateAgreementService
     */
    protected $payPalCreateAgreementService;

    /**
     * @var PayPalDoRefTransactionService $payPalDoRefTransactionService
     */
    protected $payPalDoRefTransactionService;

    /**
     * @var ChinaPaymentService $chinaPaymentService
     */
    protected $chinaPaymentService;

    /**
     * @var ChinaRefundService $chinaRefundService
     */
    protected $chinaRefundService;

    /**
     * @var BoletoPaymentService $boletoPaymentService
     */
    protected $boletoPaymentService;

    /**
     * @var string $apPaymentType
     */
    protected $apPaymentType;

    /**
     * @var APInitiateService $apInitiateService
     */
    protected $apInitiateService;

    /**
     * @var APCheckStatusService $apCheckStatusService
     */
    protected $apCheckStatusService;

    /**
     * @var boolean $ignoreCardExpiration
     */
    protected $ignoreCardExpiration;

    /**
     * @var string $reportGroup
     */
    protected $reportGroup;

    /**
     * @var string $processorID
     */
    protected $processorID;

    /**
     * @var string $thirdPartyCertificationNumber
     */
    protected $thirdPartyCertificationNumber;

    /**
     * @var \DateTime $transactionLocalDateTime
     */
    protected $transactionLocalDateTime;

    /**
     * @var string $solutionProviderTransactionID
     */
    protected $solutionProviderTransactionID;

    /**
     * @var float $surchargeAmount
     */
    protected $surchargeAmount;

    /**
     * @var string $surchargeSign
     */
    protected $surchargeSign;

    /**
     * @var string $pinDataEncryptedPIN
     */
    protected $pinDataEncryptedPIN;

    /**
     * @var string $pinDataKeySerialNumber
     */
    protected $pinDataKeySerialNumber;

    /**
     * @var int $pinDataPinBlockEncodingFormat
     */
    protected $pinDataPinBlockEncodingFormat;

    /**
     * @var float $cashbackAmount
     */
    protected $cashbackAmount;

    /**
     * @var PinDebitPurchaseService $pinDebitPurchaseService
     */
    protected $pinDebitPurchaseService;

    /**
     * @var PinDebitCreditService $pinDebitCreditService
     */
    protected $pinDebitCreditService;

    /**
     * @var PinDebitReversalService $pinDebitReversalService
     */
    protected $pinDebitReversalService;

    /**
     * @var AP $ap
     */
    protected $ap;

    /**
     * @var APAuthService $apAuthService
     */
    protected $apAuthService;

    /**
     * @var APAuthReversalService $apAuthReversalService
     */
    protected $apAuthReversalService;

    /**
     * @var APCaptureService $apCaptureService
     */
    protected $apCaptureService;

    /**
     * @var APOptionsService $apOptionsService
     */
    protected $apOptionsService;

    /**
     * @var APRefundService $apRefundService
     */
    protected $apRefundService;

    /**
     * @var APSaleService $apSaleService
     */
    protected $apSaleService;

    /**
     * @var APCheckOutDetailsService $apCheckoutDetailsService
     */
    protected $apCheckoutDetailsService;

    /**
     * @var APSessionsService $apSessionsService
     */
    protected $apSessionsService;

    /**
     * @var APUI $apUI
     */
    protected $apUI;

    /**
     * @var APTransactionDetailsService $apTransactionDetailsService
     */
    protected $apTransactionDetailsService;

    /**
     * @var APConfirmPurchaseService $apConfirmPurchaseService
     */
    protected $apConfirmPurchaseService;

    /**
     * @var PayPalGetTxnDetailsService $payPalGetTxnDetailsService
     */
    protected $payPalGetTxnDetailsService;

    /**
     * @var PayPalTransactionSearchService $payPalTransactionSearchService
     */
    protected $payPalTransactionSearchService;

    /**
     * @var CCDCCUpdateService $ccDCCUpdateService
     */
    protected $ccDCCUpdateService;

    /**
     * @var EmvRequest $emvRequest
     */
    protected $emvRequest;

    /**
     * @var merchant $merchant
     */
    protected $merchant;

    /**
     * @var string $merchantTransactionIdentifier
     */
    protected $merchantTransactionIdentifier;

    /**
     * @var HostedDataCreateService $hostedDataCreateService
     */
    protected $hostedDataCreateService;

    /**
     * @var HostedDataRetrieveService $hostedDataRetrieveService
     */
    protected $hostedDataRetrieveService;

    /**
     * @var string $merchantCategoryCode
     */
    protected $merchantCategoryCode;

    /**
     * @var string $merchantCategoryCodeDomestic
     */
    protected $merchantCategoryCodeDomestic;

    /**
     * @var string $salesSlipNumber
     */
    protected $salesSlipNumber;

    /**
     * @var string $merchandiseCode
     */
    protected $merchandiseCode;

    /**
     * @var string $merchandiseDescription
     */
    protected $merchandiseDescription;

    /**
     * @var string $paymentInitiationChannel
     */
    protected $paymentInitiationChannel;

    /**
     * @var string $extendedCreditTotalCount
     */
    protected $extendedCreditTotalCount;

    /**
     * @var string $authIndicator
     */
    protected $authIndicator;

    /**
     * @var PaymentNetworkToken $paymentNetworkToken
     */
    protected $paymentNetworkToken;

    /**
     * @var Recipient $recipient
     */
    protected $recipient;

    /**
     * @var Sender $sender
     */
    protected $sender;

    /**
     * @var AutoRentalData $autoRentalData
     */
    protected $autoRentalData;

    /**
     * @var string $paymentSolution
     */
    protected $paymentSolution;

    /**
     * @var VC $vc
     */
    protected $vc;

    /**
     * @var DecryptVisaCheckoutDataService $decryptVisaCheckoutDataService
     */
    protected $decryptVisaCheckoutDataService;

    /**
     * @var string $taxManagementIndicator
     */
    protected $taxManagementIndicator;

    /**
     * @var PromotionGroup[] $promotionGroup
     */
    protected $promotionGroup;

    /**
     * @var Wallet $wallet
     */
    protected $wallet;

    /**
     * @var Aft $aft
     */
    protected $aft;

    /**
     * @var boolean $balanceInquiry
     */
    protected $balanceInquiry;

    /**
     * @var boolean $prenoteTransaction
     */
    protected $prenoteTransaction;

    /**
     * @var EncryptPaymentDataService $encryptPaymentDataService
     */
    protected $encryptPaymentDataService;

    /**
     * @var string $nationalNetDomesticData
     */
    protected $nationalNetDomesticData;

    /**
     * @var string $subsequentAuth
     */
    protected $subsequentAuth;

    /**
     * @var float $subsequentAuthOriginalAmount
     */
    protected $subsequentAuthOriginalAmount;

    /**
     * @var BinLookupService $binLookupService
     */
    protected $binLookupService;

    /**
     * @var string $verificationCode
     */
    protected $verificationCode;

    /**
     * @var string $mobileNumber
     */
    protected $mobileNumber;

    /**
     * @var issuer $issuer
     */
    protected $issuer;

    /**
     * @var string $partnerSolutionID
     */
    protected $partnerSolutionID;

    /**
     * @var string $developerID
     */
    protected $developerID;

    /**
     * @var GETVisaCheckoutDataService $getVisaCheckoutDataService
     */
    protected $getVisaCheckoutDataService;

    /**
     * @var string $customerSignatureImage
     */
    protected $customerSignatureImage;

    /**
     * @var TransactionMetadataService $transactionMetadataService
     */
    protected $transactionMetadataService;

    /**
     * @var string $subsequentAuthFirst
     */
    protected $subsequentAuthFirst;

    /**
     * @var string $subsequentAuthReason
     */
    protected $subsequentAuthReason;

    /**
     * @var string $subsequentAuthTransactionID
     */
    protected $subsequentAuthTransactionID;

    /**
     * @var string $subsequentAuthStoredCredential
     */
    protected $subsequentAuthStoredCredential;

    /**
     * @var Loan $loan
     */
    protected $loan;

    /**
     * @var string $eligibilityInquiry
     */
    protected $eligibilityInquiry;

    /**
     * @var string $redemptionInquiry
     */
    protected $redemptionInquiry;

    /**
     * @var string $feeProgramIndicator
     */
    protected $feeProgramIndicator;

    /**
     * @var APOrderService $apOrderService
     */
    protected $apOrderService;

    /**
     * @var APCancelService $apCancelService
     */
    protected $apCancelService;

    /**
     * @var APBillingAgreementService $apBillingAgreementService
     */
    protected $apBillingAgreementService;

    /**
     * @var string $note_toPayee
     */
    protected $note_toPayee;

    /**
     * @var string $note_toPayer
     */
    protected $note_toPayer;

    /**
     * @var string $clientMetadataID
     */
    protected $clientMetadataID;

    /**
     * @var string $partnerSDKversion
     */
    protected $partnerSDKversion;

    /**
     * @var string $partnerOriginalTransactionID
     */
    protected $partnerOriginalTransactionID;

    /**
     * @var string $cardTypeSelectionIndicator
     */
    protected $cardTypeSelectionIndicator;

    /**
     * @var APCreateMandateService $apCreateMandateService
     */
    protected $apCreateMandateService;

    /**
     * @var APMandateStatusService $apMandateStatusService
     */
    protected $apMandateStatusService;

    /**
     * @var APUpdateMandateService $apUpdateMandateService
     */
    protected $apUpdateMandateService;

    /**
     * @var APImportMandateService $apImportMandateService
     */
    protected $apImportMandateService;

    /**
     * @var APRevokeMandateService $apRevokeMandateService
     */
    protected $apRevokeMandateService;

    /**
     * @var string $billPaymentType
     */
    protected $billPaymentType;

    /**
     * @var PostdatedTransaction $postdatedTransaction
     */
    protected $postdatedTransaction;

    /**
     * @var GetMasterpassDataService $getMasterpassDataService
     */
    protected $getMasterpassDataService;

    /**
     * @var CCCheckStatusService $ccCheckStatusService
     */
    protected $ccCheckStatusService;

    /**
     * @var mPOS $mPOS
     */
    protected $mPOS;

    /**
     * @var AbortService $abortService
     */
    protected $abortService;

    /**
     * @var boolean $ignoreRelaxAVS
     */
    protected $ignoreRelaxAVS;

    /**
     * @return string
     */
    public function getMerchantID()
    {
        return $this->merchantID;
    }

    /**
     * @param string $merchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchantID($merchantID)
    {
        $this->merchantID = substr($merchantID, 0, 30);

        return $this;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchantReferenceCode($merchantReferenceCode)
    {
        $this->merchantReferenceCode = substr($merchantReferenceCode, 0, 50);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDebtIndicator()
    {
        return $this->debtIndicator;
    }

    /**
     * @param boolean $debtIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDebtIndicator($debtIndicator)
    {
        $this->debtIndicator = $debtIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientLibrary()
    {
        return $this->clientLibrary;
    }

    /**
     * @param string $clientLibrary
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientLibrary($clientLibrary)
    {
        $this->clientLibrary = $clientLibrary;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientLibraryVersion()
    {
        return $this->clientLibraryVersion;
    }

    /**
     * @param string $clientLibraryVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientLibraryVersion($clientLibraryVersion)
    {
        $this->clientLibraryVersion = $clientLibraryVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientEnvironment()
    {
        return $this->clientEnvironment;
    }

    /**
     * @param string $clientEnvironment
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientEnvironment($clientEnvironment)
    {
        $this->clientEnvironment = $clientEnvironment;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecurityLibraryVersion()
    {
        return $this->clientSecurityLibraryVersion;
    }

    /**
     * @param string $clientSecurityLibraryVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientSecurityLibraryVersion($clientSecurityLibraryVersion)
    {
        $this->clientSecurityLibraryVersion = $clientSecurityLibraryVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientApplication()
    {
        return $this->clientApplication;
    }

    /**
     * @param string $clientApplication
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientApplication($clientApplication)
    {
        $this->clientApplication = $clientApplication;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientApplicationVersion()
    {
        return $this->clientApplicationVersion;
    }

    /**
     * @param string $clientApplicationVersion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientApplicationVersion($clientApplicationVersion)
    {
        $this->clientApplicationVersion = $clientApplicationVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientApplicationUser()
    {
        return $this->clientApplicationUser;
    }

    /**
     * @param string $clientApplicationUser
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientApplicationUser($clientApplicationUser)
    {
        $this->clientApplicationUser = $clientApplicationUser;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoutingCode()
    {
        return $this->routingCode;
    }

    /**
     * @param string $routingCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setRoutingCode($routingCode)
    {
        $this->routingCode = $routingCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnURL()
    {
        return $this->returnURL;
    }

    /**
     * @param string $returnURL
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setReturnURL($returnURL)
    {
        $this->returnURL = $returnURL;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setInvoiceHeader($invoiceHeader)
    {
        $this->invoiceHeader = $invoiceHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentScheme()
    {
        return $this->paymentScheme;
    }

    /**
     * @param string $paymentScheme
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaymentScheme($paymentScheme)
    {
        $this->paymentScheme = $paymentScheme;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMandateID($mandateID)
    {
        $this->mandateID = $mandateID;

        return $this;
    }

    /**
     * @return string
     */
    public function getAggregatorMerchantIdentifier()
    {
        return $this->aggregatorMerchantIdentifier;
    }

    /**
     * @param string $aggregatorMerchantIdentifier
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAggregatorMerchantIdentifier($aggregatorMerchantIdentifier)
    {
        $this->aggregatorMerchantIdentifier = $aggregatorMerchantIdentifier;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }

    /**
     * @param string $customerFirstName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCustomerFirstName($customerFirstName)
    {
        $this->customerFirstName = $customerFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }

    /**
     * @param string $customerLastName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCustomerLastName($customerLastName)
    {
        $this->customerLastName = $customerLastName;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBillTo($billTo)
    {
        $this->billTo = $billTo;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setShipTo($shipTo)
    {
        $this->shipTo = $shipTo;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPersonalID($personalID)
    {
        $this->personalID = $personalID;

        return $this;
    }

    /**
     * @return ShipFrom
     */
    public function getShipFrom()
    {
        return $this->shipFrom;
    }

    /**
     * @param ShipFrom $shipFrom
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setShipFrom($shipFrom)
    {
        $this->shipFrom = $shipFrom;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item[] $item
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setItem(array $item = null)
    {
        $this->item = $item;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPurchaseTotals($purchaseTotals)
    {
        $this->purchaseTotals = $purchaseTotals;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setFundingTotals($fundingTotals)
    {
        $this->fundingTotals = $fundingTotals;

        return $this;
    }

    /**
     * @return DCC
     */
    public function getDcc()
    {
        return $this->dcc;
    }

    /**
     * @param DCC $dcc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDcc($dcc)
    {
        $this->dcc = $dcc;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * @return Pin
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param Pin $pin
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEncryptedPayment($encryptedPayment)
    {
        $this->encryptedPayment = $encryptedPayment;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setInstallment($installment)
    {
        $this->installment = $installment;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCard($card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Check
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * @param Check $check
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCheck($check)
    {
        $this->check = $check;

        return $this;
    }

    /**
     * @return BML
     */
    public function getBml()
    {
        return $this->bml;
    }

    /**
     * @param BML $bml
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBml($bml)
    {
        $this->bml = $bml;

        return $this;
    }

    /**
     * @return GECC
     */
    public function getGecc()
    {
        return $this->gecc;
    }

    /**
     * @param GECC $gecc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGecc($gecc)
    {
        $this->gecc = $gecc;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setUcaf($ucaf)
    {
        $this->ucaf = $ucaf;

        return $this;
    }

    /**
     * @return FundTransfer
     */
    public function getFundTransfer()
    {
        return $this->fundTransfer;
    }

    /**
     * @param FundTransfer $fundTransfer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setFundTransfer($fundTransfer)
    {
        $this->fundTransfer = $fundTransfer;

        return $this;
    }

    /**
     * @return BankInfo
     */
    public function getBankInfo()
    {
        return $this->bankInfo;
    }

    /**
     * @param BankInfo $bankInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBankInfo($bankInfo)
    {
        $this->bankInfo = $bankInfo;

        return $this;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param Subscription $subscription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return RecurringSubscriptionInfo
     */
    public function getRecurringSubscriptionInfo()
    {
        return $this->recurringSubscriptionInfo;
    }

    /**
     * @param RecurringSubscriptionInfo $recurringSubscriptionInfo
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setRecurringSubscriptionInfo($recurringSubscriptionInfo)
    {
        $this->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        return $this;
    }

    /**
     * @return TokenSource
     */
    public function getTokenSource()
    {
        return $this->tokenSource;
    }

    /**
     * @param TokenSource $tokenSource
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setTokenSource($tokenSource)
    {
        $this->tokenSource = $tokenSource;

        return $this;
    }

    /**
     * @return DecisionManager
     */
    public function getDecisionManager()
    {
        return $this->decisionManager;
    }

    /**
     * @param DecisionManager $decisionManager
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDecisionManager($decisionManager)
    {
        $this->decisionManager = $decisionManager;

        return $this;
    }

    /**
     * @return OtherTax
     */
    public function getOtherTax()
    {
        return $this->otherTax;
    }

    /**
     * @param OtherTax $otherTax
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setOtherTax($otherTax)
    {
        $this->otherTax = $otherTax;

        return $this;
    }

    /**
     * @return PayPal
     */
    public function getPaypal()
    {
        return $this->paypal;
    }

    /**
     * @param PayPal $paypal
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaypal($paypal)
    {
        $this->paypal = $paypal;

        return $this;
    }

    /**
     * @return MerchantDefinedData
     */
    public function getMerchantDefinedData()
    {
        return $this->merchantDefinedData;
    }

    /**
     * @param MerchantDefinedData $merchantDefinedData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchantDefinedData($merchantDefinedData)
    {
        $this->merchantDefinedData = $merchantDefinedData;

        return $this;
    }

    /**
     * @return MerchantSecureData
     */
    public function getMerchantSecureData()
    {
        return $this->merchantSecureData;
    }

    /**
     * @param MerchantSecureData $merchantSecureData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchantSecureData($merchantSecureData)
    {
        $this->merchantSecureData = $merchantSecureData;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setJpo($jpo)
    {
        $this->jpo = $jpo;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderRequestToken()
    {
        return $this->orderRequestToken;
    }

    /**
     * @param string $orderRequestToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setOrderRequestToken($orderRequestToken)
    {
        $this->orderRequestToken = $orderRequestToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkToRequest()
    {
        return $this->linkToRequest;
    }

    /**
     * @param string $linkToRequest
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setLinkToRequest($linkToRequest)
    {
        $this->linkToRequest = $linkToRequest;

        return $this;
    }

    /**
     * @return ServiceFee
     */
    public function getServiceFee()
    {
        return $this->serviceFee;
    }

    /**
     * @param ServiceFee $serviceFee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setServiceFee($serviceFee)
    {
        $this->serviceFee = $serviceFee;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGiftCard($giftCard)
    {
        $this->giftCard = $giftCard;

        return $this;
    }

    /**
     * @return CCAuthService
     */
    public function getCcAuthService()
    {
        return $this->ccAuthService;
    }

    /**
     * @param CCAuthService $ccAuthService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcAuthService($ccAuthService)
    {
        $this->ccAuthService = $ccAuthService;

        return $this;
    }

    /**
     * @return OCTService
     */
    public function getOctService()
    {
        return $this->octService;
    }

    /**
     * @param OCTService $octService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setOctService($octService)
    {
        $this->octService = $octService;

        return $this;
    }

    /**
     * @return ECAVSService
     */
    public function getEcAVSService()
    {
        return $this->ecAVSService;
    }

    /**
     * @param ECAVSService $ecAVSService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEcAVSService($ecAVSService)
    {
        $this->ecAVSService = $ecAVSService;

        return $this;
    }

    /**
     * @return GiftCardActivationService
     */
    public function getGiftCardActivationService()
    {
        return $this->giftCardActivationService;
    }

    /**
     * @param GiftCardActivationService $giftCardActivationService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGiftCardActivationService($giftCardActivationService)
    {
        $this->giftCardActivationService = $giftCardActivationService;

        return $this;
    }

    /**
     * @return GiftCardBalanceInquiryService
     */
    public function getGiftCardBalanceInquiryService()
    {
        return $this->giftCardBalanceInquiryService;
    }

    /**
     * @param GiftCardBalanceInquiryService $giftCardBalanceInquiryService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGiftCardBalanceInquiryService($giftCardBalanceInquiryService)
    {
        $this->giftCardBalanceInquiryService = $giftCardBalanceInquiryService;

        return $this;
    }

    /**
     * @return GiftCardRedemptionService
     */
    public function getGiftCardRedemptionService()
    {
        return $this->giftCardRedemptionService;
    }

    /**
     * @param GiftCardRedemptionService $giftCardRedemptionService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGiftCardRedemptionService($giftCardRedemptionService)
    {
        $this->giftCardRedemptionService = $giftCardRedemptionService;

        return $this;
    }

    /**
     * @return GiftCardVoidService
     */
    public function getGiftCardVoidService()
    {
        return $this->giftCardVoidService;
    }

    /**
     * @param GiftCardVoidService $giftCardVoidService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGiftCardVoidService($giftCardVoidService)
    {
        $this->giftCardVoidService = $giftCardVoidService;

        return $this;
    }

    /**
     * @return GiftCardReversalService
     */
    public function getGiftCardReversalService()
    {
        return $this->giftCardReversalService;
    }

    /**
     * @param GiftCardReversalService $giftCardReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGiftCardReversalService($giftCardReversalService)
    {
        $this->giftCardReversalService = $giftCardReversalService;

        return $this;
    }

    /**
     * @return VerificationService
     */
    public function getVerificationService()
    {
        return $this->verificationService;
    }

    /**
     * @param VerificationService $verificationService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setVerificationService($verificationService)
    {
        $this->verificationService = $verificationService;

        return $this;
    }

    /**
     * @return CCSaleService
     */
    public function getCcSaleService()
    {
        return $this->ccSaleService;
    }

    /**
     * @param CCSaleService $ccSaleService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcSaleService($ccSaleService)
    {
        $this->ccSaleService = $ccSaleService;

        return $this;
    }

    /**
     * @return CCSaleCreditService
     */
    public function getCcSaleCreditService()
    {
        return $this->ccSaleCreditService;
    }

    /**
     * @param CCSaleCreditService $ccSaleCreditService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcSaleCreditService($ccSaleCreditService)
    {
        $this->ccSaleCreditService = $ccSaleCreditService;

        return $this;
    }

    /**
     * @return CCSaleReversalService
     */
    public function getCcSaleReversalService()
    {
        return $this->ccSaleReversalService;
    }

    /**
     * @param CCSaleReversalService $ccSaleReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcSaleReversalService($ccSaleReversalService)
    {
        $this->ccSaleReversalService = $ccSaleReversalService;

        return $this;
    }

    /**
     * @return CCIncrementalAuthService
     */
    public function getCcIncrementalAuthService()
    {
        return $this->ccIncrementalAuthService;
    }

    /**
     * @param CCIncrementalAuthService $ccIncrementalAuthService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcIncrementalAuthService($ccIncrementalAuthService)
    {
        $this->ccIncrementalAuthService = $ccIncrementalAuthService;

        return $this;
    }

    /**
     * @return CCCaptureService
     */
    public function getCcCaptureService()
    {
        return $this->ccCaptureService;
    }

    /**
     * @param CCCaptureService $ccCaptureService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcCaptureService($ccCaptureService)
    {
        $this->ccCaptureService = $ccCaptureService;

        return $this;
    }

    /**
     * @return CCCreditService
     */
    public function getCcCreditService()
    {
        return $this->ccCreditService;
    }

    /**
     * @param CCCreditService $ccCreditService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcCreditService($ccCreditService)
    {
        $this->ccCreditService = $ccCreditService;

        return $this;
    }

    /**
     * @return CCAuthReversalService
     */
    public function getCcAuthReversalService()
    {
        return $this->ccAuthReversalService;
    }

    /**
     * @param CCAuthReversalService $ccAuthReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcAuthReversalService($ccAuthReversalService)
    {
        $this->ccAuthReversalService = $ccAuthReversalService;

        return $this;
    }

    /**
     * @return CCAutoAuthReversalService
     */
    public function getCcAutoAuthReversalService()
    {
        return $this->ccAutoAuthReversalService;
    }

    /**
     * @param CCAutoAuthReversalService $ccAutoAuthReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcAutoAuthReversalService($ccAutoAuthReversalService)
    {
        $this->ccAutoAuthReversalService = $ccAutoAuthReversalService;

        return $this;
    }

    /**
     * @return CCDCCService
     */
    public function getCcDCCService()
    {
        return $this->ccDCCService;
    }

    /**
     * @param CCDCCService $ccDCCService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcDCCService($ccDCCService)
    {
        $this->ccDCCService = $ccDCCService;

        return $this;
    }

    /**
     * @return ServiceFeeCalculateService
     */
    public function getServiceFeeCalculateService()
    {
        return $this->serviceFeeCalculateService;
    }

    /**
     * @param ServiceFeeCalculateService $serviceFeeCalculateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setServiceFeeCalculateService($serviceFeeCalculateService)
    {
        $this->serviceFeeCalculateService = $serviceFeeCalculateService;

        return $this;
    }

    /**
     * @return ECDebitService
     */
    public function getEcDebitService()
    {
        return $this->ecDebitService;
    }

    /**
     * @param ECDebitService $ecDebitService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEcDebitService($ecDebitService)
    {
        $this->ecDebitService = $ecDebitService;

        return $this;
    }

    /**
     * @return ECCreditService
     */
    public function getEcCreditService()
    {
        return $this->ecCreditService;
    }

    /**
     * @param ECCreditService $ecCreditService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEcCreditService($ecCreditService)
    {
        $this->ecCreditService = $ecCreditService;

        return $this;
    }

    /**
     * @return ECAuthenticateService
     */
    public function getEcAuthenticateService()
    {
        return $this->ecAuthenticateService;
    }

    /**
     * @param ECAuthenticateService $ecAuthenticateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEcAuthenticateService($ecAuthenticateService)
    {
        $this->ecAuthenticateService = $ecAuthenticateService;

        return $this;
    }

    /**
     * @return PayerAuthEnrollService
     */
    public function getPayerAuthEnrollService()
    {
        return $this->payerAuthEnrollService;
    }

    /**
     * @param PayerAuthEnrollService $payerAuthEnrollService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayerAuthEnrollService($payerAuthEnrollService)
    {
        $this->payerAuthEnrollService = $payerAuthEnrollService;

        return $this;
    }

    /**
     * @return PayerAuthValidateService
     */
    public function getPayerAuthValidateService()
    {
        return $this->payerAuthValidateService;
    }

    /**
     * @param PayerAuthValidateService $payerAuthValidateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayerAuthValidateService($payerAuthValidateService)
    {
        $this->payerAuthValidateService = $payerAuthValidateService;

        return $this;
    }

    /**
     * @return TaxService
     */
    public function getTaxService()
    {
        return $this->taxService;
    }

    /**
     * @param TaxService $taxService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setTaxService($taxService)
    {
        $this->taxService = $taxService;

        return $this;
    }

    /**
     * @return DMEService
     */
    public function getDmeService()
    {
        return $this->dmeService;
    }

    /**
     * @param DMEService $dmeService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDmeService($dmeService)
    {
        $this->dmeService = $dmeService;

        return $this;
    }

    /**
     * @return AFSService
     */
    public function getAfsService()
    {
        return $this->afsService;
    }

    /**
     * @param AFSService $afsService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAfsService($afsService)
    {
        $this->afsService = $afsService;

        return $this;
    }

    /**
     * @return DAVService
     */
    public function getDavService()
    {
        return $this->davService;
    }

    /**
     * @param DAVService $davService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDavService($davService)
    {
        $this->davService = $davService;

        return $this;
    }

    /**
     * @return ExportService
     */
    public function getExportService()
    {
        return $this->exportService;
    }

    /**
     * @param ExportService $exportService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setExportService($exportService)
    {
        $this->exportService = $exportService;

        return $this;
    }

    /**
     * @return FXRatesService
     */
    public function getFxRatesService()
    {
        return $this->fxRatesService;
    }

    /**
     * @param FXRatesService $fxRatesService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setFxRatesService($fxRatesService)
    {
        $this->fxRatesService = $fxRatesService;

        return $this;
    }

    /**
     * @return BankTransferService
     */
    public function getBankTransferService()
    {
        return $this->bankTransferService;
    }

    /**
     * @param BankTransferService $bankTransferService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBankTransferService($bankTransferService)
    {
        $this->bankTransferService = $bankTransferService;

        return $this;
    }

    /**
     * @return BankTransferRefundService
     */
    public function getBankTransferRefundService()
    {
        return $this->bankTransferRefundService;
    }

    /**
     * @param BankTransferRefundService $bankTransferRefundService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBankTransferRefundService($bankTransferRefundService)
    {
        $this->bankTransferRefundService = $bankTransferRefundService;

        return $this;
    }

    /**
     * @return BankTransferRealTimeService
     */
    public function getBankTransferRealTimeService()
    {
        return $this->bankTransferRealTimeService;
    }

    /**
     * @param BankTransferRealTimeService $bankTransferRealTimeService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBankTransferRealTimeService($bankTransferRealTimeService)
    {
        $this->bankTransferRealTimeService = $bankTransferRealTimeService;

        return $this;
    }

    /**
     * @return DirectDebitMandateService
     */
    public function getDirectDebitMandateService()
    {
        return $this->directDebitMandateService;
    }

    /**
     * @param DirectDebitMandateService $directDebitMandateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDirectDebitMandateService($directDebitMandateService)
    {
        $this->directDebitMandateService = $directDebitMandateService;

        return $this;
    }

    /**
     * @return DirectDebitService
     */
    public function getDirectDebitService()
    {
        return $this->directDebitService;
    }

    /**
     * @param DirectDebitService $directDebitService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDirectDebitService($directDebitService)
    {
        $this->directDebitService = $directDebitService;

        return $this;
    }

    /**
     * @return DirectDebitRefundService
     */
    public function getDirectDebitRefundService()
    {
        return $this->directDebitRefundService;
    }

    /**
     * @param DirectDebitRefundService $directDebitRefundService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDirectDebitRefundService($directDebitRefundService)
    {
        $this->directDebitRefundService = $directDebitRefundService;

        return $this;
    }

    /**
     * @return DirectDebitValidateService
     */
    public function getDirectDebitValidateService()
    {
        return $this->directDebitValidateService;
    }

    /**
     * @param DirectDebitValidateService $directDebitValidateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDirectDebitValidateService($directDebitValidateService)
    {
        $this->directDebitValidateService = $directDebitValidateService;

        return $this;
    }

    /**
     * @return DeviceFingerprintData[]
     */
    public function getDeviceFingerprintData()
    {
        return $this->deviceFingerprintData;
    }

    /**
     * @param DeviceFingerprintData[] $deviceFingerprintData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDeviceFingerprintData(array $deviceFingerprintData = null)
    {
        $this->deviceFingerprintData = $deviceFingerprintData;

        return $this;
    }

    /**
     * @return PaySubscriptionCreateService
     */
    public function getPaySubscriptionCreateService()
    {
        return $this->paySubscriptionCreateService;
    }

    /**
     * @param PaySubscriptionCreateService $paySubscriptionCreateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaySubscriptionCreateService($paySubscriptionCreateService)
    {
        $this->paySubscriptionCreateService = $paySubscriptionCreateService;

        return $this;
    }

    /**
     * @return PaySubscriptionUpdateService
     */
    public function getPaySubscriptionUpdateService()
    {
        return $this->paySubscriptionUpdateService;
    }

    /**
     * @param PaySubscriptionUpdateService $paySubscriptionUpdateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaySubscriptionUpdateService($paySubscriptionUpdateService)
    {
        $this->paySubscriptionUpdateService = $paySubscriptionUpdateService;

        return $this;
    }

    /**
     * @return PaySubscriptionEventUpdateService
     */
    public function getPaySubscriptionEventUpdateService()
    {
        return $this->paySubscriptionEventUpdateService;
    }

    /**
     * @param PaySubscriptionEventUpdateService $paySubscriptionEventUpdateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaySubscriptionEventUpdateService($paySubscriptionEventUpdateService)
    {
        $this->paySubscriptionEventUpdateService = $paySubscriptionEventUpdateService;

        return $this;
    }

    /**
     * @return PaySubscriptionRetrieveService
     */
    public function getPaySubscriptionRetrieveService()
    {
        return $this->paySubscriptionRetrieveService;
    }

    /**
     * @param PaySubscriptionRetrieveService $paySubscriptionRetrieveService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaySubscriptionRetrieveService($paySubscriptionRetrieveService)
    {
        $this->paySubscriptionRetrieveService = $paySubscriptionRetrieveService;

        return $this;
    }

    /**
     * @return PaySubscriptionDeleteService
     */
    public function getPaySubscriptionDeleteService()
    {
        return $this->paySubscriptionDeleteService;
    }

    /**
     * @param PaySubscriptionDeleteService $paySubscriptionDeleteService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaySubscriptionDeleteService($paySubscriptionDeleteService)
    {
        $this->paySubscriptionDeleteService = $paySubscriptionDeleteService;

        return $this;
    }

    /**
     * @return PayPalPaymentService
     */
    public function getPayPalPaymentService()
    {
        return $this->payPalPaymentService;
    }

    /**
     * @param PayPalPaymentService $payPalPaymentService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalPaymentService($payPalPaymentService)
    {
        $this->payPalPaymentService = $payPalPaymentService;

        return $this;
    }

    /**
     * @return PayPalCreditService
     */
    public function getPayPalCreditService()
    {
        return $this->payPalCreditService;
    }

    /**
     * @param PayPalCreditService $payPalCreditService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalCreditService($payPalCreditService)
    {
        $this->payPalCreditService = $payPalCreditService;

        return $this;
    }

    /**
     * @return VoidService
     */
    public function getVoidService()
    {
        return $this->voidService;
    }

    /**
     * @param VoidService $voidService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setVoidService($voidService)
    {
        $this->voidService = $voidService;

        return $this;
    }

    /**
     * @return BusinessRules
     */
    public function getBusinessRules()
    {
        return $this->businessRules;
    }

    /**
     * @param BusinessRules $businessRules
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBusinessRules($businessRules)
    {
        $this->businessRules = $businessRules;

        return $this;
    }

    /**
     * @return PinlessDebitService
     */
    public function getPinlessDebitService()
    {
        return $this->pinlessDebitService;
    }

    /**
     * @param PinlessDebitService $pinlessDebitService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinlessDebitService($pinlessDebitService)
    {
        $this->pinlessDebitService = $pinlessDebitService;

        return $this;
    }

    /**
     * @return PinlessDebitValidateService
     */
    public function getPinlessDebitValidateService()
    {
        return $this->pinlessDebitValidateService;
    }

    /**
     * @param PinlessDebitValidateService $pinlessDebitValidateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinlessDebitValidateService($pinlessDebitValidateService)
    {
        $this->pinlessDebitValidateService = $pinlessDebitValidateService;

        return $this;
    }

    /**
     * @return PinlessDebitReversalService
     */
    public function getPinlessDebitReversalService()
    {
        return $this->pinlessDebitReversalService;
    }

    /**
     * @param PinlessDebitReversalService $pinlessDebitReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinlessDebitReversalService($pinlessDebitReversalService)
    {
        $this->pinlessDebitReversalService = $pinlessDebitReversalService;

        return $this;
    }

    /**
     * @return Batch
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param Batch $batch
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * @return AirlineData
     */
    public function getAirlineData()
    {
        return $this->airlineData;
    }

    /**
     * @param AirlineData $airlineData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAirlineData($airlineData)
    {
        $this->airlineData = $airlineData;

        return $this;
    }

    /**
     * @return AncillaryData
     */
    public function getAncillaryData()
    {
        return $this->ancillaryData;
    }

    /**
     * @param AncillaryData $ancillaryData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAncillaryData($ancillaryData)
    {
        $this->ancillaryData = $ancillaryData;

        return $this;
    }

    /**
     * @return LodgingData
     */
    public function getLodgingData()
    {
        return $this->lodgingData;
    }

    /**
     * @param LodgingData $lodgingData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setLodgingData($lodgingData)
    {
        $this->lodgingData = $lodgingData;

        return $this;
    }

    /**
     * @return PayPalButtonCreateService
     */
    public function getPayPalButtonCreateService()
    {
        return $this->payPalButtonCreateService;
    }

    /**
     * @param PayPalButtonCreateService $payPalButtonCreateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalButtonCreateService($payPalButtonCreateService)
    {
        $this->payPalButtonCreateService = $payPalButtonCreateService;

        return $this;
    }

    /**
     * @return PayPalPreapprovedPaymentService
     */
    public function getPayPalPreapprovedPaymentService()
    {
        return $this->payPalPreapprovedPaymentService;
    }

    /**
     * @param PayPalPreapprovedPaymentService $payPalPreapprovedPaymentService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalPreapprovedPaymentService($payPalPreapprovedPaymentService)
    {
        $this->payPalPreapprovedPaymentService = $payPalPreapprovedPaymentService;

        return $this;
    }

    /**
     * @return PayPalPreapprovedUpdateService
     */
    public function getPayPalPreapprovedUpdateService()
    {
        return $this->payPalPreapprovedUpdateService;
    }

    /**
     * @param PayPalPreapprovedUpdateService $payPalPreapprovedUpdateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalPreapprovedUpdateService($payPalPreapprovedUpdateService)
    {
        $this->payPalPreapprovedUpdateService = $payPalPreapprovedUpdateService;

        return $this;
    }

    /**
     * @return RiskUpdateService
     */
    public function getRiskUpdateService()
    {
        return $this->riskUpdateService;
    }

    /**
     * @param RiskUpdateService $riskUpdateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setRiskUpdateService($riskUpdateService)
    {
        $this->riskUpdateService = $riskUpdateService;

        return $this;
    }

    /**
     * @return FraudUpdateService
     */
    public function getFraudUpdateService()
    {
        return $this->fraudUpdateService;
    }

    /**
     * @param FraudUpdateService $fraudUpdateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setFraudUpdateService($fraudUpdateService)
    {
        $this->fraudUpdateService = $fraudUpdateService;

        return $this;
    }

    /**
     * @return CaseManagementActionService
     */
    public function getCaseManagementActionService()
    {
        return $this->caseManagementActionService;
    }

    /**
     * @param CaseManagementActionService $caseManagementActionService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCaseManagementActionService($caseManagementActionService)
    {
        $this->caseManagementActionService = $caseManagementActionService;

        return $this;
    }

    /**
     * @return RequestReserved[]
     */
    public function getReserved()
    {
        return $this->reserved;
    }

    /**
     * @param RequestReserved[] $reserved
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setReserved(array $reserved = null)
    {
        $this->reserved = $reserved;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceFingerprintID()
    {
        return $this->deviceFingerprintID;
    }

    /**
     * @param string $deviceFingerprintID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDeviceFingerprintID($deviceFingerprintID)
    {
        $this->deviceFingerprintID = $deviceFingerprintID;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDeviceFingerprintRaw()
    {
        return $this->deviceFingerprintRaw;
    }

    /**
     * @param boolean $deviceFingerprintRaw
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDeviceFingerprintRaw($deviceFingerprintRaw)
    {
        $this->deviceFingerprintRaw = $deviceFingerprintRaw;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceFingerprintHash()
    {
        return $this->deviceFingerprintHash;
    }

    /**
     * @param string $deviceFingerprintHash
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDeviceFingerprintHash($deviceFingerprintHash)
    {
        $this->deviceFingerprintHash = $deviceFingerprintHash;

        return $this;
    }

    /**
     * @return PayPalRefundService
     */
    public function getPayPalRefundService()
    {
        return $this->payPalRefundService;
    }

    /**
     * @param PayPalRefundService $payPalRefundService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalRefundService($payPalRefundService)
    {
        $this->payPalRefundService = $payPalRefundService;

        return $this;
    }

    /**
     * @return PayPalAuthReversalService
     */
    public function getPayPalAuthReversalService()
    {
        return $this->payPalAuthReversalService;
    }

    /**
     * @param PayPalAuthReversalService $payPalAuthReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalAuthReversalService($payPalAuthReversalService)
    {
        $this->payPalAuthReversalService = $payPalAuthReversalService;

        return $this;
    }

    /**
     * @return PayPalDoCaptureService
     */
    public function getPayPalDoCaptureService()
    {
        return $this->payPalDoCaptureService;
    }

    /**
     * @param PayPalDoCaptureService $payPalDoCaptureService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalDoCaptureService($payPalDoCaptureService)
    {
        $this->payPalDoCaptureService = $payPalDoCaptureService;

        return $this;
    }

    /**
     * @return PayPalEcDoPaymentService
     */
    public function getPayPalEcDoPaymentService()
    {
        return $this->payPalEcDoPaymentService;
    }

    /**
     * @param PayPalEcDoPaymentService $payPalEcDoPaymentService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalEcDoPaymentService($payPalEcDoPaymentService)
    {
        $this->payPalEcDoPaymentService = $payPalEcDoPaymentService;

        return $this;
    }

    /**
     * @return PayPalEcGetDetailsService
     */
    public function getPayPalEcGetDetailsService()
    {
        return $this->payPalEcGetDetailsService;
    }

    /**
     * @param PayPalEcGetDetailsService $payPalEcGetDetailsService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalEcGetDetailsService($payPalEcGetDetailsService)
    {
        $this->payPalEcGetDetailsService = $payPalEcGetDetailsService;

        return $this;
    }

    /**
     * @return PayPalEcSetService
     */
    public function getPayPalEcSetService()
    {
        return $this->payPalEcSetService;
    }

    /**
     * @param PayPalEcSetService $payPalEcSetService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalEcSetService($payPalEcSetService)
    {
        $this->payPalEcSetService = $payPalEcSetService;

        return $this;
    }

    /**
     * @return PayPalEcOrderSetupService
     */
    public function getPayPalEcOrderSetupService()
    {
        return $this->payPalEcOrderSetupService;
    }

    /**
     * @param PayPalEcOrderSetupService $payPalEcOrderSetupService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalEcOrderSetupService($payPalEcOrderSetupService)
    {
        $this->payPalEcOrderSetupService = $payPalEcOrderSetupService;

        return $this;
    }

    /**
     * @return PayPalAuthorizationService
     */
    public function getPayPalAuthorizationService()
    {
        return $this->payPalAuthorizationService;
    }

    /**
     * @param PayPalAuthorizationService $payPalAuthorizationService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalAuthorizationService($payPalAuthorizationService)
    {
        $this->payPalAuthorizationService = $payPalAuthorizationService;

        return $this;
    }

    /**
     * @return PayPalUpdateAgreementService
     */
    public function getPayPalUpdateAgreementService()
    {
        return $this->payPalUpdateAgreementService;
    }

    /**
     * @param PayPalUpdateAgreementService $payPalUpdateAgreementService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalUpdateAgreementService($payPalUpdateAgreementService)
    {
        $this->payPalUpdateAgreementService = $payPalUpdateAgreementService;

        return $this;
    }

    /**
     * @return PayPalCreateAgreementService
     */
    public function getPayPalCreateAgreementService()
    {
        return $this->payPalCreateAgreementService;
    }

    /**
     * @param PayPalCreateAgreementService $payPalCreateAgreementService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalCreateAgreementService($payPalCreateAgreementService)
    {
        $this->payPalCreateAgreementService = $payPalCreateAgreementService;

        return $this;
    }

    /**
     * @return PayPalDoRefTransactionService
     */
    public function getPayPalDoRefTransactionService()
    {
        return $this->payPalDoRefTransactionService;
    }

    /**
     * @param PayPalDoRefTransactionService $payPalDoRefTransactionService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalDoRefTransactionService($payPalDoRefTransactionService)
    {
        $this->payPalDoRefTransactionService = $payPalDoRefTransactionService;

        return $this;
    }

    /**
     * @return ChinaPaymentService
     */
    public function getChinaPaymentService()
    {
        return $this->chinaPaymentService;
    }

    /**
     * @param ChinaPaymentService $chinaPaymentService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setChinaPaymentService($chinaPaymentService)
    {
        $this->chinaPaymentService = $chinaPaymentService;

        return $this;
    }

    /**
     * @return ChinaRefundService
     */
    public function getChinaRefundService()
    {
        return $this->chinaRefundService;
    }

    /**
     * @param ChinaRefundService $chinaRefundService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setChinaRefundService($chinaRefundService)
    {
        $this->chinaRefundService = $chinaRefundService;

        return $this;
    }

    /**
     * @return BoletoPaymentService
     */
    public function getBoletoPaymentService()
    {
        return $this->boletoPaymentService;
    }

    /**
     * @param BoletoPaymentService $boletoPaymentService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBoletoPaymentService($boletoPaymentService)
    {
        $this->boletoPaymentService = $boletoPaymentService;

        return $this;
    }

    /**
     * @return string
     */
    public function getApPaymentType()
    {
        return $this->apPaymentType;
    }

    /**
     * @param string $apPaymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApPaymentType($apPaymentType)
    {
        $this->apPaymentType = $apPaymentType;

        return $this;
    }

    /**
     * @return APInitiateService
     */
    public function getApInitiateService()
    {
        return $this->apInitiateService;
    }

    /**
     * @param APInitiateService $apInitiateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApInitiateService($apInitiateService)
    {
        $this->apInitiateService = $apInitiateService;

        return $this;
    }

    /**
     * @return APCheckStatusService
     */
    public function getApCheckStatusService()
    {
        return $this->apCheckStatusService;
    }

    /**
     * @param APCheckStatusService $apCheckStatusService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApCheckStatusService($apCheckStatusService)
    {
        $this->apCheckStatusService = $apCheckStatusService;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIgnoreCardExpiration()
    {
        return $this->ignoreCardExpiration;
    }

    /**
     * @param boolean $ignoreCardExpiration
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setIgnoreCardExpiration($ignoreCardExpiration)
    {
        $this->ignoreCardExpiration = $ignoreCardExpiration;

        return $this;
    }

    /**
     * @return string
     */
    public function getReportGroup()
    {
        return $this->reportGroup;
    }

    /**
     * @param string $reportGroup
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setReportGroup($reportGroup)
    {
        $this->reportGroup = $reportGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorID()
    {
        return $this->processorID;
    }

    /**
     * @param string $processorID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setProcessorID($processorID)
    {
        $this->processorID = $processorID;

        return $this;
    }

    /**
     * @return string
     */
    public function getThirdPartyCertificationNumber()
    {
        return $this->thirdPartyCertificationNumber;
    }

    /**
     * @param string $thirdPartyCertificationNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setThirdPartyCertificationNumber($thirdPartyCertificationNumber)
    {
        $this->thirdPartyCertificationNumber = $thirdPartyCertificationNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
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
     * @return string
     */
    public function getSolutionProviderTransactionID()
    {
        return $this->solutionProviderTransactionID;
    }

    /**
     * @param string $solutionProviderTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSolutionProviderTransactionID($solutionProviderTransactionID)
    {
        $this->solutionProviderTransactionID = $solutionProviderTransactionID;

        return $this;
    }

    /**
     * @return float
     */
    public function getSurchargeAmount()
    {
        return $this->surchargeAmount;
    }

    /**
     * @param float $surchargeAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSurchargeAmount($surchargeAmount)
    {
        $this->surchargeAmount = $surchargeAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurchargeSign()
    {
        return $this->surchargeSign;
    }

    /**
     * @param string $surchargeSign
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSurchargeSign($surchargeSign)
    {
        $this->surchargeSign = $surchargeSign;

        return $this;
    }

    /**
     * @return string
     */
    public function getPinDataEncryptedPIN()
    {
        return $this->pinDataEncryptedPIN;
    }

    /**
     * @param string $pinDataEncryptedPIN
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinDataEncryptedPIN($pinDataEncryptedPIN)
    {
        $this->pinDataEncryptedPIN = $pinDataEncryptedPIN;

        return $this;
    }

    /**
     * @return string
     */
    public function getPinDataKeySerialNumber()
    {
        return $this->pinDataKeySerialNumber;
    }

    /**
     * @param string $pinDataKeySerialNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinDataKeySerialNumber($pinDataKeySerialNumber)
    {
        $this->pinDataKeySerialNumber = $pinDataKeySerialNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getPinDataPinBlockEncodingFormat()
    {
        return $this->pinDataPinBlockEncodingFormat;
    }

    /**
     * @param int $pinDataPinBlockEncodingFormat
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinDataPinBlockEncodingFormat($pinDataPinBlockEncodingFormat)
    {
        $this->pinDataPinBlockEncodingFormat = $pinDataPinBlockEncodingFormat;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCashbackAmount($cashbackAmount)
    {
        $this->cashbackAmount = $cashbackAmount;

        return $this;
    }

    /**
     * @return PinDebitPurchaseService
     */
    public function getPinDebitPurchaseService()
    {
        return $this->pinDebitPurchaseService;
    }

    /**
     * @param PinDebitPurchaseService $pinDebitPurchaseService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinDebitPurchaseService($pinDebitPurchaseService)
    {
        $this->pinDebitPurchaseService = $pinDebitPurchaseService;

        return $this;
    }

    /**
     * @return PinDebitCreditService
     */
    public function getPinDebitCreditService()
    {
        return $this->pinDebitCreditService;
    }

    /**
     * @param PinDebitCreditService $pinDebitCreditService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinDebitCreditService($pinDebitCreditService)
    {
        $this->pinDebitCreditService = $pinDebitCreditService;

        return $this;
    }

    /**
     * @return PinDebitReversalService
     */
    public function getPinDebitReversalService()
    {
        return $this->pinDebitReversalService;
    }

    /**
     * @param PinDebitReversalService $pinDebitReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPinDebitReversalService($pinDebitReversalService)
    {
        $this->pinDebitReversalService = $pinDebitReversalService;

        return $this;
    }

    /**
     * @return AP
     */
    public function getAp()
    {
        return $this->ap;
    }

    /**
     * @param AP $ap
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAp($ap)
    {
        $this->ap = $ap;

        return $this;
    }

    /**
     * @return APAuthService
     */
    public function getApAuthService()
    {
        return $this->apAuthService;
    }

    /**
     * @param APAuthService $apAuthService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApAuthService($apAuthService)
    {
        $this->apAuthService = $apAuthService;

        return $this;
    }

    /**
     * @return APAuthReversalService
     */
    public function getApAuthReversalService()
    {
        return $this->apAuthReversalService;
    }

    /**
     * @param APAuthReversalService $apAuthReversalService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApAuthReversalService($apAuthReversalService)
    {
        $this->apAuthReversalService = $apAuthReversalService;

        return $this;
    }

    /**
     * @return APCaptureService
     */
    public function getApCaptureService()
    {
        return $this->apCaptureService;
    }

    /**
     * @param APCaptureService $apCaptureService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApCaptureService($apCaptureService)
    {
        $this->apCaptureService = $apCaptureService;

        return $this;
    }

    /**
     * @return APOptionsService
     */
    public function getApOptionsService()
    {
        return $this->apOptionsService;
    }

    /**
     * @param APOptionsService $apOptionsService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApOptionsService($apOptionsService)
    {
        $this->apOptionsService = $apOptionsService;

        return $this;
    }

    /**
     * @return APRefundService
     */
    public function getApRefundService()
    {
        return $this->apRefundService;
    }

    /**
     * @param APRefundService $apRefundService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApRefundService($apRefundService)
    {
        $this->apRefundService = $apRefundService;

        return $this;
    }

    /**
     * @return APSaleService
     */
    public function getApSaleService()
    {
        return $this->apSaleService;
    }

    /**
     * @param APSaleService $apSaleService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApSaleService($apSaleService)
    {
        $this->apSaleService = $apSaleService;

        return $this;
    }

    /**
     * @return APCheckOutDetailsService
     */
    public function getApCheckoutDetailsService()
    {
        return $this->apCheckoutDetailsService;
    }

    /**
     * @param APCheckOutDetailsService $apCheckoutDetailsService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApCheckoutDetailsService($apCheckoutDetailsService)
    {
        $this->apCheckoutDetailsService = $apCheckoutDetailsService;

        return $this;
    }

    /**
     * @return APSessionsService
     */
    public function getApSessionsService()
    {
        return $this->apSessionsService;
    }

    /**
     * @param APSessionsService $apSessionsService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApSessionsService($apSessionsService)
    {
        $this->apSessionsService = $apSessionsService;

        return $this;
    }

    /**
     * @return APUI
     */
    public function getApUI()
    {
        return $this->apUI;
    }

    /**
     * @param APUI $apUI
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApUI($apUI)
    {
        $this->apUI = $apUI;

        return $this;
    }

    /**
     * @return APTransactionDetailsService
     */
    public function getApTransactionDetailsService()
    {
        return $this->apTransactionDetailsService;
    }

    /**
     * @param APTransactionDetailsService $apTransactionDetailsService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApTransactionDetailsService($apTransactionDetailsService)
    {
        $this->apTransactionDetailsService = $apTransactionDetailsService;

        return $this;
    }

    /**
     * @return APConfirmPurchaseService
     */
    public function getApConfirmPurchaseService()
    {
        return $this->apConfirmPurchaseService;
    }

    /**
     * @param APConfirmPurchaseService $apConfirmPurchaseService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApConfirmPurchaseService($apConfirmPurchaseService)
    {
        $this->apConfirmPurchaseService = $apConfirmPurchaseService;

        return $this;
    }

    /**
     * @return PayPalGetTxnDetailsService
     */
    public function getPayPalGetTxnDetailsService()
    {
        return $this->payPalGetTxnDetailsService;
    }

    /**
     * @param PayPalGetTxnDetailsService $payPalGetTxnDetailsService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalGetTxnDetailsService($payPalGetTxnDetailsService)
    {
        $this->payPalGetTxnDetailsService = $payPalGetTxnDetailsService;

        return $this;
    }

    /**
     * @return PayPalTransactionSearchService
     */
    public function getPayPalTransactionSearchService()
    {
        return $this->payPalTransactionSearchService;
    }

    /**
     * @param PayPalTransactionSearchService $payPalTransactionSearchService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPayPalTransactionSearchService($payPalTransactionSearchService)
    {
        $this->payPalTransactionSearchService = $payPalTransactionSearchService;

        return $this;
    }

    /**
     * @return CCDCCUpdateService
     */
    public function getCcDCCUpdateService()
    {
        return $this->ccDCCUpdateService;
    }

    /**
     * @param CCDCCUpdateService $ccDCCUpdateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcDCCUpdateService($ccDCCUpdateService)
    {
        $this->ccDCCUpdateService = $ccDCCUpdateService;

        return $this;
    }

    /**
     * @return EmvRequest
     */
    public function getEmvRequest()
    {
        return $this->emvRequest;
    }

    /**
     * @param EmvRequest $emvRequest
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEmvRequest($emvRequest)
    {
        $this->emvRequest = $emvRequest;

        return $this;
    }

    /**
     * @return merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @param merchant $merchant
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantTransactionIdentifier()
    {
        return $this->merchantTransactionIdentifier;
    }

    /**
     * @param string $merchantTransactionIdentifier
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchantTransactionIdentifier($merchantTransactionIdentifier)
    {
        $this->merchantTransactionIdentifier = $merchantTransactionIdentifier;

        return $this;
    }

    /**
     * @return HostedDataCreateService
     */
    public function getHostedDataCreateService()
    {
        return $this->hostedDataCreateService;
    }

    /**
     * @param HostedDataCreateService $hostedDataCreateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setHostedDataCreateService($hostedDataCreateService)
    {
        $this->hostedDataCreateService = $hostedDataCreateService;

        return $this;
    }

    /**
     * @return HostedDataRetrieveService
     */
    public function getHostedDataRetrieveService()
    {
        return $this->hostedDataRetrieveService;
    }

    /**
     * @param HostedDataRetrieveService $hostedDataRetrieveService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setHostedDataRetrieveService($hostedDataRetrieveService)
    {
        $this->hostedDataRetrieveService = $hostedDataRetrieveService;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantCategoryCode()
    {
        return $this->merchantCategoryCode;
    }

    /**
     * @param string $merchantCategoryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchantCategoryCode($merchantCategoryCode)
    {
        $this->merchantCategoryCode = $merchantCategoryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantCategoryCodeDomestic()
    {
        return $this->merchantCategoryCodeDomestic;
    }

    /**
     * @param string $merchantCategoryCodeDomestic
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchantCategoryCodeDomestic($merchantCategoryCodeDomestic)
    {
        $this->merchantCategoryCodeDomestic = $merchantCategoryCodeDomestic;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSalesSlipNumber($salesSlipNumber)
    {
        $this->salesSlipNumber = $salesSlipNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchandiseCode()
    {
        return $this->merchandiseCode;
    }

    /**
     * @param string $merchandiseCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchandiseCode($merchandiseCode)
    {
        $this->merchandiseCode = $merchandiseCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchandiseDescription()
    {
        return $this->merchandiseDescription;
    }

    /**
     * @param string $merchandiseDescription
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMerchandiseDescription($merchandiseDescription)
    {
        $this->merchandiseDescription = $merchandiseDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentInitiationChannel()
    {
        return $this->paymentInitiationChannel;
    }

    /**
     * @param string $paymentInitiationChannel
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaymentInitiationChannel($paymentInitiationChannel)
    {
        $this->paymentInitiationChannel = $paymentInitiationChannel;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtendedCreditTotalCount()
    {
        return $this->extendedCreditTotalCount;
    }

    /**
     * @param string $extendedCreditTotalCount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setExtendedCreditTotalCount($extendedCreditTotalCount)
    {
        $this->extendedCreditTotalCount = $extendedCreditTotalCount;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAuthIndicator($authIndicator)
    {
        $this->authIndicator = $authIndicator;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaymentNetworkToken($paymentNetworkToken)
    {
        $this->paymentNetworkToken = $paymentNetworkToken;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @return Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param Sender $sender
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return AutoRentalData
     */
    public function getAutoRentalData()
    {
        return $this->autoRentalData;
    }

    /**
     * @param AutoRentalData $autoRentalData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAutoRentalData($autoRentalData)
    {
        $this->autoRentalData = $autoRentalData;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentSolution()
    {
        return $this->paymentSolution;
    }

    /**
     * @param string $paymentSolution
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPaymentSolution($paymentSolution)
    {
        $this->paymentSolution = $paymentSolution;

        return $this;
    }

    /**
     * @return VC
     */
    public function getVc()
    {
        return $this->vc;
    }

    /**
     * @param VC $vc
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setVc($vc)
    {
        $this->vc = $vc;

        return $this;
    }

    /**
     * @return DecryptVisaCheckoutDataService
     */
    public function getDecryptVisaCheckoutDataService()
    {
        return $this->decryptVisaCheckoutDataService;
    }

    /**
     * @param DecryptVisaCheckoutDataService $decryptVisaCheckoutDataService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDecryptVisaCheckoutDataService($decryptVisaCheckoutDataService)
    {
        $this->decryptVisaCheckoutDataService = $decryptVisaCheckoutDataService;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxManagementIndicator()
    {
        return $this->taxManagementIndicator;
    }

    /**
     * @param string $taxManagementIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setTaxManagementIndicator($taxManagementIndicator)
    {
        $this->taxManagementIndicator = $taxManagementIndicator;

        return $this;
    }

    /**
     * @return PromotionGroup[]
     */
    public function getPromotionGroup()
    {
        return $this->promotionGroup;
    }

    /**
     * @param PromotionGroup[] $promotionGroup
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPromotionGroup(array $promotionGroup = null)
    {
        $this->promotionGroup = $promotionGroup;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return Aft
     */
    public function getAft()
    {
        return $this->aft;
    }

    /**
     * @param Aft $aft
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAft($aft)
    {
        $this->aft = $aft;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getBalanceInquiry()
    {
        return $this->balanceInquiry;
    }

    /**
     * @param boolean $balanceInquiry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBalanceInquiry($balanceInquiry)
    {
        $this->balanceInquiry = $balanceInquiry;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrenoteTransaction()
    {
        return $this->prenoteTransaction;
    }

    /**
     * @param boolean $prenoteTransaction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPrenoteTransaction($prenoteTransaction)
    {
        $this->prenoteTransaction = $prenoteTransaction;

        return $this;
    }

    /**
     * @return EncryptPaymentDataService
     */
    public function getEncryptPaymentDataService()
    {
        return $this->encryptPaymentDataService;
    }

    /**
     * @param EncryptPaymentDataService $encryptPaymentDataService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEncryptPaymentDataService($encryptPaymentDataService)
    {
        $this->encryptPaymentDataService = $encryptPaymentDataService;

        return $this;
    }

    /**
     * @return string
     */
    public function getNationalNetDomesticData()
    {
        return $this->nationalNetDomesticData;
    }

    /**
     * @param string $nationalNetDomesticData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setNationalNetDomesticData($nationalNetDomesticData)
    {
        $this->nationalNetDomesticData = $nationalNetDomesticData;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubsequentAuth()
    {
        return $this->subsequentAuth;
    }

    /**
     * @param string $subsequentAuth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSubsequentAuth($subsequentAuth)
    {
        $this->subsequentAuth = $subsequentAuth;

        return $this;
    }

    /**
     * @return float
     */
    public function getSubsequentAuthOriginalAmount()
    {
        return $this->subsequentAuthOriginalAmount;
    }

    /**
     * @param float $subsequentAuthOriginalAmount
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSubsequentAuthOriginalAmount($subsequentAuthOriginalAmount)
    {
        $this->subsequentAuthOriginalAmount = $subsequentAuthOriginalAmount;

        return $this;
    }

    /**
     * @return BinLookupService
     */
    public function getBinLookupService()
    {
        return $this->binLookupService;
    }

    /**
     * @param BinLookupService $binLookupService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBinLookupService($binLookupService)
    {
        $this->binLookupService = $binLookupService;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * @param string $verificationCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @param string $mobileNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartnerSolutionID()
    {
        return $this->partnerSolutionID;
    }

    /**
     * @param string $partnerSolutionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPartnerSolutionID($partnerSolutionID)
    {
        $this->partnerSolutionID = $partnerSolutionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeveloperID()
    {
        return $this->developerID;
    }

    /**
     * @param string $developerID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setDeveloperID($developerID)
    {
        $this->developerID = $developerID;

        return $this;
    }

    /**
     * @return GETVisaCheckoutDataService
     */
    public function getGetVisaCheckoutDataService()
    {
        return $this->getVisaCheckoutDataService;
    }

    /**
     * @param GETVisaCheckoutDataService $getVisaCheckoutDataService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGetVisaCheckoutDataService($getVisaCheckoutDataService)
    {
        $this->getVisaCheckoutDataService = $getVisaCheckoutDataService;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerSignatureImage()
    {
        return $this->customerSignatureImage;
    }

    /**
     * @param string $customerSignatureImage
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCustomerSignatureImage($customerSignatureImage)
    {
        $this->customerSignatureImage = $customerSignatureImage;

        return $this;
    }

    /**
     * @return TransactionMetadataService
     */
    public function getTransactionMetadataService()
    {
        return $this->transactionMetadataService;
    }

    /**
     * @param TransactionMetadataService $transactionMetadataService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setTransactionMetadataService($transactionMetadataService)
    {
        $this->transactionMetadataService = $transactionMetadataService;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubsequentAuthFirst()
    {
        return $this->subsequentAuthFirst;
    }

    /**
     * @param string $subsequentAuthFirst
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSubsequentAuthFirst($subsequentAuthFirst)
    {
        $this->subsequentAuthFirst = $subsequentAuthFirst;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubsequentAuthReason()
    {
        return $this->subsequentAuthReason;
    }

    /**
     * @param string $subsequentAuthReason
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSubsequentAuthReason($subsequentAuthReason)
    {
        $this->subsequentAuthReason = $subsequentAuthReason;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubsequentAuthTransactionID()
    {
        return $this->subsequentAuthTransactionID;
    }

    /**
     * @param string $subsequentAuthTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSubsequentAuthTransactionID($subsequentAuthTransactionID)
    {
        $this->subsequentAuthTransactionID = $subsequentAuthTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubsequentAuthStoredCredential()
    {
        return $this->subsequentAuthStoredCredential;
    }

    /**
     * @param string $subsequentAuthStoredCredential
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setSubsequentAuthStoredCredential($subsequentAuthStoredCredential)
    {
        $this->subsequentAuthStoredCredential = $subsequentAuthStoredCredential;

        return $this;
    }

    /**
     * @return Loan
     */
    public function getLoan()
    {
        return $this->loan;
    }

    /**
     * @param Loan $loan
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setLoan($loan)
    {
        $this->loan = $loan;

        return $this;
    }

    /**
     * @return string
     */
    public function getEligibilityInquiry()
    {
        return $this->eligibilityInquiry;
    }

    /**
     * @param string $eligibilityInquiry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setEligibilityInquiry($eligibilityInquiry)
    {
        $this->eligibilityInquiry = $eligibilityInquiry;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedemptionInquiry()
    {
        return $this->redemptionInquiry;
    }

    /**
     * @param string $redemptionInquiry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setRedemptionInquiry($redemptionInquiry)
    {
        $this->redemptionInquiry = $redemptionInquiry;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setFeeProgramIndicator($feeProgramIndicator)
    {
        $this->feeProgramIndicator = $feeProgramIndicator;

        return $this;
    }

    /**
     * @return APOrderService
     */
    public function getApOrderService()
    {
        return $this->apOrderService;
    }

    /**
     * @param APOrderService $apOrderService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApOrderService($apOrderService)
    {
        $this->apOrderService = $apOrderService;

        return $this;
    }

    /**
     * @return APCancelService
     */
    public function getApCancelService()
    {
        return $this->apCancelService;
    }

    /**
     * @param APCancelService $apCancelService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApCancelService($apCancelService)
    {
        $this->apCancelService = $apCancelService;

        return $this;
    }

    /**
     * @return APBillingAgreementService
     */
    public function getApBillingAgreementService()
    {
        return $this->apBillingAgreementService;
    }

    /**
     * @param APBillingAgreementService $apBillingAgreementService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApBillingAgreementService($apBillingAgreementService)
    {
        $this->apBillingAgreementService = $apBillingAgreementService;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote_toPayee()
    {
        return $this->note_toPayee;
    }

    /**
     * @param string $note_toPayee
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setNote_toPayee($note_toPayee)
    {
        $this->note_toPayee = $note_toPayee;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote_toPayer()
    {
        return $this->note_toPayer;
    }

    /**
     * @param string $note_toPayer
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setNote_toPayer($note_toPayer)
    {
        $this->note_toPayer = $note_toPayer;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientMetadataID()
    {
        return $this->clientMetadataID;
    }

    /**
     * @param string $clientMetadataID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setClientMetadataID($clientMetadataID)
    {
        $this->clientMetadataID = $clientMetadataID;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartnerSDKversion()
    {
        return $this->partnerSDKversion;
    }

    /**
     * @param string $partnerSDKversion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPartnerSDKversion($partnerSDKversion)
    {
        $this->partnerSDKversion = $partnerSDKversion;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartnerOriginalTransactionID()
    {
        return $this->partnerOriginalTransactionID;
    }

    /**
     * @param string $partnerOriginalTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPartnerOriginalTransactionID($partnerOriginalTransactionID)
    {
        $this->partnerOriginalTransactionID = $partnerOriginalTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardTypeSelectionIndicator()
    {
        return $this->cardTypeSelectionIndicator;
    }

    /**
     * @param string $cardTypeSelectionIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCardTypeSelectionIndicator($cardTypeSelectionIndicator)
    {
        $this->cardTypeSelectionIndicator = $cardTypeSelectionIndicator;

        return $this;
    }

    /**
     * @return APCreateMandateService
     */
    public function getApCreateMandateService()
    {
        return $this->apCreateMandateService;
    }

    /**
     * @param APCreateMandateService $apCreateMandateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApCreateMandateService($apCreateMandateService)
    {
        $this->apCreateMandateService = $apCreateMandateService;

        return $this;
    }

    /**
     * @return APMandateStatusService
     */
    public function getApMandateStatusService()
    {
        return $this->apMandateStatusService;
    }

    /**
     * @param APMandateStatusService $apMandateStatusService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApMandateStatusService($apMandateStatusService)
    {
        $this->apMandateStatusService = $apMandateStatusService;

        return $this;
    }

    /**
     * @return APUpdateMandateService
     */
    public function getApUpdateMandateService()
    {
        return $this->apUpdateMandateService;
    }

    /**
     * @param APUpdateMandateService $apUpdateMandateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApUpdateMandateService($apUpdateMandateService)
    {
        $this->apUpdateMandateService = $apUpdateMandateService;

        return $this;
    }

    /**
     * @return APImportMandateService
     */
    public function getApImportMandateService()
    {
        return $this->apImportMandateService;
    }

    /**
     * @param APImportMandateService $apImportMandateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApImportMandateService($apImportMandateService)
    {
        $this->apImportMandateService = $apImportMandateService;

        return $this;
    }

    /**
     * @return APRevokeMandateService
     */
    public function getApRevokeMandateService()
    {
        return $this->apRevokeMandateService;
    }

    /**
     * @param APRevokeMandateService $apRevokeMandateService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setApRevokeMandateService($apRevokeMandateService)
    {
        $this->apRevokeMandateService = $apRevokeMandateService;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillPaymentType()
    {
        return $this->billPaymentType;
    }

    /**
     * @param string $billPaymentType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setBillPaymentType($billPaymentType)
    {
        $this->billPaymentType = $billPaymentType;

        return $this;
    }

    /**
     * @return PostdatedTransaction
     */
    public function getPostdatedTransaction()
    {
        return $this->postdatedTransaction;
    }

    /**
     * @param PostdatedTransaction $postdatedTransaction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setPostdatedTransaction($postdatedTransaction)
    {
        $this->postdatedTransaction = $postdatedTransaction;

        return $this;
    }

    /**
     * @return GetMasterpassDataService
     */
    public function getGetMasterpassDataService()
    {
        return $this->getMasterpassDataService;
    }

    /**
     * @param GetMasterpassDataService $getMasterpassDataService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setGetMasterpassDataService($getMasterpassDataService)
    {
        $this->getMasterpassDataService = $getMasterpassDataService;

        return $this;
    }

    /**
     * @return CCCheckStatusService
     */
    public function getCcCheckStatusService()
    {
        return $this->ccCheckStatusService;
    }

    /**
     * @param CCCheckStatusService $ccCheckStatusService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setCcCheckStatusService($ccCheckStatusService)
    {
        $this->ccCheckStatusService = $ccCheckStatusService;

        return $this;
    }

    /**
     * @return mPOS
     */
    public function getMPOS()
    {
        return $this->mPOS;
    }

    /**
     * @param mPOS $mPOS
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setMPOS($mPOS)
    {
        $this->mPOS = $mPOS;

        return $this;
    }

    /**
     * @return AbortService
     */
    public function getAbortService()
    {
        return $this->abortService;
    }

    /**
     * @param AbortService $abortService
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setAbortService($abortService)
    {
        $this->abortService = $abortService;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIgnoreRelaxAVS()
    {
        return $this->ignoreRelaxAVS;
    }

    /**
     * @param boolean $ignoreRelaxAVS
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function setIgnoreRelaxAVS($ignoreRelaxAVS)
    {
        $this->ignoreRelaxAVS = $ignoreRelaxAVS;

        return $this;
    }
}
