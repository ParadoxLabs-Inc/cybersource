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

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote\Payment as QuotePayment;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterfaceFactory;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterfaceFactory;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Authenticate;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\AuthenticationResult;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\BrowserInfo;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\Result;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\SetupResult;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Results;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Setup;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\TransientTokenReader;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ParadoxLabs\CyberSource\Helper\Data as CyberSourceHelper;

/**
 * @see \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ManagementTest extends TestCase
{
    private const STORE_ID = 3;
    private const BASE_URL = 'https://store.example.com/';
    private const TOKEN = 'header.payload.signature';

    /**
     * @var Config|MockObject
     */
    private $config;

    /**
     * @var Setup|MockObject
     */
    private $setupService;

    /**
     * @var Authenticate|MockObject
     */
    private $authenticateService;

    /**
     * @var Results|MockObject
     */
    private $resultsService;

    /**
     * @var Persistor|MockObject
     */
    private $persistor;

    /**
     * @var TransientTokenReader|MockObject
     */
    private $tokenReader;

    /**
     * @var CardRepositoryInterface|MockObject
     */
    private $cardRepository;

    /**
     * @var CheckoutSession|MockObject
     */
    private $checkoutSession;

    /**
     * @var HttpRequest|MockObject
     */
    private $httpRequest;

    /**
     * @var RemoteAddress|MockObject
     */
    private $remoteAddress;

    /**
     * @var Quote|MockObject
     */
    private $quote;

    /**
     * @var QuotePayment|MockObject
     */
    private $payment;

    /**
     * @var Management
     */
    private $management;

    /**
     * @var bool
     */
    private bool $payerAuthEnabled = true;

    /**
     * @var string[]
     */
    private array $excludedTypes = [];

    /**
     * @var CardInterface|null
     */
    private $storedCard;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config->method('isPayerAuthEnabled')
            ->willReturnCallback(fn(): bool => $this->payerAuthEnabled);
        $this->config->method('isPayerAuthEnabledForType')
            ->willReturnCallback(
                fn(string $ccType): bool => $this->payerAuthEnabled
                    && !in_array($ccType, $this->excludedTypes, true)
            );

        $this->setupService        = $this->createMock(Setup::class);
        $this->authenticateService = $this->createMock(Authenticate::class);
        $this->resultsService      = $this->createMock(Results::class);
        $this->persistor           = $this->createMock(Persistor::class);
        $this->tokenReader         = $this->createMock(TransientTokenReader::class);
        $this->cardRepository      = $this->createMock(CardRepositoryInterface::class);
        $this->checkoutSession     = $this->createMock(CheckoutSession::class);
        $this->httpRequest         = $this->createMock(HttpRequest::class);
        $this->remoteAddress       = $this->createMock(RemoteAddress::class);

        $this->cardRepository->method('getByHash')->willReturnCallback(fn(): CardInterface => $this->resolveCard());
        $this->cardRepository->method('getById')->willReturnCallback(fn(): CardInterface => $this->resolveCard());

        $this->persistor->method('cardBinding')
            ->willReturnCallback(static fn($id): string => Persistor::BINDING_CARD_PREFIX . $id);

        $this->httpRequest->method('getHeader')->willReturnMap([
            ['Accept', false, 'text/html,application/xhtml+xml'],
            ['User-Agent', false, 'Mozilla/5.0 (RealBrowser)'],
        ]);
        $this->remoteAddress->method('getRemoteAddress')->willReturn('203.0.113.9');

        $this->quote   = $this->quoteMock();
        $this->payment = $this->createMock(QuotePayment::class);
        $this->quote->method('getId')->willReturn(1234);
        $this->quote->method('getStoreId')->willReturn(self::STORE_ID);
        $this->quote->method('getPayment')->willReturn($this->payment);
        $this->quote->method('getCustomerId')->willReturn(42);
        $this->quote->method('getBaseGrandTotal')->willReturn(24.0);
        $this->quote->method('getBaseCurrencyCode')->willReturn('usd');
        $this->quote->method('getBillingAddress')->willReturn($this->billingAddress());

        $this->checkoutSession->method('getQuoteId')->willReturn(99);

        $cartRepository = $this->createMock(CartRepositoryInterface::class);
        $cartRepository->method('getActive')->willReturn($this->quote);

        $store = $this->createMock(Store::class);
        $store->method('getBaseUrl')->willReturn(self::BASE_URL);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $setupResultFactory = $this->createMock(PayerAuthSetupResultInterfaceFactory::class);
        $setupResultFactory->method('create')->willReturnCallback(static fn(): SetupResult => new SetupResult());

        $resultFactory = $this->createMock(PayerAuthResultInterfaceFactory::class);
        $resultFactory->method('create')->willReturnCallback(static fn(): Result => new Result());

        $this->management = new Management(
            $this->config,
            $this->setupService,
            $this->authenticateService,
            $this->resultsService,
            $this->requestFactory(SetupRequestFactory::class, SetupRequest::class),
            $this->requestFactory(AuthenticationRequestFactory::class, AuthenticationRequest::class),
            $this->requestFactory(ResultsRequestFactory::class, ResultsRequest::class),
            $this->persistor,
            $this->tokenReader,
            $this->cardRepository,
            $cartRepository,
            $this->checkoutSession,
            $this->createMock(UserContextInterface::class),
            $storeManager,
            $this->remoteAddress,
            $this->httpRequest,
            new Sanitizer(),
            $setupResultFactory,
            $resultFactory,
            $this->createMock(CyberSourceHelper::class)
        );
    }

    public function testSetupSkipsWhenPayerAuthIsDisabled(): void
    {
        $this->payerAuthEnabled = false;

        $this->setupService->expects($this->never())->method('execute');
        // A record left over from before the merchant disabled Payer Auth must not survive to
        // hard-block the cart at place time (the BindingValidator no longer self-heals).
        $this->persistor->expects($this->once())->method('clear');

        $result = $this->management->setup(self::TOKEN);

        $this->assertTrue($result->getSkipped());
        $this->assertNull($result->getAccessToken());
        $this->assertNull($result->getDeviceDataCollectionUrl());
    }

    public function testAuthenticateSkipsWhenPayerAuthIsDisabled(): void
    {
        $this->payerAuthEnabled = false;

        $this->authenticateService->expects($this->never())->method('execute');
        $this->persistor->expects($this->once())->method('clear');

        $this->assertSame(
            PayerAuthResultInterface::STATUS_SKIPPED,
            $this->management->authenticate($this->browserInfo())->getStatus()
        );
    }

    /**
     * @dataProvider ambiguousSetupInputProvider
     * @param string|null $transientToken
     * @param string|null $cardHash
     * @return void
     */
    public function testSetupRequiresExactlyOneCardReference(?string $transientToken, ?string $cardHash): void
    {
        $this->expectException(InputException::class);

        $this->management->setup($transientToken, $cardHash);
    }

    /**
     * @return array<string, array{0: string|null, 1: string|null}>
     */
    public static function ambiguousSetupInputProvider(): array
    {
        return [
            'neither' => [null, null],
            'both' => [self::TOKEN, 'abc123'],
            'empty strings' => ['', ''],
        ];
    }

    public function testSetupSkipsWhenTheCardTypeIsExcluded(): void
    {
        $this->excludedTypes = ['AE'];

        $this->tokenReader->method('readJti')->willReturn('jti-abc');
        $this->tokenReader->method('read')->willReturn(['cc_type' => 'AE']);

        $this->setupService->expects($this->never())->method('execute');
        $this->persistor->expects($this->never())->method('saveReferenceId');
        $this->persistor->expects($this->once())->method('clear');

        $this->assertTrue($this->management->setup(self::TOKEN)->getSkipped());
    }

    public function testSetupSeedsTheRecordAndReturnsTheCollectionHandles(): void
    {
        $this->tokenReader->method('readJti')->willReturn('jti-abc');
        $this->tokenReader->method('read')->willReturn(['cc_type' => 'VI']);

        $request = null;
        $this->setupService->method('execute')->willReturnCallback(
            function (SetupRequest $setupRequest, ?int $storeId) use (&$request): array {
                $request = $setupRequest;
                $this->assertSame(self::STORE_ID, $storeId);

                return [
                    'consumerAuthenticationInformation' => [
                        'accessToken' => 'the.access.token',
                        'deviceDataCollectionUrl' => 'https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect',
                        'referenceId' => 'ref-123',
                    ],
                ];
            }
        );

        $this->persistor->expects($this->once())
            ->method('saveReferenceId')
            ->with($this->payment, 'ref-123', 'jti-abc', self::TOKEN);

        $result = $this->management->setup(self::TOKEN);

        $this->assertFalse($result->getSkipped());
        $this->assertSame('the.access.token', $result->getAccessToken());
        $this->assertSame(
            'https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect',
            $result->getDeviceDataCollectionUrl()
        );
        $this->assertSame(self::TOKEN, $request->getTransientToken());
        $this->assertSame('1234', $request->getClientReferenceCode());
    }

    public function testSetupStoredCardUsesThePaymentInstrumentAndCardBinding(): void
    {
        $this->storedCard = $this->card();

        $request = null;
        $this->setupService->method('execute')->willReturnCallback(
            static function (SetupRequest $setupRequest) use (&$request): array {
                $request = $setupRequest;

                return ['consumerAuthenticationInformation' => ['referenceId' => 'ref-123']];
            }
        );

        $this->persistor->expects($this->once())
            ->method('saveReferenceId')
            ->with($this->payment, 'ref-123', 'card:7', null);

        $this->assertFalse($this->management->setup(null, 'hash-abc')->getSkipped());
        $this->assertSame('PI-1234567890', $request->getPaymentInstrumentId());
        $this->assertNull($request->getTransientToken());
    }

    public function testSetupSkipsForALegacyCardWithNoPaymentInstrument(): void
    {
        $this->storedCard = $this->card(['payment_id' => '']);

        $this->setupService->expects($this->never())->method('execute');
        $this->persistor->expects($this->once())->method('clear');

        $this->assertTrue($this->management->setup(null, 'hash-abc')->getSkipped());
    }

    public function testStoredCardsAreRejectedOnGuestCarts(): void
    {
        $quote = $this->quoteMock();
        $quote->method('getStoreId')->willReturn(self::STORE_ID);
        $quote->method('getCustomerId')->willReturn(null);
        $this->management->setQuote($quote);

        $this->cardRepository->expects($this->never())->method('getByHash');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Stored cards are not available for guest checkout.');

        $this->management->setup(null, 'hash-abc');
    }

    /**
     * A hash belonging to someone else must be indistinguishable from one that does not exist.
     *
     * @return void
     */
    public function testWrongOwnerCardHashFailsClosedAsNotFound(): void
    {
        $this->storedCard = $this->card(['customer_id' => 4242]);

        $wrongOwnerMessage = $this->caughtMessage(fn() => $this->management->setup(null, 'hash-abc'));

        $this->storedCard = null;
        $missingMessage   = $this->caughtMessage(fn() => $this->management->setup(null, 'hash-abc'));

        $this->assertSame('The requested card could not be found.', $wrongOwnerMessage);
        $this->assertSame($wrongOwnerMessage, $missingMessage);
    }

    public function testInactiveOrForeignMethodCardsFailClosed(): void
    {
        $this->storedCard = $this->card(['method' => 'paradoxlabs_stripe']);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('The requested card could not be found.');

        $this->management->setup(null, 'hash-abc');
    }

    public function testAuthenticateRequiresAPriorSetup(): void
    {
        $this->persistor->method('load')->willReturn(null);

        $this->authenticateService->expects($this->never())->method('execute');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Payer Authentication has not been set up for this cart. Run setup first.');

        $this->management->authenticate($this->browserInfo());
    }

    public function testAuthenticateSkipsAndClearsWhenTheCardTypeIsExcluded(): void
    {
        $this->excludedTypes = ['AE'];

        $this->persistor->method('load')->willReturn($this->record());
        $this->tokenReader->method('read')->willReturn(['cc_type' => 'AE']);

        $this->authenticateService->expects($this->never())->method('execute');
        $this->persistor->expects($this->never())->method('saveResult');
        $this->persistor->expects($this->once())->method('clear');

        $this->assertSame(
            PayerAuthResultInterface::STATUS_SKIPPED,
            $this->management->authenticate($this->browserInfo())->getStatus()
        );
    }

    public function testAuthenticateSourcesTotalsBillToAndServerDerivedDeviceFields(): void
    {
        $this->persistor->method('load')->willReturn($this->record());
        $this->tokenReader->method('read')->willReturn(['cc_type' => 'VI']);

        $request = $this->captureAuthenticationRequest();

        $this->persistor->expects($this->once())
            ->method('saveResult')
            ->with($this->payment, $this->anything(), '24.00', 'USD', 'jti-abc');

        $this->management->authenticate($this->browserInfo());

        $emitted = $request()->toArray();

        $this->assertSame('24.00', $emitted['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $emitted['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $emitted['orderInformation']['billTo']['firstName']);
        $this->assertSame('OH', $emitted['orderInformation']['billTo']['administrativeArea']);
        $this->assertSame('ref-123', $emitted['consumerAuthenticationInformation']['referenceId']);
        $this->assertSame(['transientToken' => self::TOKEN], $emitted['tokenInformation']);

        $device = $emitted['deviceInformation'];
        $this->assertSame('Mozilla/5.0 (RealBrowser)', $device['userAgentBrowserValue']);
        $this->assertSame('text/html,application/xhtml+xml', $device['httpAcceptBrowserValue']);
        $this->assertSame('203.0.113.9', $device['ipAddress']);
        $this->assertSame('en-US', $device['httpBrowserLanguage']);
        $this->assertSame('false', $device['httpBrowserJavaEnabled']);
        $this->assertSame('true', $device['httpBrowserJavaScriptEnabled']);
        $this->assertSame('24', $device['httpBrowserColorDepth']);
        $this->assertSame('1080', $device['httpBrowserScreenHeight']);
        $this->assertSame('1920', $device['httpBrowserScreenWidth']);
        $this->assertSame('300', $device['httpBrowserTimeDifference']);
    }

    /**
     * Incomplete browser data must reach the request DTO as a missing field, so its guard fires
     * (thin device data is a silent 3DS bypass) rather than being quietly filled in here.
     *
     * @return void
     */
    public function testIncompleteBrowserInfoIsRejected(): void
    {
        $this->persistor->method('load')->willReturn($this->record());
        $request = $this->captureAuthenticationRequest();

        $this->management->authenticate($this->browserInfo()->setScreenWidth(null));

        $this->expectException(InputException::class);
        $this->expectExceptionMessageMatches('/complete browser information.*httpBrowserScreenWidth/');

        $request()->toArray();
    }

    public function testAuthenticateDefaultsToTheModulesOwnReturnRoute(): void
    {
        $this->persistor->method('load')->willReturn($this->record());
        $request = $this->captureAuthenticationRequest();

        $this->management->authenticate($this->browserInfo());

        $this->assertSame(
            'https://store.example.com/paradoxlabs-cybersource/payerauth/return',
            $request()->getReturnUrl()
        );
    }

    public function testAuthenticateAcceptsASameHostSecureReturnUrl(): void
    {
        $this->persistor->method('load')->willReturn($this->record());
        $request = $this->captureAuthenticationRequest();

        $this->management->authenticate($this->browserInfo(), 'https://store.example.com/checkout/3ds');

        $this->assertSame('https://store.example.com/checkout/3ds', $request()->getReturnUrl());
    }

    /**
     * @dataProvider badReturnUrlProvider
     * @param string $returnUrl
     * @return void
     */
    public function testAuthenticateRejectsUnsafeReturnUrls(string $returnUrl): void
    {
        $this->persistor->method('load')->willReturn($this->record());

        $this->authenticateService->expects($this->never())->method('execute');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('The return URL must be a secure URL on this store.');

        $this->management->authenticate($this->browserInfo(), $returnUrl);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function badReturnUrlProvider(): array
    {
        return [
            'foreign host' => ['https://evil.example.net/collect'],
            'insecure same host' => ['http://store.example.com/checkout/3ds'],
            'protocol relative' => ['//evil.example.net/collect'],
            'relative path' => ['/checkout/3ds'],
            'host suffix trick' => ['https://store.example.com.evil.net/collect'],
            'javascript' => ['javascript:alert(1)'],
        ];
    }

    /**
     * The GraphQL surface validates foreign origins itself, then uses the internal entry point.
     *
     * @return void
     */
    public function testPreValidatedReturnUrlsBypassTheSameHostRule(): void
    {
        $this->persistor->method('load')->willReturn($this->record());
        $request = $this->captureAuthenticationRequest();

        $this->management->authenticateWithValidatedReturnUrl(
            $this->browserInfo(),
            'https://pwa.example.net/3ds'
        );

        $this->assertSame('https://pwa.example.net/3ds', $request()->getReturnUrl());
    }

    /**
     * @dataProvider verdictProvider
     * @param Verdict $verdict
     * @param string $status
     * @return void
     */
    public function testVerdictsMapToClientStatuses(Verdict $verdict, string $status): void
    {
        $this->persistor->method('load')->willReturn($this->record());
        $this->captureAuthenticationRequest(
            new AuthenticationResult($verdict, [
                'acsUrl' => 'https://acs.example.com/step-up',
                'pareq' => 'eyJjaGFsbGVuZ2UiOiJ0cnVlIn0=',
                'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'authenticationTransactionId' => 'txn-1',
            ])
        );

        $result = $this->management->authenticate($this->browserInfo());

        $this->assertSame($status, $result->getStatus());

        if ($status === PayerAuthResultInterface::STATUS_CHALLENGE) {
            $this->assertSame('https://acs.example.com/step-up', $result->getAcsUrl());
            $this->assertSame('eyJjaGFsbGVuZ2UiOiJ0cnVlIn0=', $result->getPareq());
        } else {
            $this->assertNull($result->getAcsUrl());
            $this->assertNull($result->getPareq());
        }
    }

    /**
     * @return array<string, array{0: Verdict, 1: string}>
     */
    public static function verdictProvider(): array
    {
        return [
            'authenticated' => [Verdict::AUTHENTICATED, PayerAuthResultInterface::STATUS_SUCCESS],
            'attempted' => [Verdict::ATTEMPTED, PayerAuthResultInterface::STATUS_SUCCESS],
            // Unavailable is success-shaped for the client: the order proceeds, without a shift.
            'unavailable' => [Verdict::UNAVAILABLE, PayerAuthResultInterface::STATUS_SUCCESS],
            'failed' => [Verdict::FAILED, PayerAuthResultInterface::STATUS_FAILED],
            'challenge' => [Verdict::CHALLENGE, PayerAuthResultInterface::STATUS_CHALLENGE],
        ];
    }

    public function testTheResultDtoCannotCarryAuthorizationData(): void
    {
        $methods = get_class_methods(PayerAuthResultInterface::class);
        sort($methods);

        $this->assertSame(
            ['getAcsUrl', 'getPareq', 'getStatus', 'setAcsUrl', 'setPareq', 'setStatus'],
            $methods
        );
    }

    public function testAuthenticateRebindsAndRechecksStoredCardOwnership(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['binding' => 'card:7']));
        $this->storedCard = $this->card(['customer_id' => 4242]);

        $this->authenticateService->expects($this->never())->method('execute');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('The requested card could not be found.');

        $this->management->authenticate($this->browserInfo());
    }

    public function testAuthenticateSendsThePaymentInstrumentForAStoredCard(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['binding' => 'card:7']));
        $this->storedCard = $this->card();

        $request = $this->captureAuthenticationRequest();

        $this->management->authenticate($this->browserInfo());

        $emitted = $request()->toArray();

        $this->assertSame(['paymentInstrument' => ['id' => 'PI-1234567890']], $emitted['paymentInformation']);
        $this->assertArrayNotHasKey('tokenInformation', $emitted);
    }

    /**
     * @dataProvider unfinalizableRecordProvider
     * @param array<string, mixed>|null $record
     * @return void
     */
    public function testFinalizeRefusesWithoutAPendingChallenge(?array $record): void
    {
        $this->persistor->method('load')->willReturn($record);

        $this->resultsService->expects($this->never())->method('execute');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('There is no pending authentication challenge to complete.');

        $this->management->finalize();
    }

    /**
     * @return array<string, array{0: array<string, mixed>|null}>
     */
    public static function unfinalizableRecordProvider(): array
    {
        $challenged = [
            'auth_transaction_id' => 'txn-1',
            'verdict' => 'challenge',
            'amount' => '24.00',
            'currency' => 'USD',
            'binding' => 'jti-abc',
        ];

        return [
            'no record' => [null],
            'no transaction id' => [array_merge($challenged, ['auth_transaction_id' => null])],
            'not challenged' => [array_merge($challenged, ['verdict' => 'authenticated'])],
            'no amount' => [array_merge($challenged, ['amount' => null])],
            'no currency' => [array_merge($challenged, ['currency' => null])],
            'no binding' => [array_merge($challenged, ['binding' => null])],
        ];
    }

    public function testFinalizeReclassifiesAgainstTheAuthenticatedAmount(): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record([
                'auth_transaction_id' => 'txn-1',
                'verdict' => Verdict::CHALLENGE->value,
                'amount' => '24.00',
                'currency' => 'USD',
            ])
        );

        $request = null;
        $this->resultsService->method('execute')->willReturnCallback(
            function (ResultsRequest $resultsRequest, ?int $storeId) use (&$request): AuthenticationResult {
                $request = $resultsRequest;
                $this->assertSame(self::STORE_ID, $storeId);

                return new AuthenticationResult(Verdict::AUTHENTICATED, ['cavv' => 'secret']);
            }
        );

        // The amount/currency/binding are re-persisted from the record, never re-read from the cart:
        // a cart edited during the challenge must not silently re-point the authentication.
        $this->persistor->expects($this->once())
            ->method('saveResult')
            ->with($this->payment, $this->anything(), '24.00', 'USD', 'jti-abc');

        $result = $this->management->finalize();

        $this->assertSame(PayerAuthResultInterface::STATUS_SUCCESS, $result->getStatus());
        $this->assertSame('txn-1', $request->getAuthenticationTransactionId());
    }

    public function testUnexpectedFailuresSurfaceAsAGenericError(): void
    {
        $this->tokenReader->method('readJti')->willReturn('jti-abc');
        $this->setupService->method('execute')->willThrowException(new \RuntimeException('SQLSTATE[42S02] boom'));

        try {
            $this->management->setup(self::TOKEN);
            $this->fail('Expected a LocalizedException.');
        } catch (LocalizedException $exception) {
            $this->assertSame(LocalizedException::class, get_class($exception));
            $this->assertSame(
                'Payer authentication is temporarily unavailable. Please try again.',
                $exception->getMessage()
            );
            $this->assertStringNotContainsString('42S02', $exception->getMessage());
        }
    }

    public function testSetupResultDtoRoundTrips(): void
    {
        $result = (new SetupResult())->setSkipped(false)
            ->setAccessToken('token')
            ->setDeviceDataCollectionUrl('https://ddc.example.com');

        $this->assertInstanceOf(PayerAuthSetupResultInterface::class, $result);
        $this->assertFalse($result->getSkipped());
        $this->assertSame('token', $result->getAccessToken());
        $this->assertSame('https://ddc.example.com', $result->getDeviceDataCollectionUrl());
    }

    /**
     * Capture the AuthenticationRequest handed to the service, and control the verdict it returns.
     *
     * @param AuthenticationResult|null $result
     * @return callable(): AuthenticationRequest
     */
    private function captureAuthenticationRequest(?AuthenticationResult $result = null): callable
    {
        $captured = null;
        $result ??= new AuthenticationResult(Verdict::AUTHENTICATED, ['cavv' => 'AAABCZIhcQ==']);

        $this->authenticateService->method('execute')->willReturnCallback(
            static function (AuthenticationRequest $request) use (&$captured, $result): AuthenticationResult {
                $captured = $request;

                return $result;
            }
        );

        return static function () use (&$captured): AuthenticationRequest {
            return $captured;
        };
    }

    /**
     * Build a persisted record for a new-card (transient-token) attempt.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function record(array $overrides = []): array
    {
        return array_merge(
            [
                'reference_id' => 'ref-123',
                'auth_transaction_id' => null,
                'verdict' => null,
                'ca' => [],
                'amount' => null,
                'currency' => null,
                'binding' => 'jti-abc',
                'transient_token' => self::TOKEN,
                'created_at' => time(),
            ],
            $overrides
        );
    }

    /**
     * Build a stored CyberSource card owned by the cart's customer.
     *
     * @param array<string, mixed> $overrides
     * @return CardInterface|MockObject
     */
    private function card(array $overrides = [])
    {
        $data = array_merge(
            [
                'id' => 7,
                'customer_id' => 42,
                'active' => 1,
                'method' => 'paradoxlabs_cybersource',
                'payment_id' => 'PI-1234567890',
                'cc_type' => 'VI',
            ],
            $overrides
        );

        $card = $this->createMock(CardInterface::class);
        $card->method('getId')->willReturn($data['id']);
        $card->method('getCustomerId')->willReturn($data['customer_id']);
        $card->method('getActive')->willReturn($data['active']);
        $card->method('getMethod')->willReturn($data['method']);
        $card->method('getPaymentId')->willReturn($data['payment_id']);
        $card->method('getAdditional')->with('cc_type')->willReturn($data['cc_type']);

        return $card;
    }

    /**
     * Build a quote mock, with its magic data getters made stubbable.
     *
     * @return Quote|MockObject
     */
    private function quoteMock()
    {
        return $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'getStoreId', 'getPayment', 'getBillingAddress'])
            ->addMethods(['getCustomerId', 'getBaseGrandTotal', 'getBaseCurrencyCode', 'getCustomerEmail'])
            ->getMock();
    }

    /**
     * Build the quote billing address.
     *
     * @return QuoteAddress|MockObject
     */
    private function billingAddress()
    {
        $address = $this->createMock(QuoteAddress::class);
        $address->method('getFirstname')->willReturn('Jane');
        $address->method('getLastname')->willReturn('Doe');
        $address->method('getStreet')->willReturn(['123 Main St', 'Suite 4']);
        $address->method('getCity')->willReturn('Columbus');
        $address->method('getRegionCode')->willReturn('oh');
        $address->method('getPostcode')->willReturn('43004');
        $address->method('getCountryId')->willReturn('us');
        $address->method('getEmail')->willReturn('jane@example.com');
        $address->method('getTelephone')->willReturn('6145551234');

        return $address;
    }

    /**
     * Build a complete browser profile.
     *
     * @return PayerAuthBrowserInfoInterface
     */
    private function browserInfo(): PayerAuthBrowserInfoInterface
    {
        return (new BrowserInfo())->setLanguage('en-US')
            ->setJavaEnabled(false)
            ->setJavaScriptEnabled(true)
            ->setColorDepth(24)
            ->setScreenHeight(1080)
            ->setScreenWidth(1920)
            ->setTimeDifference(300);
    }

    /**
     * Build a request-DTO factory mock producing real DTOs.
     *
     * @param string $factoryClass
     * @param string $dtoClass
     * @return MockObject
     */
    private function requestFactory(string $factoryClass, string $dtoClass): MockObject
    {
        $factory = $this->createMock($factoryClass);
        $factory->method('create')->willReturnCallback(static fn(): object => new $dtoClass());

        return $factory;
    }

    /**
     * Resolve the stored card the repository should return, or fail as not found.
     *
     * @return CardInterface
     * @throws NoSuchEntityException
     */
    private function resolveCard(): CardInterface
    {
        if ($this->storedCard === null) {
            throw new NoSuchEntityException(__('Card with hash "%1" does not exist.', 'hash-abc'));
        }

        return $this->storedCard;
    }

    /**
     * Run a callable expected to throw, and return the exception message.
     *
     * @param callable $callback
     * @return string
     */
    private function caughtMessage(callable $callback): string
    {
        try {
            $callback();
        } catch (LocalizedException $exception) {
            return $exception->getMessage();
        }

        $this->fail('Expected an exception.');
    }
}
