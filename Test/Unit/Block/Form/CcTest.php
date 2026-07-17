<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Block\Form;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Config as PaymentConfig;
use ParadoxLabs\CyberSource\Block\Form\Cc;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Method\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers \ParadoxLabs\CyberSource\Block\Form\Cc
 */
class CcTest extends TestCase
{
    private Cc $block;
    private Context|MockObject $contextMock;
    private PaymentConfig|MockObject $paymentConfigMock;
    private Data|MockObject $helperMock;
    private \Magento\Customer\Model\Session|MockObject $customerSessionMock;
    private CheckoutSession|MockObject $checkoutSessionMock;
    private Factory|MockObject $methodFactoryMock;
    private Config|MockObject $configMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->paymentConfigMock = $this->createMock(PaymentConfig::class);
        $this->helperMock = $this->createMock(Data::class);
        $this->customerSessionMock = $this->createMock(\Magento\Customer\Model\Session::class);
        $this->checkoutSessionMock = $this->createMock(CheckoutSession::class);
        $this->methodFactoryMock = $this->createMock(Factory::class);
        $this->configMock = $this->createMock(Config::class);

        $this->block = new Cc(
            $this->contextMock,
            $this->paymentConfigMock,
            $this->helperMock,
            $this->customerSessionMock,
            $this->checkoutSessionMock,
            $this->methodFactoryMock,
            $this->configMock,
            [],
        );
    }

    /**
     * Read a protected property off the block.
     */
    private function getProtectedProperty(string $name): mixed
    {
        $property = new ReflectionProperty(Cc::class, $name);

        return $property->getValue($this->block);
    }

    /**
     * The Decision Manager fingerprint script must be keyed to the current quote, so the device
     * fingerprint collected in the browser correlates with the order eventually placed from it.
     */
    public function testGetFingerprintUrlPassesCurrentQuoteIdToConfig(): void
    {
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn(4242);

        $this->configMock->expects($this->once())
            ->method('getFingerprintUrl')
            ->with(4242)
            ->willReturn('https://h.online-metrix.net/fp/tags.js?org_id=abc&session_id=DEQXVEEG4242');

        $this->assertSame(
            'https://h.online-metrix.net/fp/tags.js?org_id=abc&session_id=DEQXVEEG4242',
            $this->block->getFingerprintUrl()
        );
    }

    /**
     * Decision Manager is optional; when disabled the config returns null and the template must be
     * able to skip the script tag entirely.
     */
    public function testGetFingerprintUrlReturnsNullWhenFingerprintingDisabled(): void
    {
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(4242);

        $this->configMock->expects($this->once())
            ->method('getFingerprintUrl')
            ->with(4242)
            ->willReturn(null);

        $this->assertNull($this->block->getFingerprintUrl());
    }

    /**
     * An empty/guest cart has no quote id yet; the block must still delegate rather than guard,
     * leaving the null-quote decision to the config.
     */
    public function testGetFingerprintUrlDelegatesEvenWithoutQuoteId(): void
    {
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(null);

        $this->configMock->expects($this->once())
            ->method('getFingerprintUrl')
            ->with(null)
            ->willReturn(null);

        $this->assertNull($this->block->getFingerprintUrl());
    }

    /**
     * The CyberSource form template must override the TokenBase default, otherwise checkout renders
     * the generic form without the UC drop-in container.
     */
    public function testTemplateIsOverriddenToCyberSourceForm(): void
    {
        $this->assertSame('ParadoxLabs_CyberSource::form/cc.phtml', $this->getProtectedProperty('_template'));
    }

    /**
     * Branding image must point at the CyberSource logo, not the empty TokenBase default.
     */
    public function testBrandingImageIsCyberSourceLogo(): void
    {
        $this->assertSame(
            'ParadoxLabs_CyberSource::images/logo.webp',
            $this->getProtectedProperty('brandingImage')
        );
    }

    public function testExtendsTokenBaseCcForm(): void
    {
        $this->assertInstanceOf(\ParadoxLabs\TokenBase\Block\Form\Cc::class, $this->block);
    }
}
