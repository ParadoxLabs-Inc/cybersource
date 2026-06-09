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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Concrete test subclass exposing the abstract sourcing hooks.
 */
class TestableCaptureContext extends CaptureContext
{
    public ?string $amount = '24.00';
    public string $currencyCode = 'USD';
    public array $billTo = [];
    public ?string $email = 'jane@example.com';
    public ?int $storeId = 1;
    public bool $saveCard = false;

    protected function getAmount(): ?string
    {
        return $this->amount;
    }

    protected function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    protected function getBillTo(): array
    {
        return $this->billTo;
    }

    protected function getEmail(): ?string
    {
        return $this->email;
    }

    protected function getStoreId(): ?int
    {
        return $this->storeId;
    }

    protected function canRequestSaveCard(): bool
    {
        return $this->saveCard;
    }

    public function exposeMapBillTo(?AddressInterface $address): array
    {
        return $this->mapBillTo($address);
    }
}

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

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->restMock = $this->createMock(Rest::class);
        $this->sanitizer = new Sanitizer();
        $this->addressHelperMock = $this->createMock(Address::class);

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
        $this->configMock->method('is3dsEnabled')->willReturn(false);
        $this->configMock->method('isDecisionManagerEnabled')->willReturn(false);

        $this->handler = new TestableCaptureContext(
            $this->configMock,
            $this->restMock,
            $this->sanitizer,
            $this->addressHelperMock,
            $this->requestFactoryMock,
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
        $this->assertSame('AUTH', $result['completeMandate']['type']);
        $this->assertFalse($result['completeMandate']['decisionManager']);
        $this->assertFalse($result['completeMandate']['consumerAuthentication']);
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
        $config->method('is3dsEnabled')->willReturn(false);
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
        $config->method('is3dsEnabled')->willReturn(true);
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

    public function testBuildRequestOmitsAmountForBillingOnlyContext(): void
    {
        $this->handler->amount = null;
        $this->handler->billTo = ['firstName' => 'Jane', 'country' => 'US'];

        $result = $this->handler->buildRequest()->toArray();

        $this->assertArrayNotHasKey('amountDetails', $result['orderInformation'] ?? []);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
        // billingType still set for the no-amount add-card case.
        $this->assertSame('FULL', $result['captureMandate']['billingType']);
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
}
