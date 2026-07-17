<?php declare(strict_types=1);
/**
 * Copyright © 2015-present ParadoxLabs, Inc.
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

namespace ParadoxLabs\CyberSource\Observer;

use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\PaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Override;

class PaymentMethodAssignDataObserver extends \ParadoxLabs\TokenBase\Observer\PaymentMethodAssignDataObserver
{
    /**
     * Assign data to the payment instance for our methods.
     *
     * Copies the Unified Checkout transient-token JWT (new-card path) from the client additional_data
     * contract into payment additional_information, where the UC auth/sale seam consumes it.
     *
     * @see \ParadoxLabs\CyberSource\Model\Gateway::hasTransientToken()
     * @see \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response
     *
     * @param InfoInterface $payment
     * @param DataObject $data
     * @param MethodInterface $method
     * @return void
     */
    #[Override]
    protected function assignTokenbaseData(
        InfoInterface $payment,
        DataObject $data,
        MethodInterface $method,
    ) {
        $this->processUnifiedCheckoutToken($payment, $data);

        parent::assignTokenbaseData($payment, $data, $method);
    }

    /**
     * Store the Unified Checkout transient token if given (new-card checkout/payment-info submit).
     *
     * Note: parent::execute() has already merged additional_data keys into the top level of $data,
     * so this covers the KO renderer, GraphQL (tokenbase_data), and legacy form-field paths alike.
     *
     * The client contract is "exactly one of {transient_token, card_id} populated per submit". When no
     * token is given (stored-card selection, or any re-assign without one), we must clear any token left
     * over from a prior assign on the same quote payment — Gateway::authorize() checks hasTransientToken()
     * before the stored-card branch, so a stale token from a failed new-card attempt would otherwise
     * authorize against the previously entered (wrong) card.
     *
     * @param InfoInterface $payment
     * @param DataObject $data
     * @return void
     */
    public function processUnifiedCheckoutToken(
        InfoInterface $payment,
        DataObject $data,
    ): void {
        $token = $data->getData('transient_token');

        if (is_string($token) && $token !== '') {
            $payment->setAdditionalInformation('transient_token', $token);

            /**
             * The mirror of the stale-token case below: a customer's persistent quote can carry a
             * tokenbase_id from a prior assign (stored-card selection, or an earlier failed attempt).
             * With no card_id in this submit, the parent's tokenbase_id fallback would reload that
             * stale card onto the payment, and the StoredCard validator would then treat this
             * new-card submit as a stored-card payment — with require_ccv on, that demands a CVV
             * the client correctly never collected, hard-failing every place-order on the quote.
             * A token submit IS a new card; clear the stale stored-card state so it stays one.
             * (card_id + token together is a broken client per the contract; leave that to parent.)
             */
            if ((string)$data->getData('card_id') === '' && $payment->getData('tokenbase_id') !== null) {
                $payment->setData('tokenbase_id', null);
                $payment->unsetData('tokenbase_card');

                $paymentAttributes = $payment->getExtensionAttributes();
                if ($paymentAttributes instanceof PaymentExtensionInterface
                    || $paymentAttributes instanceof OrderPaymentExtensionInterface) {
                    $paymentAttributes->setTokenbaseId(null);
                }
            }
        } else {
            // Empty/null token (e.g. stored card selected): drop any stale token from a prior assign.
            $payment->unsAdditionalInformation('transient_token');
        }
    }
}
