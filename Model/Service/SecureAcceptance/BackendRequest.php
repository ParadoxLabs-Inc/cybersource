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
 * BackendRequest Class
 */
class BackendRequest extends AbstractRequestHandler
{
    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    protected $tokenbaseHelper;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $backendSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * BackendRequest constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac
     * @param \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session\Quote $backendSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac,
        \ParadoxLabs\CyberSource\Model\Service\Sanitizer $sanitizer,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \Magento\Framework\App\RequestInterface $request,
        \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session\Quote $backendSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($config, $hmac, $sanitizer, $addressHelper, $cardRepository, $request);

        $this->tokenbaseHelper = $tokenbaseHelper;
        $this->urlBuilder = $urlBuilder;
        $this->backendSession = $backendSession;
        $this->storeManager = $storeManager;
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
                $this->backendSession->getQuote()->getBillingAddress()->getDataModel()
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
                return $this->tokenbaseHelper->getCurrentCustomer()->getEmail();
            }

            return $this->backendSession->getQuote()->getBillingAddress()->getEmail();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Get customer ID for the Secure Acceptance request.
     *
     * @return int|null
     */
    protected function getCustomerId()
    {
        return $this->tokenbaseHelper->getCurrentCustomer()->getId();
    }

    /**
     * Get currency for the Secure Acceptance request.
     *
     * @return string
     */
    protected function getCurrencyCode()
    {
        if ($this->backendSession->getQuoteId()) {
            return strtoupper($this->backendSession->getQuote()->getBaseCurrencyCode());
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
        return $this->backendSession->getSessionId();
    }
}
