<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Gateway;

use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Gateway\Context;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UnifiedCheckoutResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Gateway\Context
 */
class ContextTest extends TestCase
{
    private Context $context;
    private Config|MockObject $configMock;
    private Rest|MockObject $restMock;
    private UnifiedCheckoutResponse|MockObject $responseMock;
    private FollowOn|MockObject $followOnMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->restMock = $this->createMock(Rest::class);
        $this->responseMock = $this->createMock(UnifiedCheckoutResponse::class);
        $this->followOnMock = $this->createMock(FollowOn::class);

        $this->context = new Context(
            $this->configMock,
            $this->restMock,
            $this->responseMock,
            $this->followOnMock,
        );
    }

    public function testGetConfigReturnsInjectedConfig(): void
    {
        $this->assertSame($this->configMock, $this->context->getConfig());
    }

    public function testGetRestClientReturnsInjectedRestClient(): void
    {
        $this->assertSame($this->restMock, $this->context->getRestClient());
    }

    public function testGetUnifiedCheckoutResponseReturnsInjectedAuthSaleService(): void
    {
        $this->assertSame($this->responseMock, $this->context->getUnifiedCheckoutResponse());
    }

    public function testGetUnifiedCheckoutFollowOnReturnsInjectedFollowOnService(): void
    {
        $this->assertSame($this->followOnMock, $this->context->getUnifiedCheckoutFollowOn());
    }

    /**
     * The context is a plain service-locator: every accessor must be a stable passthrough, never
     * re-resolving or cloning the collaborator between calls.
     */
    public function testAccessorsAreStableAcrossRepeatedCalls(): void
    {
        $this->assertSame($this->context->getConfig(), $this->context->getConfig());
        $this->assertSame($this->context->getRestClient(), $this->context->getRestClient());
        $this->assertSame(
            $this->context->getUnifiedCheckoutResponse(),
            $this->context->getUnifiedCheckoutResponse()
        );
        $this->assertSame(
            $this->context->getUnifiedCheckoutFollowOn(),
            $this->context->getUnifiedCheckoutFollowOn()
        );
    }

    /**
     * The A1 (auth/sale) and A3 (capture/refund/void) services are distinct collaborators; a
     * copy/paste accessor swap would silently route follow-on operations at the auth service.
     */
    public function testUnifiedCheckoutAccessorsReturnDistinctServices(): void
    {
        $this->assertNotSame(
            $this->context->getUnifiedCheckoutResponse(),
            $this->context->getUnifiedCheckoutFollowOn()
        );
    }
}
