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

namespace ParadoxLabs\CyberSource\Model;

/**
 * CyberSource card model
 */
class Card extends \ParadoxLabs\TokenBase\Model\Card
{
    /**
     * Finalize before saving.
     *
     * return $this
     */
    public function beforeSave()
    {
        // Note: All gateway syncing happens via direct posts to Secure Acceptance.
        // @see \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Response for the response handling.

        // If this is a new card, set its active state to the given value (if any)
        $payment = $this->getInfoInstance();
        if ($payment instanceof \Magento\Payment\Model\InfoInterface
            && $payment->getAdditionalInformation('save') !== null
            && $this->getOrigData('last_use') === null) {
            $this->setActive((bool)$payment->getAdditionalInformation('save') ? 1 : 0);
        }

        parent::beforeSave();

        return $this;
    }

    /**
     * Finalize before deleting.
     *
     * @return $this
     */
    public function beforeDelete()
    {
        /**
         * Delete from Stripe if we have a valid record.
         */
        if (!empty($this->getPaymentId())) {
            /** @var \ParadoxLabs\CyberSource\Model\Gateway $gateway */
            $gateway = $this->getMethodInstance()->gateway();

            try {
                $gateway->setCard($this);
                $gateway->deleteCard();
            } catch (\Exception $e) {
                $this->helper->log(
                    $this->getMethod(),
                    sprintf(
                        'Failed to delete card from gateway: %s',
                        $e->getMessage()
                    )
                );
            }
        }

        parent::beforeDelete();

        return $this;
    }
}
