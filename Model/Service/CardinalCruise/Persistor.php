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
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api
     * @return void
     */
    public function savePayerAuthEnrollReply(
        \Magento\Payment\Model\InfoInterface $payment,
        \ParadoxLabs\CyberSource\Gateway\Api\ReplyMessage $api
    ) {
        $reply = $api->getPayerAuthEnrollReply();

        // TODO: Do these vary? May not always be what my test response had.
        $payerAuthPayload = [
            'acs_url' => $reply->getAcsURL(),
            'pa_req' => $reply->getPaReq(),
            'proxy_pan' => $reply->getProxyPAN(),
            'xid' => $reply->getXid(),
            'veres_enrolled' => $reply->getVeresEnrolled(),
            'authentication_path' => $reply->getAuthenticationPath(),
            'version' => $reply->getSpecificationVersion(),
            'transaction_id' => $reply->getAuthenticationTransactionID(),
            'card_bin' => $reply->getCardBin(), // TODO: Needed?
            'card_type_name' => $reply->getCardTypeName(), // TODO: Needed?
        ];

        /**
         * Note: This will often be executed in the context of a transaction that will be rolled back almost immediately
         * after, so nothing can be written to the database, straight up. But sessions don't get hit by rollback.
         */

        $this->checkoutSession->setData('payer_auth_enroll_reply', $payerAuthPayload);
    }

    /**
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
