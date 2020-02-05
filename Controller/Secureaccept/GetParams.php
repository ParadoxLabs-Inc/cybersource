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

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Hmac;

/**
 * GetParams Class
 */
class GetParams extends Action
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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Config $config,
        Hmac $hmac,
        Session $checkoutSession
    ) {
        parent::__construct($context);

        $this->config          = $config;
        $this->hmac            = $hmac;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $payload = [
            'iframeAction' => $this->getIframeUrl(),
            'iframeParams' => $this->getIframeParams(),
        ];

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData($payload);

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
            'access_key' => $this->config->getAccessKey(),
            'amount' => '0.00',
            'currency' => strtoupper($quote->getQuoteCurrencyCode()), // TODO: Is this correct/acceptable?
            'locale' => 'en-us', // CYBS docs indicate this is the only possible value
            'payment_method' => 'card',
            'profile_id' => $this->config->getCheckoutProfileId(),
            'reference_number' => $referenceId,
            'signed_date_time' => gmdate('Y-m-d\TH:i:s\Z'),
            'skip_decision_manager' => 'true',
            'transaction_type' => 'create_payment_token',
            'transaction_uuid' => $referenceId,
            'consumer_id' => $quote->getCustomerId(),
            'customer_ip_address' => $quote->getRemoteIp(),
            // 'override_backoffice_post_url' => $this->urlBuilder->getUrl('pdl_cybs/secureaccept/post'),
            'override_custom_cancel_page' => $this->_url->getUrl('pdl_cybs/secureaccept/cancel'),
            'override_custom_receipt_page' => $this->_url->getUrl('pdl_cybs/secureaccept/complete'),
            'payer_authentication_transaction_mode' => 'S', // S for ecommerce. TODO: Pass M in admin
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
                'bill_to_forename' => $post['firstname'],
                'bill_to_surname' => $post['lastname'],
                'bill_to_email' => $address->getEmail(),
                'bill_to_company_name' => $post['company'],
                'bill_to_address_country' => $post['countryId'],
                'bill_to_address_city' => $post['city'],
                'bill_to_address_state' => isset($post['regionCode']) ? $post['regionCode'] : $post['region'],
                'bill_to_address_line1' => isset($post['street'][0]) ? $post['street'][0] : '',
                'bill_to_address_line2' => isset($post['street'][1]) ? $post['street'][1] : '',
                'bill_to_address_postal_code' => $post['postcode'],
                'bill_to_phone' => $post['telephone'],
            ];
        }

        return [
            'bill_to_forename' => $address->getFirstname(),
            'bill_to_surname' => $address->getLastname(),
            'bill_to_email' => $address->getEmail(),
            'bill_to_company_name' => $address->getCompany(),
            'bill_to_address_country' => $address->getCountryId(),
            'bill_to_address_city' => $address->getCity(),
            'bill_to_address_state' => $address->getRegionCode(),
            'bill_to_address_line1' => $address->getStreetLine(1),
            'bill_to_address_line2' => $address->getStreetLine(2),
            'bill_to_address_postal_code' => $address->getPostcode(),
            'bill_to_phone' => $address->getTelephone(),
        ];
    }

    /**
     * @return string
     */
    public function getIframeUrl()
    {
        // TODO: Support create vs. update?
        return $this->config->getSecureAcceptanceEndpoint('/embedded/token/create');
    }
}
