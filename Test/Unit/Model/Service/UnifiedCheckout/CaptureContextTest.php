<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\RegionInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CaptureContext;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CaptureContext
 */
class CaptureContextTest extends TestCase
{
    private TestableCaptureContext $handler;
    private Config|MockObject $configMock;
    private Rest|MockObject $restMock;
    private Sanitizer $sanitizer;
    private Address|MockObject $addressHelperMock;
    private CaptureContextRequestFactory|MockObject $requestFactoryMock;
    private LoggerInterface|MockObject $loggerMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->restMock = $this->createMock(Rest::class);
        $this->sanitizer = new Sanitizer();
        $this->addressHelperMock = $this->createMock(Address::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->requestFactoryMock = $this->createMock(CaptureContextRequestFactory::class);
        $this->requestFactoryMock->method('create')
            ->willReturnCallback(fn() => new CaptureContextRequest());

        // Config defaults.
        $this->configMock->method('getUcClientVersion')->willReturn('0.34');
        $this->configMock->method('getUcTargetOrigins')->willReturn(['https://shop.example.com']);
        $this->configMock->method('getUcAllowedCardNetworks')->willReturn(['VISA', 'MASTERCARD']);
        $this->configMock->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $this->configMock->method('getUcBillingType')->willReturn('FULL');
        $this->configMock->method('getUcLocale')->willReturn('en_US');
        $this->configMock->method('getUcCountry')->willReturn('US');
        $this->configMock->method('getUcCompleteMandateType')->willReturn('AUTH');
        $this->configMock->method('isPayerAuthEnabled')->willReturn(false);
        $this->configMock->method('isDecisionManagerEnabled')->willReturn(false);

        $this->handler = new TestableCaptureContext(
            $this->configMock,
            $this->restMock,
            $this->sanitizer,
            $this->addressHelperMock,
            $this->requestFactoryMock,
            $this->loggerMock,
        );
    }

    public function testBuildRequestAppliesConfigToFieldTree(): void
    {
        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('0.34', $result['clientVersion']);
        $this->assertSame(['https://shop.example.com'], $result['targetOrigins']);
        $this->assertSame(['VISA', 'MASTERCARD'], $result['allowedCardNetworks']);
        $this->assertSame(['PANENTRY'], $result['allowedPaymentTypes']);
        $this->assertSame('en_US', $result['locale']);
        $this->assertSame('FULL', $result['captureMandate']['billingType']);
        // Contact collection is always suppressed: Magento collects these and sends them with the txn.
        $this->assertFalse($result['captureMandate']['requestEmail']);
        $this->assertFalse($result['captureMandate']['requestPhone']);
        $this->assertFalse($result['captureMandate']['requestShipping']);
        // requestSaveCard is never sent: the module's payment[save] checkbox is the sole consent point.
        $this->assertArrayNotHasKey('requestSaveCard', $result['captureMandate']);
        $this->assertSame('AUTH', $result['completeMandate']['type']);
        $this->assertFalse($result['completeMandate']['decisionManager']);
        $this->assertFalse($result['completeMandate']['consumerAuthentication']);
    }

    /**
     * The review step is off unless the merchant asks for it, independently of auto-place. With
     * auto-place on the final click places the order, so the button is labeled PAY — inert while
     * the step is off, since UC only ever renders buttonType on that screen. The BIN is always
     * requested in the token.
     */
    public function testBuildRequestWithAutoPlaceSuppressesReviewStepAndUsesPayButton(): void
    {
        $this->configMock->method('isUcAutoPlaceOrderEnabled')->willReturn(true);

        $result = $this->handler->buildRequest()->toArray();

        $this->assertFalse($result['captureMandate']['showConfirmationStep']);
        $this->assertSame('PAY', $result['buttonType']);
        $this->assertTrue($result['transientTokenResponseOptions']['includeCardPrefix']);
    }

    /**
     * With auto-place off the final click hands back to Magento's Place Order button, so the label
     * is CHECKOUT_AND_CONTINUE. The review step still stays off by default.
     */
    public function testBuildRequestWithoutAutoPlaceSuppressesReviewStepAndUsesContinueButton(): void
    {
        $this->configMock->method('isUcAutoPlaceOrderEnabled')->willReturn(false);

        $result = $this->handler->buildRequest()->toArray();

        $this->assertFalse($result['captureMandate']['showConfirmationStep']);
        $this->assertSame('CHECKOUT_AND_CONTINUE', $result['buttonType']);
        $this->assertTrue($result['transientTokenResponseOptions']['includeCardPrefix']);
    }

