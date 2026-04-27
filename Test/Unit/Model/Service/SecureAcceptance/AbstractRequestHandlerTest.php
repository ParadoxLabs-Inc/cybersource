<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\SecureAcceptance;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\AbstractRequestHandler;
use ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Address as AddressHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Concrete test subclass for AbstractRequestHandler
 */
class TestableRequestHandler extends AbstractRequestHandler
{
    public string $email = 'test@example.com';
    public ?int $customerId = 123;
    public string $currencyCode = 'USD';
    public string $secureAcceptUrl = 'https://example.com/secure';
    public string $sessionId = 'session123';
    public string $storeId = '1';

    protected function getEmail(): ?string
    {
        return $this->email;
    }

    protected function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    protected function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    protected function getSecureAcceptUrl($route): string
    {
        return $this->secureAcceptUrl . '/' . $route;
    }

    protected function getSessionId(): string
    {
        return $this->sessionId;
    }

    protected function getStoreId(): string
    {
        return $this->storeId;
    }

    /**
     * Expose protected method for testing
     */
    public function exposeGetTokenUpdateParams(CardInterface $card): array
    {
        return $this->getTokenUpdateParams($card);
    }

    /**
     * Expose protected method for testing
     */
    public function exposeGetTokenCreateParams(): array
    {
        return $this->getTokenCreateParams();
    }

    /**
     * Expose protected method for testing
     */
    public function exposeGetAddressFromObject(AddressInterface $address): array
    {
        return $this->getAddressFromObject($address);
    }
}

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\AbstractRequestHandler
 */
class AbstractRequestHandlerTest extends TestCase
{
    private TestableRequestHandler $handler;
    private Config|MockObject $configMock;
    private Hmac|MockObject $hmacMock;
    private Sanitizer|MockObject $sanitizerMock;
    private AddressHelper|MockObject $addressHelperMock;
    private CardRepositoryInterface|MockObject $cardRepositoryMock;
    private RequestInterface|MockObject $requestMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->hmacMock = $this->createMock(Hmac::class);
        $this->sanitizerMock = $this->createMock(Sanitizer::class);
        $this->addressHelperMock = $this->createMock(AddressHelper::class);
        $this->cardRepositoryMock = $this->createMock(CardRepositoryInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);

        // Set up sanitizer to return input by default (passthrough)
        $this->sanitizerMock->method('alphanumericPunc')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('amount')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('alpha')
            ->willReturnCallback(fn($value) => (string)$value);

