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
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\PhpEnvironment\Request as HttpRequest;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItemResource;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterfaceFactory;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Authenticate;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\BindingValidator;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Results;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Setup;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response;
use ParadoxLabs\CyberSource\Test\Integration\CyberSourceRestStub;
use ParadoxLabs\CyberSource\Test\Integration\RestStubTrait;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Payer Authentication across the whole checkout: setup and authenticate in their own requests, then a
 * real quote-to-order submit that has to find, validate and consume what they left behind.
 *
 * The unit suite proves each seam in isolation and T5 proves the transport; what only this level can
 * prove is that the record survives the trip -- persisted on a quote payment by one request, copied to
 * the order payment by Magento's own conversion, and read back by the money call in a third. Every
 * assertion here is therefore on the REQUEST BODY the module posted or on the persisted sales record,
 * never on an intermediate return value.
 *
 * The REST boundary is stubbed ({@see CyberSourceRestStub}), with /risk/v1 answered from the very
 * reply fixtures the unit suite pins, so a fixture corrected against live evidence corrects both
 * layers at once.
 *
 * Area is `frontend` deliberately: {@see Response::shouldConsumePayerAuth()} only consults the record
 * on customer-facing origins, so a global-area test would pass against a money path that never looked.
 *
 * These tests require a database and cannot run without the Magento integration harness.
 *
 * NOTE: class-level @magentoConfigFixture is ignored by the harness; config fixtures are on the METHODS.
 *
 * @magentoAppArea frontend
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CyberSourcePayerAuthCheckoutTest extends TestCase
{
    use RestStubTrait {
        simulateNewRequest as private traitSimulateNewRequest;
    }

    private const GUEST_QUOTE_ID = 'test_pa_checkout_guest';
    private const STORED_QUOTE_ID = 'test_pa_checkout_stored';
    private const REPLY_FIXTURE_DIR = __DIR__ . '/../../Unit/Model/Service/PayerAuth/_files/';
    private const PAYMENTS_PATH = '/pts/v2/payments';
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/126.0';
    private const ACCEPT_HEADER = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    private const REMOTE_ADDR = '203.0.113.10';

    private ?ObjectManager $objectManager = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();

        $this->simulateBrowserRequest();
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
     * The whole new-card flow: authenticate frictionlessly, then place.
     *
     * Three things have to be true at once for the liability shift to be real, and all three are
     * asserted on artifacts the customer cannot influence: the CAVV reached CyberSource on the money
     * call, the outcome is on the order for the merchant to see, and the record is gone so it cannot
     * be spent twice.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_checkout_quote.php
     * @return void
     */
    public function testNewCardAuthenticationRidesTheMoneyCallAndIsConsumed(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));

        $this->runSetupAndAuthenticate();

        // ---- place ----
        $this->simulateNewRequest();

        $order = $this->placeCart();

        $payments = $stub->getCallsMatching(self::PAYMENTS_PATH);
        self::assertCount(1, $payments, 'Placement posts exactly one payment.');

        $consumerAuthentication = $payments[0]['params']['consumerAuthenticationInformation'] ?? [];

        self::assertSame(
            'AJkBBkhgQQAAAE4gSEJydQAAAAA=',
            $consumerAuthentication['cavv'] ?? null,
            'The authenticated CAVV must ride the payment call, or there is no liability shift.'
        );
        self::assertSame('Y', $consumerAuthentication['paresStatus'] ?? null);
        self::assertSame('05', $consumerAuthentication['eciRaw'] ?? null);
        self::assertSame('2.2.0', $consumerAuthentication['paSpecificationVersion'] ?? null);
        self::assertSame(
            'vbv',
            $payments[0]['params']['processingInformation']['commerceIndicator'] ?? null,
            'The Visa text indicator must be mapped onto processingInformation.commerceIndicator.'
        );

        $payment = $order->getPayment();

        // The /pts/v2/payments reply echoes none of this back (G2 finding 3), so anything on the order
        // can only have come from the persisted record. TokenBase flattens the response tree onto the
        // payment, hence the dotted keys.
        self::assertSame('05', $payment->getAdditionalInformation('consumer_authentication.eci'));
        self::assertSame('Y', $payment->getAdditionalInformation('consumer_authentication.paresStatus'));
        self::assertSame('Y', $payment->getAdditionalInformation('consumer_authentication.veresEnrolled'));
        self::assertSame(
            '2.2.0',
            $payment->getAdditionalInformation('consumer_authentication.specificationVersion')
        );

        self::assertNull(
            $payment->getAdditionalInformation(Persistor::PERSIST_KEY),
            'The record is one-shot: an approved place must consume it.'
        );
    }

    /**
     * Payer Authentication turned off: the checkout must place exactly as it did before PA-1 existed.
     *
     * Two assertions, both absolute -- no /risk/v1 traffic at all (a merchant who has not enabled 3DS
     * must not be billed for authentication calls) and not one byte of authentication data in the
     * payment body (the pre-PA-1 request shape, unchanged).
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 0
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_checkout_quote.php
     * @return void
     */
    public function testDisabledPayerAuthPlacesWithoutAnyAuthenticationTraffic(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));

        $setupResult = $this->management()->setup($this->transientToken());
        $this->simulateNewRequest();
        $authResult = $this->management()->authenticate($this->browserInfo());

        self::assertTrue($setupResult->getSkipped());
        self::assertSame(PayerAuthResultInterface::STATUS_SKIPPED, $authResult->getStatus());

        $this->simulateNewRequest();

        $order = $this->placeCart();

        self::assertSame(
            Order::STATE_PROCESSING,
            $order->getState(),
            'A disabled-Payer-Auth checkout must place normally.'
        );

        foreach ($stub->getCalledPaths() as $path) {
            self::assertStringNotContainsString('/risk/v1', $path, 'Disabled Payer Auth issues no risk calls.');
        }

        $payments = $stub->getCallsMatching(self::PAYMENTS_PATH);
        self::assertCount(1, $payments);
        self::assertStringNotContainsString(
            'consumerAuthenticationInformation',
            json_encode($payments[0]['params'], JSON_THROW_ON_ERROR),
            'With Payer Auth off the payment body must be byte-identical to the pre-PA-1 shape.'
        );
    }

    /**
     * The stored-card flow, end to end: a vault card is authenticated by its TMS payment instrument and
     * charged by the same one.
     *
     * The setups request shape is asserted because it is the G1 finding in practice -- a vaulted card
     * has no transient token, so the authentication has to address the payment instrument directly or
     * there is nothing to authenticate.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_stored_card_quote.php
     * @return void
     */
    public function testStoredCardAuthenticationRidesTheMoneyCallAndIsConsumed(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));

        $setupResult = $this->management(self::STORED_QUOTE_ID)->setup(null, $this->storedCardHash());

        self::assertFalse($setupResult->getSkipped(), 'A fully tokenized vault card must not skip.');

        $setups = $stub->getCallsMatching(Setup::SETUP_PATH);
        self::assertCount(1, $setups);
        self::assertSame(
            'P456',
            $setups[0]['params']['paymentInformation']['paymentInstrument']['id'] ?? null,
            'A stored card is authenticated by its TMS payment instrument.'
        );
        self::assertArrayNotHasKey(
            'tokenInformation',
            $setups[0]['params'],
            'A vaulted card has no transient token to send.'
        );

        $this->simulateNewRequest();

        $authResult = $this->management(self::STORED_QUOTE_ID)->authenticate($this->browserInfo());
        self::assertSame(PayerAuthResultInterface::STATUS_SUCCESS, $authResult->getStatus());

        // ---- place ----
        $this->simulateNewRequest();

        $order = $this->placeCart(self::STORED_QUOTE_ID);

        $payments = $stub->getCallsMatching(self::PAYMENTS_PATH);
        self::assertCount(1, $payments, 'Placement posts exactly one payment.');
        self::assertSame(
            'P456',
            $payments[0]['params']['paymentInformation']['paymentInstrument']['id'] ?? null,
            'The charge must be on the very instrument that was authenticated.'
        );
        self::assertSame(
            'AJkBBkhgQQAAAE4gSEJydQAAAAA=',
            $payments[0]['params']['consumerAuthenticationInformation']['cavv'] ?? null,
            'The stored-card charge must carry the authenticated CAVV.'
        );
        self::assertSame(
            'vbv',
            $payments[0]['params']['processingInformation']['commerceIndicator'] ?? null
        );

        self::assertNull(
            $order->getPayment()->getAdditionalInformation(Persistor::PERSIST_KEY),
            'The record is one-shot: an approved place must consume it.'
        );
    }

    /**
     * The cart grew after the customer authenticated it: the authentication no longer covers the
     * money, so the charge must not happen at all -- and must keep not happening on a retry.
     *
     * This is the binding rule doing the only job that matters -- a client that can authenticate $30
     * and then place $45 with the liability shift has no liability shift, it has a bypass. The
     * assertion that the gateway was never called is therefore the point: blocking after the money
     * moved would not be blocking.
     *
     * The qty goes UP deliberately: that is the attack direction. The rule is charge <= authenticated,
     * so a REDUCTION is legal and is covered separately by
     * {@see testChargeBelowTheAuthenticatedAmountStillPlaces()}.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_checkout_quote.php
     * @return void
     */
    public function testCartChangedAfterAuthenticationBlocksThePlacement(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));

        $this->runSetupAndAuthenticate();

        $authenticatedAmount = $this->loadRecord(self::GUEST_QUOTE_ID)['amount'] ?? null;
        self::assertIsString($authenticatedAmount);

        // The customer goes back and adds a unit: same cart, same card, different money.
        $this->changeCartQuantity(self::GUEST_QUOTE_ID, 3);

        self::assertNotSame(
            $authenticatedAmount,
            $this->baseGrandTotal(self::GUEST_QUOTE_ID),
            'The scenario is meaningless unless the cart total actually moved.'
        );

        $this->simulateNewRequest();

        try {
            $this->placeCart();
            self::fail('A drifted authentication must not place.');
        } catch (CommandException $exception) {
            self::assertStringContainsString('verify your payment again', $exception->getMessage());
        }

        self::assertSame(
            [],
            $stub->getCallsMatching(self::PAYMENTS_PATH),
            'The stale CAVV must never reach the gateway: the money call must not happen at all.'
        );
        self::assertNotNull(
            $this->loadRecord(self::GUEST_QUOTE_ID),
            'The record must SURVIVE the block: discarding it would let the retry place unauthenticated.'
        );

        // Retry-bypass regression: the SAME persisted record refuses again, and again.
        $this->assertRetryIsStillRefused('verify your payment again');

        self::assertSame(
            [],
            $stub->getCallsMatching(self::PAYMENTS_PATH),
            'The retry must not reach the gateway either.'
        );
    }

    /**
     * A charge BELOW the authenticated amount still places: the rule is charge <= authenticated.
     *
     * The authenticated amount is the quote grand total the cardholder approved -- a ceiling. Store
     * credit, gift cards and partial-payment modules all legitimately reduce what actually reaches the
     * gateway, and refusing those would break checkout for a case that carries no risk. A cart
     * reduction is the cheapest way to produce that divergence through the real place path.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_checkout_quote.php
     * @return void
     */
    public function testChargeBelowTheAuthenticatedAmountStillPlaces(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));

        $this->runSetupAndAuthenticate();

        $authenticatedAmount = $this->loadRecord(self::GUEST_QUOTE_ID)['amount'] ?? null;
        self::assertIsString($authenticatedAmount);

        // Same cart, same card, LESS money than was authenticated.
        $this->changeCartQuantity(self::GUEST_QUOTE_ID, 1);

        self::assertLessThan(
            (float)$authenticatedAmount,
            (float)$this->baseGrandTotal(self::GUEST_QUOTE_ID),
            'The scenario is meaningless unless the charge actually dropped below the authenticated amount.'
        );

        $this->simulateNewRequest();

        $order = $this->placeCart();

        self::assertNotEmpty(
            $stub->getCallsMatching(self::PAYMENTS_PATH),
            'A charge at or under the authenticated amount must reach the gateway.'
        );

        $body = $stub->getCallsMatching(self::PAYMENTS_PATH)[0]['params'] ?? [];

        self::assertNotEmpty(
            $body['consumerAuthenticationInformation']['cavv'] ?? null,
            'The liability shift must ride the reduced charge.'
        );
        self::assertNull(
            $order->getPayment()->getAdditionalInformation(Persistor::PERSIST_KEY),
            'The record is one-shot: an approved place must consume it.'
        );
    }

    /**
     * A failed authentication is final: place must refuse, must refuse without quietly charging the
     * card unauthenticated, and must refuse the RETRY the same way.
     *
     * Retry-bypass regression test. The original implementation discarded the record on its way to
     * throwing, so a second Place Order found nothing, resolved to null, and placed the order
     * unauthenticated -- a complete 3DS bypass reachable by clicking the button twice.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_checkout_quote.php
     * @return void
     */
    public function testFailedAuthenticationBlocksThePlacementAndTheRetry(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-2-frictionless-fail.json'));

        $authResult = $this->runSetupAndAuthenticate();

        self::assertSame(PayerAuthResultInterface::STATUS_FAILED, $authResult->getStatus());

        $this->simulateNewRequest();

        try {
            $this->placeCart();
            self::fail('A failed authentication must not place.');
        } catch (CommandException $exception) {
            self::assertStringContainsString('could not be verified', $exception->getMessage());
        }

        self::assertSame(
            [],
            $stub->getCallsMatching(self::PAYMENTS_PATH),
            'A failed authentication must not be silently downgraded to an unauthenticated charge.'
        );
        self::assertNotNull(
            $this->loadRecord(self::GUEST_QUOTE_ID),
            'The failed record must SURVIVE: discarding it is what made the retry a bypass.'
        );

        // Re-submitting Place Order hits the same wall: the persisted record refuses repeatedly.
        $this->assertRetryIsStillRefused('could not be verified');

        // And running setup() again does not launder it: the obligation survives the re-seed, so the
        // "fail, re-run setup, place" route is closed too.
        $this->simulateNewRequest();
        $this->management()->setup($this->transientToken());

        $reseeded = $this->loadRecord(self::GUEST_QUOTE_ID);

        self::assertNull($reseeded['verdict'] ?? null, 'A re-seed clears the verdict...');
        self::assertSame(
            'failed',
            $reseeded['obligation'] ?? null,
            '...but NOT the obligation: that is what stops the re-setup bypass.'
        );

        $this->assertRetryIsStillRefused('verify your payment again');

        self::assertSame(
            [],
            $stub->getCallsMatching(self::PAYMENTS_PATH),
            'None of the retries may reach the gateway.'
        );
    }

    /**
     * Assert the persisted record refuses the charge again, twice over.
     *
     * Deliberately NOT a second placeCart(): a failed CartManagement::placeOrder() unwinds through
     * QuoteManagement's rollback path, which leaves the integration framework's own isolation
     * transaction unusable ("Rolled back transaction has not been completed correctly") -- a harness
     * limit, not product behavior. This asserts the same thing one layer down and against the REAL
     * record as it sits in the database: reloaded from scratch, the gate refuses, and refuses again.
     *
     * @param string $expectedMessage
     * @return void
     */
    private function assertRetryIsStillRefused(string $expectedMessage): void
    {
        /** @var BindingValidator $validator */
        $validator = $this->objectManager->create(BindingValidator::class);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $this->simulateNewRequest();

            $payment = $this->loadQuote(self::GUEST_QUOTE_ID)->getPayment();
            $record  = $this->objectManager->get(Persistor::class)->load($payment);

            self::assertNotNull($record, 'Attempt ' . $attempt . ': the record must still be there.');

            try {
                $validator->resolve(
                    $payment,
                    $this->baseGrandTotal(self::GUEST_QUOTE_ID),
                    (string)$this->loadQuote(self::GUEST_QUOTE_ID)->getBaseCurrencyCode(),
                    (string)($record['binding'] ?? '')
                );
                self::fail('Attempt ' . $attempt . ': re-submitting place must not be a way around this.');
            } catch (CommandException $exception) {
                self::assertStringContainsString($expectedMessage, $exception->getMessage());
            }
        }
    }

    /**
     * A subscription rebill is merchant-initiated: there is no cardholder to authenticate, so the
     * record must not be consulted at all.
     *
     * The record left here is deliberately stale (authenticated at the original cart total). If the
     * MIT branch consulted the validator it would throw and kill the rebill; if it consumed the record
     * it would attach a CAVV that authenticated different money. Neither may happen.
     *
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_active 1
     * @magentoConfigFixture default_store payment/paradoxlabs_cybersource/cardinal_card_types AE,VI,MC,DI,JCB,DN
     * @magentoDataFixture ParadoxLabs_CyberSource::Test/Integration/_files/cybersource_payer_auth_stored_card_quote.php
     * @return void
     */
    public function testSubscriptionRebillIgnoresALingeringRecord(): void
    {
        $stub = $this->registerRestStub($this->payerAuthResponder('case-2-1-success.json'));

        $this->management(self::STORED_QUOTE_ID)->setup(null, $this->storedCardHash());
        $this->simulateNewRequest();
        $this->management(self::STORED_QUOTE_ID)->authenticate($this->browserInfo());

        // Stale it, then run the cart as a scheduled rebill.
        $this->changeCartQuantity(self::STORED_QUOTE_ID, 3);
        $this->flagPayment(self::STORED_QUOTE_ID, 'is_subscription_generated', 1);

        $this->simulateNewRequest();

        $order = $this->placeCart(self::STORED_QUOTE_ID);

        self::assertSame(Order::STATE_PROCESSING, $order->getState(), 'The rebill must place.');

        $payments = $stub->getCallsMatching(self::PAYMENTS_PATH);
        self::assertCount(1, $payments);
        self::assertArrayNotHasKey(
            'consumerAuthenticationInformation',
            $payments[0]['params'],
            'An unattended rebill must not carry a cardholder authentication.'
        );
        self::assertSame(
            'recurring',
            $payments[0]['params']['processingInformation']['commerceIndicator'] ?? null,
            'The MIT indicator must survive: nothing from the record may overwrite it.'
        );
        self::assertNotNull(
            $order->getPayment()->getAdditionalInformation(Persistor::PERSIST_KEY),
            'A record the MIT branch never consults must be left exactly as it was found.'
        );
    }

    /**
     * Run setup() and authenticate() as two separate requests against the fixture cart.
     *
     * @return PayerAuthResultInterface
     */
    private function runSetupAndAuthenticate(): PayerAuthResultInterface
    {
        $setupResult = $this->management()->setup($this->transientToken());

        self::assertFalse($setupResult->getSkipped(), 'Payer Auth is enabled, so setup must not skip.');

        $this->simulateNewRequest();

        return $this->management()->authenticate($this->browserInfo());
    }

    /**
     * Place a fixture cart through the real checkout submit.
     *
     * @param string $reservedOrderId
     * @return Order
     */
    private function placeCart(string $reservedOrderId = self::GUEST_QUOTE_ID): Order
    {
        $quote = $this->loadQuote($reservedOrderId);

        $orderId = $this->objectManager->create(CartManagementInterface::class)
            ->placeOrder((int)$quote->getId());

        self::assertGreaterThan(0, (int)$orderId, 'The cart must have placed an order.');

        /** @var Order $order */
        $order = $this->objectManager->create(OrderCollectionFactory::class)
            ->create()
            ->addFieldToFilter('entity_id', (int)$orderId)
            ->setPageSize(1)
            ->getFirstItem();

        return $order->load((int)$order->getId());
    }

    /**
     * Get a Payer Auth service bound to the fixture cart, as a new request would resolve it.
     *
     * @param string $reservedOrderId
     * @return Management
     */
    private function management(string $reservedOrderId = self::GUEST_QUOTE_ID): Management
    {
        /** @var Management $management */
        $management = $this->objectManager->create(Management::class);

        return $management->setQuote($this->loadQuote($reservedOrderId));
    }

    /**
     * Read the transient token the fixture put on the cart, exactly as the checkout client posted it.
     *
     * @return string
     */
    private function transientToken(): string
    {
        $token = $this->loadQuote(self::GUEST_QUOTE_ID)
            ->getPayment()
            ->getAdditionalInformation('transient_token');

        self::assertIsString($token, 'The fixture must have seeded a transient token.');

        return $token;
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
     * Responder mapping /risk/v1 onto the pinned fixtures and everything else onto an approval.
     *
     * @param string $authenticationFixture Fixture file for authentications and authentication-results.
     * @return callable
     */
    private function payerAuthResponder(string $authenticationFixture): callable
    {
        $payments = $this->sequencedResponder();

        return function (string $method, string $path, array $params) use ($authenticationFixture, $payments): array {
            return match ($path) {
                Setup::SETUP_PATH => $this->replyFixture('setups-201.json'),
                Authenticate::AUTHENTICATIONS_PATH,
                Results::RESULTS_PATH => $this->replyFixture($authenticationFixture),
                default => $payments($method, $path, $params),
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
     * Load a fixture quote, always fresh: memoized quotes would hide missing persistence.
     *
     * @param string $reservedOrderId
     * @return Quote
     */
    private function loadQuote(string $reservedOrderId): Quote
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(QuoteCollectionFactory::class)
            ->create()
            ->addFieldToFilter('reserved_order_id', $reservedOrderId)
            ->setPageSize(1)
            ->getFirstItem();

        self::assertGreaterThan(0, (int)$quote->getId(), 'Fixture should have created cart ' . $reservedOrderId);

        return $quote->load((int)$quote->getId());
    }

    /**
     * Read the hash of the vault card the stored-card cart pays with, as the client holds it.
     *
     * @return string
     */
    private function storedCardHash(): string
    {
        $cardId = (int)$this->loadQuote(self::STORED_QUOTE_ID)->getPayment()->getData('tokenbase_id');

        self::assertGreaterThan(0, $cardId, 'The fixture must have vaulted a card on the cart.');

        $hash = (string)$this->objectManager->get(CardRepositoryInterface::class)
            ->getById((string)$cardId)
            ->getHash();

        self::assertNotEmpty($hash, 'A vaulted card always carries a hash.');

        return $hash;
    }

    /**
     * Change the quantity on a cart's only item and recollect, as a cart edit would.
     *
     * @param string $reservedOrderId
     * @param float $qty
     * @return void
     */
    private function changeCartQuantity(string $reservedOrderId, float $qty): void
    {
        $quote = $this->loadQuote($reservedOrderId);
        $items = $quote->getAllVisibleItems();

        self::assertNotEmpty($items, 'The cart must have an item to change.');

        // The item row is written through its own resource: saving the quote alone leaves the changed
        // qty in memory, and the cart would silently place at its original total.
        $items[0]->setQty($qty);
        $this->objectManager->get(QuoteItemResource::class)->save($items[0]);

        $quote->setTotalsCollectedFlag(false)->collectTotals();

        $this->objectManager->create(CartRepositoryInterface::class)->save($quote);
    }

    /**
     * Set a flag in a cart payment's additional_information, as an upstream module would.
     *
     * @param string $reservedOrderId
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function flagPayment(string $reservedOrderId, string $key, mixed $value): void
    {
        $quote = $this->loadQuote($reservedOrderId);
        $quote->getPayment()->setAdditionalInformation($key, $value);

        $this->objectManager->create(CartRepositoryInterface::class)->save($quote);
    }

    /**
     * Read the persisted payer_auth record straight from the cart.
     *
     * @param string $reservedOrderId
     * @return array|null
     */
    private function loadRecord(string $reservedOrderId): ?array
    {
        return $this->objectManager->get(Persistor::class)
            ->load($this->loadQuote($reservedOrderId)->getPayment());
    }

    /**
     * Get a cart's base grand total, formatted the way the record stores it.
     *
     * @param string $reservedOrderId
     * @return string
     */
    private function baseGrandTotal(string $reservedOrderId): string
    {
        return number_format((float)$this->loadQuote($reservedOrderId)->getBaseGrandTotal(), 2, '.', '');
    }

    /**
     * Drop the quote registries too, on top of the sales ones the trait clears.
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
     * Give the shared request the browser facts a real checkout request would carry.
     *
     * authenticate() derives userAgentBrowserValue, httpAcceptBrowserValue and ipAddress from the live
     * HTTP request rather than from client input, and AuthenticationRequest rejects an incomplete
     * deviceInformation tree outright. The integration harness builds its request from an empty
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
}