    /**
     * A merchant who turns the review step on gets it at customer checkout, which is also the only
     * place the buttonType label surfaces.
     */
    public function testBuildRequestWithReviewStepEnabledKeepsConfirmationStep(): void
    {
        $this->configMock->method('isUcAutoPlaceOrderEnabled')->willReturn(true);
        $this->configMock->method('isUcReviewStepEnabled')->willReturn(true);

        $result = $this->handler->buildRequest()->toArray();

        $this->assertTrue($result['captureMandate']['showConfirmationStep']);
        $this->assertSame('PAY', $result['buttonType']);
    }

    /**
     * A no-amount context is add-card (customer payment-info / admin card management): SAVE_CARD
     * button with the confirmation step suppressed (both surfaces submit right after tokenization,
     * so UC's review pane is a pure extra click), regardless of the auto-place or review-step config.
     */
    public function testBuildRequestForAddCardContextUsesSaveCardButtonNotAutoPlaceMapping(): void
    {
        $this->configMock->method('isUcAutoPlaceOrderEnabled')->willReturn(true);
        $this->configMock->method('isUcReviewStepEnabled')->willReturn(true);
        $this->handler->amount = null;

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('SAVE_CARD', $result['buttonType']);
        $this->assertFalse($result['captureMandate']['showConfirmationStep']);
        $this->assertTrue($result['transientTokenResponseOptions']['includeCardPrefix']);
    }

    /**
     * A with-amount context that is not a customer checkout (admin order create) only tokenizes —
     * the admin reviews and submits the order themselves — so PAY would be a mislabel and UC's
     * review step redundant: CARD_PAYMENT button, confirmation step suppressed, regardless of
     * the auto-place or review-step config.
     */
    public function testBuildRequestForNonCustomerCheckoutUsesCardPaymentWithoutConfirmationStep(): void
    {
        $this->configMock->method('isUcAutoPlaceOrderEnabled')->willReturn(true);
        $this->configMock->method('isUcReviewStepEnabled')->willReturn(true);
        $this->handler->customerCheckout = false;

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('CARD_PAYMENT', $result['buttonType']);
        $this->assertFalse($result['captureMandate']['showConfirmationStep']);
        $this->assertTrue($result['transientTokenResponseOptions']['includeCardPrefix']);
    }

    public function testBuildRequestMapsPaymentActionToCompleteMandateType(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('getUcClientVersion')->willReturn('0.34');
        $config->method('getUcTargetOrigins')->willReturn([]);
        $config->method('getUcAllowedCardNetworks')->willReturn([]);
        $config->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $config->method('getUcBillingType')->willReturn('FULL');
        $config->method('getUcLocale')->willReturn('en_US');
        $config->method('getUcCountry')->willReturn('US');
        $config->method('isPayerAuthEnabled')->willReturn(false);
        $config->method('isDecisionManagerEnabled')->willReturn(false);
        // payment_action=authorize_capture -> CAPTURE
        $config->method('getUcCompleteMandateType')->willReturn('CAPTURE');

        $handler = new TestableCaptureContext(
            $config,
            $this->restMock,
            $this->sanitizer,
            $this->addressHelperMock,
            $this->requestFactoryMock,
        );

        $result = $handler->buildRequest()->toArray();

        $this->assertSame('CAPTURE', $result['completeMandate']['type']);
    }

    public function testBuildRequestAppliesEnabled3dsAndDecisionManagerFlags(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('getUcClientVersion')->willReturn('0.34');
        $config->method('getUcTargetOrigins')->willReturn([]);
        $config->method('getUcAllowedCardNetworks')->willReturn([]);
        $config->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $config->method('getUcBillingType')->willReturn('FULL');
        $config->method('getUcLocale')->willReturn('en_US');
        $config->method('getUcCountry')->willReturn('US');
        $config->method('getUcCompleteMandateType')->willReturn('AUTH');
        $config->method('isPayerAuthEnabled')->willReturn(true);
        $config->method('isDecisionManagerEnabled')->willReturn(true);

        $handler = new TestableCaptureContext(
            $config,
            $this->restMock,
            $this->sanitizer,
            $this->addressHelperMock,
            $this->requestFactoryMock,
        );

        $result = $handler->buildRequest()->toArray();

        $this->assertTrue($result['completeMandate']['consumerAuthentication']);
        $this->assertTrue($result['completeMandate']['decisionManager']);
    }

    public function testBuildRequestSourcesAmountAndCurrency(): void
    {
        $this->handler->amount = '24.00';
        $this->handler->currencyCode = 'USD';

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
    }

