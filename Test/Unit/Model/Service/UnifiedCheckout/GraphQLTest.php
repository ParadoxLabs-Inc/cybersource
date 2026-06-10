<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\GraphQl\Model\Query\ContextInterface;
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

        // The unit-test code generator may or may not emit getStore() on the extension interface
        // depending on whether the generated stub has been pre-built. Branch accordingly.
        $builder = $this->getMockBuilder(ContextExtensionInterface::class)
            ->disableOriginalConstructor();

        if (method_exists(ContextExtensionInterface::class, 'getStore')) {
            $builder->onlyMethods(['getStore']);
        } else {
            $builder->addMethods(['getStore']);
        }

        $contextExtension = $builder->getMockForAbstractClass();
        $contextExtension->method('getStore')->willReturn($store);

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
        $builder = $this->getMockBuilder(ContextExtensionInterface::class)
            ->disableOriginalConstructor();

        if (method_exists(ContextExtensionInterface::class, 'getStore')) {
            $builder->onlyMethods(['getStore']);
        } else {
            $builder->addMethods(['getStore']);
        }

        $contextExtension = $builder->getMockForAbstractClass();
        $contextExtension->method('getStore')->willReturn(null);

        $context = $this->createMock(ContextInterface::class);
        $context->method('getExtensionAttributes')->willReturn($contextExtension);
        $context->method('getUserId')->willReturn(0);

        $handler = $this->makeHandler([]);
        $handler->setGraphQLContext($context, []);

        $method = new \ReflectionMethod($handler, 'getCurrencyCode');

        $this->assertSame('', $method->invoke($handler));
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
            $this->graphQLHelperMock,
        );
    }
}