        $this->handler = new TestableRequestHandler(
            $this->configMock,
            $this->hmacMock,
            $this->sanitizerMock,
            $this->addressHelperMock,
            $this->cardRepositoryMock,
            $this->requestMock,
        );
    }

    public function testGetTokenUpdateParamsReturnsCorrectArray(): void
    {
        $cardMock = $this->createMock(CardInterface::class);
        $cardMock->method('getPaymentId')
            ->willReturn('token123');
        $cardMock->method('getHash')
            ->willReturn('hash456');

        $result = $this->handler->exposeGetTokenUpdateParams($cardMock);

        $this->assertSame('update_payment_token', $result['transaction_type']);
        $this->assertSame('true', $result['allow_payment_token_update']);
        $this->assertSame('token123', $result['payment_token']);
        $this->assertSame('hash456', $result['merchant_defined_data99']);
    }

    public function testGetTokenCreateParamsReturnsCorrectArray(): void
    {
        $result = $this->handler->exposeGetTokenCreateParams();

        $this->assertSame('create_payment_token', $result['transaction_type']);
        $this->assertSame('0.00', $result['amount']);
        $this->assertSame('USD', $result['currency']);
    }

    public function testGetTokenCreateParamsUsesDifferentCurrency(): void
    {
        $this->handler->currencyCode = 'EUR';

        $result = $this->handler->exposeGetTokenCreateParams();

        $this->assertSame('EUR', $result['currency']);
    }

    public function testGetAddressFromObjectReturnsCorrectParams(): void
    {
        // Set up sanitizer passthrough for this test
        $this->sanitizerMock->method('asciiAlphanumericPunc')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('email')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('postcode')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('phone')
            ->willReturnCallback(fn($value) => (string)$value);

        $regionMock = $this->createMock(RegionInterface::class);
        $regionMock->method('getRegionCode')
            ->willReturn('CA');

        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getFirstname')
            ->willReturn('John');
        $addressMock->method('getLastname')
            ->willReturn('Doe');
        $addressMock->method('getCompany')
            ->willReturn('ACME Inc');
        $addressMock->method('getCountryId')
            ->willReturn('US');
        $addressMock->method('getCity')
            ->willReturn('Los Angeles');
        $addressMock->method('getRegion')
            ->willReturn($regionMock);
        $addressMock->method('getStreet')
            ->willReturn(['123 Main St', 'Suite 100']);
        $addressMock->method('getPostcode')
            ->willReturn('90210');
        $addressMock->method('getTelephone')
            ->willReturn('555-123-4567');

        $result = $this->handler->exposeGetAddressFromObject($addressMock);

        $this->assertSame('John', $result['bill_to_forename']);
        $this->assertSame('Doe', $result['bill_to_surname']);
        $this->assertSame('test@example.com', $result['bill_to_email']);
        $this->assertSame('ACME Inc', $result['bill_to_company_name']);
        $this->assertSame('US', $result['bill_to_address_country']);
        $this->assertSame('Los Angeles', $result['bill_to_address_city']);
        $this->assertSame('CA', $result['bill_to_address_state']);
        $this->assertSame('123 Main St', $result['bill_to_address_line1']);
        $this->assertSame('Suite 100', $result['bill_to_address_line2']);
        $this->assertSame('90210', $result['bill_to_address_postal_code']);
        $this->assertSame('555-123-4567', $result['bill_to_phone']);
    }

    public function testGetAddressFromObjectThrowsExceptionOnMissingFirstname(): void
    {
        $regionMock = $this->createMock(RegionInterface::class);

        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getFirstname')
            ->willReturn('');
        $addressMock->method('getLastname')
            ->willReturn('Doe');
        $addressMock->method('getStreet')
            ->willReturn(['123 Main St']);
        $addressMock->method('getCountryId')
            ->willReturn('US');
        $addressMock->method('getRegion')
            ->willReturn($regionMock);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Please enter a billing address.');

        $this->handler->exposeGetAddressFromObject($addressMock);
    }

    public function testGetAddressFromObjectThrowsExceptionOnMissingLastname(): void
    {
        $regionMock = $this->createMock(RegionInterface::class);

        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getFirstname')
            ->willReturn('John');
        $addressMock->method('getLastname')
            ->willReturn('');
        $addressMock->method('getStreet')
            ->willReturn(['123 Main St']);
        $addressMock->method('getCountryId')
            ->willReturn('US');
        $addressMock->method('getRegion')
            ->willReturn($regionMock);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Please enter a billing address.');

        $this->handler->exposeGetAddressFromObject($addressMock);
    }

    public function testGetAddressFromObjectThrowsExceptionOnMissingStreet(): void
    {
        $regionMock = $this->createMock(RegionInterface::class);

        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getFirstname')
            ->willReturn('John');
        $addressMock->method('getLastname')
            ->willReturn('Doe');
        $addressMock->method('getStreet')
            ->willReturn([]);
        $addressMock->method('getCountryId')
            ->willReturn('US');
        $addressMock->method('getRegion')
            ->willReturn($regionMock);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Please enter a billing address.');

        $this->handler->exposeGetAddressFromObject($addressMock);
    }

    public function testGetAddressFromObjectThrowsExceptionOnMissingCountry(): void
    {
        $regionMock = $this->createMock(RegionInterface::class);

        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getFirstname')
            ->willReturn('John');
        $addressMock->method('getLastname')
            ->willReturn('Doe');
        $addressMock->method('getStreet')
            ->willReturn(['123 Main St']);
        $addressMock->method('getCountryId')
            ->willReturn('');
        $addressMock->method('getRegion')
            ->willReturn($regionMock);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Please enter a billing address.');

        $this->handler->exposeGetAddressFromObject($addressMock);
    }

    public function testGetIframeUrlReturnsCreateEndpointWhenNoCardId(): void
    {
        $this->requestMock->method('getParam')
            ->with('card_id')
            ->willReturn(null);

        $this->configMock->expects($this->once())
            ->method('setStoreId')
            ->with('1');

        $this->configMock->method('getSecureAcceptEndpoint')
            ->with('/embedded/token/create')
            ->willReturn('https://testsecureacceptance.cybersource.com/embedded/token/create');

        $result = $this->handler->getIframeUrl();

        $this->assertSame('https://testsecureacceptance.cybersource.com/embedded/token/create', $result);
    }

    public function testGetIframeUrlReturnsUpdateEndpointWhenCardIdPresent(): void
    {
        $this->requestMock->method('getParam')
            ->with('card_id')
            ->willReturn('card_hash_123');

        $this->configMock->expects($this->once())
            ->method('setStoreId')
            ->with('1');

        $this->configMock->method('getSecureAcceptEndpoint')
            ->with('/embedded/token/update')
            ->willReturn('https://testsecureacceptance.cybersource.com/embedded/token/update');

        $result = $this->handler->getIframeUrl();

        $this->assertSame('https://testsecureacceptance.cybersource.com/embedded/token/update', $result);
    }

    public function testGetAddressFromObjectHandlesSingleLineAddress(): void
    {
        $this->sanitizerMock->method('asciiAlphanumericPunc')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('email')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('postcode')
            ->willReturnCallback(fn($value) => (string)$value);
        $this->sanitizerMock->method('phone')
            ->willReturnCallback(fn($value) => (string)$value);

        $regionMock = $this->createMock(RegionInterface::class);
        $regionMock->method('getRegionCode')
            ->willReturn('NY');

        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->method('getFirstname')
            ->willReturn('Jane');
        $addressMock->method('getLastname')
            ->willReturn('Smith');
        $addressMock->method('getCompany')
            ->willReturn(null);
        $addressMock->method('getCountryId')
            ->willReturn('US');
        $addressMock->method('getCity')
            ->willReturn('New York');
        $addressMock->method('getRegion')
            ->willReturn($regionMock);
        $addressMock->method('getStreet')
            ->willReturn(['456 Broadway']);
        $addressMock->method('getPostcode')
            ->willReturn('10001');
        $addressMock->method('getTelephone')
            ->willReturn('212-555-0100');

        $result = $this->handler->exposeGetAddressFromObject($addressMock);

        $this->assertSame('456 Broadway', $result['bill_to_address_line1']);
        $this->assertSame('', $result['bill_to_address_line2']);
    }
}
