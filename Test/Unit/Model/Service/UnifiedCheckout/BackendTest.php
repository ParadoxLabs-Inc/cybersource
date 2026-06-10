<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Backend\Model\Session\Quote as BackendSession;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
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
        $this->backendSessionMock = $this->createMock(BackendSession::class);
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

    /**
     * Build a Backend handler with the given configured "Additional Target Origins" extras.
     *
     * @param string[] $configuredOrigins
     * @return Backend
     */
    private function makeHandler(array $configuredOrigins): Backend
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
