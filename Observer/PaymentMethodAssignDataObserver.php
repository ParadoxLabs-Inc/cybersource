<?php
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
 * @link https://support.paradoxlabs.com
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

        $payment->setAdditionalInformation('payerauth_session_id', $data->getData('payerauth_session_id'));
        $payment->setAdditionalInformation('response_jwt', $data->getData('response_jwt'));

        if (!empty($data->getData('response_jwt'))
            && empty($data->getData('card_id'))) {
            $payment->setData('tokenbase_id', null);
        }
    }
}
