<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Frontend;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Frontend
 */
class FrontendTest extends TestCase
{
    private Frontend $handler;
    private Config|MockObject $configMock;
    private Rest|MockObject $restMock;
    private Address|MockObject $addressHelperMock;
    private CheckoutSession|MockObject $checkoutSessionMock;
    private CustomerSession|MockObject $customerSessionMock;
    private StoreManagerInterface|MockObject $storeManagerMock;
    private HttpRequest|MockObject $requestMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->restMock = $this->createMock(Rest::class);
        $this->addressHelperMock = $this->createMock(Address::class);
        $this->checkoutSessionMock = $this->createMock(CheckoutSession::class);
        $this->customerSessionMock = $this->createMock(CustomerSession::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->requestMock = $this->createMock(HttpRequest::class);

        $requestFactory = $this->createMock(CaptureContextRequestFactory::class);
        $requestFactory->method('create')
            ->willReturnCallback(fn() => new CaptureContextRequest());

        // Config passthrough defaults.
        $this->configMock->method('getUcClientVersion')->willReturn('0.34');
        $this->configMock->method('getUcTargetOrigins')->willReturn(['https://shop.example.com']);
        $this->configMock->method('getUcAllowedCardNetworks')->willReturn(['VISA']);
        $this->configMock->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $this->configMock->method('getUcBillingType')->willReturn('FULL');
        $this->configMock->method('getUcLocale')->willReturn('en_US');
        $this->configMock->method('getUcCountry')->willReturn('US');
        $this->configMock->method('getUcCompleteMandateType')->willReturn('AUTH');
        $this->configMock->method('is3dsEnabled')->willReturn(false);
        $this->configMock->method('isDecisionManagerEnabled')->willReturn(false);

        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getBaseCurrencyCode')->willReturn('USD');
        $store->method('getBaseUrl')->willReturn('https://store.example.com/');
        $this->storeManagerMock->method('getStore')->willReturn($store);

        $this->handler = new Frontend(
            $this->configMock,
            $this->restMock,
            new Sanitizer(),
            $this->addressHelperMock,
            $requestFactory,
            $this->createMock(LoggerInterface::class),
            $this->checkoutSessionMock,
            $this->customerSessionMock,
            $this->storeManagerMock,
            $this->requestMock,
        );
    }

    public function testBuildRequestSourcesAmountCurrencyAndBillToFromQuote(): void
    {
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);

        $billingAddress = $this->makeQuoteBillingAddress();
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBillingAddress', 'getData'])
            ->getMock();
        // getBaseGrandTotal/getBaseCurrencyCode are magic on Quote; they route through
        // DataObject::__call to the stubbed getData() below.
        $quote->method('getData')->willReturnMap([
            ['base_grand_total', null, 24.0],
            ['base_currency_code', null, 'USD'],
        ]);
        $quote->method('getBillingAddress')->willReturn($billingAddress);

