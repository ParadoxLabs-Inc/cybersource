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
use const CURLOPT_CUSTOMREQUEST;
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
     * Send a REST API DELETE request to the given resource path. Will sign the request per API specifications.
     *
     * DELETE signs identically to GET — no request body, therefore no payload Digest — and uses the same
     * header list (host, date, request-target, v-c-merchant-id). Used for TMS token deletion (deleteCard),
     * e.g. DELETE /tms/v2/payment-instruments/{id}. CyberSource returns 204 No Content on success, so the
     * (empty) body is returned as-is rather than JSON-decoded.
     *
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return string Raw response body (typically empty on a 204).
     * @throws \Exception
     */
    public function delete($path, $params = [], $responseType = 'application/hal+json')
    {
        $client = $this->getHttpClient($path);

        $headers = [
            'Accept' => $responseType,
            'Content-Type' => 'application/json;charset=utf-8',
        ];
        $headers += $this->signRequest($path, $params, 'DELETE');

        $requestUri = $this->config->getRestEndpoint($path, $this->storeId);
        if (!empty($params)) {
            $requestUri .= '?' . http_build_query($params);
        }

        $client->setHeaders($headers);

        // ClientInterface exposes no delete(); the verb is forced via CURLOPT_CUSTOMREQUEST. User-set curl
        // options are applied AFTER the method defaults inside Curl::makeRequest(), so calling get() and
        // overriding the verb yields a body-less DELETE that reuses GET's (digest-free) signing path.
        $client->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
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
        $client = $this->sendSigned($path, $params, $responseType);

        return (array)json_decode((string)$client->getBody(), true);
    }

    /**
     * Send a REST API POST request and return the raw response body string (not JSON-decoded).
     *
     * Some CyberSource endpoints (e.g. Unified Checkout capture-context) respond with a bare
     * JWT string under Content-Type: application/jwt rather than a JSON document. Decoding such
     * a body as JSON yields null/empty, so this sibling of post() returns the raw bytes. Signing
     * and error handling are identical to post().
     *
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return string Raw response body (e.g. a JWT string).
     * @throws \Exception
     */
    public function postRaw(string $path, array $params = [], string $responseType = 'application/jwt'): string
    {
        $client = $this->sendSigned($path, $params, $responseType);

        return (string)$client->getBody();
    }

    /**
     * Build, sign, and dispatch a POST request; throw on non-2xx; return the HTTP client on success.
     *
     * Encodes $params to JSON once and uses that byte-string for both the request body and the payload
     * Digest, ensuring the signature covers the exact bytes transmitted. post() and postRaw() differ only
     * in how they consume the response body — both delegate the signed-send and error block here.
     *
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return ClientInterface HTTP client after a successful (2xx) response.
     * @throws \Exception
     */
    private function sendSigned(string $path, array $params, string $responseType): ClientInterface
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

        return $client;
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
