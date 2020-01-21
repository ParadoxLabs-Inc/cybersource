<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Chad Bender <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model;

use Magento\Payment\Model\CcGenericConfigProvider;
use Magento\Payment\Model\CcConfig;

/**
 * ConfigProvider Class
 */
class ConfigProvider extends CcGenericConfigProvider
{
    const CODE = 'paradoxlabs_cybersource';

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    // protected $resource;

    /**
     * @param CcConfig $ccConfig
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     * @param \Magento\Customer\Model\Session $customerSession *Proxy
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \ParadoxLabs\CyberSource\Helper\Data $dataHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $methodCodes
     */
    public function __construct(
        CcConfig $ccConfig,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Payment\Model\Config $paymentConfig,
        \ParadoxLabs\CyberSource\Helper\Data $dataHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        // \Magento\Framework\App\ResourceConnection $resource,
        array $methodCodes = []
    ) {
        $this->paymentHelper    = $paymentHelper;
        $this->checkoutSession  = $checkoutSession;
        $this->customerSession  = $customerSession;
        $this->dataHelper       = $dataHelper;
        $this->paymentConfig    = $paymentConfig;
        $this->urlBuilder       = $urlBuilder;
        // $this->resource         = $resource;

        parent::__construct($ccConfig, $paymentHelper, [static::CODE]);
    }

    /**
     * Returns applicable stored cards
     *
     * @return array
     */
    public function getStoredCards()
    {
        return $this->dataHelper->getActiveCustomerCardsByMethod(static::CODE);
    }

    /**
     * If card can be saved for further use
     *
     * @return boolean
     */
    public function canSaveCard()
    {
        if ($this->customerSession->isLoggedIn()) {
            return true;
        }

        return false;
    }

    /**
     * Get checkout config.
     *
     * @return array
     */
    public function getConfig()
    {
        if (!$this->methods[static::CODE]->isAvailable()) {
            return [];
        }

        $config             = parent::getConfig();
        $selected           = null;
        $storedCardOptions  = [];
        
        if ($this->canSaveCard()) {
            $cards              = $this->getStoredCards();

            /** @var \ParadoxLabs\TokenBase\Model\Card $card */
            foreach ($cards as $card) {
                $storedCardOptions[]    = [
                    'id'       => $card->getHash(),
                    'label'    => $card->getLabel(),
                    'selected' => false,
                    'type'     => $card->getAdditional('cc_type'),
                ];

                $selected               = $card->getHash();
            }
        }

        $config = array_merge_recursive($config, [
            'payment' => [
                static::CODE => [
                    'useVault'                => true,
                    'storedCards'             => $storedCardOptions,
                    'selectedCard'            => $selected,
                    'logoImage'               => $this->getLogoImage(),
                    'defaultSaveCard'         => $this->defaultSaveCard(),
                    // TODO: Abstract this stuff out somewhere/how
                    'iframeAction'            => 'https://testsecureacceptance.cybersource.com/embedded/token/create',
                    'iframeParams'            => $this->getIframeParams(),
                ],
            ],
        ]);

        return $config;
    }

    /**
     * Get payment method logo URL (if enabled)
     *
     * @return string|false
     */
    public function getLogoImage()
    {
        if ($this->methods[static::CODE]->getConfigData('show_branding')) {
            return $this->ccConfig->getViewFileUrl('ParadoxLabs_CyberSource::images/logo.webp');
        }

        return false;
    }

    /**
     * Whether to default the save card option to yes or no.
     *
     * @return bool
     */
    public function defaultSaveCard()
    {
        return $this->methods[static::CODE]->getConfigData('savecard_opt_out') ? true : false;
    }

    // TODO: Go through and add phpdoc comment descriptions
    /**
     * @param string $key
     * @return mixed
     */
    protected function getConfigValue($key)
    {
        return $this->methods[static::CODE]->getConfigData($key);
    }

    /**
     * @return array
     */
    protected function getIframeParams()
    {
        $quote = $this->checkoutSession->getQuote();

        $referenceId = uniqid('', true);

        $signedParams = [
            'access_key' => $this->getConfigValue('access_key'),
            'amount' => '0.00',
            'currency' => strtoupper($quote->getQuoteCurrencyCode()), // TODO: Is this correct/acceptable?
            'locale' => 'en-us', // CYBS docs indicate this is the only possible value
            'payment_method' => 'card',
            'profile_id' => $this->getConfigValue('checkout_profile_id'),
            'reference_number' => $referenceId,
            'signed_date_time' => gmdate('Y-m-d\TH:i:s\Z'),
            'skip_decision_manager' => 'true',
            'transaction_type' => 'create_payment_token',
            'transaction_uuid' => $referenceId,
            'signed_field_names' => '',
            'override_backoffice_post_url' => $this->urlBuilder->getUrl('paradoxlabs_cybersource/ebc/post'),
            'override_custom_cancel_page' => $this->urlBuilder->getUrl('paradoxlabs_cybersource/ebc/cancel'),
            'override_custom_receipt_page' => $this->urlBuilder->getUrl('paradoxlabs_cybersource/ebc/completed'),
            'bill_to_forename' => 'Ryan',
            'bill_to_surname' => 'Hoerr',
            'bill_to_email' => 'ryan@paradoxlabs.com',
            'bill_to_address_country' => 'US',
            'bill_to_address_city' => 'Lancaster',
            'bill_to_address_state' => 'PA',
            'bill_to_address_line1' => '8 N Queen St',
            'bill_to_address_line2' => '9th Floor',
            'bill_to_address_postal_code' => '17603',
        ];

        $signedParams['signed_field_names'] = implode(',', array_keys($signedParams));

        $unsignedParams = [
            'signature' => $this->getSignature($signedParams),
            // TODO: Could add bill_to_* fields on checkout if we track addr separately
        ];

        return $signedParams + $unsignedParams;
    }

    /**
     * @param array $signedParams
     * @return string
     */
    protected function getSignature(array $signedParams)
    {
        $params       = [];
        $signedFields = explode(',', $signedParams['signed_field_names']);
        foreach ($signedFields as $key) {
            $params[] = $key . '=' . $signedParams[$key];
        }

        return base64_encode(
            hash_hmac(
                'sha256',
                implode(',', $params),
                $this->getConfigValue('secret_key'),
                true
            )
        );
    }

    /**
     * TODO: This does nooot belong here. Does it?
     * @param \Magento\Quote\Model\Quote $quote
     * @return string
     */
    // protected function getReservedOrderId(\Magento\Quote\Model\Quote $quote)
    // {
    //     $quote->reserveOrderId();
    //
    //     /**
    //      * Persist increment_id -- if it's changed, it's new
    //      */
    //     if ($quote->getId()
    //         && empty($quote->getOrigData('reserved_order_id'))
    //         && !empty($quote->getReservedOrderId())) {
    //         // Save quote.reserved_order_id directly to the DB with no other interaction -- only efficient option.
    //         $connection = $this->resource->getConnection('checkout');
    //         $connection->update(
    //             $connection->getTableName('quote'),
    //             [
    //                 'reserved_order_id' => $quote->getReservedOrderId(),
    //             ],
    //             [
    //                 'entity_id=?' => $quote->getId(),
    //             ]
    //         );
    //     }
    //
    //     return $quote->getReservedOrderId();
    // }
}
