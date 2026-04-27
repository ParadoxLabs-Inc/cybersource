<?php declare(strict_types=1);
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\StateException;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Payment;
use ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage;

class Persistor
{
    const PERSIST_KEY = 'payer_auth_enroll_reply';

    /**
     * Persistor constructor.
     *
     * @param Session $checkoutSession *Proxy
     * @param \Magento\Quote\Model\ResourceModel\Quote\Payment $paymentResource
     * @param State $appState
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        protected readonly Session $checkoutSession,
        protected readonly \Magento\Quote\Model\ResourceModel\Quote\Payment $paymentResource,
        protected readonly State $appState,
        protected readonly CartRepositoryInterface $cartRepository
    ) {
    }

    /**
     * Save the Payer Auth enrollment reply data persistently for access in later requests
     *
     * Checkout DB transaction rollback vastly limits our options.
     *
     * @param InfoInterface $payment
     * @param ReplyMessage $api
     * @return void
     */
    public function savePayerAuthEnrollReply(
        InfoInterface $payment,
        ReplyMessage $api
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

        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND) {
            $this->checkoutSession->setData(static::PERSIST_KEY, $payerAuthPayload);
        }

        if ($payment instanceof \Magento\Sales\Model\Order\Payment) {
            $quote = $this->cartRepository->get($payment->getOrder()->getQuoteId());
            $payment = $quote->getPayment();
        }

        if ($payment instanceof Payment) {
            $payment->setAdditionalInformation(static::PERSIST_KEY, $payerAuthPayload);
            $this->paymentResource->save($payment);
        }
    }

    /**
     * Load the latest saved Payer Auth enrollment reply for the current frontend user/checkout attempt
     *
     * @param InfoInterface $payment
     * @return array
     * @throws StateException
     */
    public function loadPayerAuthEnrollReply(InfoInterface $payment)
    {
        if ($payment instanceof Payment
            && !empty($payment->getAdditionalInformation(static::PERSIST_KEY))) {
            return $payment->getAdditionalInformation(static::PERSIST_KEY);
        }

        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND) {
            $reply = $this->checkoutSession->getData(static::PERSIST_KEY);
        }

        if (empty($reply)) {
            throw new StateException(
                __('Unable to find Payer Authentication enrollment data. Please try again.')
            );
        }

        return $reply;
    }
}
