<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model;

/**
 * Payment method
 */
class Method extends \ParadoxLabs\TokenBase\Model\AbstractMethod
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Gateway
     */
    protected $gateway;

    /**
     * Initialize/return the API gateway class.
     *
     * @api
     *
     * @return \ParadoxLabs\CyberSource\Model\Gateway
     */
    public function gateway()
    {
        if ($this->gateway->isInitialized() !== true) {
            $this->gateway->init(['store_id' => $this->getData('store')]);
        }

        return $this->gateway;
    }

    /**
     * Resync billing address et al. before auth/capture.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     */
    protected function resyncStoredCard(\Magento\Payment\Model\InfoInterface $payment)
    {
        // All card updates are done via Secure Acceptance requests; skip server-side resync saves.
        return $this;
    }

    /**
     * Store response statuses persistently.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \ParadoxLabs\TokenBase\Model\Gateway\Response $response
     * @return \Magento\Payment\Model\InfoInterface
     */
    protected function storeTransactionStatuses(
        \Magento\Payment\Model\InfoInterface $payment,
        \ParadoxLabs\TokenBase\Model\Gateway\Response $response
    ) {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        if (empty($payment->getData('cc_avs_status'))) {
            $payment->setData('cc_avs_status', $response->getData('ccAuthReply.avsCode'));
        }

        if (empty($payment->getData('cc_cid_status'))) {
            $payment->setData('cc_cid_status', $response->getData('ccAuthReply.cvCode'));
        }

        if (!empty($response->getData('ccAuthReply.authorizationCode'))) {
            $payment->setData('cc_approval', $response->getData('ccAuthReply.authorizationCode'));
        }

        return $payment;
    }
}
