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

namespace ParadoxLabs\CyberSource\Model\Service\SecureAcceptance;

/**
 * AbstractRequestHandler Class
 */
abstract class AbstractRequestHandler
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac
     */
    protected $hmac;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Sanitizer
     */
    protected $sanitizer;

    /**
     * @var \ParadoxLabs\TokenBase\Helper\Address
     */
    protected $addressHelper;

    /**
     * @var \ParadoxLabs\TokenBase\Api\CardRepositoryInterface
     */
    protected $cardRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * AbstractRequestHandler constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac
     * @param \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac,
        \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->config = $config;
        $this->hmac = $hmac;
        $this->sanitizer = $sanitizer;
        $this->addressHelper = $addressHelper;
        $this->cardRepository = $cardRepository;
        $this->request = $request;
    }

    /**
     * Get the Secure Acceptance iframe URL
     *
     * @return string
     */
    public function getIframeUrl()
    {
        if (!empty($this->request->getParam('card_id'))) {
            return $this->config->getSecureAcceptEndpoint('/embedded/token/update');
        }

        return $this->config->getSecureAcceptEndpoint('/embedded/token/create');
    }

    /**
     * Get the Secure Acceptance initialization client request params. Billing address, etc.
     *
     * @return array
     */
    public function getIframeParams()
    {
        $params = $this->getGeneralParams();

        $card   = $this->getCard();
        if ($card !== null) {
            $params += $this->getTokenUpdateParams($card);
        } else {
            $params += $this->getTokenCreateParams();
        }

        $params += $this->getBillingAddressParams();

        $params['signed_field_names'] = implode(',', array_keys($params));
        $params['signature']          = $this->hmac->getSignature($params);

        return $params;
    }

    /**
     * Get general input parameters for Secure Acceptance checkout.
     *
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function getGeneralParams()
    {
        // NB: Reference ID must be unique, but we have no reason to tie it back to anything identifiable here.
        $referenceId = uniqid('', true);

        return [
            'access_key' => $this->sanitizer->alphanumeric($this->config->getSecureAcceptAccessKey(), 32),
            'locale' => $this->sanitizer->alphanumericPunc('en-us', 5), // Only possible value per doc
            'payment_method' => $this->sanitizer->alphanumericPunc('card', 30),
            'profile_id' => $this->sanitizer->alphanumericPunc($this->config->getSecureAcceptProfileId(), 36),
            'reference_number' => $this->sanitizer->alphanumericPunc($referenceId, 50),
            'signed_date_time' => $this->sanitizer->isoDate(gmdate('Y-m-d\TH:i:s\Z')),
            'skip_decision_manager' => $this->config->isCardStorageValidationEnabled() ? 'false' : 'true',
            'transaction_uuid' => $this->sanitizer->asciiAlphanumericPunc($referenceId, 50),
            'consumer_id' => $this->sanitizer->alphanumericPunc($this->getCustomerId(), 100),
            'merchant_defined_data99' => $this->getSessionId(),
            'merchant_defined_data100' => $this->request->getParam('source'),
            'override_custom_receipt_page' => $this->getSecureAcceptUrl('complete'),
            'partner_solution_id' => $this->sanitizer->alphanumeric($this->config->getSolutionId(), 8),
            'signed_field_names' => '',
            // NB: No device_fingerprint_id because skip_decision_manager=true
            // NB: No payer auth here because that doesn't work on create_/update_payment_token, per CyberSource
        ];
    }

    /**
     * Get Secure Acceptance parameters for updating an existing token
     *
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return array
     */
    protected function getTokenUpdateParams(\ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
    {
        return [
            'transaction_type' => $this->sanitizer->alphanumericPunc('update_payment_token', 60),
            'allow_payment_token_update' => 'true',
            'payment_token' => $card->getPaymentId(),
            'merchant_defined_data99' => $card->getHash(),
        ];
    }

    /**
     * Get Secure Acceptance parameters for creating a new token
     *
     * @return array
     */
    protected function getTokenCreateParams()
    {
        return [
            'transaction_type' => $this->sanitizer->alphanumericPunc('create_payment_token', 60),
            'amount' => $this->sanitizer->amount('0.00'),
            'currency' => $this->sanitizer->alpha($this->getCurrencyCode(), 3),
        ];
    }

    /**
     * Get Secure Acceptance billing address input parameters
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBillingAddressParams()
    {
        $post = $this->request->getPostValue('billing');
        $post['country_id']  = $post['country_id']  ?? $post['countryId']  ?? null;
        $post['region_id']   = $post['region_id']   ?? $post['regionId']   ?? null;
        $post['region_code'] = $post['region_code'] ?? $post['regionCode'] ?? null;

        return $this->getAddressFromObject(
            $this->addressHelper->buildAddressFromInput($post)
        );
    }

    /**
     * Get Secure Acceptance billing address params from the given address object
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getAddressFromObject(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        $street = $address->getStreet();

        return [
            'bill_to_forename' => $this->sanitizer->alphanumericPunc($address->getFirstname(), 60),
            'bill_to_surname' => $this->sanitizer->alphanumericPunc($address->getLastname(), 60),
            'bill_to_email' => $this->sanitizer->email($this->getEmail()),
            'bill_to_company_name' => $this->sanitizer->alphanumericPunc($address->getCompany(), 40),
            'bill_to_address_country' => $this->sanitizer->alpha(strtoupper((string)$address->getCountryId()), 2),
            'bill_to_address_city' => $this->sanitizer->alphanumericPunc($address->getCity(), 40),
            'bill_to_address_state' => $this->sanitizer->alphanumericPunc(
                strtoupper((string)$address->getRegion()->getRegionCode()),
                2
            ),
            'bill_to_address_line1' => $this->sanitizer->alphanumericPunc(
                isset($street[0]) ? $street[0] : null,
                60
            ),
            'bill_to_address_line2' => $this->sanitizer->alphanumericPunc(
                isset($street[1]) ? $street[1] : null,
                60
            ),
            'bill_to_address_postal_code' => $this->sanitizer->postcode(
                $address->getPostcode(),
                $address->getCountryId()
            ),
            'bill_to_phone' => $this->sanitizer->phone($address->getTelephone(), 15),
        ];
    }

    /**
     * Get the stored card from the request's card_id card hash, or null if none.
     *
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface|null
     */
    protected function getCard()
    {
        if (empty($this->request->getParam('card_id'))) {
            return null;
        }

        try {
            return $this->cardRepository->getByHash($this->request->getParam('card_id'));
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Get customer email for the Secure Acceptance request.
     *
     * @return string|null
     */
    abstract protected function getEmail();

    /**
     * Get customer ID for the Secure Acceptance request.
     *
     * @return int|null
     */
    abstract protected function getCustomerId();

    /**
     * Get currency for the Secure Acceptance token create request.
     *
     * @return string
     */
    abstract protected function getCurrencyCode();

    /**
     * Get a return URL for the Secure Acceptance request.
     *
     * @param string $route
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    abstract protected function getSecureAcceptUrl($route);

    /**
     * Get the current user's session ID, for persistence around potential SameSite cookie restrictions.
     *
     * @return string
     */
    abstract protected function getSessionId();
}
