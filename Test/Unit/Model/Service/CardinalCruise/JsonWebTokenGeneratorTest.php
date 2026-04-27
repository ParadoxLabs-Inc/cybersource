<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\CardinalCruise;

use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenEncoder;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\JsonWebTokenGenerator
 */
class JsonWebTokenGeneratorTest extends TestCase
{
    private JsonWebTokenGenerator $generator;
    private Config|MockObject $configMock;
    private JsonWebTokenEncoder|MockObject $encoderMock;
    private RequestInterface|MockObject $requestMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->encoderMock = $this->createMock(JsonWebTokenEncoder::class);
        $this->requestMock = $this->createMock(RequestInterface::class);

        $this->generator = new JsonWebTokenGenerator(
            $this->configMock,
            $this->encoderMock,
            $this->requestMock,
        );
    }

    public function testGetAmountMultipliesBy100(): void
    {
        $result = $this->invokeGetAmount(99.99, 'USD');

        $this->assertSame(9999.0, $result);
    }

    public function testGetAmountRoundsCorrectly(): void
    {
        $result = $this->invokeGetAmount(99.999, 'USD');

        $this->assertSame(10000.0, $result);
    }

    public function testGetAmountZero(): void
    {
        $result = $this->invokeGetAmount(0, 'USD');

        $this->assertSame(0.0, $result);
    }

    public function testGetAmountLargeValue(): void
    {
        $result = $this->invokeGetAmount(12345.67, 'EUR');

        $this->assertSame(1234567.0, $result);
    }

    public function testGetPayloadAddressSingleStreetLine(): void
    {
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getStreet')->willReturn(['123 Main St']);
        $addressMock->method('getFirstname')->willReturn('John');
        $addressMock->method('getMiddlename')->willReturn(null);
        $addressMock->method('getLastname')->willReturn('Doe');
        $addressMock->method('getCity')->willReturn('Anytown');
        $addressMock->method('getRegion')->willReturn('CA');
        $addressMock->method('getPostcode')->willReturn('12345');
        $addressMock->method('getCountryId')->willReturn('US');
        $addressMock->method('getTelephone')->willReturn('555-1234');

        $result = $this->invokeGetPayloadAddress($addressMock);

        $this->assertSame('123 Main St', $result['Address1']);
        $this->assertNull($result['Address2']);
        $this->assertNull($result['Address3']);
        $this->assertSame('John', $result['FirstName']);
        $this->assertSame('Doe', $result['LastName']);
    }

    public function testGetPayloadAddressMultipleStreetLines(): void
    {
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getStreet')->willReturn(['123 Main St', 'Apt 4B', 'Building C']);
        $addressMock->method('getFirstname')->willReturn('Jane');
        $addressMock->method('getMiddlename')->willReturn('M');
        $addressMock->method('getLastname')->willReturn('Smith');
        $addressMock->method('getCity')->willReturn('Big City');
        $addressMock->method('getRegion')->willReturn('NY');
        $addressMock->method('getPostcode')->willReturn('10001');
        $addressMock->method('getCountryId')->willReturn('US');
        $addressMock->method('getTelephone')->willReturn('555-5678');

        $result = $this->invokeGetPayloadAddress($addressMock);

        $this->assertSame('123 Main St', $result['Address1']);
        $this->assertSame('Apt 4B', $result['Address2']);
        $this->assertSame('Building C', $result['Address3']);
        $this->assertSame('M', $result['MiddleName']);
    }

    public function testGetPayloadAddressEmptyStreet(): void
    {
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getStreet')->willReturn([]);
        $addressMock->method('getFirstname')->willReturn('Test');
        $addressMock->method('getMiddlename')->willReturn(null);
        $addressMock->method('getLastname')->willReturn('User');
        $addressMock->method('getCity')->willReturn('Test City');
        $addressMock->method('getRegion')->willReturn('TS');
        $addressMock->method('getPostcode')->willReturn('00000');
        $addressMock->method('getCountryId')->willReturn('US');
        $addressMock->method('getTelephone')->willReturn('555-0000');

        $result = $this->invokeGetPayloadAddress($addressMock);

        $this->assertNull($result['Address1']);
        $this->assertNull($result['Address2']);
        $this->assertNull($result['Address3']);
    }

    public function testGetPayloadItemsNameTruncation(): void
    {
        $longName = str_repeat('A', 200);

        $quoteMock = $this->createMock(Quote::class);
        $itemMock = $this->createMock(QuoteItem::class);
        $itemMock->method('getName')->willReturn($longName);
        $itemMock->method('getSku')->willReturn('SKU123');
        $itemMock->method('getQty')->willReturn(1);
        $itemMock->method('getPrice')->willReturn(10.00);

        $quoteMock->method('getAllVisibleItems')->willReturn([$itemMock]);

        $result = $this->invokeGetPayloadItems($quoteMock);

        $this->assertCount(1, $result);
        $this->assertSame(128, strlen((string) $result[0]['Name']));
    }

    public function testGetPayloadItemsSkuTruncation(): void
    {
        $longSku = str_repeat('X', 50);

        $quoteMock = $this->createMock(Quote::class);
        $itemMock = $this->createMock(QuoteItem::class);
        $itemMock->method('getName')->willReturn('Test Product');
        $itemMock->method('getSku')->willReturn($longSku);
        $itemMock->method('getQty')->willReturn(2);
        $itemMock->method('getPrice')->willReturn(25.00);

        $quoteMock->method('getAllVisibleItems')->willReturn([$itemMock]);

        $result = $this->invokeGetPayloadItems($quoteMock);

        $this->assertCount(1, $result);
        $this->assertSame(20, strlen((string) $result[0]['SKU']));
    }

    public function testGetPayloadItemsMultipleItems(): void
    {
        $quoteMock = $this->createMock(Quote::class);

        $item1 = $this->createMock(QuoteItem::class);
        $item1->method('getName')->willReturn('Product 1');
        $item1->method('getSku')->willReturn('SKU1');
        $item1->method('getQty')->willReturn(2);
        $item1->method('getPrice')->willReturn(10.00);

        $item2 = $this->createMock(QuoteItem::class);
        $item2->method('getName')->willReturn('Product 2');
        $item2->method('getSku')->willReturn('SKU2');
        $item2->method('getQty')->willReturn(1);
        $item2->method('getPrice')->willReturn(20.00);

        $quoteMock->method('getAllVisibleItems')->willReturn([$item1, $item2]);

        $result = $this->invokeGetPayloadItems($quoteMock);

        $this->assertCount(2, $result);
        $this->assertSame('Product 1', $result[0]['Name']);
        $this->assertSame('Product 2', $result[1]['Name']);
        $this->assertEquals(2, $result[0]['Quantity']);
        $this->assertEquals(1, $result[1]['Quantity']);
    }

    public function testGetPayloadItemsEmpty(): void
    {
        $quoteMock = $this->createMock(Quote::class);
        $quoteMock->method('getAllVisibleItems')->willReturn([]);

        $result = $this->invokeGetPayloadItems($quoteMock);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetJwtReturnsEmptyWhenPayerAuthDisabled(): void
    {
        $this->configMock->method('isPayerAuthEnabled')->willReturn(false);

        $quoteMock = $this->createMock(CartInterface::class);

        $result = $this->generator->getJwt($quoteMock);

        $this->assertSame('', $result);
    }

    public function testGetJwtCallsEncoderWhenEnabled(): void
    {
        $this->configMock->method('isPayerAuthEnabled')->willReturn(true);
        $this->configMock->method('getCardinalSecretKeyId')->willReturn('key-id');
        $this->configMock->method('getCardinalOrgUnitId')->willReturn('org-unit');

        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getEmail')->willReturn('test@example.com');
        $addressMock->method('getStreet')->willReturn(['123 Main']);
        $addressMock->method('getFirstname')->willReturn('Test');
        $addressMock->method('getMiddlename')->willReturn(null);
        $addressMock->method('getLastname')->willReturn('User');
        $addressMock->method('getCity')->willReturn('City');
        $addressMock->method('getRegion')->willReturn('ST');
        $addressMock->method('getPostcode')->willReturn('12345');
        $addressMock->method('getCountryId')->willReturn('US');
        $addressMock->method('getTelephone')->willReturn('555-1234');

        // Use Quote mock - some methods are magic/data accessors, some are real
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBillingAddress', 'isVirtual', 'getAllVisibleItems'])
            ->addMethods(['getGrandTotal', 'getQuoteCurrencyCode'])
            ->getMock();
        $quoteMock->method('getBillingAddress')->willReturn($addressMock);
        $quoteMock->method('getGrandTotal')->willReturn(100.00);
        $quoteMock->method('getQuoteCurrencyCode')->willReturn('USD');
        $quoteMock->method('isVirtual')->willReturn(true);
        $quoteMock->method('getAllVisibleItems')->willReturn([]);

        $this->encoderMock->expects($this->once())
            ->method('pack')
            ->willReturn('encoded.jwt.token');

        $result = $this->generator->getJwt($quoteMock);

        $this->assertSame('encoded.jwt.token', $result);
    }

    /**
     * Helper to invoke protected getAmount method
     */
    private function invokeGetAmount(float $grandTotal, string $currencyCode): float
    {
        $method = new ReflectionMethod(JsonWebTokenGenerator::class, 'getAmount');
        $method->setAccessible(true);

        return $method->invoke($this->generator, $grandTotal, $currencyCode);
    }

    /**
     * Helper to invoke protected getPayloadAddress method
     */
    private function invokeGetPayloadAddress(AddressInterface $address): array
    {
        $method = new ReflectionMethod(JsonWebTokenGenerator::class, 'getPayloadAddress');
        $method->setAccessible(true);

        return $method->invoke($this->generator, $address);
    }

    /**
     * Helper to invoke protected getPayloadItems method
     */
    private function invokeGetPayloadItems(Quote $quote): array
    {
        $method = new ReflectionMethod(JsonWebTokenGenerator::class, 'getPayloadItems');
        $method->setAccessible(true);

        return $method->invoke($this->generator, $quote);
    }
}
