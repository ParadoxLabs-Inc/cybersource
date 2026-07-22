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
use Magento\Framework\Exception\RuntimeException;
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
     * @throws RuntimeException On any non-2xx HTTP response (code = HTTP status).
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
            $this->throwOnHttpError(
                $this->config->getRestEndpoint($path, $this->storeId),
                json_encode($params),
                $client
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
     * @throws RuntimeException On any non-2xx HTTP response (code = HTTP status).
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
            $this->throwOnHttpError(
                $this->config->getRestEndpoint($path, $this->storeId),
                json_encode($params),
                $client
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
     * The Accept default must stay application/hal+json: the /pts/v2/* endpoints produce hal+json, and
     * CyberSource's gateway answers an unacceptable Accept (e.g. application/json) with a plain-text
     * 404 "Resource not found" — not a 406 — which presents as a missing endpoint.
     *
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return array Decoded JSON response. Non-array decode results (null/scalar) are cast to an empty/wrapped array.
     * @throws RuntimeException On any non-2xx HTTP response (code = HTTP status).
     */
    public function post(string $path, array $params = [], string $responseType = 'application/hal+json'): array
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
     * @throws RuntimeException On any non-2xx HTTP response (code = HTTP status).
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
     * @throws RuntimeException On any non-2xx HTTP response (code = HTTP status).
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
            $this->throwOnHttpError($requestUri, $jsonBody, $client);
        }

        return $client;
    }

    /**
     * Log a masked error message and throw for any non-2xx HTTP response.
     *
     * Shared by get(), delete(), and sendSigned(). The $requestUri logged here must NOT include
     * a query string — params are already logged (masked) on the REQUEST line, so duplicating them
     * raw in the URI line would leak PII. get()/delete() pass the bare endpoint URI; sendSigned()
     * passes the URI it built (POST has no query string).
     *
     * The thrown exception is a LocalizedException subclass with a GENERIC, customer-safe message: a plain
     * \Exception here was wrapped by Magento's PaymentInformationManagement into the generic "A server
     * error stopped your order from being placed", while surfacing the raw gateway message instead could
     * leak processor internals to the shopper. The raw extracted message + HTTP status stay in the log
     * (masked) and ride on the exception CAUSE (getPrevious()) for programmatic consumers. The HTTP status
     * is preserved EXACTLY as the exception code — Gateway::capture()/refund() retry logic and
     * FollowOn/deleteCard 404 mapping all key on getCode(), and LocalizedException round-trips the
     * constructor $code through getCode() unchanged.
     *
     * @param string $requestUri Endpoint URI without query string.
     * @param string $jsonParams JSON-encoded request params (will be masked before logging).
     * @param ClientInterface $client HTTP client after the response has been received.
     * @return never
     * @throws RuntimeException Always throws, with a generic message and the HTTP status as the code.
     */
    private function throwOnHttpError(string $requestUri, string $jsonParams, ClientInterface $client): never
    {
        $responseJson = json_decode((string)$client->getBody(), true);
        $status       = (int)$client->getStatus();

        // The error body may not be JSON at all (empty 404 body on a TMS DELETE, proxy HTML on a 502), so
        // json_decode() yields null and neither key resolves. The message MUST end up a non-empty string —
        // strict_types would otherwise make `new Exception($int, ...)` raise a TypeError.
        $message = $responseJson['message'] ?? $responseJson['response']['rmsg'] ?? null;

        if (!is_string($message) || $message === '') {
            // Some CyberSource errors return a bare, non-JSON reason (e.g. a 404 "Resource not found" on
            // /pts/v2/payments), so the structured keys above are null. Surface that plain-text body so the
            // real cause reaches the order comment/exception instead of a status-only "HTTP 404". Skip HTML
            // error pages (proxy 502s) and cap the length; fall back to the status when nothing usable remains.
            $rawBody = trim((string)$client->getBody());

            $message = ($rawBody !== '' && !str_starts_with($rawBody, '<'))
                ? $this->sanitizer->maskJson(mb_substr($rawBody, 0, 255))
                : 'HTTP ' . $status;
        }

        $this->helper->log(
            $this->config::CODE,
            $requestUri . "\n"
            . 'REQUEST: ' . $this->sanitizer->maskJson($jsonParams) . "\n"
            . 'RESPONSE: ' . $this->sanitizer->maskJson((string)$client->getBody()),
            true
        );

        throw new RuntimeException(
            __('The transaction was declined. Please verify your payment details and try again.'),
            new Exception($message, $status),
            $status
        );
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
        // RFC 1123/7231 date: hours must be zero-padded (H). The previous 'G' emitted e.g. "9:05:03"
        // for GMT hours 0-9 — a malformed Date header a strict peer may reject. HMAC is unaffected
        // (the signature is computed over the header exactly as sent).
        $date = date("D, d M Y H:i:s \G\M\T");

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
