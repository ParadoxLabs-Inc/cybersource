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
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->config = $config;
        $this->encoder = $encoder;
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
    }

    /**
     * Get the packed JSON Web Token for the current frontend checkout quote
     *
     * @return string
     */
    public function getJwt()
    {
        if ($this->config->isPayerAuthEnabled() === false) {
            return '';
        }

        $payload = [
            'jti' => uniqid('', true),
            'iat' => time(),
            'iss' => $this->config->getCardinalSecretKeyId(),
            'OrgUnitId' => $this->config->getCardinalOrgUnitId(),
            'Payload' => $this->getOrderPayload(),
            'ObjectifyPayload' => true,
        ];

        return $this->encoder->pack($payload);
    }

    /**
     * Get the order payload for the current frontend checkout quote
     *
     * @return array
     */
    protected function getOrderPayload()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        $email = $quote->getBillingAddress()->getEmail() ?: $this->request->getParam('guest_email');

        $payload = [
            'OrderDetails' => [
                'Amount' => $this->getAmount($quote->getGrandTotal(), $quote->getQuoteCurrencyCode()),
                'CurrencyCode' => $quote->getQuoteCurrencyCode(),
                'OrderChannel' => 'S', // S for ecommerce
            ],
            'Consumer' => [
                'Email1' => substr($email, 0, 255),
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
            'State' => $address->getRegionCode() ?: $address->getRegion(),
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
                'Name' => substr($item->getName(), 0, 128),
                'SKU' => substr($item->getSku(), 0, 20),
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
}
