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

use Magento\Framework\Exception\LocalizedException;

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
        // Send only if we have an info instance for payment data
        if ($this->hasData('info_instance') && $this->getData('no_sync') !== true) {
            if ($this->hasOrigData('payment_id') && $this->getData('address') !== $this->getOrigData('address')) {
                // TODO: Update billing address on the token, because it changed since the token was created.
                // TODO: Update card from account somehow?
            }
        }

        // TODO: Get card set to active if that's chosen on checkout.

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
