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

namespace ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest;

/**
 * Rest Class
 */
class Rest extends \ParadoxLabs\CyberSource\Block\Adminhtml\Config\ApiTest\AbstractTest
{
    const CREDENTIAL_KEYS = [
        'rest_secret_key_id',
        'rest_secret_key',
    ];

    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Rest
     */
    protected $restClient;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \ParadoxLabs\TokenBase\Model\Method\Factory $methodFactory
     * @param \ParadoxLabs\CyberSource\Model\Service\Rest $restClient
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \ParadoxLabs\TokenBase\Model\Method\Factory $methodFactory,
        \ParadoxLabs\CyberSource\Model\Service\Rest $restClient,
        array $data = []
    ) {
        parent::__construct($context, $helper, $storeFactory, $websiteFactory, $methodFactory, $data);

        $this->restClient = $restClient;
    }

    /**
     * Validate the REST API keys.
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function testApi()
    {
        try {
            $this->checkRequiredFields();
            $this->checkFormFactor();

            $this->testConnection();
        } catch (\Exception $e) {
            return $e->getMessage() . $this->getUserManualInstruction();
        }

        return __(
            'REST API connected successfully. (%1)',
            $this->getMethod()->getConfigData('test') ? __('SANDBOX') : __('PRODUCTION')
        );
    }

    /**
     * Validate whether the REST API creds match the expected form factor.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkFormFactor()
    {
        $keyId = (string)$this->getMethod()->getConfigData('rest_secret_key_id');
        if (preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/i', $keyId) === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Secret Key ID is not in the expected format; please verify you\'ve entered the correct data.')
            );
        }

        if (strlen((string)$this->getMethod()->getConfigData('rest_secret_key')) < 32) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Secret Key is shorter than expected; please verify you\'ve entered the correct data.')
            );
        }
    }

    /**
     * Test the given REST API credentials.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    protected function testConnection()
    {
        /**
         * NB: We do this by trying to fetch details of transaction id 1. It won't exist, but it'll throw an auth
         * failure first if invalid.
         *
         * VALID: {"message":"The requested resource does not exist"} (404 not found)
         * INVALID: {"response": {"rmsg": "Authentication Failed"}} (401 unauthorized)
         */

        try {
            $this->restClient->setStoreId($this->getStoreId());
            $this->restClient->get('/tss/v2/transactions/1');
        } catch (\Zend_Http_Client_Exception $exception) {
            if ($exception->getCode() === 404) {
                return;
            }

            if ($exception->getCode() === 401) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('%1: Your API credentials are invalid.', $exception->getMessage())
                );
            }

            throw $exception;
        }
    }
}
