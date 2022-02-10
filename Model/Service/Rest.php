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

namespace ParadoxLabs\CyberSource\Model\Service;

/**
 * Rest Class
 */
class Rest
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $helper;

    /**
     * @var int|null
     */
    protected $storeId;

    /**
     * Rest constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \ParadoxLabs\CyberSource\Helper\Data $helper
    ) {
        $this->config = $config;
        $this->httpClientFactory = $httpClientFactory;
        $this->helper = $helper;
    }

    /**
     * Send a REST API GET request to the given resource path. Will sign the request per API specifications.
     *
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function get($path, $params = [], $responseType = 'application/hal+json')
    {
        $client = $this->getHttpClient($path);
        $client->setParameterGet($params);
        $client->setHeaders('Accept', $responseType);
        $client->setHeaders('Content-Type', 'application/json;charset=utf-8');

        $this->signRequest($client, $path, $params, 'GET');

        $response = $client->request(\Zend_Http_Client::GET);

        // Throw exception on non-2xx response code
        if (substr((string)$response->getStatus(), 0, 1) !== '2') {
            $responseJson = json_decode((string)$response->getBody(), true);

            $message = $responseJson['message']
                ?? $responseJson['response']['rmsg']
                ?? $response->getStatus() . ' ' . $response->getMessage();

            throw new \Zend_Http_Client_Exception(
                $message,
                $response->getStatus()
            );
        }

        return $response->getBody();
    }

    /**
     * Get an HTTP client for REST
     *
     * @param string $path
     * @return \Magento\Framework\HTTP\ZendClient
     */
    protected function getHttpClient($path)
    {
        $clientConfig = [
            'adapter'     => \Zend_Http_Client_Adapter_Curl::class,
            'timeout'     => 15,
            'verifypeer' => true,
            'verifyhost' => 2,
            'curloptions' => [
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ],
        ];

        /** @var \Magento\Framework\HTTP\ZendClient $httpClient */
        $httpClient = $this->httpClientFactory->create();
        $httpClient->setUri($this->config->getRestEndpoint($path, $this->storeId));
        $httpClient->setConfig($clientConfig);

        return $httpClient;
    }

    /**
     * Sign the given REST request and parameters.
     *
     * @param \Magento\Framework\HTTP\ZendClient $client
     * @param string $path
     * @param array $params
     * @param string $httpMethod
     * @return void
     */
    protected function signRequest(\Magento\Framework\HTTP\ZendClient $client, $path, $params, $httpMethod)
    {
        $host = parse_url((string)$client->getUri(true), PHP_URL_HOST);
        $date = date("D, d M Y G:i:s \G\M\T");

        $client->setHeaders('Date', $date);
        $client->setHeaders('Host', $host);
        $client->setHeaders('v-c-merchant-id', $this->config->getMerchantId($this->storeId));

        /**
         * Note: POST signing requires additional Digest of payload. Not implemented yet.
         * @see https://developer.cybersource.com/api/developer-guides/dita-gettingstarted/authentication/GenerateHeader
         * /httpSignatureAuthentication.html
         */

        $signatureParts = [
            'host' => 'host: ' . $host,
            'date' => 'date: ' . $date,
            '(request-target)' => '(request-target): ' . strtolower((string)$httpMethod) . ' ' . $path
                . (!empty($params) ? '?' . http_build_query($params) : ''),
            'v-c-merchant-id' => 'v-c-merchant-id: ' . $this->config->getMerchantId($this->storeId),
        ];

        $signature = base64_encode(
            hash_hmac(
                'sha256',
                utf8_encode(implode("\n", $signatureParts)),
                base64_decode((string)$this->config->getRestSecretKey($this->storeId)),
                true
            )
        );
        $signatureHeader = [
            'keyid="' . $this->config->getRestSecretKeyId($this->storeId) . '"',
            'algorithm="HmacSHA256"',
            'headers="' . implode(' ', array_keys($signatureParts)) . '"',
            'signature="' . $signature . '"',
        ];
        $client->setHeaders('Signature', implode(', ', $signatureHeader));
    }

    /**
     * Set store ID for the current request. This determines scope configuration is loaded from. null for assumed scope.
     *
     * @param int|null $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }
}
