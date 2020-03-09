<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

/**
 * Persistor Class
 */
class Persistor
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Persistor constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Save the Payer Auth enrollment reply data persistently for access in later requests
     *
     * Checkout DB transaction rollback vastly limits our options.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api
     * @return void
     */
    public function savePayerAuthEnrollReply(
        \Magento\Payment\Model\InfoInterface $payment,
        \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api
    ) {
        $reply = $api->getPayerAuthEnrollReply();

        // NB: We only use acsURL, paRes, and authenticationTransactionID, but special cases may require others.
        $payerAuthPayload = [
            'acsRenderingType' => $reply->getAcsRenderingType(),
            'acsTransactionID' => $reply->getAcsTransactionID(),
            'acsURL' => $reply->getAcsURL(),
            'authenticationPath' => $reply->getAuthenticationPath(),
            'authenticationResult' => $reply->getAuthenticationResult(),
            'authenticationStatusMessage' => $reply->getAuthenticationStatusMessage(),
            'authenticationStatusReason' => $reply->getAuthenticationStatusReason(),
            'authenticationTransactionID' => $reply->getAuthenticationTransactionID(),
            'authenticationType' => $reply->getAuthenticationType(),
            'authorizationPayload' => $reply->getAuthorizationPayload(),
            'cardBin' => $reply->getCardBin(),
            'cardholderMessage' => $reply->getCardholderMessage(),
            'cardTypeName' => $reply->getCardTypeName(),
            'cavv' => $reply->getCavv(),
            'cavvAlgorithm' => $reply->getCavvAlgorithm(),
            'challengeCancelCode' => $reply->getChallengeCancelCode(),
            'challengeRequired' => $reply->getChallengeRequired(),
            'commerceIndicator' => $reply->getCommerceIndicator(),
            'decoupledAuthenticationIndicator' => $reply->getDecoupledAuthenticationIndicator(),
            'directoryServerErrorCode' => $reply->getDirectoryServerErrorCode(),
            'directoryServerErrorDescription' => $reply->getDirectoryServerErrorDescription(),
            'directoryServerTransactionID' => $reply->getDirectoryServerTransactionID(),
            'eci' => $reply->getEci(),
            'eciRaw' => $reply->getEciRaw(),
            'effectiveAuthenticationType' => $reply->getEffectiveAuthenticationType(),
            'ivrEnabledMessage' => $reply->getIvrEnabledMessage(),
            'ivrEncryptionKey' => $reply->getIvrEncryptionKey(),
            'ivrEncryptionMandatory' => $reply->getIvrEncryptionMandatory(),
            'ivrEncryptionType' => $reply->getIvrEncryptionType(),
            'ivrLabel' => $reply->getIvrLabel(),
            'ivrPrompt' => $reply->getIvrPrompt(),
            'ivrStatusMessage' => $reply->getIvrStatusMessage(),
            'networkScore' => $reply->getNetworkScore(),
            'paReq' => $reply->getPaReq(),
            'paresStatus' => $reply->getParesStatus(),
            'proofXML' => $reply->getProofXML(),
            'proxyPAN' => $reply->getProxyPAN(),
            'reasonCode' => $reply->getReasonCode(),
            'sdkTransactionID' => $reply->getSdkTransactionID(),
            'specificationVersion' => $reply->getSpecificationVersion(),
            'stepUpUrl' => $reply->getStepUpUrl(),
            'threeDSServerTransactionID' => $reply->getThreeDSServerTransactionID(),
            'ucafAuthenticationData' => $reply->getUcafAuthenticationData(),
            'ucafCollectionIndicator' => $reply->getUcafCollectionIndicator(),
            'veresEnrolled' => $reply->getVeresEnrolled(),
            'whiteListStatus' => $reply->getWhiteListStatus(),
            'whiteListStatusSource' => $reply->getWhiteListStatusSource(),
            'xid' => $reply->getXid(),
        ];

        /**
         * Note: This will often be executed in the context of a transaction that will be rolled back almost immediately
         * after, so nothing can be written to the database, straight up. But sessions don't get hit by rollback.
         */

        $this->checkoutSession->setData('payer_auth_enroll_reply', $payerAuthPayload);
    }

    /**
     * Load the latest saved Payer Auth enrollment reply for the current frontend user/checkout attempt
     *
     * @return array
     */
    public function loadPayerAuthEnrollReply()
    {
        $reply = $this->checkoutSession->getData('payer_auth_enroll_reply');

        if (empty($reply)) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to find Payer Authentication enrollment data. Please try again.')
            );
        }

        return $reply;
    }
}
