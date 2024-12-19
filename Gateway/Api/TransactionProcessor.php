<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

use ParadoxLabs\CyberSource\Model\Config\Config;
use SoapClient;

/**
 * CyberSource Web Service
 */
class TransactionProcessor extends SoapClient
{
    /**
     * @var array $classmap The defined classes
     */
    private static $classmap
        = [
            'Item' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Item',
            'CCAuthService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCAuthService',
            'OCTService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\OCTService',
            'VerificationService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VerificationService',
            'CCSaleService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCSaleService',
            'CCSaleCreditService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCSaleCreditService',
            'CCSaleReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCSaleReversalService',
            'CCIncrementalAuthService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCIncrementalAuthService',
            'CCCaptureService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCaptureService',
            'CCCreditService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCreditService',
            'CCCreditAuthService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCreditAuthService',
            'CCAuthReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCAuthReversalService',
            'CCAutoAuthReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCAutoAuthReversalService',
            'CCCreditAuthReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCreditAuthReversalService',
            'CCDCCService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCDCCService',
            'ServiceFeeCalculateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ServiceFeeCalculateService',
            'ECDebitService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECDebitService',
            'ECCreditService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECCreditService',
            'ECAuthenticateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECAuthenticateService',
            'PayerAuthEnrollService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayerAuthEnrollService',
            'PayerAuthValidateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayerAuthValidateService',
            'PayerAuthSetupService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayerAuthSetupService',
            'TaxService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\TaxService',
            'DMEService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DMEService',
            'AFSService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AFSService',
            'DAVService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DAVService',
            'ExportService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ExportService',
            'FXRatesService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FXRatesService',
            'BankTransferService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BankTransferService',
            'BankTransferRefundService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BankTransferRefundService',
            'BankTransferRealTimeService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BankTransferRealTimeService',
            'DirectDebitMandateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitMandateService',
            'DirectDebitService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitService',
            'DirectDebitRefundService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitRefundService',
            'DirectDebitValidateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitValidateService',
            'DeviceFingerprintData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DeviceFingerprintData',
            'PaySubscriptionCreateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionCreateService',
            'PaySubscriptionUpdateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionUpdateService',
            'PaySubscriptionEventUpdateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionEventUpdateService',
            'PaySubscriptionRetrieveService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionRetrieveService',
            'PaySubscriptionDeleteService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionDeleteService',
            'PayPalPaymentService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalPaymentService',
            'PayPalCreditService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalCreditService',
            'PayPalEcSetService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcSetService',
            'PayPalEcGetDetailsService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcGetDetailsService',
            'PayPalEcDoPaymentService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcDoPaymentService',
            'PayPalDoCaptureService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalDoCaptureService',
            'PayPalAuthReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalAuthReversalService',
            'PayPalRefundService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalRefundService',
            'PayPalEcOrderSetupService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcOrderSetupService',
            'PayPalAuthorizationService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalAuthorizationService',
            'PayPalUpdateAgreementService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalUpdateAgreementService',
            'PayPalCreateAgreementService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalCreateAgreementService',
            'PayPalDoRefTransactionService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalDoRefTransactionService',
            'VoidService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VoidService',
            'PinlessDebitService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinlessDebitService',
            'PinlessDebitValidateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinlessDebitValidateService',
            'PinlessDebitReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinlessDebitReversalService',
            'PinDebitPurchaseService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinDebitPurchaseService',
            'PinDebitCreditService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinDebitCreditService',
            'PinDebitReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinDebitReversalService',
            'PayPalButtonCreateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalButtonCreateService',
            'PayPalPreapprovedPaymentService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalPreapprovedPaymentService',
            'PayPalPreapprovedUpdateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalPreapprovedUpdateService',
            'ChinaPaymentService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ChinaPaymentService',
            'ChinaRefundService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ChinaRefundService',
            'BoletoPaymentService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BoletoPaymentService',
            'PersonalID' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PersonalID',
            'Routing' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Routing',
            'Address' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Address',
            'APInitiateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APInitiateService',
            'APCheckStatusService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCheckStatusService',
            'RiskUpdateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\RiskUpdateService',
            'FraudUpdateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FraudUpdateService',
            'CaseManagementActionService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CaseManagementActionService',
            'EncryptPaymentDataService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\EncryptPaymentDataService',
            'InvoiceHeader' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\InvoiceHeader',
            'BusinessRules' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BusinessRules',
            'BillTo' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BillTo',
            'ShipTo' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ShipTo',
            'ShipFrom' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ShipFrom',
            'Card' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Card',
            'Check' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Check',
            'BML' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BML',
            'OtherTax' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\OtherTax',
            'Aft' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Aft',
            'Wallet' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Wallet',
            'PurchaseTotals' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PurchaseTotals',
            'FundingTotals' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FundingTotals',
            'GECC' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GECC',
            'UCAF' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\UCAF',
            'Network' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Network',
            'Brands' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Brands',
            'FundTransfer' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FundTransfer',
            'BankInfo' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BankInfo',
            'RecurringSubscriptionInfo' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\RecurringSubscriptionInfo',
            'PaySubscriptionEvent' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionEvent',
            'Subscription' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Subscription',
            'TokenSource' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\TokenSource',
            'PaymentNetworkToken' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaymentNetworkToken',
            'DecisionManager' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DecisionManager',
            'Authentication' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Authentication',
            'DecisionManagerTravelData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DecisionManagerTravelData',
            'DecisionManagerTravelLeg' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DecisionManagerTravelLeg',
            'Batch' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Batch',
            'PayPal' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPal',
            'JPO' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\JPO',
            'Token' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Token',
            'AP' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AP',
            'APDevice' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APDevice',
            'APAuthService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APAuthService',
            'APImportMandateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APImportMandateService',
            'APAuthReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APAuthReversalService',
            'APCaptureService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCaptureService',
            'APOptionsService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APOptionsService',
            'APRefundService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APRefundService',
            'APSaleService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APSaleService',
            'APCheckOutDetailsService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCheckOutDetailsService',
            'APTransactionDetailsService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APTransactionDetailsService',
            'APConfirmPurchaseService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APConfirmPurchaseService',
            'APSessionsService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APSessionsService',
            'APUI' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APUI',
            'PayPalGetTxnDetailsService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalGetTxnDetailsService',
            'PayPalTransactionSearchService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalTransactionSearchService',
            'Recipient' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Recipient',
            'Sender' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Sender',
            'CCCheckStatusService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCheckStatusService',
            'RequestMessage' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\RequestMessage',
            'VC' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VC',
            'DecryptVisaCheckoutDataService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DecryptVisaCheckoutDataService',
            'DCC' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DCC',
            'Promotion' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Promotion',
            'PromotionGroup' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PromotionGroup',
            'PromotionGroupReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PromotionGroupReply',
            'BalanceInfo' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BalanceInfo',
            'PaymentInsightsInformation' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaymentInsightsInformation',
            'AdditionalToken' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AdditionalToken',
            'CCAuthReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCAuthReply',
            'OCTReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\OCTReply',
            'VerificationReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VerificationReply',
            'CCSaleReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCSaleReply',
            'CCSaleCreditReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCSaleCreditReply',
            'CCSaleReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCSaleReversalReply',
            'CCIncrementalAuthReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCIncrementalAuthReply',
            'CCCaptureReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCaptureReply',
            'ServiceFeeCalculateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ServiceFeeCalculateReply',
            'CCCreditReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCreditReply',
            'CCCreditAuthReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCreditAuthReply',
            'PinDebitPurchaseReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinDebitPurchaseReply',
            'PinDebitCreditReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinDebitCreditReply',
            'PinDebitReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinDebitReversalReply',
            'CCAuthReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCAuthReversalReply',
            'CCAutoAuthReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCAutoAuthReversalReply',
            'CCCreditAuthReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCreditAuthReversalReply',
            'ECAVSReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECAVSReply',
            'ECDebitReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECDebitReply',
            'ECCreditReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECCreditReply',
            'ECAuthenticateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECAuthenticateReply',
            'PayerAuthSetupReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayerAuthSetupReply',
            'PayerAuthEnrollReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayerAuthEnrollReply',
            'PayerAuthValidateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayerAuthValidateReply',
            'TaxReplyItem' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\TaxReplyItem',
            'TaxReplyItemJurisdiction' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\TaxReplyItemJurisdiction',
            'TaxReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\TaxReply',
            'DeviceFingerprint' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DeviceFingerprint',
            'AFSReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AFSReply',
            'DAVReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DAVReply',
            'DeniedPartiesMatch' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DeniedPartiesMatch',
            'ExportReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ExportReply',
            'FXQuote' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FXQuote',
            'FXRatesReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FXRatesReply',
            'BankTransferReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BankTransferReply',
            'BankTransferRealTimeReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BankTransferRealTimeReply',
            'DirectDebitMandateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitMandateReply',
            'BankTransferRefundReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BankTransferRefundReply',
            'DirectDebitReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitReply',
            'DirectDebitValidateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitValidateReply',
            'DirectDebitRefundReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DirectDebitRefundReply',
            'PaySubscriptionCreateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionCreateReply',
            'PaySubscriptionUpdateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionUpdateReply',
            'PaySubscriptionEventUpdateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionEventUpdateReply',
            'PaySubscriptionRetrieveReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionRetrieveReply',
            'PaySubscriptionDeleteReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaySubscriptionDeleteReply',
            'PayPalPaymentReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalPaymentReply',
            'PayPalCreditReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalCreditReply',
            'VoidReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VoidReply',
            'PinlessDebitReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinlessDebitReply',
            'PinlessDebitValidateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinlessDebitValidateReply',
            'PinlessDebitReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PinlessDebitReversalReply',
            'PayPalButtonCreateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalButtonCreateReply',
            'PayPalPreapprovedPaymentReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalPreapprovedPaymentReply',
            'PayPalPreapprovedUpdateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalPreapprovedUpdateReply',
            'PayPalEcSetReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcSetReply',
            'PayPalEcGetDetailsReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcGetDetailsReply',
            'PayPalEcDoPaymentReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcDoPaymentReply',
            'PayPalDoCaptureReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalDoCaptureReply',
            'PayPalAuthReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalAuthReversalReply',
            'PayPalRefundReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalRefundReply',
            'PayPalEcOrderSetupReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalEcOrderSetupReply',
            'PayPalAuthorizationReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalAuthorizationReply',
            'PayPalUpdateAgreementReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalUpdateAgreementReply',
            'PayPalCreateAgreementReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalCreateAgreementReply',
            'PayPalDoRefTransactionReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalDoRefTransactionReply',
            'RiskUpdateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\RiskUpdateReply',
            'FraudUpdateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FraudUpdateReply',
            'CaseManagementActionReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CaseManagementActionReply',
            'RuleResultItem' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\RuleResultItem',
            'RuleResultItems' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\RuleResultItems',
            'DecisionReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DecisionReply',
            'ProviderFields' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ProviderFields',
            'Provider' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Provider',
            'ProviderField' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ProviderField',
            'AdditionalFields' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AdditionalFields',
            'Field' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Field',
            'MorphingElement' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\MorphingElement',
            'Element' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Element',
            'VelocityCounts' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VelocityCounts',
            'Travel' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Travel',
            'DMEReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DMEReply',
            'ProfileReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ProfileReply',
            'CCDCCReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCDCCReply',
            'paymentCurrencyOffer' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\paymentCurrencyOffer',
            'CCDCCUpdateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCDCCUpdateReply',
            'ChinaPaymentReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ChinaPaymentReply',
            'ChinaRefundReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ChinaRefundReply',
            'BoletoPaymentReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BoletoPaymentReply',
            'APInitiateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APInitiateReply',
            'APCheckStatusReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCheckStatusReply',
            'SellerProtection' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\SellerProtection',
            'APReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APReply',
            'APAuthReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APAuthReply',
            'APAuthReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APAuthReversalReply',
            'APCaptureReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCaptureReply',
            'APOptionsReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APOptionsReply',
            'APOptionsOption' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APOptionsOption',
            'APRefundReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APRefundReply',
            'APSaleReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APSaleReply',
            'APCheckOutDetailsReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCheckOutDetailsReply',
            'APTransactionDetailsReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APTransactionDetailsReply',
            'APConfirmPurchaseReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APConfirmPurchaseReply',
            'APSessionsReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APSessionsReply',
            'CCCheckStatusReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCCheckStatusReply',
            'ReplyMessage' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ReplyMessage',
            'FaultDetails' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\FaultDetails',
            'AirlineData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AirlineData',
            'Leg' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Leg',
            'AncillaryData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AncillaryData',
            'Service' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Service',
            'LodgingData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\LodgingData',
            'Pos' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Pos',
            'Pin' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Pin',
            'EncryptedPayment' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\EncryptedPayment',
            'Installment' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Installment',
            'MDDField' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\MDDField',
            'MerchantDefinedData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\MerchantDefinedData',
            'AuxiliaryField' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AuxiliaryField',
            'AuxiliaryData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AuxiliaryData',
            'MerchantSecureData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\MerchantSecureData',
            'ReplyReserved' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ReplyReserved',
            'RequestReserved' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\RequestReserved',
            'PayPalGetTxnDetailsReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalGetTxnDetailsReply',
            'PayPalTransactionSearchReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PayPalTransactionSearchReply',
            'PaypalTransaction' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PaypalTransaction',
            'CCDCCUpdateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CCDCCUpdateService',
            'ServiceFee' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ServiceFee',
            'EmvRequest' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\EmvRequest',
            'EmvReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\EmvReply',
            'OriginalTransaction' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\OriginalTransaction',
            'HostedDataCreateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\HostedDataCreateService',
            'HostedDataRetrieveService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\HostedDataRetrieveService',
            'HostedDataCreateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\HostedDataCreateReply',
            'HostedDataRetrieveReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\HostedDataRetrieveReply',
            'AutoRentalData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AutoRentalData',
            'AutoRental' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AutoRental',
            'AgencyInformation' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AgencyInformation',
            'HealthCare' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\HealthCare',
            'VCReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VCReply',
            'VCCardArt' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VCCardArt',
            'VCCustomData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VCCustomData',
            'DecryptVisaCheckoutDataReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DecryptVisaCheckoutDataReply',
            'GetVisaCheckoutDataReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GetVisaCheckoutDataReply',
            'EncryptPaymentDataReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\EncryptPaymentDataReply',
            'BinLookupService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BinLookupService',
            'BinLookupReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\BinLookupReply',
            'issuer' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\issuer',
            'GETVisaCheckoutDataService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GETVisaCheckoutDataService',
            'TransactionMetadataService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\TransactionMetadataService',
            'Loan' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Loan',
            'APOrderService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APOrderService',
            'APOrderReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APOrderReply',
            'APUpdateOrderService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APUpdateOrderService',
            'APExtendOrderService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APExtendOrderService',
            'APCancelService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCancelService',
            'APCancelReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCancelReply',
            'APBillingAgreementService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APBillingAgreementService',
            'APBillingAgreementReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APBillingAgreementReply',
            'Passenger' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Passenger',
            'PostdatedTransaction' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PostdatedTransaction',
            'APCreateMandateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCreateMandateService',
            'APCreateMandateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APCreateMandateReply',
            'APMandateStatusService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APMandateStatusService',
            'APMandateStatusReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APMandateStatusReply',
            'APUpdateMandateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APUpdateMandateService',
            'GetMasterpassDataService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GetMasterpassDataService',
            'GetMasterpassDataReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GetMasterpassDataReply',
            'APUpdateMandateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APUpdateMandateReply',
            'APImportMandateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APImportMandateReply',
            'APRevokeMandateService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APRevokeMandateService',
            'APRevokeMandateReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\APRevokeMandateReply',
            'Category' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Category',
            'ECAVSService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ECAVSService',
            'GiftCardActivationService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardActivationService',
            'GiftCardBalanceInquiryService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardBalanceInquiryService',
            'GiftCardVoidService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardVoidService',
            'GiftCardReversalService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardReversalService',
            'GiftCardRedemptionService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardRedemptionService',
            'GiftCardReloadService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardReloadService',
            'GiftCardRefundService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardRefundService',
            'GiftCard' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCard',
            'GiftCardActivationReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardActivationReply',
            'GiftCardBalanceInquiryReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardBalanceInquiryReply',
            'GiftCardRedemptionReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardRedemptionReply',
            'GiftCardReversalReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardReversalReply',
            'GiftCardVoidReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardVoidReply',
            'GiftCardReloadReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardReloadReply',
            'GiftCardRefundReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\GiftCardRefundReply',
            'mPOS' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\mPOS',
            'AbortService' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AbortService',
            'AbortReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AbortReply',
            'merchant' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\merchant',
            'DecisionEarlyReply' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\DecisionEarlyReply',
            'ProfileReplyEarly' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\ProfileReplyEarly',
            'VelocityCountsEarly' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VelocityCountsEarly',
            'VelocityElement' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VelocityElement',
            'PauseRuleResultItems' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PauseRuleResultItems',
            'PauseRuleResultItem' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\PauseRuleResultItem',
            'payByPoints' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\payByPoints',
            'Recurring' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Recurring',
            'Customer' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Customer',
            'VehicleData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\VehicleData',
            'TokenProvisioningInformation' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\TokenProvisioningInformation',
            'Unscheduled' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Unscheduled',
            'AuthorizationOptions' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\AuthorizationOptions',
            'CaptureOptions' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\CaptureOptions',
            'EmbeddedActions' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\EmbeddedActions',
            'Capture' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\Capture',
            'authenticationData' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\authenticationData',
            'benefit' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\benefit',
            'serviceProcessing' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\serviceProcessing',
            'language' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\language',
            'accountHolder' => 'ParadoxLabs\\CyberSource\\Gateway\\Api\\accountHolder',
        ];

    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder
     */
    protected $objectBuilder;

