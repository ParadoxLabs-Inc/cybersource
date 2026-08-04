<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Backend\Model\Session\Quote as BackendSession;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Backend;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Helper\Data;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Backend
 */
class BackendTest extends TestCase
{
    private Rest|MockObject $restMock;
    private Address|MockObject $addressHelperMock;
    private CaptureContextRequestFactory|MockObject $requestFactoryMock;
    private Data|MockObject $tokenbaseHelperMock;
    private BackendSession|MockObject $backendSessionMock;
    private StoreManagerInterface|MockObject $storeManagerMock;
    private HttpRequest|MockObject $requestMock;
    private BackendUrlInterface|MockObject $backendUrlMock;

    protected function setUp(): void
    {
        $this->restMock = $this->createMock(Rest::class);
        $this->addressHelperMock = $this->createMock(Address::class);
        // getQuoteId is a magic accessor, but unlike DataObject subclasses, SessionManager's
        // __call() delegates to session storage (unavailable with the constructor disabled) -
        // there is no getData() to route through. Stub __call() itself instead (real declared
        // method) so getQuoteId() can be configured per test without addMethods(). getQuote is
        // a real declared method.
        $this->backendSessionMock = $this->getMockBuilder(BackendSession::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getQuote', '__call'])
            ->getMock();
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->requestMock = $this->createMock(HttpRequest::class);
        $this->backendUrlMock = $this->createMock(BackendUrlInterface::class);

        $this->requestFactoryMock = $this->createMock(CaptureContextRequestFactory::class);
        $this->requestFactoryMock->method('create')
            ->willReturnCallback(fn() => new CaptureContextRequest());

        // Admin add-card billing-only context: no order-create quote in session.
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getStoreId')->willReturn(1);
        $customer->method('getEmail')->willReturn('jane@example.com');

        $this->tokenbaseHelperMock = $this->createMock(Data::class);
        $this->tokenbaseHelperMock->method('getCurrentCustomer')->willReturn($customer);

        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getBaseCurrencyCode')->willReturn('USD');
        $this->storeManagerMock->method('getStore')->willReturn($store);

        // Backend base URL includes the admin front name path; only the origin should survive.
        $this->backendUrlMock->method('getBaseUrl')->willReturn('https://admin.example.com/backend/admin/');
    }

    public function testBuildRequestDerivesTargetOriginFromAdminBaseUrlWithoutConfig(): void
    {
        $handler = $this->makeHandler([]);

        $result = $handler->buildRequest()->toArray();

        // Fresh install (no configured extras) must still send the admin origin.
        $this->assertSame(['https://admin.example.com'], $result['targetOrigins']);
    }

    public function testBuildRequestMergesDerivedAdminOriginWithConfiguredExtras(): void
    {
        $handler = $this->makeHandler(['https://admin.example.com', 'https://headless.example.com']);

        $result = $handler->buildRequest()->toArray();

        // Derived admin origin first, configured extras after, deduped.
        $this->assertSame(
            ['https://admin.example.com', 'https://headless.example.com'],
            $result['targetOrigins']
        );
    }

    public function testBuildRequestSourcesAmountCurrencyAndBillToFromOrderCreateQuote(): void
    {
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);

        $billingAddress = $this->makeQuoteBillingAddress();
        $quote = $this->makeQuote(57.0, 'CAD', $billingAddress);
        $quote->method('getStoreId')->willReturn(1);

        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(99);
        $this->backendSessionMock->method('getQuote')->willReturn($quote);

        $result = $this->makeHandler([])->buildRequest()->toArray();

        $this->assertSame('57.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('CAD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
        $this->assertSame('123', $result['orderInformation']['billTo']['buildingNumber']);
        $this->assertSame('jane@example.com', $result['orderInformation']['billTo']['email']);
        // UC save-card checkbox is never requested; the module payment[save] checkbox governs vaulting.
        $this->assertArrayNotHasKey('requestSaveCard', $result['captureMandate']);
    }

    public function testBuildRequestIgnoresOrderCreateQuoteForPaymentinfoSource(): void
    {
        // Admin customer card management (source=paymentinfo) while an order-create quote is open
        // in the same session: still a zero-amount tokenization-only context.
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);
        $this->requestMock->method('getParam')->willReturnMap([
            ['source', null, 'paymentinfo'],
        ]);

        $billingAddress = $this->makeQuoteBillingAddress();
        $quote = $this->makeQuote(57.0, 'CAD', $billingAddress);
        $quote->method('getStoreId')->willReturn(1);
        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(99);
        $this->backendSessionMock->method('getQuote')->willReturn($quote);

        $result = $this->makeHandler([])->buildRequest()->toArray();

        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('SAVE_CARD', $result['buttonType']);
        $this->assertArrayNotHasKey('completeMandate', $result);
    }

