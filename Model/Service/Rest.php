<?php
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 * @link https://support.paradoxlabs.com
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
     * @deprecated Class is nonfunctional in 2.4.6+.
     * @see \Magento\Framework\HTTP\ClientInterface via $this->communicatorFactory
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
     * @var \Magento\Framework\HTTP\ClientInterfaceFactory
     */
    protected $communicatorFactory;

    /**
     * Rest constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     * @param \Magento\Framework\HTTP\ClientInterfaceFactory|null $communicatorFactory
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \ParadoxLabs\CyberSource\Helper\Data $helper,
        \Magento\Framework\HTTP\ClientInterfaceFactory $communicatorFactory = null
    ) {
        $this->config = $config;
        $this->httpClientFactory = $httpClientFactory;
        $this->helper = $helper;

        // BC preservation -- argument added in 1.3.1
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->communicatorFactory = $communicatorFactory ?? $om->get(
            \Magento\Framework\HTTP\ClientInterfaceFactory::class
        );
    }

    /**
     * Send a REST API GET request to the given resource path. Will sign the request per API specifications.
     *
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return string
     * @throws \Exception
     */
    public function get($path, $params = [], $responseType = 'application/hal+json')
    {
        $client = $this->getHttpClient($path);

        $headers = [
            'Accept' => $responseType,
            'Content-Type' => 'application/json;charset=utf-8',
        ];
        $headers += $this->signRequest($path, $params, 'GET');

        $requestUri = $this->config->getRestEndpoint($path, $this->storeId);
        if (!empty($params)) {
            $requestUri .= '?' . http_build_query($params);
        }

        $client->setHeaders($headers);
        $client->get($requestUri);

        // Throw exception on non-2xx response code
        if (substr((string)$client->getStatus(), 0, 1) !== '2') {
            $responseJson = json_decode((string)$client->getBody(), true);

            $message = $responseJson['message']
                ?? $responseJson['response']['rmsg']
                ?? $client->getStatus();

            $this->helper->log($this->config::CODE, $requestUri, true);
            $this->helper->log($this->config::CODE, $headers['Date'] ?? '', true);
            $this->helper->log($this->config::CODE, $headers['Host'] ?? '', true);
            $this->helper->log($this->config::CODE, $headers['v-c-merchant-id'] ?? '', true);
            $this->helper->log($this->config::CODE, $headers['Signature'] ?? '', true);
            $this->helper->log(
                $this->config::CODE,
                'REQUEST: '.json_encode($params) . "\n" . 'RESPONSE: ' . $client->getBody(),
                true
            );

            throw new \Exception(
                $message,
                $client->getStatus()
            );
        }

        return $client->getBody();
    }

    /**
     * Get an HTTP client for REST
     *
     * @param string $path
     * @return \Magento\Framework\HTTP\ClientInterface
     */
    protected function getHttpClient($path)
    {
        /** @var \Magento\Framework\HTTP\Client\Curl|\Magento\Framework\HTTP\Client\Socket $communicator */
        $communicator = $this->communicatorFactory->create();
        $communicator->setTimeout(15);
        $communicator->setOption(\CURLOPT_SSL_VERIFYPEER, true);
        $communicator->setOption(\CURLOPT_SSL_VERIFYHOST, 2);

        return $communicator;
    }

    /**
     * Generate signature headers for the given REST request and parameters.
     *
     * @param string $path
     * @param array $params
     * @param string $httpMethod
     * @return array
     */
    protected function signRequest($path, $params, $httpMethod): array
    {
        $host = parse_url((string)$this->config->getRestEndpoint($path, $this->storeId), PHP_URL_HOST);
        $date = date("D, d M Y G:i:s \G\M\T");

        $headers = [];
        $headers['Date'] = $date;
        $headers['Host'] = $host;
        $headers['v-c-merchant-id'] = $this->config->getMerchantId($this->storeId);

        /**
         * Note: POST signing requires additional Digest of payload. Not implemented yet.
         * @see https://developer.cybersource.com/docs/cybs/en-us/platform/get-started/all/rest/get-started-rest/ \
         * authentication/GenerateHeader/httpSignatureAuthentication.html
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
                mb_convert_encoding(implode("\n", $signatureParts), 'UTF-8', mb_list_encodings()),
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
        $headers['Signature'] = implode(', ', $signatureHeader);

        return $headers;
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