    public function testBuildRequestSendsMinimumAmountForBillingOnlyContext(): void
    {
        $this->handler->amount = null;
        $this->handler->billTo = ['firstName' => 'Jane', 'country' => 'US'];

        $result = $this->handler->buildRequest()->toArray();

        // UC requires a POSITIVE totalAmount on every capture context (zero and omission both 400,
        // sandbox-bisected 2026-07-24): no-amount add-card contexts send the 0.01 minimum and omit
        // completeMandate, so nothing can ever be charged against the context.
        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
        $this->assertArrayNotHasKey('completeMandate', $result);
        // billingType still set for the no-amount add-card case.
        $this->assertSame('FULL', $result['captureMandate']['billingType']);
    }

    /**
     * REGRESSION (Bug B, fixed): a $0 cart degrades to the tokenization-only context.
     *
     * A $0 cart (100%-off coupon, free trial, zero-priced configurable) is not the add-card case:
     * Frontend::getAmount()/Backend::getAmount() return null only for source=paymentinfo or a
     * missing quote, so a real quote whose base_grand_total is 0.0 yields the non-null string "0".
     * UC rejects totalAmount 0/0.00 with a 400 "Invalid total amount" in every shape (sandbox
     * bisect, CaptureContext.php in-file note), which surfaced as a generic decline and left the
     * drop-in unable to render at all.
     *
     * buildRequest() now normalizes a non-positive amount to null, so it takes the same
     * tokenization-only path as add-card: the 0.01 minimum UC accepts, and no completeMandate.
     */
    public function testBuildRequestForZeroAmountFallsBackToTokenizationOnlyContext(): void
    {
        // What a $0 quote produces: getAmount() returns the string "0", never null.
        $this->handler->amount = '0';
        $this->handler->billTo = ['firstName' => 'Jane', 'country' => 'US'];

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        // Nothing can be charged against a context with no mandate; the server runs its own $0
        // exchange auth after the drop-in returns the transient token.
        $this->assertArrayNotHasKey('completeMandate', $result);
        $this->assertSame('SAVE_CARD', $result['buttonType']);
    }

    /**
     * A negative total cannot occur through Magento's totals, but must never build a payable
     * context if it somehow does.
     */
    public function testBuildRequestForNegativeAmountFallsBackToTokenizationOnlyContext(): void
    {
        $this->handler->amount = '-5.00';
        $this->handler->billTo = ['firstName' => 'Jane', 'country' => 'US'];

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertArrayNotHasKey('completeMandate', $result);
    }

    public function testBuildRequestUsesBillToCountryWhenPresent(): void
    {
        $this->handler->amount = null;
        $this->handler->billTo = ['country' => 'CA'];

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('CA', $result['country']);
    }

    public function testBuildRequestFallsBackToConfigCountryWithoutBillTo(): void
    {
        $this->handler->amount = null;
        $this->handler->billTo = [];

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('US', $result['country']);
    }

    public function testGenerateScopesStoreIdAndPostsRawToCaptureContextEndpoint(): void
    {
        $this->configMock->expects($this->once())->method('setStoreId')->with(1);
        $this->restMock->expects($this->once())->method('setStoreId')->with(1);

        $this->restMock->expects($this->once())
            ->method('postRaw')
            ->with(
                CaptureContext::CAPTURE_CONTEXT_PATH,
                $this->callback(function ($body): bool {
                    return isset($body['clientVersion']) && $body['captureMandate']['billingType'] === 'FULL';
                })
            )
            ->willReturn('header.payload.signature');

        $result = $this->handler->generate();

        $this->assertSame('header.payload.signature', $result);
    }

    public function testMapBillToBuildsUcFieldTreeWithBuildingNumber(): void
    {
        $region = $this->createMock(RegionInterface::class);
        $region->method('getRegionCode')->willReturn('CA');

        $address = $this->createMock(AddressInterface::class);
        $address->method('getFirstname')->willReturn('Jane');
        $address->method('getLastname')->willReturn('Doe');
        $address->method('getStreet')->willReturn(['123 Main St', 'Suite 4']);
        $address->method('getCity')->willReturn('Los Angeles');
        $address->method('getRegion')->willReturn($region);
        $address->method('getPostcode')->willReturn('90210');
        $address->method('getCountryId')->willReturn('US');
        $address->method('getTelephone')->willReturn('5551234567');

        $result = $this->handler->exposeMapBillTo($address);

        $this->assertSame('Jane', $result['firstName']);
        $this->assertSame('Doe', $result['lastName']);
        $this->assertSame('123 Main St', $result['address1']);
        $this->assertSame('Suite 4', $result['address2']);
        // The separate buildingNumber field is derived from address1's leading digits.
        $this->assertSame('123', $result['buildingNumber']);
        $this->assertSame('Los Angeles', $result['locality']);
        $this->assertSame('CA', $result['administrativeArea']);
        $this->assertSame('90210', $result['postalCode']);
        $this->assertSame('US', $result['country']);
        $this->assertSame('jane@example.com', $result['email']);
    }

