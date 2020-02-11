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

namespace ParadoxLabs\CyberSource\Controller\Secureaccept;

use Magento\Framework\Controller\ResultFactory;

/**
 * GetParams Class
 */
class GetParams extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Sanitizer
     */
    protected $sanitizer;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac,
        \Magento\Checkout\Model\Session $checkoutSession,
        \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
    ) {
        parent::__construct($context);

        $this->config          = $config;
        $this->hmac            = $hmac;
        $this->checkoutSession = $checkoutSession;
        $this->sanitizer       = $sanitizer;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $payload = [
                'iframeAction' => $this->getIframeUrl(),
                'iframeParams' => $this->getIframeParams(),
            ];

            $result->setData($payload);
        } catch (\Exception $exception) {
            // TODO: Handle error case
            $result->setHttpResponseCode(500);
            $result->setData([
                'message' => $exception->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getIframeParams()
    {
        $quote       = $this->checkoutSession->getQuote();
        $referenceId = uniqid('', true);

        $params = [
            'access_key' => $this->sanitizer->alphanumeric($this->config->getSecureAcceptAccessKey(), 32),
            'amount' => $this->sanitizer->amount('0.00'),
            'currency' => $this->sanitizer->alpha(strtoupper($quote->getQuoteCurrencyCode()), 3),
            'locale' => $this->sanitizer->alphanumericPunc('en-us', 5), // Only possible value per doc
            'payment_method' => $this->sanitizer->alphanumericPunc('card', 30),
            'profile_id' => $this->sanitizer->alphanumericPunc($this->config->getSecureAcceptProfileId(), 36),
            'reference_number' => $this->sanitizer->alphanumericPunc($referenceId, 50),
            'signed_date_time' => $this->sanitizer->isoDate(gmdate('Y-m-d\TH:i:s\Z')),
            'skip_decision_manager' => $this->sanitizer->alphanumericPunc('true', 5),
            'transaction_type' => $this->sanitizer->alphanumericPunc('create_payment_token', 60),
            'transaction_uuid' => $this->sanitizer->asciiAlphanumericPunc($referenceId, 50),
            'consumer_id' => $this->sanitizer->alphanumericPunc($quote->getCustomerId(), 100),
            'customer_ip_address' => $this->sanitizer->ipAddress($quote->getRemoteIp()),
            'override_custom_cancel_page' => $this->sanitizer->url(
                $this->_url->getUrl('pdl_cybs/secureaccept/cancel')
            ),
            'override_custom_receipt_page' => $this->sanitizer->url(
                $this->_url->getUrl('pdl_cybs/secureaccept/complete')
            ),
            'payer_authentication_transaction_mode' => $this->sanitizer->alpha(
                'S', // S for ecommerce. TODO: Pass M in admin
                1
            ),
            'partner_solution_id' => $this->sanitizer->alphanumeric($this->config->getSolutionId(), 8),
            'signed_field_names' => '',
            // TODO: Add in device_fingerprint_id
        ];
        $params += $this->getBillingAddressParams();

        $params['signed_field_names'] = implode(',', array_keys($params));
        $params['signature']          = $this->hmac->getSignature($params);

        return $params;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBillingAddressParams()
    {
        $address = $this->checkoutSession->getQuote()->getBillingAddress();
        $post = $this->getRequest()->getPostValue('billingAddress');

        if (!empty($post)) {
            return [
                'bill_to_forename' => $this->sanitizer->alphanumericPunc($post['firstname'], 60),
                'bill_to_surname' => $this->sanitizer->alphanumericPunc($post['lastname'], 60),
                'bill_to_email' => $this->sanitizer->email($address->getEmail()),
                'bill_to_company_name' => $this->sanitizer->alphanumericPunc($post['company'], 40),
                'bill_to_address_country' => $this->sanitizer->alpha(strtoupper($post['countryId']), 2),
                'bill_to_address_city' => $this->sanitizer->alphanumericPunc($post['city'], 50),
                'bill_to_address_state' => $this->sanitizer->alphanumericPunc(
                    strtoupper(isset($post['regionCode']) ? $post['regionCode'] : $post['region']),
                    2
                ),
                'bill_to_address_line1' => $this->sanitizer->alphanumericPunc(
                    isset($post['street'][0]) ? $post['street'][0] : '',
                    60
                ),
                'bill_to_address_line2' => $this->sanitizer->alphanumericPunc(
                    isset($post['street'][1]) ? $post['street'][1] : '',
                    60
                ),
                'bill_to_address_postal_code' => $this->sanitizer->postcode($post['postcode'], $post['countryId']),
                'bill_to_phone' => $this->sanitizer->phone($post['telephone'], 15),
            ];
        }

        return [
            'bill_to_forename' => $this->sanitizer->alphanumericPunc($address->getFirstname(), 60),
            'bill_to_surname' => $this->sanitizer->alphanumericPunc($address->getLastname(), 60),
            'bill_to_email' => $this->sanitizer->email($address->getEmail()),
            'bill_to_company_name' => $this->sanitizer->alphanumericPunc($address->getCompany(), 40),
            'bill_to_address_country' => $this->sanitizer->alpha(strtoupper($address->getCountryId()), 2),
            'bill_to_address_city' => $this->sanitizer->alphanumericPunc($address->getCity(), 40),
            'bill_to_address_state' => $this->sanitizer->alphanumericPunc(
                strtoupper($address->getRegionCode() ?: $address->getRegion()),
                2
            ),
            'bill_to_address_line1' => $this->sanitizer->alphanumericPunc($address->getStreetLine(1), 60),
            'bill_to_address_line2' => $this->sanitizer->alphanumericPunc($address->getStreetLine(2), 60),
            'bill_to_address_postal_code' => $this->sanitizer->postcode(
                $address->getPostcode(),
                $address->getCountryId()
            ),
            'bill_to_phone' => $this->sanitizer->phone($address->getTelephone(), 15),
        ];
    }

    /**
     * @return string
     */
    public function getIframeUrl()
    {
        // TODO: Support create vs. update?
        return $this->config->getSecureAcceptEndpoint('/embedded/token/create');
    }
}
