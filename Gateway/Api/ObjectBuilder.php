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
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Gateway\Api;

use Magento\Sales\Api\Data\OrderInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;

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
     * Create a CyberSource SOAP client
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param array $soapOptions
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TransactionProcessor
     */
    public function getProcessor(\ParadoxLabs\CyberSource\Model\Config\Config $config, $soapOptions = [])
    {
        return new TransactionProcessor(
            static::SOAP_DEFAULTS + $soapOptions,
            $config,
            $this
        );
    }

    /**
     * Get the WSSE SOAP request security header
     *
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
     * Get the WSSE SOAP request signer
     *
     * @return \ParadoxLabs\CyberSource\Gateway\Api\WsseSignature
     */
    public function getSecuritySignature()
    {
        return new WsseSignature();
    }

    /**
     * Create a SOAP request message object
     *
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
     * Create a SOAP billing address object
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BillTo
     */
    public function getOrderBillTo(OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order\Address $billingAddress */
        $billingAddress = $order->getBillingAddress();

        $billTo = new BillTo();
        $billTo->setFirstName($billingAddress->getFirstname());
        $billTo->setLastName($billingAddress->getLastname());
        $billTo->setStreet1($billingAddress->getStreetLine(1));
        $billTo->setStreet2($billingAddress->getStreetLine(2));
        $billTo->setCity($billingAddress->getCity());
        $billTo->setState($billingAddress->getRegion());
        $billTo->setCountry($billingAddress->getCountryId());
        $billTo->setPostalCode($billingAddress->getPostcode());
        $billTo->setPhoneNumber($billingAddress->getTelephone());
        $billTo->setEmail($billingAddress->getEmail());
        $billTo->setIpAddress($order->getRemoteIp());

        return $billTo;
    }

    /**
     * Create a SOAP shipping address object
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ShipTo
     */
    public function getOrderShipTo(OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order\Address $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        $shipTo = new ShipTo();
        $shipTo->setFirstName($shippingAddress->getFirstname());
        $shipTo->setLastName($shippingAddress->getLastname());
        $shipTo->setStreet1($shippingAddress->getStreetLine(1));
        $shipTo->setStreet2($shippingAddress->getStreetLine(2));
        $shipTo->setCity($shippingAddress->getCity());
        $shipTo->setState($shippingAddress->getRegion());
        $shipTo->setCountry($shippingAddress->getCountryId());
        $shipTo->setPostalCode($shippingAddress->getPostcode());
        $shipTo->setPhoneNumber($shippingAddress->getTelephone());

        return $shipTo;
    }

    /**
     * Create an array of SOAP items, for the given order items
     *
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
     * Create a SOAP item object
     *
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
     * Create a SOAP token reference
     *
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return \ParadoxLabs\CyberSource\Gateway\Api\RecurringSubscriptionInfo
     */
    public function getTokenInfo(CardInterface $card)
    {
        /** @var \ParadoxLabs\CyberSource\Model\Card $card */
        $tokenInfo = new RecurringSubscriptionInfo();
        $tokenInfo->setSubscriptionID($card->getPaymentId());

        return $tokenInfo;
    }

    /**
     * Create a SOAP transaction totals object
     *
     * @param string $currency
     * @param float $amount
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
     * Create a SOAP authorization indicator
     *
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
     * Create a SOAP capture indicator
     *
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
     * Create a SOAP credit/refund indicator
     *
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
     * Create a SOAP auth reversal/void indicator
     *
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
     * Create a SOAP auth reversal/void indicator
     *
     * @param string $transactionId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VoidService
     */
    public function getVoidService($transactionId = null)
    {
        $voidService = new VoidService('true');
        if ($transactionId !== null) {
            $voidService->setVoidRequestID($transactionId);
        }

        return $voidService;
    }

    /**
     * Create a SOAP token deletion indicator
     *
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PaySubscriptionDeleteService
     */
    public function getPaySubscriptionDeleteService()
    {
        return new PaySubscriptionDeleteService('true');
    }

    /**
     * Create a SOAP merchant-defined-data object for the given fields
     *
     * Input should be an array, with the field index as the array key of each value. N=1-20
     *
     * @param array $fieldsArray
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MerchantDefinedData
     */
    public function getMerchantDefinedData($fieldsArray)
    {
        // NB: Using deprecated Field#s because MDDField[] doesn't appear to work through the SoapClient classes.
        $merchantDefinedData = new MerchantDefinedData();
        foreach ($fieldsArray as $key => $value) {
            call_user_func([$merchantDefinedData, 'setField' . (int)$key], $value);
        }

        return $merchantDefinedData;
    }

    /**
     * Create a SOAP card object for transmitting CVN
     *
     * @param string|int $cvn
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Card
     */
    public function getCardForCvn($cvn)
    {
        $card = new Card();
        $card->setCvNumber($cvn);

        return $card;
    }

    /**
     * Create a SOAP Payer Authentication enrollment indicator
     *
     * @param string $referenceId
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthEnrollService
     */
    public function getPayerAuthEnrollService($referenceId)
    {
        $enrollService = new PayerAuthEnrollService('true');
        $enrollService->setReferenceID($referenceId);

        return $enrollService;
    }

    /**
     * Create a SOAP Payer Authentication validation indicator, including data from the CCA JWT payload.
     *
     * @param string $transactionId
     * @param string $responseJWT
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService
     */
    public function getPayerAuthValidateService($transactionId, $responseJWT)
    {
        $validateService = new PayerAuthValidateService('true');
        $validateService->setAuthenticationTransactionID($transactionId);
        $validateService->setSignedPARes($responseJWT);

        return $validateService;
    }

    /**
     * Create a SOAP authorization business rules object (validation override flags).
     *
     * @param bool $ignoreCVV
     * @param bool $ignoreAVS
     * @return \ParadoxLabs\CyberSource\Gateway\Api\BusinessRules
     */
    public function getBusinessRules($ignoreCVV = false, $ignoreAVS = false)
    {
        $businessRules = new BusinessRules();
        $businessRules->setIgnoreCVResult($ignoreCVV ? 'true' : 'false');
        $businessRules->setIgnoreAVSResult($ignoreAVS ? 'true' : 'false');

        return $businessRules;
    }

    /**
     * Create a SOAP decision manager object, to enable or disable the DM service for a request.
     *
     * @param bool $enable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DecisionManager
     */
    public function enableDecisionManager($enable = true)
    {
        $decisionManager = new DecisionManager();
        $decisionManager->setEnabled($enable ? 'true' : 'false');

        return $decisionManager;
    }
}