    public function testBuildRequestMergesDerivedOriginsBeforeConfigExtras(): void
    {
        $this->handler->derivedOrigins = ['https://store.example.com'];

        $result = $this->handler->buildRequest()->toArray();

        // Derived origin first, configured "Additional Target Origins" extras after.
        $this->assertSame(
            ['https://store.example.com', 'https://shop.example.com'],
            $result['targetOrigins']
        );
    }

    public function testBuildRequestDedupesDerivedAndConfiguredOrigins(): void
    {
        // Config also returns https://shop.example.com (setUp); derived duplicate must collapse.
        $this->handler->derivedOrigins = ['https://shop.example.com'];

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame(['https://shop.example.com'], $result['targetOrigins']);
    }

    public function testBuildRequestNormalizesConfiguredOriginExtras(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('getUcClientVersion')->willReturn('0.34');
        // Config extras must be normalized to browser-origin form (lowercase host, default port
        // stripped, no path) and deduped against the derived origin AFTER normalization.
        $config->method('getUcTargetOrigins')->willReturn([
            'https://EXAMPLE.com:443/',
            'https://Headless.example.com/some/path/',
        ]);
        $config->method('getUcAllowedCardNetworks')->willReturn([]);
        $config->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $config->method('getUcBillingType')->willReturn('FULL');
        $config->method('getUcLocale')->willReturn('en_US');
        $config->method('getUcCountry')->willReturn('US');
        $config->method('getUcCompleteMandateType')->willReturn('AUTH');
        $config->method('isPayerAuthEnabled')->willReturn(false);
        $config->method('isDecisionManagerEnabled')->willReturn(false);

        $handler = new TestableCaptureContext(
            $config,
            $this->restMock,
            $this->sanitizer,
            $this->addressHelperMock,
            $this->requestFactoryMock,
        );
        $handler->derivedOrigins = ['https://example.com'];

        $result = $handler->buildRequest()->toArray();

        $this->assertSame(
            ['https://example.com', 'https://headless.example.com'],
            $result['targetOrigins']
        );
    }

    public function testBuildRequestDropsSchemelessConfigExtrasWithInfoLog(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('getUcClientVersion')->willReturn('0.34');
        // A schemeless entry (no scheme://) must be dropped and logged; a valid entry is kept.
        $config->method('getUcTargetOrigins')->willReturn([
            'headless.example.com',
            'https://valid.example.com/',
        ]);
        $config->method('getUcAllowedCardNetworks')->willReturn([]);
        $config->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $config->method('getUcBillingType')->willReturn('FULL');
        $config->method('getUcLocale')->willReturn('en_US');
        $config->method('getUcCountry')->willReturn('US');
        $config->method('getUcCompleteMandateType')->willReturn('AUTH');
        $config->method('isPayerAuthEnabled')->willReturn(false);
        $config->method('isDecisionManagerEnabled')->willReturn(false);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('headless.example.com'));

        $handler = new TestableCaptureContext(
            $config,
            $this->restMock,
            $this->sanitizer,
            $this->addressHelperMock,
            $this->requestFactoryMock,
            $logger,
        );

        $result = $handler->buildRequest()->toArray();

