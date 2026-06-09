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
        } else {
            // Empty/null token (e.g. stored card selected): drop any stale token from a prior assign.
            $payment->unsAdditionalInformation('transient_token');
        }
    }
}
