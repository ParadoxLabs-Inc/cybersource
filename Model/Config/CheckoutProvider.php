<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model\Config;

use Magento\Payment\Model\CcGenericConfigProvider;
use Magento\Payment\Model\CcConfig;

/**
 * ConfigProvider Class
 */
class CheckoutProvider extends CcGenericConfigProvider
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
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator
     */
    protected $jsonWebTokenGenerator;

    /**
     * @param CcConfig $ccConfig
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     * @param \Magento\Customer\Model\Session $customerSession *Proxy
     * @param \ParadoxLabs\CyberSource\Helper\Data $dataHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator $jsonWebTokenGenerator
     * @param array $methodCodes
     */
    public function __construct(
        CcConfig $ccConfig,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \ParadoxLabs\CyberSource\Helper\Data $dataHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        Config $config,
        \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator $jsonWebTokenGenerator,
        array $methodCodes = []
    ) {
        $this->paymentHelper    = $paymentHelper;
        $this->checkoutSession  = $checkoutSession;
        $this->customerSession  = $customerSession;
        $this->dataHelper       = $dataHelper;
        $this->urlBuilder       = $urlBuilder;
        $this->config           = $config;
        $this->jsonWebTokenGenerator = $jsonWebTokenGenerator;

        parent::__construct($ccConfig, $paymentHelper, [Config::CODE]);
    }

    /**
     * Returns applicable stored cards
     *
     * @return array
     */
    public function getStoredCards()
    {
        return $this->dataHelper->getActiveCustomerCardsByMethod(Config::CODE);
    }

    /**
     * If card can be saved for further use (is customer logged in)
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
        if (!$this->methods[Config::CODE]->isAvailable()) {
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
                    'id'        => $card->getHash(),
                    'label'     => $card->getLabel(),
                    'selected'  => false,
                    'new'       => false,
                    'type'      => $card->getType(),
                    'cc_bin'    => $card->getAdditional('cc_bin'),
                    'cc_last_4' => $card->getAdditional('cc_last_4'),
                ];

                $selected               = $card->getHash();
            }
        }

        $config = array_merge_recursive($config, [
            'payment' => [
                Config::CODE => [
                    'useVault'        => true,
                    'canSaveCard'     => $this->canSaveCard(),
                    'forceSaveCard'   => $this->forceSaveCard(),
                    'defaultSaveCard' => $this->defaultSaveCard(),
                    'storedCards'     => $storedCardOptions,
                    'selectedCard'    => $selected,
                    'logoImage'       => $this->getLogoImage(),
                    'requireCcv'      => $this->requireCcv(),
                    'paramUrl'        => $this->urlBuilder->getUrl('pdl_cybs/secureAccept/getParams'),
                    'fingerprintUrl'  => $this->config->getFingerprintUrl($this->checkoutSession->getQuoteId()),
                    'cardinalScript'  => $this->config->getCardinalSongbirdUrl(),
                    'cardinalAuthUrl' => $this->urlBuilder->getUrl('pdl_cybs/cardinalCruise/getAuthPayload'),
                    'cardinalJWT'     => $this->jsonWebTokenGenerator->getJwt(),
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
        if ($this->methods[Config::CODE]->getConfigData('show_branding')) {
            return $this->ccConfig->getViewFileUrl('ParadoxLabs_CyberSource::images/logo.webp');
        }

        return false;
    }

    /**
     * Whether to force customers to enter CCV when using a stored card.
     *
     * @return bool
     */
    public function requireCcv()
    {
        return $this->methods[Config::CODE]->getConfigData('require_ccv') ? true : false;
    }

    /**
     * Whether to give customers the 'save this card' option, or just assume yes.
     *
     * @return bool
     */
    public function forceSaveCard()
    {
        return $this->methods[Config::CODE]->getConfigData('allow_unsaved') ? false : true;
    }

    /**
     * Whether to default the save card option to yes or no.
     *
     * @return bool
     */
    public function defaultSaveCard()
    {
        return $this->methods[Config::CODE]->getConfigData('savecard_opt_out') ? true : false;
    }
}