    /**
     * REGRESSION (Bug B, fixed): a $0 admin order-create quote degrades to the minimum amount.
     *
     * Admin order create for a $0 total (100%-off, free replacement) hit the same trap as the
     * storefront. Backend::getAmount() (Model/Service/UnifiedCheckout/Backend.php:93-114) returns
     * null only for source=paymentinfo or a missing order-create quote; with a live quote it does
     * `(string)$total`, and (string)0.0 is "0" -- non-null. buildRequest() sent totalAmount "0.00",
     * which UC rejects with a 400 "Invalid total amount", dead-ending the admin card form on a
     * generic decline. A non-positive amount now degrades to the tokenization-only context the
     * admin add-card case already uses.
     */
    public function testBuildRequestForZeroTotalOrderCreateQuoteFallsBackToMinimumAmount(): void
    {
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);

        $quote = $this->makeQuote(0.0, 'USD', $this->makeQuoteBillingAddress());
        $quote->method('getStoreId')->willReturn(1);

        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(99);
        $this->backendSessionMock->method('getQuote')->willReturn($quote);

        $result = $this->makeHandler([])->buildRequest()->toArray();

        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertArrayNotHasKey('completeMandate', $result);
    }

    public function testBuildRequestBillingOnlyWithStoreDefaultCurrencyWhenNoQuote(): void
    {
        // Admin add-card: no order-create quote in session -> tokenization-only context. UC demands
        // a positive totalAmount, so the 0.01 minimum is sent (store-default currency) and
        // completeMandate is omitted.
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);
        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(null);

        $result = $this->makeHandler([])->buildRequest()->toArray();

        $this->assertSame('0.01', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertArrayNotHasKey('completeMandate', $result);
        $this->assertSame('FULL', $result['captureMandate']['billingType']);
        $this->assertArrayNotHasKey('requestSaveCard', $result['captureMandate']);
        // Admin add-card pane: SAVE_CARD button, review step suppressed.
        $this->assertSame('SAVE_CARD', $result['buttonType']);
        $this->assertFalse($result['captureMandate']['showConfirmationStep']);
    }

    public function testBuildRequestForOrderCreateQuoteUsesCardPaymentNotAutoPlaceMapping(): void
    {
        // Admin order create has an amount, but isCustomerCheckout() is overridden to false: the
        // drop-in click only tokenizes (the admin clicks Submit Order; nothing is paid on the
        // click), so PAY/auto-place must not apply even with uc_auto_place_order enabled.
        $this->requestMock->method('getPostValue')->with('billing')->willReturn(null);

        $quote = $this->makeQuote(57.0, 'USD', $this->makeQuoteBillingAddress());
        $quote->method('getStoreId')->willReturn(1);

        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(99);
        $this->backendSessionMock->method('getQuote')->willReturn($quote);

        $result = $this->makeHandler([], true)->buildRequest()->toArray();

        $this->assertSame('CARD_PAYMENT', $result['buttonType']);
        $this->assertFalse($result['captureMandate']['showConfirmationStep']);
    }

    public function testBuildRequestSourcesBillToFromPostBillingInputOverQuote(): void
    {
        // POST billing input takes precedence over the session quote's billing address.
        $this->requestMock->method('getPostValue')->with('billing')->willReturn([
            'firstName' => 'Bob',
            'lastName' => 'Smith',
            'street' => ['500 Market St'],
            'city' => 'Philadelphia',
            'regionCode' => 'PA',
            'postcode' => '19106',
            'countryId' => 'US',
            'telephone' => '5559876543',
        ]);

        $inputAddress = $this->makeCustomerAddress('Bob', 'Smith', '500 Market St', 'PA');
        $this->addressHelperMock->expects($this->once())
            ->method('buildAddressFromInput')
            ->with($this->callback(static function (array $billing): bool {
                // normalizeBillingInputKeys must add snake_case keys the address helper reads.
                return ($billing['country_id'] ?? null) === 'US'
                    && ($billing['region_code'] ?? null) === 'PA';
            }))
            ->willReturn($inputAddress);

        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(null);

        $result = $this->makeHandler([])->buildRequest()->toArray();

        $this->assertSame('Bob', $result['orderInformation']['billTo']['firstName']);
        $this->assertSame('500', $result['orderInformation']['billTo']['buildingNumber']);
        $this->assertSame('PA', $result['orderInformation']['billTo']['administrativeArea']);
    }

    public function testGetCurrencyCodeFallsBackToStoreDefaultWhenNoQuote(): void
    {
        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(null);

        $method = new \ReflectionMethod(Backend::class, 'getCurrencyCode');

        // No quote -> upper-cased store base currency (USD from setUp).
        $this->assertSame('USD', $method->invoke($this->makeHandler([])));
    }

    public function testGetAmountReturnsNullWhenQuoteTotalMissing(): void
    {
        $quote = $this->makeQuote(null, 'USD', $this->createMock(QuoteAddress::class));
        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(99);
        $this->backendSessionMock->method('getQuote')->willReturn($quote);

        $method = new \ReflectionMethod(Backend::class, 'getAmount');

        // A quote whose base grand total is null yields a billing-only (null) amount.
        $this->assertNull($method->invoke($this->makeHandler([])));
    }

    public function testGetStoreIdFallsBackToCurrentCustomerWhenNoQuote(): void
    {
        // No quote -> store ID comes from the registered current customer (1 from setUp).
        $this->backendSessionMock->method('__call')->with('getQuoteId', [])->willReturn(null);

        $method = new \ReflectionMethod(Backend::class, 'getStoreId');

        $this->assertSame(1, $method->invoke($this->makeHandler([])));
    }

    /**
     * Build an order-create quote mock with the given totals/currency/billing address.
     *
     * @param float|null $baseGrandTotal
     * @param string $baseCurrencyCode
     * @param QuoteAddress|MockObject $billingAddress
     * @return Quote|MockObject
     */
    private function makeQuote(
        ?float $baseGrandTotal,
        string $baseCurrencyCode,
        QuoteAddress|MockObject $billingAddress
    ): Quote|MockObject {
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBillingAddress', 'getStoreId', 'getData'])
            ->getMock();
        // getBaseGrandTotal/getBaseCurrencyCode are magic on Quote; they route through
        // DataObject::__call to the stubbed getData() below.
        $quote->method('getData')->willReturnMap([
            ['base_grand_total', null, $baseGrandTotal],
            ['base_currency_code', null, $baseCurrencyCode],
        ]);
        $quote->method('getBillingAddress')->willReturn($billingAddress);

        return $quote;
    }

    /**
     * Build a Magento customer-data billing address (as returned by getDataModel()).
     *
     * @return QuoteAddress|MockObject
     */
    private function makeQuoteBillingAddress(): QuoteAddress|MockObject
    {
        $quoteAddress = $this->createMock(QuoteAddress::class);
        $quoteAddress->method('getDataModel')
            ->willReturn($this->makeCustomerAddress('Jane', 'Doe', '123 Main St', 'CA'));
        $quoteAddress->method('getEmail')->willReturn('jane@example.com');

        return $quoteAddress;
    }

    /**
     * Build a customer-data address with the given identity/street/region.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $street1
     * @param string $regionCode
     * @return AddressInterface|MockObject
     */
    private function makeCustomerAddress(
        string $firstName,
        string $lastName,
        string $street1,
        string $regionCode
    ): AddressInterface|MockObject {
        $region = $this->createMock(RegionInterface::class);
        $region->method('getRegionCode')->willReturn($regionCode);

        $address = $this->createMock(AddressInterface::class);
        $address->method('getFirstname')->willReturn($firstName);
        $address->method('getLastname')->willReturn($lastName);
        $address->method('getStreet')->willReturn([$street1]);
        $address->method('getCity')->willReturn('Anytown');
        $address->method('getRegion')->willReturn($region);
        $address->method('getPostcode')->willReturn('90210');
        $address->method('getCountryId')->willReturn('US');
        $address->method('getTelephone')->willReturn('5551234567');

        return $address;
    }

    /**
     * Build a Backend handler with the given configured "Additional Target Origins" extras.
     *
     * @param string[] $configuredOrigins
     * @param bool $autoPlace
     * @return Backend
     */
    private function makeHandler(
        array $configuredOrigins,
        bool $autoPlace = false,
    ): Backend {
        $config = $this->createMock(Config::class);
        $config->method('getUcClientVersion')->willReturn('0.34');
        $config->method('getUcTargetOrigins')->willReturn($configuredOrigins);
        $config->method('getUcAllowedCardNetworks')->willReturn(['VISA']);
        $config->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $config->method('getUcBillingType')->willReturn('FULL');
        $config->method('getUcLocale')->willReturn('en_US');
        $config->method('getUcCountry')->willReturn('US');
        $config->method('getUcCompleteMandateType')->willReturn('AUTH');
        $config->method('isPayerAuthEnabled')->willReturn(false);
        $config->method('isDecisionManagerEnabled')->willReturn(false);
        $config->method('isUcAutoPlaceOrderEnabled')->willReturn($autoPlace);

        return new Backend(
            $config,
            $this->restMock,
            new Sanitizer(),
            $this->addressHelperMock,
            $this->requestFactoryMock,
            $this->createMock(LoggerInterface::class),
            $this->tokenbaseHelperMock,
            $this->backendSessionMock,
            $this->storeManagerMock,
            $this->requestMock,
            $this->backendUrlMock,
        );
    }
}
