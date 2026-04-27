<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DOMDocument;
use DOMXPath;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ReturnTypeWillChange;
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
            'Item' => \ParadoxLabs\CyberSource\Gateway\Api\Item::class,
            'CCAuthService' => \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService::class,
            'OCTService' => \ParadoxLabs\CyberSource\Gateway\Api\OCTService::class,
            'VerificationService' => \ParadoxLabs\CyberSource\Gateway\Api\VerificationService::class,
            'CCSaleService' => \ParadoxLabs\CyberSource\Gateway\Api\CCSaleService::class,
            'CCSaleCreditService' => \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditService::class,
            'CCSaleReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalService::class,
            'CCIncrementalAuthService' => \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthService::class,
            'CCCaptureService' => \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService::class,
            'CCCreditService' => \ParadoxLabs\CyberSource\Gateway\Api\CCCreditService::class,
            'CCCreditAuthService' => \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthService::class,
            'CCAuthReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalService::class,
            'CCAutoAuthReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalService::class,
            'CCCreditAuthReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReversalService::class,
            'CCDCCService' => \ParadoxLabs\CyberSource\Gateway\Api\CCDCCService::class,
            'ServiceFeeCalculateService' => \ParadoxLabs\CyberSource\Gateway\Api\ServiceFeeCalculateService::class,
            'ECDebitService' => \ParadoxLabs\CyberSource\Gateway\Api\ECDebitService::class,
            'ECCreditService' => \ParadoxLabs\CyberSource\Gateway\Api\ECCreditService::class,
            'ECAuthenticateService' => \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateService::class,
            'PayerAuthEnrollService' => \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService::class,
            'PayerAuthValidateService' => \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService::class,
            'PayerAuthSetupService' => \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthSetupService::class,
            'TaxService' => \ParadoxLabs\CyberSource\Gateway\Api\TaxService::class,
            'DMEService' => \ParadoxLabs\CyberSource\Gateway\Api\DMEService::class,
            'AFSService' => \ParadoxLabs\CyberSource\Gateway\Api\AFSService::class,
            'DAVService' => \ParadoxLabs\CyberSource\Gateway\Api\DAVService::class,
            'ExportService' => \ParadoxLabs\CyberSource\Gateway\Api\ExportService::class,
            'FXRatesService' => \ParadoxLabs\CyberSource\Gateway\Api\FXRatesService::class,
            'BankTransferService' => \ParadoxLabs\CyberSource\Gateway\Api\BankTransferService::class,
            'BankTransferRefundService' => \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundService::class,
            'BankTransferRealTimeService' => \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRealTimeService::class,
            'DirectDebitMandateService' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateService::class,
            'DirectDebitService' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitService::class,
            'DirectDebitRefundService' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundService::class,
            'DirectDebitValidateService' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitValidateService::class,
            'DeviceFingerprintData' => \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprintData::class,
            'PaySubscriptionCreateService' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateService::class,
            'PaySubscriptionUpdateService' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateService::class,
            'PaySubscriptionEventUpdateService' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEventUpdateService::class,
            'PaySubscriptionRetrieveService' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveService::class,
            'PaySubscriptionDeleteService' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteService::class,
            'PayPalPaymentService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalPaymentService::class,
            'PayPalCreditService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreditService::class,
            'PayPalEcSetService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetService::class,
            'PayPalEcGetDetailsService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsService::class,
            'PayPalEcDoPaymentService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentService::class,
            'PayPalDoCaptureService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureService::class,
            'PayPalAuthReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthReversalService::class,
            'PayPalRefundService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundService::class,
            'PayPalEcOrderSetupService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcOrderSetupService::class,
            'PayPalAuthorizationService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationService::class,
            'PayPalUpdateAgreementService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementService::class,
            'PayPalCreateAgreementService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreateAgreementService::class,
            'PayPalDoRefTransactionService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionService::class,
            'VoidService' => \ParadoxLabs\CyberSource\Gateway\Api\VoidService::class,
            'PinlessDebitService' => \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitService::class,
            'PinlessDebitValidateService' => \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitValidateService::class,
            'PinlessDebitReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitReversalService::class,
            'PinDebitPurchaseService' => \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseService::class,
            'PinDebitCreditService' => \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditService::class,
            'PinDebitReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\PinDebitReversalService::class,
            'PayPalButtonCreateService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateService::class,
            'PayPalPreapprovedPaymentService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentService::class,
            'PayPalPreapprovedUpdateService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateService::class,
            'ChinaPaymentService' => \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentService::class,
            'ChinaRefundService' => \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundService::class,
            'BoletoPaymentService' => \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentService::class,
            'PersonalID' => \ParadoxLabs\CyberSource\Gateway\Api\PersonalID::class,
            'Routing' => \ParadoxLabs\CyberSource\Gateway\Api\Routing::class,
            'Address' => \ParadoxLabs\CyberSource\Gateway\Api\Address::class,
            'APInitiateService' => \ParadoxLabs\CyberSource\Gateway\Api\APInitiateService::class,
            'APCheckStatusService' => \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusService::class,
            'RiskUpdateService' => \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateService::class,
            'FraudUpdateService' => \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateService::class,
            'CaseManagementActionService' => \ParadoxLabs\CyberSource\Gateway\Api\CaseManagementActionService::class,
            'EncryptPaymentDataService' => \ParadoxLabs\CyberSource\Gateway\Api\EncryptPaymentDataService::class,
            'InvoiceHeader' => \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader::class,
            'BusinessRules' => \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules::class,
            'BillTo' => \ParadoxLabs\CyberSource\Gateway\Api\BillTo::class,
            'ShipTo' => \ParadoxLabs\CyberSource\Gateway\Api\ShipTo::class,
            'ShipFrom' => \ParadoxLabs\CyberSource\Gateway\Api\ShipFrom::class,
            'Card' => \ParadoxLabs\CyberSource\Gateway\Api\Card::class,
            'Check' => \ParadoxLabs\CyberSource\Gateway\Api\Check::class,
            'BML' => \ParadoxLabs\CyberSource\Gateway\Api\BML::class,
            'OtherTax' => \ParadoxLabs\CyberSource\Gateway\Api\OtherTax::class,
            'Aft' => \ParadoxLabs\CyberSource\Gateway\Api\Aft::class,
            'Wallet' => \ParadoxLabs\CyberSource\Gateway\Api\Wallet::class,
            'PurchaseTotals' => \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals::class,
            'FundingTotals' => \ParadoxLabs\CyberSource\Gateway\Api\FundingTotals::class,
            'GECC' => \ParadoxLabs\CyberSource\Gateway\Api\GECC::class,
            'UCAF' => \ParadoxLabs\CyberSource\Gateway\Api\UCAF::class,
            'Network' => \ParadoxLabs\CyberSource\Gateway\Api\Network::class,
            'Brands' => \ParadoxLabs\CyberSource\Gateway\Api\Brands::class,
            'FundTransfer' => \ParadoxLabs\CyberSource\Gateway\Api\FundTransfer::class,
            'BankInfo' => \ParadoxLabs\CyberSource\Gateway\Api\BankInfo::class,
            'RecurringSubscriptionInfo' => \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo::class,
            'PaySubscriptionEvent' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEvent::class,
            'Subscription' => \ParadoxLabs\CyberSource\Gateway\Api\Subscription::class,
            'TokenSource' => \ParadoxLabs\CyberSource\Gateway\Api\TokenSource::class,
            'PaymentNetworkToken' => \ParadoxLabs\CyberSource\Gateway\Api\PaymentNetworkToken::class,
            'DecisionManager' => \ParadoxLabs\CyberSource\Gateway\Api\DecisionManager::class,
            'Authentication' => \ParadoxLabs\CyberSource\Gateway\Api\Authentication::class,
            'DecisionManagerTravelData' => \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelData::class,
            'DecisionManagerTravelLeg' => \ParadoxLabs\CyberSource\Gateway\Api\DecisionManagerTravelLeg::class,
            'Batch' => \ParadoxLabs\CyberSource\Gateway\Api\Batch::class,
            'PayPal' => \ParadoxLabs\CyberSource\Gateway\Api\PayPal::class,
            'JPO' => \ParadoxLabs\CyberSource\Gateway\Api\JPO::class,
            'Token' => \ParadoxLabs\CyberSource\Gateway\Api\Token::class,
            'AP' => \ParadoxLabs\CyberSource\Gateway\Api\AP::class,
            'APDevice' => \ParadoxLabs\CyberSource\Gateway\Api\APDevice::class,
            'APAuthService' => \ParadoxLabs\CyberSource\Gateway\Api\APAuthService::class,
            'APImportMandateService' => \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateService::class,
            'APAuthReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalService::class,
            'APCaptureService' => \ParadoxLabs\CyberSource\Gateway\Api\APCaptureService::class,
            'APOptionsService' => \ParadoxLabs\CyberSource\Gateway\Api\APOptionsService::class,
            'APRefundService' => \ParadoxLabs\CyberSource\Gateway\Api\APRefundService::class,
            'APSaleService' => \ParadoxLabs\CyberSource\Gateway\Api\APSaleService::class,
            'APCheckOutDetailsService' => \ParadoxLabs\CyberSource\Gateway\Api\APCheckOutDetailsService::class,
            'APTransactionDetailsService' => \ParadoxLabs\CyberSource\Gateway\Api\APTransactionDetailsService::class,
            'APConfirmPurchaseService' => \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseService::class,
            'APSessionsService' => \ParadoxLabs\CyberSource\Gateway\Api\APSessionsService::class,
            'APUI' => \ParadoxLabs\CyberSource\Gateway\Api\APUI::class,
            'PayPalGetTxnDetailsService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsService::class,
            'PayPalTransactionSearchService' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchService::class,
            'Recipient' => \ParadoxLabs\CyberSource\Gateway\Api\Recipient::class,
            'Sender' => \ParadoxLabs\CyberSource\Gateway\Api\Sender::class,
            'CCCheckStatusService' => \ParadoxLabs\CyberSource\Gateway\Api\CCCheckStatusService::class,
            'RequestMessage' => \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage::class,
            'VC' => \ParadoxLabs\CyberSource\Gateway\Api\VC::class,
            'DecryptVisaCheckoutDataService' => \ParadoxLabs\CyberSource\Gateway\Api\DecryptVisaCheckoutDataService::class,
            'DCC' => \ParadoxLabs\CyberSource\Gateway\Api\DCC::class,
            'Promotion' => \ParadoxLabs\CyberSource\Gateway\Api\Promotion::class,
            'PromotionGroup' => \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroup::class,
            'PromotionGroupReply' => \ParadoxLabs\CyberSource\Gateway\Api\PromotionGroupReply::class,
            'BalanceInfo' => \ParadoxLabs\CyberSource\Gateway\Api\BalanceInfo::class,
            'PaymentInsightsInformation' => \ParadoxLabs\CyberSource\Gateway\Api\PaymentInsightsInformation::class,
            'AdditionalToken' => \ParadoxLabs\CyberSource\Gateway\Api\AdditionalToken::class,
            'CCAuthReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReply::class,
            'OCTReply' => \ParadoxLabs\CyberSource\Gateway\Api\OCTReply::class,
            'VerificationReply' => \ParadoxLabs\CyberSource\Gateway\Api\VerificationReply::class,
            'CCSaleReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReply::class,
            'CCSaleCreditReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCSaleCreditReply::class,
            'CCSaleReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCSaleReversalReply::class,
            'CCIncrementalAuthReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCIncrementalAuthReply::class,
            'CCCaptureReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureReply::class,
            'ServiceFeeCalculateReply' => \ParadoxLabs\CyberSource\Gateway\Api\ServiceFeeCalculateReply::class,
            'CCCreditReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCCreditReply::class,
            'CCCreditAuthReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReply::class,
            'PinDebitPurchaseReply' => \ParadoxLabs\CyberSource\Gateway\Api\PinDebitPurchaseReply::class,
            'PinDebitCreditReply' => \ParadoxLabs\CyberSource\Gateway\Api\PinDebitCreditReply::class,
            'PinDebitReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\PinDebitReversalReply::class,
            'CCAuthReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalReply::class,
            'CCAutoAuthReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCAutoAuthReversalReply::class,
            'CCCreditAuthReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCCreditAuthReversalReply::class,
            'ECAVSReply' => \ParadoxLabs\CyberSource\Gateway\Api\ECAVSReply::class,
            'ECDebitReply' => \ParadoxLabs\CyberSource\Gateway\Api\ECDebitReply::class,
            'ECCreditReply' => \ParadoxLabs\CyberSource\Gateway\Api\ECCreditReply::class,
            'ECAuthenticateReply' => \ParadoxLabs\CyberSource\Gateway\Api\ECAuthenticateReply::class,
            'PayerAuthSetupReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthSetupReply::class,
            'PayerAuthEnrollReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollReply::class,
            'PayerAuthValidateReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateReply::class,
            'TaxReplyItem' => \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItem::class,
            'TaxReplyItemJurisdiction' => \ParadoxLabs\CyberSource\Gateway\Api\TaxReplyItemJurisdiction::class,
            'TaxReply' => \ParadoxLabs\CyberSource\Gateway\Api\TaxReply::class,
            'DeviceFingerprint' => \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprint::class,
            'AFSReply' => \ParadoxLabs\CyberSource\Gateway\Api\AFSReply::class,
            'DAVReply' => \ParadoxLabs\CyberSource\Gateway\Api\DAVReply::class,
            'DeniedPartiesMatch' => \ParadoxLabs\CyberSource\Gateway\Api\DeniedPartiesMatch::class,
            'ExportReply' => \ParadoxLabs\CyberSource\Gateway\Api\ExportReply::class,
            'FXQuote' => \ParadoxLabs\CyberSource\Gateway\Api\FXQuote::class,
            'FXRatesReply' => \ParadoxLabs\CyberSource\Gateway\Api\FXRatesReply::class,
            'BankTransferReply' => \ParadoxLabs\CyberSource\Gateway\Api\BankTransferReply::class,
            'BankTransferRealTimeReply' => \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRealTimeReply::class,
            'DirectDebitMandateReply' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateReply::class,
            'BankTransferRefundReply' => \ParadoxLabs\CyberSource\Gateway\Api\BankTransferRefundReply::class,
            'DirectDebitReply' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitReply::class,
            'DirectDebitValidateReply' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitValidateReply::class,
            'DirectDebitRefundReply' => \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitRefundReply::class,
            'PaySubscriptionCreateReply' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionCreateReply::class,
            'PaySubscriptionUpdateReply' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionUpdateReply::class,
            'PaySubscriptionEventUpdateReply' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionEventUpdateReply::class,
            'PaySubscriptionRetrieveReply' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionRetrieveReply::class,
            'PaySubscriptionDeleteReply' => \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteReply::class,
            'PayPalPaymentReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalPaymentReply::class,
            'PayPalCreditReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreditReply::class,
            'VoidReply' => \ParadoxLabs\CyberSource\Gateway\Api\VoidReply::class,
            'PinlessDebitReply' => \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitReply::class,
            'PinlessDebitValidateReply' => \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitValidateReply::class,
            'PinlessDebitReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitReversalReply::class,
            'PayPalButtonCreateReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateReply::class,
            'PayPalPreapprovedPaymentReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedPaymentReply::class,
            'PayPalPreapprovedUpdateReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalPreapprovedUpdateReply::class,
            'PayPalEcSetReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcSetReply::class,
            'PayPalEcGetDetailsReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcGetDetailsReply::class,
            'PayPalEcDoPaymentReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcDoPaymentReply::class,
            'PayPalDoCaptureReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoCaptureReply::class,
            'PayPalAuthReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthReversalReply::class,
            'PayPalRefundReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalRefundReply::class,
            'PayPalEcOrderSetupReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalEcOrderSetupReply::class,
            'PayPalAuthorizationReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalAuthorizationReply::class,
            'PayPalUpdateAgreementReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalUpdateAgreementReply::class,
            'PayPalCreateAgreementReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalCreateAgreementReply::class,
            'PayPalDoRefTransactionReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalDoRefTransactionReply::class,
            'RiskUpdateReply' => \ParadoxLabs\CyberSource\Gateway\Api\RiskUpdateReply::class,
            'FraudUpdateReply' => \ParadoxLabs\CyberSource\Gateway\Api\FraudUpdateReply::class,
            'CaseManagementActionReply' => \ParadoxLabs\CyberSource\Gateway\Api\CaseManagementActionReply::class,
            'RuleResultItem' => \ParadoxLabs\CyberSource\Gateway\Api\RuleResultItem::class,
            'RuleResultItems' => \ParadoxLabs\CyberSource\Gateway\Api\RuleResultItems::class,
            'DecisionReply' => \ParadoxLabs\CyberSource\Gateway\Api\DecisionReply::class,
            'ProviderFields' => \ParadoxLabs\CyberSource\Gateway\Api\ProviderFields::class,
            'Provider' => \ParadoxLabs\CyberSource\Gateway\Api\Provider::class,
            'ProviderField' => \ParadoxLabs\CyberSource\Gateway\Api\ProviderField::class,
            'AdditionalFields' => \ParadoxLabs\CyberSource\Gateway\Api\AdditionalFields::class,
            'Field' => \ParadoxLabs\CyberSource\Gateway\Api\Field::class,
            'MorphingElement' => \ParadoxLabs\CyberSource\Gateway\Api\MorphingElement::class,
            'Element' => \ParadoxLabs\CyberSource\Gateway\Api\Element::class,
            'VelocityCounts' => \ParadoxLabs\CyberSource\Gateway\Api\VelocityCounts::class,
            'Travel' => \ParadoxLabs\CyberSource\Gateway\Api\Travel::class,
            'DMEReply' => \ParadoxLabs\CyberSource\Gateway\Api\DMEReply::class,
            'ProfileReply' => \ParadoxLabs\CyberSource\Gateway\Api\ProfileReply::class,
            'CCDCCReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCDCCReply::class,
            'paymentCurrencyOffer' => \ParadoxLabs\CyberSource\Gateway\Api\paymentCurrencyOffer::class,
            'CCDCCUpdateReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateReply::class,
            'ChinaPaymentReply' => \ParadoxLabs\CyberSource\Gateway\Api\ChinaPaymentReply::class,
            'ChinaRefundReply' => \ParadoxLabs\CyberSource\Gateway\Api\ChinaRefundReply::class,
            'BoletoPaymentReply' => \ParadoxLabs\CyberSource\Gateway\Api\BoletoPaymentReply::class,
            'APInitiateReply' => \ParadoxLabs\CyberSource\Gateway\Api\APInitiateReply::class,
            'APCheckStatusReply' => \ParadoxLabs\CyberSource\Gateway\Api\APCheckStatusReply::class,
            'SellerProtection' => \ParadoxLabs\CyberSource\Gateway\Api\SellerProtection::class,
            'APReply' => \ParadoxLabs\CyberSource\Gateway\Api\APReply::class,
            'APAuthReply' => \ParadoxLabs\CyberSource\Gateway\Api\APAuthReply::class,
            'APAuthReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\APAuthReversalReply::class,
            'APCaptureReply' => \ParadoxLabs\CyberSource\Gateway\Api\APCaptureReply::class,
            'APOptionsReply' => \ParadoxLabs\CyberSource\Gateway\Api\APOptionsReply::class,
            'APOptionsOption' => \ParadoxLabs\CyberSource\Gateway\Api\APOptionsOption::class,
            'APRefundReply' => \ParadoxLabs\CyberSource\Gateway\Api\APRefundReply::class,
            'APSaleReply' => \ParadoxLabs\CyberSource\Gateway\Api\APSaleReply::class,
            'APCheckOutDetailsReply' => \ParadoxLabs\CyberSource\Gateway\Api\APCheckOutDetailsReply::class,
            'APTransactionDetailsReply' => \ParadoxLabs\CyberSource\Gateway\Api\APTransactionDetailsReply::class,
            'APConfirmPurchaseReply' => \ParadoxLabs\CyberSource\Gateway\Api\APConfirmPurchaseReply::class,
            'APSessionsReply' => \ParadoxLabs\CyberSource\Gateway\Api\APSessionsReply::class,
            'CCCheckStatusReply' => \ParadoxLabs\CyberSource\Gateway\Api\CCCheckStatusReply::class,
            'ReplyMessage' => \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage::class,
            'FaultDetails' => \ParadoxLabs\CyberSource\Gateway\Api\FaultDetails::class,
            'AirlineData' => \ParadoxLabs\CyberSource\Gateway\Api\AirlineData::class,
            'Leg' => \ParadoxLabs\CyberSource\Gateway\Api\Leg::class,
            'AncillaryData' => \ParadoxLabs\CyberSource\Gateway\Api\AncillaryData::class,
            'Service' => \ParadoxLabs\CyberSource\Gateway\Api\Service::class,
            'LodgingData' => \ParadoxLabs\CyberSource\Gateway\Api\LodgingData::class,
            'Pos' => \ParadoxLabs\CyberSource\Gateway\Api\Pos::class,
            'Pin' => \ParadoxLabs\CyberSource\Gateway\Api\Pin::class,
            'EncryptedPayment' => \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment::class,
            'Installment' => \ParadoxLabs\CyberSource\Gateway\Api\Installment::class,
            'MDDField' => \ParadoxLabs\CyberSource\Gateway\Api\MDDField::class,
            'MerchantDefinedData' => \ParadoxLabs\CyberSource\Gateway\Api\MerchantDefinedData::class,
            'AuxiliaryField' => \ParadoxLabs\CyberSource\Gateway\Api\AuxiliaryField::class,
            'AuxiliaryData' => \ParadoxLabs\CyberSource\Gateway\Api\AuxiliaryData::class,
            'MerchantSecureData' => \ParadoxLabs\CyberSource\Gateway\Api\MerchantSecureData::class,
            'ReplyReserved' => \ParadoxLabs\CyberSource\Gateway\Api\ReplyReserved::class,
            'RequestReserved' => \ParadoxLabs\CyberSource\Gateway\Api\RequestReserved::class,
            'PayPalGetTxnDetailsReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalGetTxnDetailsReply::class,
            'PayPalTransactionSearchReply' => \ParadoxLabs\CyberSource\Gateway\Api\PayPalTransactionSearchReply::class,
            'PaypalTransaction' => \ParadoxLabs\CyberSource\Gateway\Api\PaypalTransaction::class,
            'CCDCCUpdateService' => \ParadoxLabs\CyberSource\Gateway\Api\CCDCCUpdateService::class,
            'ServiceFee' => \ParadoxLabs\CyberSource\Gateway\Api\ServiceFee::class,
            'EmvRequest' => \ParadoxLabs\CyberSource\Gateway\Api\EmvRequest::class,
            'EmvReply' => \ParadoxLabs\CyberSource\Gateway\Api\EmvReply::class,
            'OriginalTransaction' => \ParadoxLabs\CyberSource\Gateway\Api\OriginalTransaction::class,
            'HostedDataCreateService' => \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateService::class,
            'HostedDataRetrieveService' => \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveService::class,
            'HostedDataCreateReply' => \ParadoxLabs\CyberSource\Gateway\Api\HostedDataCreateReply::class,
            'HostedDataRetrieveReply' => \ParadoxLabs\CyberSource\Gateway\Api\HostedDataRetrieveReply::class,
            'AutoRentalData' => \ParadoxLabs\CyberSource\Gateway\Api\AutoRentalData::class,
            'AutoRental' => \ParadoxLabs\CyberSource\Gateway\Api\AutoRental::class,
            'AgencyInformation' => \ParadoxLabs\CyberSource\Gateway\Api\AgencyInformation::class,
            'HealthCare' => \ParadoxLabs\CyberSource\Gateway\Api\HealthCare::class,
            'VCReply' => \ParadoxLabs\CyberSource\Gateway\Api\VCReply::class,
            'VCCardArt' => \ParadoxLabs\CyberSource\Gateway\Api\VCCardArt::class,
            'VCCustomData' => \ParadoxLabs\CyberSource\Gateway\Api\VCCustomData::class,
            'DecryptVisaCheckoutDataReply' => \ParadoxLabs\CyberSource\Gateway\Api\DecryptVisaCheckoutDataReply::class,
            'GetVisaCheckoutDataReply' => \ParadoxLabs\CyberSource\Gateway\Api\GetVisaCheckoutDataReply::class,
            'EncryptPaymentDataReply' => \ParadoxLabs\CyberSource\Gateway\Api\EncryptPaymentDataReply::class,
            'BinLookupService' => \ParadoxLabs\CyberSource\Gateway\Api\BinLookupService::class,
            'BinLookupReply' => \ParadoxLabs\CyberSource\Gateway\Api\BinLookupReply::class,
            'issuer' => \ParadoxLabs\CyberSource\Gateway\Api\issuer::class,
            'GETVisaCheckoutDataService' => \ParadoxLabs\CyberSource\Gateway\Api\GETVisaCheckoutDataService::class,
            'TransactionMetadataService' => \ParadoxLabs\CyberSource\Gateway\Api\TransactionMetadataService::class,
            'Loan' => \ParadoxLabs\CyberSource\Gateway\Api\Loan::class,
            'APOrderService' => \ParadoxLabs\CyberSource\Gateway\Api\APOrderService::class,
            'APOrderReply' => \ParadoxLabs\CyberSource\Gateway\Api\APOrderReply::class,
            'APUpdateOrderService' => \ParadoxLabs\CyberSource\Gateway\Api\APUpdateOrderService::class,
            'APExtendOrderService' => \ParadoxLabs\CyberSource\Gateway\Api\APExtendOrderService::class,
            'APCancelService' => \ParadoxLabs\CyberSource\Gateway\Api\APCancelService::class,
            'APCancelReply' => \ParadoxLabs\CyberSource\Gateway\Api\APCancelReply::class,
            'APBillingAgreementService' => \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementService::class,
            'APBillingAgreementReply' => \ParadoxLabs\CyberSource\Gateway\Api\APBillingAgreementReply::class,
            'Passenger' => \ParadoxLabs\CyberSource\Gateway\Api\Passenger::class,
            'PostdatedTransaction' => \ParadoxLabs\CyberSource\Gateway\Api\PostdatedTransaction::class,
            'APCreateMandateService' => \ParadoxLabs\CyberSource\Gateway\Api\APCreateMandateService::class,
            'APCreateMandateReply' => \ParadoxLabs\CyberSource\Gateway\Api\APCreateMandateReply::class,
            'APMandateStatusService' => \ParadoxLabs\CyberSource\Gateway\Api\APMandateStatusService::class,
            'APMandateStatusReply' => \ParadoxLabs\CyberSource\Gateway\Api\APMandateStatusReply::class,
            'APUpdateMandateService' => \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateService::class,
            'GetMasterpassDataService' => \ParadoxLabs\CyberSource\Gateway\Api\GetMasterpassDataService::class,
            'GetMasterpassDataReply' => \ParadoxLabs\CyberSource\Gateway\Api\GetMasterpassDataReply::class,
            'APUpdateMandateReply' => \ParadoxLabs\CyberSource\Gateway\Api\APUpdateMandateReply::class,
            'APImportMandateReply' => \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateReply::class,
            'APRevokeMandateService' => \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateService::class,
            'APRevokeMandateReply' => \ParadoxLabs\CyberSource\Gateway\Api\APRevokeMandateReply::class,
            'Category' => \ParadoxLabs\CyberSource\Gateway\Api\Category::class,
            'ECAVSService' => \ParadoxLabs\CyberSource\Gateway\Api\ECAVSService::class,
            'GiftCardActivationService' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardActivationService::class,
            'GiftCardBalanceInquiryService' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardBalanceInquiryService::class,
            'GiftCardVoidService' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardVoidService::class,
            'GiftCardReversalService' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardReversalService::class,
            'GiftCardRedemptionService' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardRedemptionService::class,
            'GiftCardReloadService' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardReloadService::class,
            'GiftCardRefundService' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardRefundService::class,
            'GiftCard' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCard::class,
            'GiftCardActivationReply' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardActivationReply::class,
            'GiftCardBalanceInquiryReply' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardBalanceInquiryReply::class,
            'GiftCardRedemptionReply' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardRedemptionReply::class,
            'GiftCardReversalReply' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardReversalReply::class,
            'GiftCardVoidReply' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardVoidReply::class,
            'GiftCardReloadReply' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardReloadReply::class,
            'GiftCardRefundReply' => \ParadoxLabs\CyberSource\Gateway\Api\GiftCardRefundReply::class,
            'mPOS' => \ParadoxLabs\CyberSource\Gateway\Api\mPOS::class,
            'AbortService' => \ParadoxLabs\CyberSource\Gateway\Api\AbortService::class,
            'AbortReply' => \ParadoxLabs\CyberSource\Gateway\Api\AbortReply::class,
            'merchant' => \ParadoxLabs\CyberSource\Gateway\Api\merchant::class,
            'DecisionEarlyReply' => \ParadoxLabs\CyberSource\Gateway\Api\DecisionEarlyReply::class,
            'ProfileReplyEarly' => \ParadoxLabs\CyberSource\Gateway\Api\ProfileReplyEarly::class,
            'VelocityCountsEarly' => \ParadoxLabs\CyberSource\Gateway\Api\VelocityCountsEarly::class,
            'VelocityElement' => \ParadoxLabs\CyberSource\Gateway\Api\VelocityElement::class,
            'PauseRuleResultItems' => \ParadoxLabs\CyberSource\Gateway\Api\PauseRuleResultItems::class,
            'PauseRuleResultItem' => \ParadoxLabs\CyberSource\Gateway\Api\PauseRuleResultItem::class,
            'payByPoints' => \ParadoxLabs\CyberSource\Gateway\Api\payByPoints::class,
            'Recurring' => \ParadoxLabs\CyberSource\Gateway\Api\Recurring::class,
            'Customer' => \ParadoxLabs\CyberSource\Gateway\Api\Customer::class,
            'VehicleData' => \ParadoxLabs\CyberSource\Gateway\Api\VehicleData::class,
            'TokenProvisioningInformation' => \ParadoxLabs\CyberSource\Gateway\Api\TokenProvisioningInformation::class,
            'Unscheduled' => \ParadoxLabs\CyberSource\Gateway\Api\Unscheduled::class,
            'AuthorizationOptions' => \ParadoxLabs\CyberSource\Gateway\Api\AuthorizationOptions::class,
            'CaptureOptions' => \ParadoxLabs\CyberSource\Gateway\Api\CaptureOptions::class,
            'EmbeddedActions' => \ParadoxLabs\CyberSource\Gateway\Api\EmbeddedActions::class,
            'Capture' => \ParadoxLabs\CyberSource\Gateway\Api\Capture::class,
            'authenticationData' => \ParadoxLabs\CyberSource\Gateway\Api\authenticationData::class,
            'benefit' => \ParadoxLabs\CyberSource\Gateway\Api\benefit::class,
            'serviceProcessing' => \ParadoxLabs\CyberSource\Gateway\Api\serviceProcessing::class,
            'language' => \ParadoxLabs\CyberSource\Gateway\Api\language::class,
            'accountHolder' => \ParadoxLabs\CyberSource\Gateway\Api\accountHolder::class,
        ];

    /**
     * @param array $options An array of SoapClient options/config
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder $objectBuilder
     * @throws \SoapFault
     */
    public function __construct(
        array $options,
        protected readonly Config $config,
        protected readonly ObjectBuilder $objectBuilder
    ) {
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

        parent::__construct($this->config->getSoapWsdl(), $options);
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
    #[ReturnTypeWillChange]
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
        $requestDom = new DOMDocument('1.0', 'utf-8');
        $requestDom->loadXML($request);

        $domXPath = new DOMXPath($requestDom);
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
            $requestDom->createElementNS(WsseSignature::DS_NS, 'ds:SignatureValue', base64_encode((string) $signature))
        );
        $keyInfo                       = $signatureElement->appendChild(
            $requestDom->createElementNS(WsseSignature::DS_NS, 'ds:KeyInfo')
        );
        $securityTokenReferenceElement = $keyInfo->appendChild(
            $requestDom->createElementNS(WsseSignature::WSSE_NS, 'wsse:SecurityTokenReference')
        );
        $keyReference                  = $securityTokenReferenceElement->appendChild(
            $requestDom->createElementNS(WsseSignature::WSSE_NS, 'wsse:Reference')
        );
        $keyReference->setAttribute('URI', "#X509Token");

        // Convert Document to String
        $request = (string)$requestDom->saveXML();

        return $request;
    }
}