    /**
     * @param array $options An array of SoapClient options/config
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder $objectBuilder
     * @throws \SoapFault
     */
    public function __construct(
        array $options,
        Config $config,
        ObjectBuilder $objectBuilder
    ) {
        $this->config = $config;
        $this->objectBuilder = $objectBuilder;

        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][ $key ])) {
                $options['classmap'][ $key ] = $value;
            }
        }

        $options = array_merge(
            [
                'features' => 1,
            ],
            $options
        );

        parent::__construct($config->getSoapWsdl(), $options);
    }

    /**
     * @param RequestMessage $input
     * @return ReplyMessage
     */
    public function runTransaction(RequestMessage $input)
    {
        if ($this->config->getSoapAuthType() === 'transaction_key') {
            $this->__setSoapHeaders(
                $this->objectBuilder->getSecurityHeader(
                    $this->config->getMerchantId(),
                    $this->config->getSoapTransactionKey()
                )
            );
        }

        return $this->__soapCall('runTransaction', [$input]);
    }

    /**
     * Replace generic request with our own signed HTTPS request
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param bool $oneWay
     * @return string
     */
    #[\ReturnTypeWillChange]
    function __doRequest($request, $location, $action, $version, $oneWay = false)
    {
        if ($this->config->getSoapAuthType() === 'cert') {
            $request = $this->signWithCertificate($request);
        }

        return parent::__doRequest($request, $location, $action, $version, $oneWay);
    }

    /**
     * Sign SOAP request with the configured P12 certificate and password
     *
     * Based on CyberSource PHPSoapToolkit
     *
     * @param string $request
     * @return string
     * @throws \DOMException
     * @see https://github.com/CyberSource/cybersource-soap-toolkit/blob/master/PHPSoapToolkit/ExtendedClientWithToken.php
     */
    public function signWithCertificate(string $request): string
    {
        // Load request and add security headers
        $requestDom = new \DOMDocument('1.0', 'utf-8');
        $requestDom->loadXML($request);

        $domXPath = new \DOMXPath($requestDom);
        $domXPath->registerNamespace('SOAP-ENV', WsseSignature::SOAP_NS);

        // Mark SOAP-ENV:Body with wsu:Id for signing
        $bodyNode = $domXPath->query('/SOAP-ENV:Envelope/SOAP-ENV:Body')->item(0);
        $bodyNode->setAttributeNS(WsseSignature::WSU_NS, 'wsu:Id', 'Body');

        // Extract or Create SoapHeader
        $headerNode = $domXPath->query('/SOAP-ENV:Envelope/SOAP-ENV:Header')->item(0);
        if (!$headerNode) {
            $headerNode = $requestDom->documentElement->insertBefore(
                $requestDom->createElementNS(WsseSignature::SOAP_NS, 'SOAP-ENV:Header'),
                $bodyNode
            );
        }

        // Create BinarySecurityToken
        $wsseSignature = $this->objectBuilder->getSecuritySignature();
        $securityToken = $wsseSignature->generateSecurityToken(
            $requestDom,
            $this->config->getSoapCertificate(),
            $this->config->getSoapCertPassword()
        );

        // Prepare Security element
        $securityElement = $headerNode->appendChild(
            $requestDom->createElementNS(WsseSignature::WSSE_NS, 'wsse:Security')
        );
        $securityElement->appendChild($securityToken);


        // Create Signature element and build SignedInfo for elements with provided ids
        $signatureElement = $securityElement->appendChild(
            $requestDom->createElementNS(WsseSignature::DS_NS, 'ds:Signature')
        );
        $signInfo         = $signatureElement->appendChild(
            $wsseSignature->buildSignedInfo($requestDom, ['Body'])
        );

        // Combine Binary Security Token with Signature element
        $signature = null;
        openssl_sign(
            $wsseSignature->canonicalizeNode($signInfo),
            $signature,
            $wsseSignature->getPrivateKey(),
            OPENSSL_ALGO_SHA256
        );

        $signatureElement->appendChild(
            $requestDom->createElementNS(WsseSignature::DS_NS, 'ds:SignatureValue', base64_encode($signature))
        );
        $keyInfo = $signatureElement->appendChild(
            $requestDom->createElementNS(WsseSignature::DS_NS, 'ds:KeyInfo')
        );
        $securityTokenReferenceElement = $keyInfo->appendChild(
            $requestDom->createElementNS(WsseSignature::WSSE_NS, 'wsse:SecurityTokenReference')
        );
        $keyReference = $securityTokenReferenceElement->appendChild(
            $requestDom->createElementNS(WsseSignature::WSSE_NS, 'wsse:Reference')
        );
        $keyReference->setAttribute('URI', "#X509Token");

        // Convert Document to String
        $request = (string)$requestDom->saveXML();

        return $request;
    }
}
