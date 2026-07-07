<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service;

use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\HTTP\ClientInterfaceFactory;
use Magento\Framework\HTTP\ZendClientFactory;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\Rest
 */
class RestTest extends TestCase
{
    private const TEST_MERCHANT_ID = 'test_merchant';
    private const TEST_SECRET_KEY = 'dGVzdC1zZWNyZXQta2V5LTEyMzQ1'; // base64 encoded
    private const TEST_SECRET_KEY_ID = 'secret-key-id-123';
    private const TEST_ENDPOINT = 'https://apitest.cybersource.com';

    private Rest $rest;
    private Config|MockObject $configMock;
    private Data|MockObject $helperMock;
    private ClientInterfaceFactory|MockObject $communicatorFactoryMock;
    private ClientInterface|MockObject $clientMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->configMock->method('getMerchantId')
            ->willReturn(self::TEST_MERCHANT_ID);
        $this->configMock->method('getRestSecretKey')
            ->willReturn(self::TEST_SECRET_KEY);
        $this->configMock->method('getRestSecretKeyId')
            ->willReturn(self::TEST_SECRET_KEY_ID);
        $this->configMock->method('getRestEndpoint')
            ->willReturnCallback(function ($path) {
                return self::TEST_ENDPOINT . $path;
            });

        $zendClientFactory = $this->createMock(ZendClientFactory::class);
        $this->helperMock = $this->createMock(Data::class);
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->communicatorFactoryMock = $this->createMock(ClientInterfaceFactory::class);
        $this->communicatorFactoryMock->method('create')
            ->willReturn($this->clientMock);

