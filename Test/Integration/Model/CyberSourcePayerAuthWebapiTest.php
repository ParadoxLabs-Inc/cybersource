<?php declare(strict_types=1);
/**
 * ParadoxLabs, Inc.
 * https://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  https://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     https://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Test\Integration\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request as HttpRequest;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterfaceFactory;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Api\GuestPayerAuthManagementInterface;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Authenticate;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Results;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Setup;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\RestStubTrait;
use PHPUnit\Framework\TestCase;

/**
 * Payer Authentication service contract behind the webapi routes (etc/webapi.xml), with the REST
 * HTTP boundary stubbed.
 *
 * These tests exist because the api-functional suite CANNOT cover the happy paths: it drives a live
 * application over HTTP, in another process, so ParadoxLabs\CyberSource\Model\Service\Rest is not
 * stubbable there and every gateway-touching case would need a provisioned sandbox MID
 * (m2-extension-cybersource#4). What the api-functional suite proves is routing, ACL and the guards
 * that resolve before the socket opens; what this suite proves is the behaviour a client sees once
 * CyberSource answers.
 *
 * The wire-shape assertions run the returned DTOs through the very
 * {@see ServiceOutputProcessor} the webapi framework serializes with, so "absent from the response
 * payload" means absent from the JSON a REST caller receives, not merely absent from a getter.
 *
 * Reply fixtures are the ones pinned by the unit suite (Test/Unit/Model/Service/PayerAuth/_files):
 * deliberately shared, so a fixture corrected against live evidence corrects both layers at once.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 *
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CyberSourcePayerAuthWebapiTest extends TestCase
{
    use RestStubTrait {
        simulateNewRequest as private traitSimulateNewRequest;
    }

    private const RESERVED_ORDER_ID = 'test_payer_auth_guest';
    private const REPLY_FIXTURE_DIR = __DIR__ . '/../../Unit/Model/Service/PayerAuth/_files/';
    private const TRANSIENT_TOKEN_JTI = '5b4c3d2e-1f0a-49b8-8c7d-6e5f4a3b2c1d';
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/126.0';
    private const ACCEPT_HEADER = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    private const REMOTE_ADDR = '203.0.113.10';

    private ?ObjectManager $objectManager = null;
    private ?ServiceOutputProcessor $outputProcessor = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->outputProcessor = $this->objectManager->get(ServiceOutputProcessor::class);

        $this->simulateBrowserRequest();
    }

    /**
     * Give the shared request the browser facts a real checkout request would carry.
     *
     * authenticate() derives userAgentBrowserValue, httpAcceptBrowserValue and ipAddress from the
     * live HTTP request rather than from client input, and AuthenticationRequest rejects an
     * incomplete deviceInformation tree outright (a thin profile silently degrades an enrolled card
     * to "not enrolled" -- a 3DS bypass). The integration harness builds its request from an empty
     * $_SERVER, so without this the service has nothing to derive.
     *
     * @return void
     */
    private function simulateBrowserRequest(): void
    {
        $request = $this->objectManager->get(RequestInterface::class);

        if (!$request instanceof HttpRequest) {
            self::fail('The integration request must be an HTTP request for Payer Auth to profile.');
        }

        $request->getHeaders()->addHeaders([
            'User-Agent' => self::USER_AGENT,
            'Accept' => self::ACCEPT_HEADER,
        ]);
        $request->getServer()->set('REMOTE_ADDR', self::REMOTE_ADDR);

        // RemoteAddress memoizes the address on first read; drop it so it reads the value above.
        $this->objectManager->removeSharedInstance(RemoteAddress::class);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->unregisterRestStub();

        parent::tearDown();
    }

    /**
     * The record custody contract: setup() and authenticate() are separate HTTP requests that share
     * nothing but the quote-payment row, so the binding, the referenceId and the transient token
     * seeded by the first must be readable by the second after every in-memory cache is dropped.
     *
     * Dropping the quote caches between the two calls is the whole point -- without it the second
     * call would be handed the very quote object the first one mutated, and the test would pass on
     * a service that never persisted anything at all.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_guest_quote.php
     * @return void
     */
    public function testSetupRecordIsConsumedByASeparateAuthenticateRequest(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));
        $maskedId = $this->getMaskedQuoteId();

        $setupResult = $this->guestManagement()->setup($maskedId, $this->transientToken());

        self::assertFalse($setupResult->getSkipped(), 'Payer Auth is enabled, so setup must not skip.');
        self::assertSame(
            'https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect',
            $setupResult->getDeviceDataCollectionUrl()
        );
        self::assertNotEmpty($setupResult->getAccessToken(), 'The DDC iframe needs the access token.');

        $seeded = $this->loadRecord();
        self::assertSame(self::TRANSIENT_TOKEN_JTI, $seeded['binding'] ?? null);
        self::assertSame('2611dbe9-b63b-4ac4-a172-a4278a32aecb', $seeded['reference_id'] ?? null);
        self::assertArrayHasKey('verdict', $seeded);
        self::assertNull($seeded['verdict'], 'Setup seeds a record with no verdict yet.');

        // ---- second request ----
        $this->simulateNewRequest();

        $authResult = $this->guestManagement()->authenticate($maskedId, $this->browserInfo());

        self::assertSame(PayerAuthResultInterface::STATUS_SUCCESS, $authResult->getStatus());

        $authenticationCalls = $stub->getCallsMatching(Authenticate::AUTHENTICATIONS_PATH);
        self::assertCount(1, $authenticationCalls, 'authenticate() must issue exactly one authentications call.');
        self::assertSame(
            $seeded['reference_id'],
            $authenticationCalls[0]['params']['consumerAuthenticationInformation']['referenceId'] ?? null,
            'The second request must send the referenceId the first one persisted.'
        );

        $stored = $this->loadRecord();
        self::assertSame(Verdict::AUTHENTICATED->value, $stored['verdict'] ?? null);
        self::assertSame(self::TRANSIENT_TOKEN_JTI, $stored['binding'] ?? null);
        self::assertSame($this->getQuoteBaseAmount(), $stored['amount'] ?? null);
        self::assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $stored['ca']['cavv'] ?? null);
    }

    /**
     * The setup response must not carry the referenceId. It is the server's correlation handle for
     * the attempt; handing it to the browser would let a client point authenticate() at somebody
     * else's setup.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_guest_quote.php
     * @return void
     */
    public function testSetupResponsePayloadWithholdsTheReferenceId(): void
    {
        $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));

        $payload = $this->outputProcessor->process(
            $this->guestManagement()->setup($this->getMaskedQuoteId(), $this->transientToken()),
            GuestPayerAuthManagementInterface::class,
            'setup'
        );

        self::assertArrayHasKey('access_token', $payload);
        self::assertArrayHasKey('device_data_collection_url', $payload);
        self::assertSame(
            [],
            $this->findForbiddenKeys($payload, ['reference_id', 'referenceId', 'transient_token', 'binding']),
            'The setup response must expose only the device-data-collection handles.'
        );
    }

    /**
     * A frictionless success must report status only. CAVV, ECI, XID and the internal verdict are
     * authorization-bearing or diagnostic; none of them belongs in a browser-reachable payload.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_guest_quote.php
     * @return void
     */
    public function testAuthenticateResponsePayloadCarriesNoAuthenticationSecrets(): void
    {
        $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));
        $maskedId = $this->getMaskedQuoteId();

        $this->guestManagement()->setup($maskedId, $this->transientToken());
        $this->simulateNewRequest();

        $result = $this->guestManagement()->authenticate($maskedId, $this->browserInfo());
        $payload = $this->outputProcessor->process(
            $result,
            GuestPayerAuthManagementInterface::class,
            'authenticate'
        );

        self::assertSame(PayerAuthResultInterface::STATUS_SUCCESS, $payload['status'] ?? null);
        self::assertSame(
            [],
            $this->findForbiddenKeys(
                $payload,
                ['cavv', 'eci', 'eci_raw', 'xid', 'verdict', 'pares_status', 'ca', 'token']
            ),
            'The authenticate response must carry nothing authorization-bearing.'
        );
    }

    /**
     * A challenged authentication must hand the client exactly what the step-up iframe needs -- the
     * issuer ACS URL and the base64 CReq -- and nothing else.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_guest_quote.php
     * @return void
     */
    public function testChallengeReplySurfacesTheStepUpHandles(): void
    {
        $this->registerRestStub($this->payerAuthResponder('case-2-10a-challenge.json'));
        $maskedId = $this->getMaskedQuoteId();

        $this->guestManagement()->setup($maskedId, $this->transientToken());
        $this->simulateNewRequest();

        $result = $this->guestManagement()->authenticate($maskedId, $this->browserInfo());

        self::assertSame(PayerAuthResultInterface::STATUS_CHALLENGE, $result->getStatus());
        self::assertSame(
            'https://1merchantacsstag.cardinalcommerce.com/MerchantACSWeb/creq.jsp',
            $result->getAcsUrl()
        );
        self::assertNotEmpty($result->getPareq(), 'The step-up form POST needs the CReq payload.');

        $record = $this->loadRecord();
        self::assertSame(Verdict::CHALLENGE->value, $record['verdict'] ?? null);
        self::assertSame('6544863011992807913018', $record['auth_transaction_id'] ?? null);
    }

    /**
     * With Payer Authentication turned off, every route must answer `skipped` and the gateway must
     * not be touched at all. The call count is the assertion that matters: a merchant who has not
     * enabled 3DS must not be billed for authentication traffic, and a client that trusts `skipped`
     * must not be racing a request it cannot see.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 0
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_guest_quote.php
     * @return void
     */
    public function testDisabledPayerAuthSkipsWithoutAnyGatewayCall(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));
        $maskedId = $this->getMaskedQuoteId();

        $setupResult = $this->guestManagement()->setup($maskedId, $this->transientToken());
        $this->simulateNewRequest();
        $authResult = $this->guestManagement()->authenticate($maskedId, $this->browserInfo());

        self::assertTrue($setupResult->getSkipped());
        self::assertNull($setupResult->getAccessToken());
        self::assertSame(PayerAuthResultInterface::STATUS_SKIPPED, $authResult->getStatus());
        self::assertSame([], $stub->calls, 'A disabled method must issue zero CyberSource calls.');
        self::assertNull($this->loadRecord(), 'A skipped attempt must not leave a record behind.');
    }

    /**
     * Responder mapping the three /risk/v1 endpoints onto the pinned unit fixtures.
     *
     * @param string $authenticationFixture Fixture file for authentications and authentication-results.
     * @return callable
     */
    private function payerAuthResponder(string $authenticationFixture): callable
    {
        return function (string $method, string $path, array $params) use ($authenticationFixture): array {
            return match ($path) {
                Setup::SETUP_PATH => $this->replyFixture('setups-201.json'),
                Authenticate::AUTHENTICATIONS_PATH,
                Results::RESULTS_PATH => $this->replyFixture($authenticationFixture),
                default => throw CyberSourceRestStub::httpError('Unexpected path ' . $path, 404),
            };
        };
    }

    /**
     * Decode one of the unit suite's pinned reply fixtures.
     *
     * @param string $file
     * @return array
     */
    private function replyFixture(string $file): array
    {
        $contents = file_get_contents(self::REPLY_FIXTURE_DIR . $file);

        self::assertIsString($contents, 'Missing Payer Auth reply fixture: ' . $file);

        return (array)json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get a fresh guest-cart service instance, as a new HTTP request would.
     *
     * @return GuestPayerAuthManagementInterface
     */
    private function guestManagement(): GuestPayerAuthManagementInterface
    {
        return $this->objectManager->create(GuestPayerAuthManagementInterface::class);
    }

    /**
     * Build a complete browser profile, as the client JS collects it.
     *
     * @return PayerAuthBrowserInfoInterface
     */
    private function browserInfo(): PayerAuthBrowserInfoInterface
    {
        return $this->objectManager->get(PayerAuthBrowserInfoInterfaceFactory::class)
            ->create()
            ->setLanguage('en-US')
            ->setJavaEnabled(false)
            ->setJavaScriptEnabled(true)
            ->setColorDepth(24)
            ->setScreenHeight(1080)
            ->setScreenWidth(1920)
            ->setTimeDifference(300);
    }

    /**
     * Mint an unsigned transient-token JWT carrying a jti and Visa card metadata.
     *
     * TransientTokenReader deliberately does not verify the signature (a forged jti binds an attempt
     * to itself and nothing else), so a hand-built payload is a faithful stand-in here.
     *
     * @return string
     */
    private function transientToken(): string
    {
        $payload = [
            'jti' => self::TRANSIENT_TOKEN_JTI,
            'content' => [
                'paymentInformation' => [
                    'card' => [
                        'type' => ['value' => '001'],
                        'expirationMonth' => ['value' => '01'],
                        'expirationYear' => ['value' => '2029'],
                        'number' => [
                            'maskedValue' => ['value' => 'XXXXXXXXXXXX1111'],
                            'bin' => ['value' => '411111'],
                        ],
                    ],
                ],
            ],
        ];

        return 'eyJhbGciOiJSUzI1NiJ9.'
            . rtrim(strtr(base64_encode(json_encode($payload, JSON_THROW_ON_ERROR)), '+/', '-_'), '=')
            . '.integrationsignature';
    }

    /**
     * Get the fixture cart's masked id, as a guest client would hold it.
     *
     * @return string
     */
    private function getMaskedQuoteId(): string
    {
        $maskedId = $this->objectManager->get(QuoteIdToMaskedQuoteIdInterface::class)
            ->execute((int)$this->loadQuote()->getId());

        self::assertNotEmpty($maskedId, 'The fixture must have created a masked cart id.');

        return $maskedId;
    }

    /**
     * Get the fixture cart's base grand total, formatted the way the record stores it.
     *
     * @return string
     */
    private function getQuoteBaseAmount(): string
    {
        return number_format((float)$this->loadQuote()->getBaseGrandTotal(), 2, '.', '');
    }

    /**
     * Read the persisted payer_auth record straight from the database.
     *
     * @return array|null
     */
    private function loadRecord(): ?array
    {
        return $this->objectManager->get(Persistor::class)->load($this->loadQuote()->getPayment());
    }

    /**
     * Load the fixture quote, always fresh: memoized quotes would hide missing persistence.
     *
     * @return Quote
     */
    private function loadQuote(): Quote
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(QuoteCollectionFactory::class)
            ->create()
            ->addFieldToFilter('reserved_order_id', self::RESERVED_ORDER_ID)
            ->setPageSize(1)
            ->getFirstItem();

        self::assertGreaterThan(0, (int)$quote->getId(), 'Fixture should have created the guest cart.');

        return $quote->load((int)$quote->getId());
    }

    /**
     * Drop the quote registries too, on top of the sales ones the trait clears.
     *
     * QuoteRepository memoizes by id AND by masked id, so without this the "second request" would be
     * handed the very quote instance the first call left its in-memory record on -- which is exactly
     * the bug this suite is meant to catch.
     *
     * @return void
     */
    private function simulateNewRequest(): void
    {
        $this->traitSimulateNewRequest();

        $this->objectManager->removeSharedInstance(CartRepositoryInterface::class, true);
        $this->objectManager->removeSharedInstance(CartRepositoryInterface::class);
        $this->objectManager->removeSharedInstance(QuoteRepository::class);
    }

    /**
     * Recursively collect any of the given keys present anywhere in a response payload.
     *
     * @param array $payload
     * @param array $forbidden
     * @return array
     */
    private function findForbiddenKeys(array $payload, array $forbidden): array
    {
        $found = [];

        foreach ($payload as $key => $value) {
            if (in_array((string)$key, $forbidden, true)) {
                $found[] = (string)$key;
            }

            if (is_array($value)) {
                $found = array_merge($found, $this->findForbiddenKeys($value, $forbidden));
            }
        }

        return array_values(array_unique($found));
    }
}
