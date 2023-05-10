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

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

/**
 * JsonWebTokenGenerator Class
 *
 * @see https://cardinaldocs.atlassian.net/wiki/spaces/CC/pages/32950/Request+Objects
 */
class JsonWebTokenGenerator
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder
     */
    protected $encoder;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * JsonWebTokenGenerator constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder $encoder
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder $encoder,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->config = $config;
        $this->encoder = $encoder;
        $this->request = $request;
    }

    /**
     * Get the packed JSON Web Token for the current frontend checkout quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return string
     */
    public function getJwt(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        if ($this->config->isPayerAuthEnabled() === false) {
            return '';
        }

        $payload = [
            'jti' => uniqid('', true),
            'iat' => time(),
            'iss' => $this->config->getCardinalSecretKeyId(),
            'OrgUnitId' => $this->config->getCardinalOrgUnitId(),
            'Payload' => $this->getOrderPayload($quote),
            'ReferenceId' => $this->getReferenceId(),
            'ObjectifyPayload' => true,
        ];

        return $this->encoder->pack($payload);
    }

    /**
     * Get the order payload for the current frontend checkout quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return array
     */
    protected function getOrderPayload(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        $email = $quote->getBillingAddress()->getEmail() ?: $this->request->getParam('guest_email');

        $payload = [
            'OrderDetails' => [
                'Amount' => $this->getAmount($quote->getGrandTotal(), $quote->getQuoteCurrencyCode()),
                'CurrencyCode' => $quote->getQuoteCurrencyCode(),
                'OrderChannel' => 'S', // S for ecommerce
            ],
            'Consumer' => [
                'Email1' => substr((string)$email, 0, 255),
                'BillingAddress' => $this->getPayloadAddress($quote->getBillingAddress()),
            ],
        ];

        if ($quote instanceof \Magento\Quote\Model\Quote) {
            $payload['Cart'] = $this->getPayloadItems($quote);
        }

        if ($quote->isVirtual() === false) {
            $payload['Consumer']['ShippingAddress'] = $this->getPayloadAddress($quote->getShippingAddress());
        }

        return $payload;
    }

    /**
     * Get an address payload
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return array
     */
    protected function getPayloadAddress(\Magento\Quote\Api\Data\AddressInterface $address)
    {
        $street = $address->getStreet();

        return [
            'FirstName' => $address->getFirstname(),
            'MiddleName' => $address->getMiddlename(),
            'LastName' => $address->getLastname(),
            'Address1' => isset($street[0]) ? $street[0] : null,
            'Address2' => isset($street[1]) ? $street[1] : null,
            'Address3' => isset($street[2]) ? $street[2] : null,
            'City' => $address->getCity(),
            'State' => $address->getRegion(),
            'PostalCode' => $address->getPostcode(),
            'CountryCode' => $address->getCountryId(),
            'Phone1' => $address->getTelephone(),
        ];
    }

    /**
     * Get the items payload for the given quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    protected function getPayloadItems(\Magento\Quote\Model\Quote $quote)
    {
        $items = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $items[] = [
                'Name' => substr((string)$item->getName(), 0, 128),
                'SKU' => substr((string)$item->getSku(), 0, 20),
                'Quantity' => $item->getQty(),
                'Price' => (float)$item->getPrice(),
            ];
        }

        return $items;
    }

    /**
     * Format the grand total amount according to Cardinal Cruise spec
     *
     * @param float $grandTotal
     * @param string $currencyCode
     * @return float
     */
    protected function getAmount($grandTotal, $currencyCode)
    {
        // Per Cardinal Cruise docs: "Unformatted total transaction amount without any decimalization."
        // Assuming for the moment this does not vary by currency and they assume a factor of 100 globally.
        return round($grandTotal * 100);
    }

    /**
     * Get the Cardinal Cruise reference or session ID
     *
     * @return mixed
     */
    protected function getReferenceId()
    {
        return $this->request->getParam('payerauth_session_id');
    }
}
