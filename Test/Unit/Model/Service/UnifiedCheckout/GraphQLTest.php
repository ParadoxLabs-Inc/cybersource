<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Store\Model\Store;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\GraphQL;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Model\Api\GraphQL as GraphQLHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\GraphQL
 */
class GraphQLTest extends TestCase
{
    private Rest|MockObject $restMock;
    private Address|MockObject $addressHelperMock;
    private CaptureContextRequestFactory|MockObject $requestFactoryMock;
    private GraphQLHelper|MockObject $graphQLHelperMock;
    private ContextInterface|MockObject $contextMock;

    protected function setUp(): void
    {
        $this->restMock = $this->createMock(Rest::class);
        $this->addressHelperMock = $this->createMock(Address::class);
        $this->graphQLHelperMock = $this->createMock(GraphQLHelper::class);

        $this->requestFactoryMock = $this->createMock(CaptureContextRequestFactory::class);
        $this->requestFactoryMock->method('create')
            ->willReturnCallback(fn() => new CaptureContextRequest());

        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getBaseCurrencyCode')->willReturn('USD');
        $store->method('getBaseUrl')->willReturn('https://store.example.com/');

        // Hand-written stub rather than the raw generated extension class: in CI the class is
        // generated without the store attribute, so getStore() doesn't exist there. See
        // ContextExtensionStub for the full rationale.
        $contextExtension = (new ContextExtensionStub())->setStore($store);

        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->contextMock->method('getExtensionAttributes')->willReturn($contextExtension);
        $this->contextMock->method('getUserId')->willReturn(0);
    }

    public function testBuildRequestDerivesTargetOriginFromStoreBaseUrlWithoutConfig(): void
    {
        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, []);

        $result = $handler->buildRequest()->toArray();

