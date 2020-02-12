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
    const SOAP_DEFAULTS = [
        'connection_timeout' => 3,
        'encoding' => 'utf-8',
        'exceptions' => true,
        'trace' => true,
    ];

    /**
     * @param string|null $wsdl
     * @param array $soapOptions
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TransactionProcessor
     */
    public function getProcessor($wsdl, $soapOptions = [])
    {
        return new TransactionProcessor(
            static::SOAP_DEFAULTS + $soapOptions,
            $wsdl
        );
    }

    /**
     * @param string $username
     * @param string $password
     * @return \ParadoxLabs\CyberSource\Gateway\Api\WsseHeader
     */
    public function getSecurityHeader($username, $password)
    {
        return new WsseHeader(
            '',
            '',
            null,
            true,
            null,
            $username,
            $password
        );
    }

    /**
     * @param string $merchantId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RequestMessage
     */
    public function getRequest($merchantId)
    {
        $request = new RequestMessage();
        $request->setMerchantID($merchantId);

        // All requests require a unique reference code, but that doesn't always make sense. Fill a sane default.
        // Particular requests can override this if needed (EG. order increment ID).
        $request->setMerchantReferenceCode(uniqid('', true));

        return $request;
    }

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
        $billTo->setCountry($billingAddress->getCountryId());
        $billTo->setPostalCode($billingAddress->getPostcode());
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
        $shipTo->setCountry($shippingAddress->getCountryId());
        $shipTo->setPostalCode($shippingAddress->getPostcode());
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
        $soapItem->setTaxAmount($orderItem->getTaxAmount()); // NB: Row total tax

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

    /**
     * @param string $currency
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PurchaseTotals
     */
    public function getPurchaseTotals($currency, $amount)
    {
        $purchaseTotals = new PurchaseTotals();
        $purchaseTotals->setCurrency($currency);
        $purchaseTotals->setGrandTotalAmount($amount);

        return $purchaseTotals;
    }

    /**
     * @param string $commerceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthService
     */
    public function getAuthService($commerceIndicator = null)
    {
        $ccAuthService = new CCAuthService('true');
        if ($commerceIndicator !== null) {
            $ccAuthService->setCommerceIndicator($commerceIndicator);
        }

        return $ccAuthService;
    }

    /**
     * @param string $transactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCaptureService
     */
    public function getCaptureService($transactionId = null)
    {
        $ccCaptureService = new CCCaptureService('true');
        if ($transactionId !== null) {
            $ccCaptureService->setAuthRequestID($transactionId);
        }

        return $ccCaptureService;
    }

    /**
     * @param string $commerceIndicator
     * @param string $transactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCCreditService
     */
    public function getCreditService($commerceIndicator = null, $transactionId = null)
    {
        $ccCreditService = new CCCreditService('true');
        if ($commerceIndicator !== null) {
            $ccCreditService->setCommerceIndicator($commerceIndicator);
        }
        if (!empty($transactionId)) {
            $ccCreditService->setCaptureRequestID($transactionId);
        }

        return $ccCreditService;
    }

    /**
     * @param string $transactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CCAuthReversalService
     */
    public function getAuthReversalService($transactionId = null)
    {
        $ccAuthReversalService = new CCAuthReversalService('true');
        if ($transactionId !== null) {
            $ccAuthReversalService->setAuthRequestID($transactionId);
        }

        return $ccAuthReversalService;
    }

    /**
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteService
     */
    public function getPaySubscriptionDeleteService()
    {
        return new PaySubscriptionDeleteService('true');
    }

    /**
     * @param array $fieldsArray
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MerchantDefinedData
     */
    public function getMerchantDefinedData($fieldsArray)
    {
        // NB: Using deprecated Field#s because MDDField[] doesn't appear to work through the SoapClient classes.
        $fields = [];
        $merchantDefinedData = new MerchantDefinedData();
        foreach ($fieldsArray as $key => $value) {
            call_user_func([$merchantDefinedData, 'setField' . (int)$key], $value);
        }

        return $merchantDefinedData;
    }

    /**
     * @param string|int $cvv
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function getCardForCvv($cvv)
    {
        $card = new Card();
        $card->setCvNumber($cvv);

        return $card;
    }
}
