<?php declare(strict_types=1);
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
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Service;

use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\Socket;
use Exception;
use Magento\Framework\HTTP\ClientInterfaceFactory;
use Magento\Framework\HTTP\ZendClientFactory;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;

class Rest
{
    /**
     * @var int|null
     */
    protected $storeId;

    /**
     * Rest constructor.
     *
     * @param Config $config
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param Data $helper
     * @param \Magento\Framework\HTTP\ClientInterfaceFactory $communicatorFactory
     */
    public function __construct(
        protected readonly Config $config,
        /**
         * @deprecated Class is nonfunctional in 2.4.6+.
         * @see \Magento\Framework\HTTP\ClientInterface via $this->communicatorFactory
         */
        protected readonly ZendClientFactory $httpClientFactory,
        protected readonly Data $helper,
        protected readonly ClientInterfaceFactory $communicatorFactory,
        protected readonly Sanitizer $sanitizer,
    ) {
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
        if (!str_starts_with((string)$client->getStatus(), '2')) {
            $responseJson = json_decode((string)$client->getBody(), true);

            $message = $responseJson['message']
                ?? $responseJson['response']['rmsg']
                ?? $client->getStatus();

            $this->helper->log(
                $this->config::CODE,
                $requestUri . "\n" . 'REQUEST: ' . json_encode($params) . "\n" . 'RESPONSE: ' . $client->getBody(),
                true
            );

            throw new Exception(
                $message,
                $client->getStatus()
            );
        }

        return $client->getBody();
    }

    /**
     * Send a REST API POST request to the given resource path. Will sign the request per API specifications.
     *
     * The JSON body is encoded once and reused for both the payload Digest and the request body, so the
     * digest is computed over the exact bytes transmitted.
     *
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return array Decoded JSON response. Non-array decode results (null/scalar) are cast to an empty/wrapped array.
     * @throws \Exception
     */
    public function post(string $path, array $params = [], string $responseType = 'application/json'): array
    {
        $client   = $this->getHttpClient($path);
        $jsonBody = json_encode($params);

        $headers = [
            'Accept' => $responseType,
            'Content-Type' => 'application/json;charset=utf-8',
        ];
        $headers += $this->signRequest($path, $params, 'POST', $jsonBody);

        $requestUri = $this->config->getRestEndpoint($path, $this->storeId);

        $client->setHeaders($headers);
        $client->post($requestUri, $jsonBody);

        // Throw exception on non-2xx response code
        if (!str_starts_with((string)$client->getStatus(), '2')) {
            $responseJson = json_decode((string)$client->getBody(), true);

            $message = $responseJson['message']
                ?? $responseJson['response']['rmsg']
                ?? $client->getStatus();

            $this->helper->log(
                $this->config::CODE,
                $requestUri . "\n"
                . 'REQUEST: ' . $this->sanitizer->maskJson($jsonBody) . "\n"
                . 'RESPONSE: ' . $this->sanitizer->maskJson((string)$client->getBody()),
                true
            );

            throw new Exception(
                $message,
                $client->getStatus()
            );
        }

        return (array)json_decode((string)$client->getBody(), true);
    }

    /**
     * Get an HTTP client for REST
     *
     * @param string $path
     * @return ClientInterface
     */
    protected function getHttpClient($path)
    {
        /** @var Curl|Socket $communicator */
        $communicator = $this->communicatorFactory->create();
        $communicator->setTimeout(15);
        $communicator->setOption(CURLOPT_SSL_VERIFYPEER, true);
        $communicator->setOption(CURLOPT_SSL_VERIFYHOST, 2);

        return $communicator;
    }

    /**
     * Generate signature headers for the given REST request and parameters.
     *
     * @param string $path
     * @param array $params
     * @param string $httpMethod
     * @param string|null $jsonBody
     * @return array
     */
    protected function signRequest($path, $params, $httpMethod, ?string $jsonBody = null): array
    {
        $host = parse_url((string)$this->config->getRestEndpoint($path, $this->storeId), PHP_URL_HOST);
        $date = date("D, d M Y G:i:s \G\M\T");

        $headers                    = [];
        $headers['Date']            = $date;
        $headers['Host']            = $host;
        $headers['v-c-merchant-id'] = $this->config->getMerchantId($this->storeId);

        $hasBody = $jsonBody !== null
            && in_array(strtoupper((string)$httpMethod), ['POST', 'PUT', 'PATCH'], true);

        /**
         * POST/PUT/PATCH signing requires a Digest of the payload, both as a header and within the
         * signature string. The request-target carries no query string for these methods.
         *
         * @see https://developer.cybersource.com/docs/cybs/en-us/platform/get-started/all/rest/get-started-rest/ \
         * authentication/GenerateHeader/httpSignatureAuthentication.html
         */
        if ($hasBody) {
            // Digest MUST be computed over the exact bytes transmitted as the POST body (raw $jsonBody).
            $digestValue       = 'SHA-256=' . base64_encode(hash('sha256', $jsonBody, true));
            $headers['Digest'] = $digestValue;

            $signatureParts = [
                'host' => 'host: ' . $host,
                'date' => 'date: ' . $date,
                'request-target' => 'request-target: ' . strtolower((string)$httpMethod) . ' ' . $path,
                'digest' => 'digest: ' . $digestValue,
                'v-c-merchant-id' => 'v-c-merchant-id: ' . $this->config->getMerchantId($this->storeId),
            ];

            return $this->buildSignatureHeader($headers, $signatureParts);
        }

        $signatureParts = [
            'host' => 'host: ' . $host,
            'date' => 'date: ' . $date,
            'request-target' => 'request-target: ' . strtolower((string)$httpMethod) . ' ' . $path
                . (!empty($params) ? '?' . http_build_query($params) : ''),
            'v-c-merchant-id' => 'v-c-merchant-id: ' . $this->config->getMerchantId($this->storeId),
        ];

        return $this->buildSignatureHeader($headers, $signatureParts);
    }

    /**
     * Compute the HMAC signature over the given parts and append the Signature header.
     *
     * @param array $headers
     * @param array $signatureParts
     * @return array
     */
    private function buildSignatureHeader(array $headers, array $signatureParts): array
    {
        $signature            = base64_encode(
            hash_hmac(
                'sha256',
                mb_convert_encoding(implode("\n", $signatureParts), 'UTF-8', mb_list_encodings()),
                base64_decode((string)$this->config->getRestSecretKey($this->storeId)),
                true
            )
        );
        $signatureHeader      = [
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
