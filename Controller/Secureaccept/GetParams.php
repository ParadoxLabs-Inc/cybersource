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
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        parent::__construct($context);

        $this->config          = $config;
        $this->hmac            = $hmac;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->sanitizer       = $sanitizer;
        $this->addressHelper   = $addressHelper;
        $this->cardRepository  = $cardRepository;
        $this->storeManager    = $storeManager;
        $this->remoteAddress   = $remoteAddress;
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
            $result->setHttpResponseCode(400);
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
        $card        = $this->getCard();
        $referenceId = uniqid('', true);

        $params = [
            'access_key' => $this->sanitizer->alphanumeric($this->config->getSecureAcceptAccessKey(), 32),
            'locale' => $this->sanitizer->alphanumericPunc('en-us', 5), // Only possible value per doc
            'payment_method' => $this->sanitizer->alphanumericPunc('card', 30),
            'profile_id' => $this->sanitizer->alphanumericPunc($this->config->getSecureAcceptProfileId(), 36),
            'reference_number' => $this->sanitizer->alphanumericPunc($referenceId, 50),
            'signed_date_time' => $this->sanitizer->isoDate(gmdate('Y-m-d\TH:i:s\Z')),
            'skip_decision_manager' => $this->sanitizer->alphanumericPunc('true', 5),
            'transaction_type' => $this->sanitizer->alphanumericPunc('create_payment_token', 60),
            'transaction_uuid' => $this->sanitizer->asciiAlphanumericPunc($referenceId, 50),
            'consumer_id' => $this->sanitizer->alphanumericPunc($this->getCustomerId(), 100),
            'customer_ip_address' => $this->sanitizer->ipAddress($this->remoteAddress->getRemoteAddress()),
            'merchant_defined_data100' => $this->getRequest()->getParam('source'),
            'override_custom_cancel_page' => $this->getUrl('pdl_cybs/secureaccept/cancel'),
            'override_custom_receipt_page' => $this->getUrl('pdl_cybs/secureaccept/complete'),
            'partner_solution_id' => $this->sanitizer->alphanumeric($this->config->getSolutionId(), 8),
            'signed_field_names' => '',
            // NB: No device_fingerprint_id because skip_decision_manager=true
            // NB: No payer auth here because that doesn't work on create_/update_payment_token
        ];

        if ($card !== null) {
            $params['allow_payment_token_update'] = 'true';
            $params['transaction_type'] = $this->sanitizer->alphanumericPunc('update_payment_token', 60);
            $params['payment_token'] = $card->getPaymentId();
            $params['merchant_defined_data99'] = $card->getHash();
        } else {
            $params['amount'] = $this->sanitizer->amount('0.00');
            $params['currency'] = $this->sanitizer->alpha($this->getCurrencyCode(), 3);
        }

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
        $source = $this->getRequest()->getParam('source');
        $post = $this->getRequest()->getPostValue('billing');

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

        // No input, pull from quote
        return $this->getAddressFromObject(
            $this->checkoutSession->getQuote()->getBillingAddress()->getDataModel()
        );
    }

    /**
     * @return string
     */
    public function getIframeUrl()
    {
        if (!empty($this->getRequest()->getParam('id'))) {
            return $this->config->getSecureAcceptEndpoint('/embedded/token/update');
        }

        return $this->config->getSecureAcceptEndpoint('/embedded/token/create');
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
     * @return string|null
     */
    protected function getEmail()
    {
        try {
            if ($this->getRequest()->getParam('source') === 'paymentinfo') {
                return $this->customerSession->getCustomerData()->getEmail();
            }

            return $this->checkoutSession->getQuote()->getBillingAddress()->getEmail();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface|null
     */
    protected function getCard()
    {
        try {
            return $this->cardRepository->getByHash($this->getRequest()->getParam('id'));
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return int|null
     */
    protected function getCustomerId()
    {
        if ($this->checkoutSession->getQuoteId()) {
            return $this->checkoutSession->getQuote()->getCustomerId();
        }

        return $this->customerSession->getCustomerId();
    }

    /**
     * @return string
     */
    protected function getCurrencyCode()
    {
        if ($this->checkoutSession->getQuoteId()) {
            return strtoupper($this->checkoutSession->getQuote()->getQuoteCurrencyCode());
        }

        return strtoupper($this->storeManager->getStore()->getCurrentCurrency()->getCode());
    }

    /**
     * @param string $route
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getUrl($route)
    {
        return $this->sanitizer->url(
            $this->_url->getUrl($route)
        );
    }
}
