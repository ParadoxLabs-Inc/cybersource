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

namespace ParadoxLabs\CyberSource\Gateway\Api;

/**
 * ObjectBuilder Class
 */
class ObjectBuilder
{
    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function getOrderBillTo(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order\Address $billingAddress */
        $billingAddress = $order->getBillingAddress();

        $billTo = new BillTo();
        $billTo->setFirstName($billingAddress->getFirstname());
        $billTo->setLastName($billingAddress->getLastname());
        $billTo->setStreet1($billingAddress->getStreetLine(1));
        $billTo->setStreet2($billingAddress->getStreetLine(2));
        $billTo->setCity($billingAddress->getCity());
        $billTo->setState($billingAddress->getRegionCode());
        $billTo->setPostalCode($billingAddress->getPostcode());
        $billTo->setCountry($billingAddress->getCountryId());
        $billTo->setPhoneNumber($billingAddress->getTelephone());
        $billTo->setEmail($billingAddress->getEmail());
        $billTo->setIpAddress($order->getRemoteIp());

        return $billTo;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function getOrderShipTo(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order\Address $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        $shipTo = new ShipTo();
        $shipTo->setFirstName($shippingAddress->getFirstname());
        $shipTo->setLastName($shippingAddress->getLastname());
        $shipTo->setStreet1($shippingAddress->getStreetLine(1));
        $shipTo->setStreet2($shippingAddress->getStreetLine(2));
        $shipTo->setCity($shippingAddress->getCity());
        $shipTo->setState($shippingAddress->getRegionCode());
        $shipTo->setPostalCode($shippingAddress->getPostcode());
        $shipTo->setCountry($shippingAddress->getCountryId());
        $shipTo->setPhoneNumber($shippingAddress->getTelephone());

        return $shipTo;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item[] $orderItems
     * @return array
     */
    public function getOrderItems($orderItems)
    {
        if (empty($orderItems)) {
            return [];
        }

        $items = [];
        $i     = 0;
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getQtyToInvoice() <= 0) {
                continue;
            }

            $items[] = $this->getOrderItem($orderItem, $i++);
        }

        return $items;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param int $lineNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Item
     */
    public function getOrderItem(\Magento\Sales\Model\Order\Item $orderItem, $lineNumber)
    {
        $soapItem = new Item($lineNumber);
        $soapItem->setUnitPrice($orderItem->getPrice());
        $soapItem->setQuantity($orderItem->getQtyToInvoice());
        $soapItem->setProductSKU($orderItem->getSku());
        $soapItem->setProductName($orderItem->getName());
        $soapItem->setTaxAmount($orderItem->getTaxAmount()); // TODO: Verify unit or row amount on both sides
        $soapItem->setDiscountAmount($orderItem->getDiscountAmount()); // TODO: Verify unit or row amount

        return $soapItem;
    }

    /**
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function getTokenInfo(\ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
    {
        $tokenInfo = new RecurringSubscriptionInfo();
        $tokenInfo->setSubscriptionID($card->getPaymentId());

        return $tokenInfo;
    }
}
