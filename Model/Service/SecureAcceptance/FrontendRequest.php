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
 * FrontendRequest Class
 */
class FrontendRequest extends AbstractRequestHandler
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * FrontendRequest constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac
     * @param \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac,
        \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($config, $hmac, $sanitizer, $addressHelper, $cardRepository, $request);

        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->remoteAddress = $remoteAddress;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
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
        $params = parent::getGeneralParams();
        $params['customer_ip_address'] = $this->sanitizer->ipAddress($this->remoteAddress->getRemoteAddress());

        return $params;
    }

    /**
     * Get Secure Acceptance billing address input parameters
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBillingAddressParams()
    {
        $billingAddress = parent::getBillingAddressParams();

        // If no input, pull from quote
        if (empty($billingAddress)) {
            $billingAddress = $this->getAddressFromObject(
                $this->checkoutSession->getQuote()->getBillingAddress()->getDataModel()
            );
        }

        return $billingAddress;
    }

    /**
     * Get customer email for the Secure Acceptance request.
     *
     * @return string|null
     */
    protected function getEmail()
    {
        try {
            if ($this->request->getParam('source') === 'paymentinfo') {
                return $this->customerSession->getCustomerData()->getEmail();
            }

            if (!empty($this->checkoutSession->getQuote()->getBillingAddress()->getEmail())) {
                return $this->checkoutSession->getQuote()->getBillingAddress()->getEmail();
            }

            // Fall back to guest email parameter iff there's none on the quote.
            return $this->request->getParam('guest_email');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get customer ID for the Secure Acceptance request.
     *
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
     * Get currency for the Secure Acceptance request.
     *
     * @return string
     */
    protected function getCurrencyCode()
    {
        if ($this->checkoutSession->getQuoteId()) {
            return strtoupper($this->checkoutSession->getQuote()->getBaseCurrencyCode());
        }

        return strtoupper($this->storeManager->getStore()->getBaseCurrencyCode());
    }

    /**
     * Get a return URL for the Secure Acceptance request.
     *
     * @param string $route
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getSecureAcceptUrl($route)
    {
        return $this->sanitizer->url(
            $this->urlBuilder->getUrl('pdl_cybs/secureAccept/' . $route)
        );
    }

    /**
     * Get the current user's session ID, for persistence around potential SameSite cookie restrictions.
     *
     * @return string
     */
    protected function getSessionId()
    {
        return $this->checkoutSession->getSessionId();
    }
}