        // Schemeless entry dropped; valid entry normalized and present.
        $this->assertSame(['https://valid.example.com'], $result['targetOrigins']);
        $this->assertNotContains('headless.example.com', $result['targetOrigins']);
    }

    public function testNormalizeOriginStripsDefaultHttpsPortAndPath(): void
    {
        $this->assertSame(
            'https://shop.example.com',
            $this->handler->exposeNormalizeOrigin('https://shop.example.com:443/checkout/payment/')
        );
    }

    public function testNormalizeOriginKeepsExplicitNonstandardPort(): void
    {
        $this->assertSame(
            'https://shop.example.com:8443',
            $this->handler->exposeNormalizeOrigin('https://shop.example.com:8443/')
        );
    }

    public function testNormalizeOriginStripsPathAndTrailingSlash(): void
    {
        $this->assertSame(
            'https://shop.example.com',
            $this->handler->exposeNormalizeOrigin('https://shop.example.com/some/store/view/')
        );
    }

    public function testNormalizeOriginAllowsHttpLocalhost(): void
    {
        $this->assertSame(
            'http://localhost',
            $this->handler->exposeNormalizeOrigin('http://localhost/')
        );
        $this->assertSame(
            'http://localhost:8080',
            $this->handler->exposeNormalizeOrigin('http://localhost:8080/magento/')
        );
    }

    public function testNormalizeOriginReturnsNullForInvalidUrl(): void
    {
        $this->assertNull($this->handler->exposeNormalizeOrigin(null));
        $this->assertNull($this->handler->exposeNormalizeOrigin(''));
        $this->assertNull($this->handler->exposeNormalizeOrigin('not a url'));
        $this->assertNull($this->handler->exposeNormalizeOrigin('//www.example.com/'));
    }

    public function testMapBillToReturnsEmptyForNullAddress(): void
    {
        $this->assertSame([], $this->handler->exposeMapBillTo(null));
    }

    public function testMapBillToOmitsBuildingNumberWhenNoLeadingDigits(): void
    {
        $region = $this->createMock(RegionInterface::class);
        $region->method('getRegionCode')->willReturn('NY');

        $address = $this->createMock(AddressInterface::class);
        $address->method('getFirstname')->willReturn('Jane');
        $address->method('getLastname')->willReturn('Doe');
        $address->method('getStreet')->willReturn(['Main Street']);
        $address->method('getCity')->willReturn('NYC');
        $address->method('getRegion')->willReturn($region);
        $address->method('getPostcode')->willReturn('10001');
        $address->method('getCountryId')->willReturn('US');
        $address->method('getTelephone')->willReturn('5551234567');

        $result = $this->handler->exposeMapBillTo($address);

        $this->assertArrayNotHasKey('buildingNumber', $result);
        $this->assertSame('Main Street', $result['address1']);
    }

    public function testMapBillToOmitsEmailOnlyWhenEmailIsInvalid(): void
    {
        // Sanitizer::email() throws InputException on an invalid address; mapBillTo() must tolerate
        // that and drop only the 'email' entry, not the rest of the billTo tree.
        $this->handler->email = 'not-an-email';

        $region = $this->createMock(RegionInterface::class);
        $region->method('getRegionCode')->willReturn('CA');

        $address = $this->createMock(AddressInterface::class);
        $address->method('getFirstname')->willReturn('Jane');
        $address->method('getLastname')->willReturn('Doe');
        $address->method('getStreet')->willReturn(['123 Main St']);
        $address->method('getCity')->willReturn('Los Angeles');
        $address->method('getRegion')->willReturn($region);
        $address->method('getPostcode')->willReturn('90210');
        $address->method('getCountryId')->willReturn('US');
        $address->method('getTelephone')->willReturn('5551234567');

        $result = $this->handler->exposeMapBillTo($address);

        $this->assertArrayNotHasKey('email', $result);
        $this->assertSame('Jane', $result['firstName']);
        $this->assertSame('Doe', $result['lastName']);
        $this->assertSame('123 Main St', $result['address1']);
        $this->assertSame('Los Angeles', $result['locality']);
        $this->assertSame('CA', $result['administrativeArea']);
        $this->assertSame('90210', $result['postalCode']);
        $this->assertSame('US', $result['country']);
    }

    public function testMapBillToPreservesValidEmail(): void
    {
        $this->handler->email = 'jane@example.com';

        $result = $this->handler->exposeMapBillTo($this->addressStub());

        $this->assertSame('jane@example.com', $result['email']);
    }

    /**
     * @dataProvider emptyEmailProvider
     */
    #[DataProvider('emptyEmailProvider')]
    public function testMapBillToOmitsEmailWhenNullOrEmpty(?string $email): void
    {
        $this->handler->email = $email;

        $result = $this->handler->exposeMapBillTo($this->addressStub());

        $this->assertArrayNotHasKey('email', $result);
    }

    /**
     * Minimal address mock for tests that only care about the email field of mapBillTo().
     *
     * @return AddressInterface|MockObject
     */
    private function addressStub(): AddressInterface|MockObject
    {
        $region = $this->createMock(RegionInterface::class);
        $region->method('getRegionCode')->willReturn('CA');

        $address = $this->createMock(AddressInterface::class);
        $address->method('getStreet')->willReturn([]);
        $address->method('getRegion')->willReturn($region);

        return $address;
    }

    /**
     * @return array<string, array{0: string|null}>
     */
    public static function emptyEmailProvider(): array
    {
        return [
            'null email' => [null],
            'empty string email' => [''],
        ];
    }
}