        $this->checkoutSessionMock->method('getQuoteId')->willReturn(99);
        $this->checkoutSessionMock->method('getQuote')->willReturn($quote);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
        $this->assertSame('123', $result['orderInformation']['billTo']['buildingNumber']);
        // UC save-card checkbox is never requested; the module payment[save] checkbox governs vaulting.
        $this->assertArrayNotHasKey('requestSaveCard', $result['captureMandate']);
    }

    public function testBuildRequestIgnoresActiveCartForPaymentinfoSource(): void
    {
        // Customer card management (source=paymentinfo) with an unrelated active cart in session:
        // still a zero-amount tokenization-only context — never priced at the cart total.
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);
        $this->requestMock->method('getParam')->willReturnMap([
            ['source', null, 'paymentinfo'],
        ]);
        // The currency path still reads the quote (base currency is the same either way); only
        // the amount must ignore it.
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData'])
            ->getMock();
        $quote->method('getData')->willReturnMap([
            ['base_grand_total', null, 24.0],
            ['base_currency_code', null, 'USD'],
        ]);
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(99);
        $this->checkoutSessionMock->method('getQuote')->willReturn($quote);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('SAVE_CARD', $result['buttonType']);
        $this->assertArrayNotHasKey('completeMandate', $result);
    }

    /**
     * DEFECT DOCUMENTATION (Bug B) -- INVERT THIS TEST WHEN THE DEFECT IS FIXED.
     *
     * A real cart totalling 0.00 (100%-off coupon, free trial, zero-priced item) is not the
     * add-card case. Frontend::getAmount() (Model/Service/UnifiedCheckout/Frontend.php:79-100)
     * returns null only for source=paymentinfo or a missing quote id; with a live quote it does
     * `(string)$total`, and (string)0.0 is "0" -- a non-null string.
     *
     * CaptureContext::buildRequest() then takes the has-an-amount branch, so the context goes out
     * with completeMandate and totalAmount "0.00", which UC rejects with a 400 "Invalid total
     * amount" (documented by the in-file bisect note at CaptureContext.php:160-166). The shopper
     * sees a generic decline and cannot check out.
     *
     * The companion skipped test states the correct behavior.
     */
    public function testBuildRequestForZeroTotalCartSendsUnpayableZeroTotal(): void
    {
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);

        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBillingAddress', 'getData'])
            ->getMock();
        $quote->method('getData')->willReturnMap([
            ['base_grand_total', null, 0.0],
            ['base_currency_code', null, 'USD'],
        ]);
        $quote->method('getBillingAddress')->willReturn($this->makeQuoteBillingAddress());

        $this->checkoutSessionMock->method('getQuoteId')->willReturn(99);
        $this->checkoutSessionMock->method('getQuote')->willReturn($quote);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);

        $result = $this->handler->buildRequest()->toArray();

        // DEFECT: getAmount() returned "0" (not null), so the 0.01 tokenization fallback is skipped
        // and a mandate is attached to an amount UC will not accept.
        $this->assertSame('0.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertArrayHasKey('completeMandate', $result);
    }

    /**
     * CORRECT BEHAVIOR (Bug B) -- unskip when a $0 cart is handled.
     *
     * A $0 checkout still has to collect and vault a card (the order may rebill later, and the
     * gateway runs its own $0 exchange auth server-side). The context should therefore degrade to
     * the tokenization-only shape already used for add-card: the 0.01 minimum UC accepts, with
     * completeMandate omitted so nothing can be charged against it.
     */
    public function testBuildRequestForZeroTotalCartShouldFallBackToTokenizationOnlyContext(): void
    {
        $this->markTestSkipped(
            'Bug B: Frontend::getAmount() (Model/Service/UnifiedCheckout/Frontend.php:79-100) returns'
            . ' the string "0" for a $0 quote rather than null, so CaptureContext::buildRequest()'
            . ' (CaptureContext.php:120-172) attaches completeMandate and sends totalAmount "0.00".'
            . ' UC rejects that with 400 "Invalid total amount" (bisect note at CaptureContext.php'
            . ':160-166), so no $0 cart can render the drop-in.'
        );

        // @phpstan-ignore-next-line deadCode.unreachable -- retained for the post-fix unskip.
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);

        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBillingAddress', 'getData'])
            ->getMock();
        $quote->method('getData')->willReturnMap([
            ['base_grand_total', null, 0.0],
            ['base_currency_code', null, 'USD'],
        ]);
        $quote->method('getBillingAddress')->willReturn($this->makeQuoteBillingAddress());

        $this->checkoutSessionMock->method('getQuoteId')->willReturn(99);
        $this->checkoutSessionMock->method('getQuote')->willReturn($quote);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertArrayNotHasKey('completeMandate', $result);
    }

    public function testBuildRequestBillingOnlyWhenNoQuote(): void
    {
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(null);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $result = $this->handler->buildRequest()->toArray();

        // No quote -> tokenization-only context: UC demands a positive totalAmount, so the 0.01
        // minimum is sent (store-default currency) and completeMandate is omitted; billingType still set.
        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertArrayNotHasKey('completeMandate', $result);
        $this->assertSame('FULL', $result['captureMandate']['billingType']);
        $this->assertSame('US', $result['country']);
        $this->assertArrayNotHasKey('requestSaveCard', $result['captureMandate']);
    }

    public function testBuildRequestDerivesTargetOriginFromStoreBaseUrlWithoutConfig(): void
    {
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(null);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $config = $this->createMock(Config::class);
        $config->method('getUcClientVersion')->willReturn('0.34');
        $config->method('getUcTargetOrigins')->willReturn([]);
        $config->method('getUcAllowedCardNetworks')->willReturn(['VISA']);
        $config->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $config->method('getUcBillingType')->willReturn('FULL');
        $config->method('getUcLocale')->willReturn('en_US');
        $config->method('getUcCountry')->willReturn('US');
        $config->method('getUcCompleteMandateType')->willReturn('AUTH');
        $config->method('is3dsEnabled')->willReturn(false);
        $config->method('isDecisionManagerEnabled')->willReturn(false);

        $requestFactory = $this->createMock(CaptureContextRequestFactory::class);
        $requestFactory->method('create')
            ->willReturnCallback(fn() => new CaptureContextRequest());

        $handler = new Frontend(
            $config,
            $this->restMock,
            new Sanitizer(),
            $this->addressHelperMock,
            $requestFactory,
            $this->createMock(LoggerInterface::class),
            $this->checkoutSessionMock,
            $this->customerSessionMock,
            $this->storeManagerMock,
            $this->requestMock,
        );

        $result = $handler->buildRequest()->toArray();

        // Fresh install (no configured extras) must still send the store's own origin.
        $this->assertSame(['https://store.example.com'], $result['targetOrigins']);
    }

    public function testBuildRequestMergesDerivedStoreOriginWithConfiguredExtras(): void
    {
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(null);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $result = $this->handler->buildRequest()->toArray();

        // Derived store origin first, configured extras after, deduped.
        $this->assertSame(
            ['https://store.example.com', 'https://shop.example.com'],
            $result['targetOrigins']
        );
    }

    public function testBuildRequestSourcesBillToFromPostBillingInputOverQuote(): void
    {
        // POST billing input takes precedence over the session quote. The checkout JS posts the
        // quote.billingAddress() fields with camelCase keys (countryId/regionId/regionCode — see
        // getCaptureContextParams() in paradoxlabs_cybersource.js); normalizeBillingInputKeys()
        // must add the snake_case keys the address helper reads, or country/region are dropped.
        $this->requestMock->method('getPostValue')->with('billing')->willReturn([
            'firstname' => 'Bob',
            'lastname' => 'Smith',
            'street' => ['500 Market St'],
            'city' => 'Philadelphia',
            'regionCode' => 'PA',
            'regionId' => 51,
            'region' => 'Pennsylvania',
            'postcode' => '19106',
            'countryId' => 'US',
            'telephone' => '5559876543',
        ]);
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(null);
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $region = $this->createMock(RegionInterface::class);
        $region->method('getRegionCode')->willReturn('PA');

        $inputAddress = $this->createMock(AddressInterface::class);
        $inputAddress->method('getFirstname')->willReturn('Bob');
        $inputAddress->method('getLastname')->willReturn('Smith');
        $inputAddress->method('getStreet')->willReturn(['500 Market St']);
        $inputAddress->method('getCity')->willReturn('Philadelphia');
        $inputAddress->method('getRegion')->willReturn($region);
        $inputAddress->method('getPostcode')->willReturn('19106');
        $inputAddress->method('getCountryId')->willReturn('US');
        $inputAddress->method('getTelephone')->willReturn('5559876543');

        $this->addressHelperMock->expects($this->once())
            ->method('buildAddressFromInput')
            ->with($this->callback(static function (array $billing): bool {
                // normalizeBillingInputKeys() adds the flat snake_case keys; the string region
                // name passes through untouched (it is not the nested GraphQL region object).
                return ($billing['country_id'] ?? null) === 'US'
                    && ($billing['region_id'] ?? null) === 51
                    && ($billing['region_code'] ?? null) === 'PA'
                    && ($billing['region'] ?? null) === 'Pennsylvania';
            }))
            ->willReturn($inputAddress);

        $result = $this->handler->buildRequest()->toArray();

        $this->assertSame('Bob', $result['orderInformation']['billTo']['firstName']);
        $this->assertSame('500', $result['orderInformation']['billTo']['buildingNumber']);
        $this->assertSame('PA', $result['orderInformation']['billTo']['administrativeArea']);
        $this->assertSame('US', $result['orderInformation']['billTo']['country']);
    }

    /**
     * Build a Magento customer-data billing address (as returned by getDataModel()).
     */
    private function makeQuoteBillingAddress(): QuoteAddress|MockObject
    {
        $region = $this->createMock(RegionInterface::class);
        $region->method('getRegionCode')->willReturn('CA');

        $customerAddress = $this->createMock(AddressInterface::class);
        $customerAddress->method('getFirstname')->willReturn('Jane');
        $customerAddress->method('getLastname')->willReturn('Doe');
        $customerAddress->method('getStreet')->willReturn(['123 Main St']);
        $customerAddress->method('getCity')->willReturn('Los Angeles');
        $customerAddress->method('getRegion')->willReturn($region);
        $customerAddress->method('getPostcode')->willReturn('90210');
        $customerAddress->method('getCountryId')->willReturn('US');
        $customerAddress->method('getTelephone')->willReturn('5551234567');

        $quoteAddress = $this->createMock(QuoteAddress::class);
        $quoteAddress->method('getDataModel')->willReturn($customerAddress);
        $quoteAddress->method('getEmail')->willReturn('jane@example.com');

        return $quoteAddress;
    }
}
