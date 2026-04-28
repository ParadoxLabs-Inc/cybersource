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

namespace ParadoxLabs\CyberSource\Model;

use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Model\InfoInterface;
use ParadoxLabs\TokenBase\Model\AbstractMethod;
use ParadoxLabs\TokenBase\Model\Gateway\Response;

/**
 * Payment method
 */
class Method extends AbstractMethod
{
    /**
     * Initialize/return the API gateway class.
     *
     * @return Gateway
     * @api
     */
    #[\Override]
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
     * @param InfoInterface $payment
     * @return $this
     */
    #[\Override]
    protected function resyncStoredCard(InfoInterface $payment)
    {
        // All card updates are done via Secure Acceptance requests; skip server-side resync saves.
        return $this;
    }

    /**
     * Store response statuses persistently.
     *
     * @param InfoInterface $payment
     * @param Response $response
     * @return InfoInterface
     */
    #[\Override]
    protected function storeTransactionStatuses(
        InfoInterface $payment,
        Response $response
    ) {
        /** @var Payment $payment */
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
