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
