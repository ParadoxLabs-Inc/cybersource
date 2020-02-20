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
     * @var \ParadoxLabs\CyberSource\Model\Service\Hmac
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
     * @param \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac
     * @param \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac,
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
     * @return string
     */
    public function getIframeUrl()
    {
        if (!empty($this->request->getParam('id'))) {
            return $this->config->getSecureAcceptEndpoint('/embedded/token/update');
        }

        return $this->config->getSecureAcceptEndpoint('/embedded/token/create');
    }

    /**
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
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function getGeneralParams()
    {
        $referenceId = uniqid('', true);

        return [
            'access_key' => $this->sanitizer->alphanumeric($this->config->getSecureAcceptAccessKey(), 32),
            'locale' => $this->sanitizer->alphanumericPunc('en-us', 5), // Only possible value per doc
            'payment_method' => $this->sanitizer->alphanumericPunc('card', 30),
            'profile_id' => $this->sanitizer->alphanumericPunc($this->config->getSecureAcceptProfileId(), 36),
            'reference_number' => $this->sanitizer->alphanumericPunc($referenceId, 50),
            'signed_date_time' => $this->sanitizer->isoDate(gmdate('Y-m-d\TH:i:s\Z')),
            'skip_decision_manager' => $this->sanitizer->alphanumericPunc('true', 5),
            'transaction_uuid' => $this->sanitizer->asciiAlphanumericPunc($referenceId, 50),
            'consumer_id' => $this->sanitizer->alphanumericPunc($this->getCustomerId(), 100),
            'merchant_defined_data100' => $this->request->getParam('source'),
            'override_custom_cancel_page' => $this->getSecureAcceptUrl('cancel'),
            'override_custom_receipt_page' => $this->getSecureAcceptUrl('complete'),
            'partner_solution_id' => $this->sanitizer->alphanumeric($this->config->getSolutionId(), 8),
            'signed_field_names' => '',
            // NB: No device_fingerprint_id because skip_decision_manager=true
            // NB: No payer auth here because that doesn't work on create_/update_payment_token
        ];
    }

    /**
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
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBillingAddressParams()
    {
        $source = $this->request->getParam('source');
        $post = $this->request->getPostValue('billing');

        // My Payment Data array input
        if ($source === 'paymentinfo') {
            return $this->getAddressFromObject(
                $this->addressHelper->buildAddressFromInput($post)
            );
        }

        // Checkout array input
        if (!empty($post)) {
            return $this->getAddressFromCheckoutPost($post);
        }

        return [];
    }

    /**
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
            'bill_to_address_country' => $this->sanitizer->alpha(strtoupper($address->getCountryId()), 2),
            'bill_to_address_city' => $this->sanitizer->alphanumericPunc($address->getCity(), 40),
            'bill_to_address_state' => $this->sanitizer->alphanumericPunc(
                strtoupper($address->getRegion()->getRegionCode()),
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
     * @param array $post
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getAddressFromCheckoutPost($post)
    {
        return [
            'bill_to_forename' => $this->sanitizer->alphanumericPunc($post['firstname'], 60),
            'bill_to_surname' => $this->sanitizer->alphanumericPunc($post['lastname'], 60),
            'bill_to_email' => $this->sanitizer->email($this->getEmail()),
            'bill_to_company_name' => $this->sanitizer->alphanumericPunc($post['company'], 40),
            'bill_to_address_country' => $this->sanitizer->alpha(strtoupper($post['countryId']), 2),
            'bill_to_address_city' => $this->sanitizer->alphanumericPunc($post['city'], 50),
            'bill_to_address_state' => $this->sanitizer->alphanumericPunc(
                strtoupper(isset($post['regionCode']) ? $post['regionCode'] : $post['region']),
                2
            ),
            'bill_to_address_line1' => $this->sanitizer->alphanumericPunc(
                isset($post['street'][0]) ? $post['street'][0] : null,
                60
            ),
            'bill_to_address_line2' => $this->sanitizer->alphanumericPunc(
                isset($post['street'][1]) ? $post['street'][1] : null,
                60
            ),
            'bill_to_address_postal_code' => $this->sanitizer->postcode($post['postcode'], $post['countryId']),
            'bill_to_phone' => $this->sanitizer->phone($post['telephone'], 15),
        ];
    }

    /**
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface|null
     */
    protected function getCard()
    {
        try {
            return $this->cardRepository->getByHash($this->request->getParam('id'));
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return string|null
     */
    abstract protected function getEmail();

    /**
     * @return int|null
     */
    abstract protected function getCustomerId();

    /**
     * @return string
     */
    abstract protected function getCurrencyCode();

    /**
     * @param string $route
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    abstract protected function getSecureAcceptUrl($route);
}