        // No configured extras: only the derived store origin is sent. Headless storefront
        // origins still require the "Additional Target Origins" config.
        $this->assertSame(['https://store.example.com'], $result['targetOrigins']);
    }

    public function testBuildRequestMergesDerivedStoreOriginWithConfiguredExtras(): void
    {
        $handler = $this->makeHandler(['https://store.example.com', 'https://headless.example.com']);
        $handler->setGraphQLContext($this->contextMock, []);

        $result = $handler->buildRequest()->toArray();

        // Derived store origin first, configured headless extras after, deduped.
        $this->assertSame(
            ['https://store.example.com', 'https://headless.example.com'],
            $result['targetOrigins']
        );
    }

    public function testBuildRequestUsesConfiguredOriginsOnlyWhenNoContext(): void
    {
        $handler = $this->makeHandler(['https://headless.example.com']);

        $result = $handler->buildRequest()->toArray();

        // No resolver context: nothing derivable, configured extras are the only source.
        $this->assertSame(['https://headless.example.com'], $result['targetOrigins']);
    }

    public function testGetCurrencyCodeReturnsEmptyStringWhenStoreContextUnavailable(): void
    {
        // A context whose extension attributes carry no store (getStore() => null) must not fatal
        // the currency resolution chain; it should degrade to '' like the amount path does.
        // Stub extension class (see setUp() for rationale); no store set, so getStore()
        // naturally returns null.
        $contextExtension = new ContextExtensionStub();

        $context = $this->createMock(ContextInterface::class);
        $context->method('getExtensionAttributes')->willReturn($contextExtension);
        $context->method('getUserId')->willReturn(0);

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($context, []);

        $method = new \ReflectionMethod($handler, 'getCurrencyCode');

        $this->assertSame('', $method->invoke($handler));
    }

    public function testBuildRequestSourcesAmountCurrencyAndBillToFromCart(): void
    {
        $billingAddress = $this->makeQuoteBillingAddress();
        $quote = $this->makeQuote(31.5, 'EUR', $billingAddress);
        $this->graphQLHelperMock->expects($this->once())
            ->method('getQuote')
            ->with(0, 'cart123')
            ->willReturn($quote);

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, ['cartId' => 'cart123']);

        $result = $handler->buildRequest()->toArray();

        $this->assertSame('31.50', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('EUR', $result['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
        $this->assertSame('123', $result['orderInformation']['billTo']['buildingNumber']);
        $this->assertSame('jane@example.com', $result['orderInformation']['billTo']['email']);
    }

    public function testBuildRequestBillingOnlyWhenNoCartId(): void
    {
        // Headless add-card: no cartId -> no cart lookup, no amount node, currency from store.
        $this->graphQLHelperMock->expects($this->never())->method('getQuote');

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, []);

        $result = $handler->buildRequest()->toArray();

        $this->assertArrayNotHasKey('orderInformation', $result);
        $this->assertSame('FULL', $result['captureMandate']['billingType']);
        // Guest (userId 0) does not surface save card.
        $this->assertFalse($result['captureMandate']['requestSaveCard']);
    }

    public function testBuildRequestSourcesBillToFromInputBillingAddressOverCart(): void
    {
        // Explicit billingAddress input arg takes precedence; the cart is never queried for billTo.
        // The input uses the REAL CustomerAddressInput schema shape: country_code (CountryCodeEnum)
        // plus a nested region object — NOT flat countryId/regionCode, which GraphQL cannot emit.
        // getBillTo() must normalize those to the flat snake_case keys the address helper reads,
        // or country/region are silently dropped from the capture-context billTo.
        $schemaShapedInput = [
            'firstname' => 'Bob',
            'lastname' => 'Smith',
            'street' => ['500 Market St'],
            'city' => 'Philadelphia',
            'postcode' => '19106',
            'country_code' => 'US',
            'region' => [
                'region_code' => 'PA',
                'region_id' => 51,
            ],
            'telephone' => '5559876543',
        ];

        $inputAddress = $this->makeCustomerAddress('Bob', 'Smith', '500 Market St', 'PA');
        $this->addressHelperMock->expects($this->once())
            ->method('buildAddressFromInput')
            ->with($this->callback(static function (array $billing): bool {
                // Normalized flat keys buildAddressFromInput() reads; nested region flattened.
                return ($billing['country_id'] ?? null) === 'US'
                    && ($billing['region_id'] ?? null) === 51
                    && ($billing['region_code'] ?? null) === 'PA'
                    && ($billing['region'] ?? null) === 'PA';
            }))
            ->willReturn($inputAddress);
        $this->graphQLHelperMock->expects($this->never())->method('getQuote');

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, ['billingAddress' => $schemaShapedInput]);

        $result = $handler->buildRequest()->toArray();

        $this->assertSame('Bob', $result['orderInformation']['billTo']['firstName']);
        $this->assertSame('500', $result['orderInformation']['billTo']['buildingNumber']);
        $this->assertSame('PA', $result['orderInformation']['billTo']['administrativeArea']);
        $this->assertSame('US', $result['orderInformation']['billTo']['country']);
    }

    public function testCanRequestSaveCardTrueForAuthenticatedCustomer(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $context->method('getUserId')->willReturn(42);

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($context, []);

        $method = new \ReflectionMethod(GraphQL::class, 'canRequestSaveCard');

        // Authenticated GraphQL customer (userId > 0) surfaces save card.
        $this->assertTrue($method->invoke($handler));
    }

    public function testGetEmailFallsBackToGuestEmailArgWhenNoCart(): void
    {
        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, ['guestEmail' => 'guest@example.com']);

        $method = new \ReflectionMethod(GraphQL::class, 'getEmail');

        // No cart -> guestEmail input arg is the fallback.
        $this->assertSame('guest@example.com', $method->invoke($handler));
    }

    public function testGetAmountReturnsNullWhenCartTotalMissing(): void
    {
        $quote = $this->makeQuote(null, 'USD', $this->createMock(QuoteAddress::class));
        $this->graphQLHelperMock->method('getQuote')->with(0, 'cart123')->willReturn($quote);

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, ['cartId' => 'cart123']);

        $method = new \ReflectionMethod(GraphQL::class, 'getAmount');

        $this->assertNull($method->invoke($handler));
    }

    public function testGetAmountPropagatesCartAuthorizationFailure(): void
    {
        // A supplied cartId that cannot be resolved or does not belong to the caller MUST deny. This
        // previously degraded to a billing-only context and still minted a capture context, leaving the
        // authorization check non-functional (a regression vs the Secure Acceptance predecessor, which
        // propagated). Billing-only is reserved for "no cartId supplied" — see testGetAmountWithoutCart.
        $this->graphQLHelperMock->method('getQuote')
            ->willThrowException(
                new GraphQlAuthorizationException(__('The current user cannot perform operations on cart "bad"'))
            );

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, ['cartId' => 'bad']);

        $method = new \ReflectionMethod(GraphQL::class, 'getAmount');

        $this->expectException(GraphQlAuthorizationException::class);

        $method->invoke($handler);
    }

    public function testGetBillToPropagatesCartAuthorizationFailure(): void
    {
        // getBillTo() has its own defensive catch (Throwable); it must not re-swallow the denial that
        // getQuote() raises, or the billing-only fallback silently resurfaces one layer up.
        $this->graphQLHelperMock->method('getQuote')
            ->willThrowException(
                new GraphQlAuthorizationException(__('The current user cannot perform operations on cart "bad"'))
            );

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, ['cartId' => 'bad']);

        $method = new \ReflectionMethod(GraphQL::class, 'getBillTo');

        $this->expectException(GraphQlAuthorizationException::class);

        $method->invoke($handler);
    }

    public function testGetEmailPropagatesCartAuthorizationFailure(): void
    {
        // Same as getBillTo(): the guestEmail fallback must not mask a cart denial.
        $this->graphQLHelperMock->method('getQuote')
            ->willThrowException(
                new GraphQlAuthorizationException(__('The current user cannot perform operations on cart "bad"'))
            );

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($this->contextMock, ['cartId' => 'bad', 'guestEmail' => 'guest@example.com']);

        $method = new \ReflectionMethod(GraphQL::class, 'getEmail');

        $this->expectException(GraphQlAuthorizationException::class);

        $method->invoke($handler);
    }

    /**
     * Build a cart mock with the given totals/currency/billing address.
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
            ->onlyMethods(['getBillingAddress', 'getData'])
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
     * Build a GraphQL handler with the given configured "Additional Target Origins" extras.
     *
     * @param string[] $configuredOrigins
     * @return GraphQL
     */
    private function makeHandler(array $configuredOrigins): GraphQL
    {
        $config = $this->createMock(Config::class);
        $config->method('getUcClientVersion')->willReturn('0.34');
        $config->method('getUcTargetOrigins')->willReturn($configuredOrigins);
        $config->method('getUcAllowedCardNetworks')->willReturn(['VISA']);
        $config->method('getUcAllowedPaymentTypes')->willReturn(['PANENTRY']);
        $config->method('getUcBillingType')->willReturn('FULL');
        $config->method('getUcLocale')->willReturn('en_US');
        $config->method('getUcCountry')->willReturn('US');
        $config->method('getUcCompleteMandateType')->willReturn('AUTH');
        $config->method('is3dsEnabled')->willReturn(false);
        $config->method('isDecisionManagerEnabled')->willReturn(false);

        return new GraphQL(
            $config,
            $this->restMock,
            new Sanitizer(),
            $this->addressHelperMock,
            $this->requestFactoryMock,
            $this->createMock(LoggerInterface::class),
            $this->graphQLHelperMock,
        );
    }
}
