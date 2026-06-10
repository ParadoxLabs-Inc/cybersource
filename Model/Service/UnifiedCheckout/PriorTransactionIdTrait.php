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

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout;

use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Shared prior-transaction-id resolution for the original PAYMENT id of a payment.
 *
 * Used both for the unlinked-credit refund fallback (Gateway) and the MIT
 * merchantInitiatedTransaction.previousTransactionId reference (Response). REST and SOAP share the
 * same transaction-id space (D4), so a single resolution serves both code paths.
 *
 * @see \ParadoxLabs\CyberSource\Model\Gateway
 * @see Response
 */
trait PriorTransactionIdTrait
{
    /**
     * Best-effort prior (original payment) transaction id, stripped of any -capture/-refund suffix.
     *
     * Resolved from the payment's parent transaction id, falling back to the last transaction id.
     * Returns '' when no prior id is reachable.
     *
     * @param InfoInterface $payment
     * @return string
     */
    protected function getPriorTransactionId(InfoInterface $payment): string
    {
        /** @var Payment $payment */
        $txnId = $payment->getParentTransactionId() ?: $payment->getLastTransId();

        return substr((string)$txnId, 0, strcspn((string)$txnId, '-'));
    }
}