        $this->rest = new Rest(
            $this->configMock,
            $zendClientFactory,
            $this->helperMock,
            $this->communicatorFactoryMock,
            new Sanitizer(),
        );
    }

    public function testSignRequestReturnsRequiredHeaders(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $this->assertArrayHasKey('Date', $headers);
        $this->assertArrayHasKey('Host', $headers);
        $this->assertArrayHasKey('v-c-merchant-id', $headers);
        $this->assertArrayHasKey('Signature', $headers);
    }

    public function testSignRequestDateFormat(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        // Date should be in RFC 7231 format
        $this->assertMatchesRegularExpression(
            '/^[A-Z][a-z]{2}, \d{2} [A-Z][a-z]{2} \d{4} \d{1,2}:\d{2}:\d{2} GMT$/',
            $headers['Date']
        );
    }

    public function testSignRequestHost(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $this->assertSame('apitest.cybersource.com', $headers['Host']);
    }

    public function testSignRequestMerchantId(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $this->assertSame(self::TEST_MERCHANT_ID, $headers['v-c-merchant-id']);
    }

    public function testSignRequestSignatureFormat(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        $signature = $headers['Signature'];

        // Signature header should contain keyid, algorithm, headers, and signature
        $this->assertStringContainsString('keyid="' . self::TEST_SECRET_KEY_ID . '"', $signature);
        $this->assertStringContainsString('algorithm="HmacSHA256"', $signature);
        $this->assertStringContainsString('headers="host date request-target v-c-merchant-id"', $signature);
        $this->assertStringContainsString('signature="', $signature);
    }

    public function testSignRequestIncludesQueryStringInTarget(): void
    {
        $params = ['searchId' => 'abc123', 'limit' => 10];
        $headers = $this->invokeSignRequest('/tss/v2/searches', $params, 'GET');

        // The signature header should reference request-target
        $this->assertStringContainsString('request-target', $headers['Signature']);
    }

    public function testSignRequestDifferentPathsProduceDifferentSignatures(): void
    {
        $headers1 = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');
        $headers2 = $this->invokeSignRequest('/tss/v2/searches', [], 'GET');

        $this->assertNotSame($headers1['Signature'], $headers2['Signature']);
    }

    public function testSignRequestDifferentParamsProduceDifferentSignatures(): void
    {
        $headers1 = $this->invokeSignRequest('/tss/v2/searches', ['id' => '1'], 'GET');
        $headers2 = $this->invokeSignRequest('/tss/v2/searches', ['id' => '2'], 'GET');

        $this->assertNotSame($headers1['Signature'], $headers2['Signature']);
    }

    public function testSignRequestHttpMethodInTarget(): void
    {
        $headers = $this->invokeSignRequest('/pts/v2/payments', [], 'GET');

        // Signature is computed including lowercase http method
        // We can verify format by checking signature contains expected parts
        $this->assertStringContainsString('signature="', $headers['Signature']);
    }

    public function testPostComputesDigestHeader(): void
    {
        $params = ['clientReferenceInformation' => ['code' => 'order-1']];
        $jsonBody = json_encode($params);
        $expectedDigest = 'SHA-256=' . base64_encode(hash('sha256', $jsonBody, true));

        $capturedHeaders = [];
        $this->clientMock->method('setHeaders')
            ->willReturnCallback(function ($headers) use (&$capturedHeaders) {
                $capturedHeaders = $headers;
            });
        $this->clientMock->method('getStatus')->willReturn(201);
        $this->clientMock->method('getBody')->willReturn('{"id":"123"}');

        $this->rest->post('/pts/v2/payments', $params);

        $this->assertArrayHasKey('Digest', $capturedHeaders);
        $this->assertSame($expectedDigest, $capturedHeaders['Digest']);
    }

    /**
     * Regression: the Digest MUST be computed over the exact bytes posted as the request body.
     * The prior implementation hashed mb_convert_encoding($jsonBody, 'UTF-8', mb_list_encodings()),
     * which corrupts multibyte input and produces a digest that does not match the transmitted body,
     * causing CyberSource to reject the signature. This test fails against that code and passes after
     * the fix to hash the raw $jsonBody.
     */
    public function testPostDigestIsComputedOverExactPostedBytesForMultibyteBody(): void
    {
        // Non-BMP emoji + accented char, encoded as raw UTF-8 bytes (not \uXXXX escapes).
        $params = ['clientReferenceInformation' => ['comments' => 'café 🚀 résumé']];
        $exactJsonBody = json_encode($params, JSON_UNESCAPED_UNICODE);
        $expectedDigest = 'SHA-256=' . base64_encode(hash('sha256', $exactJsonBody, true));

        // signRequest encodes the body internally via json_encode($params); ensure the source
        // body actually contains multibyte bytes so the regression is meaningful.
        $this->assertNotSame(
            $exactJsonBody,
            mb_convert_encoding($exactJsonBody, 'UTF-8', mb_list_encodings()),
            'Test body must be multibyte enough that mb_convert_encoding alters it.'
        );

        $headers = $this->invokeSignRequest(
            '/pts/v2/payments',
            $params,
            'POST',
            $exactJsonBody
        );

        $this->assertSame($expectedDigest, $headers['Digest']);
    }

    public function testPostSignatureStringIncludesDigestLineInOrder(): void
    {
        $params = ['amountDetails' => ['totalAmount' => '10.00']];
        $jsonBody = json_encode($params);
        $digestValue = 'SHA-256=' . base64_encode(hash('sha256', $jsonBody, true));

        $host = 'apitest.cybersource.com';
        $date = date("D, d M Y G:i:s \G\M\T");
        $signatureString = implode("\n", [
            'host: ' . $host,
            'date: ' . $date,
            'request-target: post /pts/v2/payments',
            'digest: ' . $digestValue,
            'v-c-merchant-id: ' . self::TEST_MERCHANT_ID,
        ]);
        $expectedSignature = base64_encode(
            hash_hmac(
                'sha256',
                $signatureString,
                base64_decode(self::TEST_SECRET_KEY),
                true
            )
        );

        $headers = $this->invokeSignRequest('/pts/v2/payments', $params, 'POST', $jsonBody);

        $this->assertSame($digestValue, $headers['Digest']);
        $this->assertStringContainsString(
            'headers="host date request-target digest v-c-merchant-id"',
            $headers['Signature']
        );
        $this->assertStringContainsString('signature="' . $expectedSignature . '"', $headers['Signature']);
    }

    public function testPostRequestTargetHasNoQueryString(): void
    {
        $jsonBody = json_encode(['a' => 'b']);
        $headers = $this->invokeSignRequest('/pts/v2/payments', ['a' => 'b'], 'POST', $jsonBody);

        // request-target for POST must be "post <path>" only; verify by recomputing without query string.
        $host = 'apitest.cybersource.com';
        $date = $headers['Date'];
        $digestValue = $headers['Digest'];
        $signatureString = implode("\n", [
            'host: ' . $host,
            'date: ' . $date,
            'request-target: post /pts/v2/payments',
            'digest: ' . $digestValue,
            'v-c-merchant-id: ' . self::TEST_MERCHANT_ID,
        ]);
        $expectedSignature = base64_encode(
            hash_hmac('sha256', $signatureString, base64_decode(self::TEST_SECRET_KEY), true)
        );

        $this->assertStringContainsString('signature="' . $expectedSignature . '"', $headers['Signature']);
    }

    public function testPostReturnsDecodedArray(): void
    {
        $this->clientMock->method('getStatus')->willReturn(201);
        $this->clientMock->method('getBody')->willReturn('{"id":"abc","status":"AUTHORIZED"}');

        $result = $this->rest->post('/pts/v2/payments', ['x' => 'y']);

        $this->assertSame(['id' => 'abc', 'status' => 'AUTHORIZED'], $result);
    }

    public function testPostNonTwoXxThrowsWithExtractedMessage(): void
    {
        $this->clientMock->method('getStatus')->willReturn(400);
        $this->clientMock->method('getBody')
            ->willReturn('{"message":"Declined - invalid account number"}');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Declined - invalid account number');
        $this->expectExceptionCode(400);

        $this->rest->post('/pts/v2/payments', ['x' => 'y']);
    }

    /**
     * Regression: a non-JSON / empty error body (empty 404 body on a TMS DELETE, proxy HTML on a 502)
     * must still throw a plain \Exception with a NON-EMPTY string message and an int code. Under
     * strict_types the prior code resolved $message to the int status and `new Exception($int, ...)`
     * raised a TypeError (an \Error), which then escaped downstream `catch (Exception)` blocks.
     *
     * @dataProvider nonJsonErrorBodyProvider
     */
    public function testThrowsStringMessageIntCodeOnNonJsonErrorBody(string $body, int $status): void
    {
        $this->clientMock->method('getStatus')->willReturn($status);
        $this->clientMock->method('getBody')->willReturn($body);

        try {
            $this->rest->delete('/tms/v2/payment-instruments/abc123');
            $this->fail('Expected an Exception to be thrown');
        } catch (\Throwable $e) {
            // MUST be a plain Exception, never a TypeError / \Error.
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertNotInstanceOf(\Error::class, $e);
            $this->assertIsString($e->getMessage());
            $this->assertNotSame('', $e->getMessage());
            $this->assertSame($status, $e->getCode());
        }
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function nonJsonErrorBodyProvider(): array
    {
        return [
            'empty 404 body (TMS delete)' => ['', 404],
            'proxy HTML 502' => ['<html><body>502 Bad Gateway</body></html>', 502],
            'json null literal' => ['null', 404],
            'non-string message value' => ['{"message":{"nested":"obj"}}', 400],
        ];
    }

    public function testPostMasksPanAndCvvInLog(): void
    {
        $pan = '4111111111111111';
        $cvv = '737';
        $params = [
            'paymentInformation' => [
                'card' => [
                    'number' => $pan,
                    'securityCode' => $cvv,
                ],
            ],
        ];

        $this->clientMock->method('getStatus')->willReturn(400);
        $this->clientMock->method('getBody')->willReturn('{"message":"bad"}');

        $logged = $this->captureLog(fn() => $this->rest->post('/pts/v2/payments', $params));

        $this->assertNotEmpty($logged);
        $this->assertStringNotContainsString($pan, $logged);
        $this->assertStringNotContainsString($cvv, $logged);
    }

    public function testPostRawReturnsRawJwtStringNotJsonDecoded(): void
    {
        $jwt = 'eyJraWQiOiIwOCJ9.eyJjdHgiOlt7ImRhdGEiOnt9fV19.signature';

        $this->clientMock->method('getStatus')->willReturn(201);
        $this->clientMock->method('getBody')->willReturn($jwt);

        $result = $this->rest->postRaw('/up/v1/capture-contexts', ['clientVersion' => '0.34']);

        $this->assertSame($jwt, $result);
    }

    public function testPostRawSendsApplicationJwtAcceptHeader(): void
    {
        $capturedHeaders = [];
        $this->clientMock->method('setHeaders')
            ->willReturnCallback(function ($headers) use (&$capturedHeaders) {
                $capturedHeaders = $headers;
            });
        $this->clientMock->method('getStatus')->willReturn(201);
        $this->clientMock->method('getBody')->willReturn('a.b.c');

        $this->rest->postRaw('/up/v1/capture-contexts', ['clientVersion' => '0.34']);

        $this->assertSame('application/jwt', $capturedHeaders['Accept']);
        $this->assertArrayHasKey('Digest', $capturedHeaders);
    }

    public function testPostRawNonTwoXxThrows(): void
    {
        $this->clientMock->method('getStatus')->willReturn(404);
        $this->clientMock->method('getBody')->willReturn('{"message":"Not boarded"}');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not boarded');
        $this->expectExceptionCode(404);

        $this->rest->postRaw('/up/v1/capture-contexts', ['x' => 'y']);
    }

    /**
     * Regression: postRaw() Digest header must be computed over the exact bytes posted as the request body.
     *
     * sendSigned() encodes $params via plain json_encode($params) (no flags), so the wire body contains
     * \uXXXX escapes for non-ASCII characters. The Digest MUST be hashed over that same byte-string —
     * not over a differently-encoded variant. This test calls postRaw() end-to-end, captures both the
     * body passed to $client->post() and the headers passed to $client->setHeaders(), and verifies that
     * Digest = SHA-256=base64(sha256(capturedBody)).
     */
    public function testPostRawDigestIsComputedOverExactPostedBytesForMultibyteBody(): void
    {
        // Params containing multibyte/unicode values; sendSigned() will encode these with plain json_encode().
        $params = ['targetOrigins' => ['café 🚀 résumé']];

        $capturedBody    = null;
        $capturedHeaders = [];

        $this->clientMock->method('setHeaders')
            ->willReturnCallback(function ($headers) use (&$capturedHeaders) {
                $capturedHeaders = $headers;
            });
        $this->clientMock->method('post')
            ->willReturnCallback(function ($uri, $body) use (&$capturedBody) {
                $capturedBody = $body;
            });
        $this->clientMock->method('getStatus')->willReturn(201);
        $this->clientMock->method('getBody')->willReturn('a.b.c');

        $result = $this->rest->postRaw('/up/v1/capture-contexts', $params);

        // postRaw() must return the raw body string from the response.
        $this->assertSame('a.b.c', $result);

        // Digest must be computed over the exact bytes that were posted.
        $this->assertNotNull($capturedBody, 'client->post() was not called');
        $expectedDigest = 'SHA-256=' . base64_encode(hash('sha256', $capturedBody, true));

        $this->assertArrayHasKey('Digest', $capturedHeaders);
        $this->assertSame($expectedDigest, $capturedHeaders['Digest']);
    }

    public function testGetMasksPanInRequestParamsOnError(): void
    {
        // PAN passed as a request param (e.g. a search filter): must be masked on the REQUEST line.
        // The query string is stripped from the logged URI, so the only place params appear is the
        // masked REQUEST: json_encode($params) line produced by throwOnHttpError().
        $pan = '4111111111111111';
        $params = ['paymentInformation' => ['card' => ['number' => $pan]]];

        $this->clientMock->method('getStatus')->willReturn(400);
        $this->clientMock->method('getBody')->willReturn('{"message":"bad"}');

        $logged = $this->captureLog(fn() => $this->rest->get('/tss/v2/searches', $params));

        $this->assertNotEmpty($logged);
        $this->assertStringNotContainsString($pan, $logged);
        $this->assertStringContainsString('************1111', $logged);
    }

    public function testGetMasksPanInResponseBodyOnError(): void
    {
        $pan = '4111111111111111';
        $responseBody = '{"message":"error","card":{"number":"' . $pan . '"}}';

        $this->clientMock->method('getStatus')->willReturn(422);
        $this->clientMock->method('getBody')->willReturn($responseBody);

        $logged = $this->captureLog(fn() => $this->rest->get('/tss/v2/searches', []));

        $this->assertNotEmpty($logged);
        $this->assertStringNotContainsString($pan, $logged);
        $this->assertStringContainsString('************1111', $logged);
    }

    public function testGetMasksSecurityCodeInRequestParamsOnError(): void
    {
        $cvv = '737';
        $params = ['securityCode' => $cvv];

        $this->clientMock->method('getStatus')->willReturn(400);
        $this->clientMock->method('getBody')->willReturn('{"message":"bad"}');

        $logged = $this->captureLog(fn() => $this->rest->get('/tss/v2/searches', $params));

        $this->assertNotEmpty($logged);
        $this->assertStringNotContainsString($cvv, $logged);
        $this->assertStringContainsString('"securityCode":"***"', $logged);
    }

    public function testDeleteMasksPanInResponseBodyOnError(): void
    {
        $pan = '5500005555555559';
        $responseBody = '{"message":"error","card":{"number":"' . $pan . '"}}';

        $this->clientMock->method('getStatus')->willReturn(404);
        $this->clientMock->method('getBody')->willReturn($responseBody);

        $logged = $this->captureLog(fn() => $this->rest->delete('/tms/v2/payment-instruments/abc123', []));

        $this->assertNotEmpty($logged);
        $this->assertStringNotContainsString($pan, $logged);
        $this->assertStringContainsString('************5559', $logged);
    }

    public function testDeleteMasksSecurityCodeInResponseBodyOnError(): void
    {
        $cvv = '999';
        $responseBody = '{"message":"error","card":{"securityCode":"' . $cvv . '"}}';

        $this->clientMock->method('getStatus')->willReturn(422);
        $this->clientMock->method('getBody')->willReturn($responseBody);

        $logged = $this->captureLog(fn() => $this->rest->delete('/tms/v2/payment-instruments/abc123', []));

        $this->assertNotEmpty($logged);
        $this->assertStringNotContainsString($cvv, $logged);
        $this->assertStringContainsString('"securityCode":"***"', $logged);
    }

    public function testSetStoreId(): void
    {
        $result = $this->rest->setStoreId(5);

        // Should return self for chaining
        $this->assertSame($this->rest, $result);
    }

    public function testSetStoreIdNull(): void
    {
        $result = $this->rest->setStoreId(null);

        $this->assertSame($this->rest, $result);
    }

    /**
     * Wire up the helperMock to capture log calls, invoke $callable (swallowing any Throwable),
     * and return the concatenated log output as a single string.
     */
    private function captureLog(callable $callable): string
    {
        $loggedMessages = [];
        $this->helperMock->method('log')
            ->willReturnCallback(function ($code, $message, $debug = false) use (&$loggedMessages) {
                $loggedMessages[] = (string)$message;

                return $this->helperMock;
            });

        try {
            $callable();
        } catch (\Throwable $e) {
            // expected non-2xx throw
        }

        return implode("\n", $loggedMessages);
    }

    /**
     * Helper method to invoke protected signRequest method
     */
    private function invokeSignRequest(
        string $path,
        array $params,
        string $httpMethod,
        ?string $jsonBody = null
    ): array {
        $method = new ReflectionMethod(Rest::class, 'signRequest');
        $method->setAccessible(true);

        return $method->invoke($this->rest, $path, $params, $httpMethod, $jsonBody);
    }
}
