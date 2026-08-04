<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Plugin\Magento\Multishipping\Block\Checkout;

use Magento\Multishipping\Block\Checkout\Billing as MultishippingBilling;
use Magento\Payment\Model\MethodInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Plugin\Magento\Multishipping\Block\Checkout\Billing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Plugin\Magento\Multishipping\Block\Checkout\Billing
 */
class BillingTest extends TestCase
{
    private Config|MockObject $configMock;
    private MultishippingBilling|MockObject $subjectMock;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->subjectMock = $this->createMock(MultishippingBilling::class);
    }

    public function testRemovesMethodWhenPayerAuthEnabled(): void
    {
        $this->configMock->method('isPayerAuthEnabled')->willReturn(true);

        $methods = [
            'checkmo' => $this->createMethodMock('checkmo'),
            Config::CODE => $this->createMethodMock(Config::CODE),
        ];

        $result = (new Billing($this->configMock))->afterGetMethods($this->subjectMock, $methods);

        $this->assertArrayNotHasKey(Config::CODE, $result);
        $this->assertArrayHasKey('checkmo', $result);
    }

    public function testLeavesMethodWhenPayerAuthDisabled(): void
    {
        $this->configMock->method('isPayerAuthEnabled')->willReturn(false);

        $methods = [
            'checkmo' => $this->createMethodMock('checkmo'),
            Config::CODE => $this->createMethodMock(Config::CODE),
        ];

        $result = (new Billing($this->configMock))->afterGetMethods($this->subjectMock, $methods);

        $this->assertSame($methods, $result);
    }

    public function testLeavesOtherMethodCodesUntouched(): void
    {
        $this->configMock->method('isPayerAuthEnabled')->willReturn(true);

        $methods = [
            'checkmo' => $this->createMethodMock('checkmo'),
            'paradoxlabs_authnetcim' => $this->createMethodMock('paradoxlabs_authnetcim'),
        ];

        $result = (new Billing($this->configMock))->afterGetMethods($this->subjectMock, $methods);

        $this->assertSame($methods, $result);
    }

    public function testEmptyMethodListIsReturnedUnchanged(): void
    {
        $this->configMock->expects($this->never())->method('isPayerAuthEnabled');

        $result = (new Billing($this->configMock))->afterGetMethods($this->subjectMock, []);

        $this->assertSame([], $result);
    }

    private function createMethodMock(string $code): MethodInterface|MockObject
    {
        $method = $this->createMock(MethodInterface::class);
        $method->method('getCode')->willReturn($code);

        return $method;
    }
}
