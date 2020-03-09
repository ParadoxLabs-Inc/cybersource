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

namespace ParadoxLabs\CyberSource\Observer;

/**
 * PaymentMethodAssignDataObserver Class
 */
class PaymentMethodAssignDataObserver extends \ParadoxLabs\TokenBase\Observer\PaymentMethodAssignDataObserver
{
    /**
     * Store the Response JWT on the payment object from checkout input payment data.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param \Magento\Framework\DataObject $data
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return void
     */
    protected function assignStandardData(
        \Magento\Payment\Model\InfoInterface $payment,
        \Magento\Framework\DataObject $data,
        \Magento\Payment\Model\MethodInterface $method
    ) {
        parent::assignStandardData($payment, $data, $method);

        $payment->setAdditionalInformation('response_jwt', $data->getData('response_jwt'));
    }
}
