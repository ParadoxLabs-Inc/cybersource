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

namespace ParadoxLabs\CyberSource\Model\Service\CardinalCruise;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use ParadoxLabs\CyberSource\Model\Config\Config;

class JsonWebTokenGenerator
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * JsonWebTokenGenerator constructor.
     *
     * @param Config $config
     * @param JsonWebTokenEncoder $encoder
     * @param Session $checkoutSession *Proxy
     * @param RequestInterface $request
     */
    public function __construct(
        protected readonly Config $config,
        protected readonly JsonWebTokenEncoder $encoder,
        protected readonly RequestInterface $request
    ) {
    }

    /**
     * Get the packed JSON Web Token for the current frontend checkout quote
     *
     * @param CartInterface $quote
     * @return string
     */
    public function getJwt(CartInterface $quote)
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
     * @param CartInterface $quote
     * @return array
     */
    protected function getOrderPayload(CartInterface $quote)
    {
        $email = $quote->getBillingAddress()->getEmail() ?: $this->request->getParam('guest_email');

        $payload = [
            'OrderDetails' => [
                'Amount' => $this->getAmount($quote->getGrandTotal(), $quote->getQuoteCurrencyCode()),
                'CurrencyCode' => $quote->getQuoteCurrencyCode(),
                'OrderChannel' => 'S', // S for ecommerce
            ],
            'Consumer' => [
                'Email1' => mb_substr((string)$email, 0, 255),
                'BillingAddress' => $this->getPayloadAddress($quote->getBillingAddress()),
            ],
        ];

        if ($quote instanceof Quote) {
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
     * @param AddressInterface $address
     * @return array
     */
    protected function getPayloadAddress(AddressInterface $address)
    {
        $street = $address->getStreet();

        return [
            'FirstName' => $address->getFirstname(),
            'MiddleName' => $address->getMiddlename(),
            'LastName' => $address->getLastname(),
            'Address1' => $street[0] ?? null,
            'Address2' => $street[1] ?? null,
            'Address3' => $street[2] ?? null,
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
     * @param Quote $quote
     * @return array
     */
    protected function getPayloadItems(Quote $quote)
    {
        $items = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $items[] = [
                'Name' => mb_substr((string)$item->getName(), 0, 128),
                'SKU' => mb_substr((string)$item->getSku(), 0, 20),
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
