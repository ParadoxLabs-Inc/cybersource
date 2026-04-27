<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Block\Info;

use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Sales\Model\Order\Payment\Info as PaymentInfo;
use ParadoxLabs\CyberSource\Block\Info\Cc;
use ParadoxLabs\CyberSource\Helper\Data as Helper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Block\Info\Cc
 */
class CcTest extends TestCase
{
    private Cc $block;
    private Context|MockObject $contextMock;
    private PaymentConfig|MockObject $paymentConfigMock;
    private Helper|MockObject $helperMock;
    private PaymentInfo|MockObject $paymentInfoMock;
    private State|MockObject $appStateMock;
    private EventManager|MockObject $eventManagerMock;

    protected function setUp(): void
    {
        $this->appStateMock = $this->createMock(State::class);
        $this->eventManagerMock = $this->createMock(EventManager::class);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getAppState')
            ->willReturn($this->appStateMock);
        $this->contextMock->method('getEventManager')
            ->willReturn($this->eventManagerMock);

        $this->paymentConfigMock = $this->createMock(PaymentConfig::class);
        $this->helperMock = $this->createMock(Helper::class);

        // Use MockBuilder to add magic methods
        $this->paymentInfoMock = $this->getMockBuilder(PaymentInfo::class)
            ->disableOriginalConstructor()
            ->addMethods(['getMethod', 'getCcLast4', 'getCcType'])
            ->onlyMethods(['getData', 'getAdditionalInformation'])
            ->getMock();

        $this->block = new Cc(
            $this->contextMock,
            $this->paymentConfigMock,
            $this->helperMock,
            [],
        );

        $this->block->setData('info', $this->paymentInfoMock);
    }

    public function testPrepareSpecificInformationSecureModeReturnsNoAdditionalData(): void
    {
        // Set up secure mode (frontend)
        $this->appStateMock->method('getAreaCode')
            ->willReturn('frontend');

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturn(null);
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturn(null);

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        // Should not contain admin-specific fields
        $this->assertArrayNotHasKey('AVS Response', $result->getData());
        $this->assertArrayNotHasKey('CVN Response', $result->getData());
        $this->assertArrayNotHasKey('Fraud Risk Score', $result->getData());
        $this->assertArrayNotHasKey('Risk Factor', $result->getData());
    }

    public function testPrepareSpecificInformationNonSecureModeIncludesAvsResponse(): void
    {
        // Set up non-secure mode (admin)
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturnCallback(function ($key) {
                if ($key === 'cc_avs_status') {
                    return 'Y';
                }

                return null;
            });
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturn(null);

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $this->helperMock->method('translateAvs')
            ->with('Y')
            ->willReturn('Address and Zip Match');

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        $this->assertSame('Address and Zip Match', $result->getData('AVS Response'));
    }

    public function testPrepareSpecificInformationNonSecureModeIncludesCvnResponse(): void
    {
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturnCallback(function ($key) {
                if ($key === 'cc_cid_status') {
                    return 'M';
                }

                return null;
            });
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturn(null);

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $this->helperMock->method('translateCvn')
            ->with('M')
            ->willReturn('CVN Match');

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        $this->assertSame('CVN Match', $result->getData('CVN Response'));
    }

    public function testPrepareSpecificInformationNonSecureModeIncludesFraudScore(): void
    {
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturn(null);
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturnCallback(function ($key) {
                if ($key === 'afsReply.afsResult') {
                    return '45';
                }

                return null;
            });

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        $this->assertEquals('45/100', (string)$result->getData('Fraud Risk Score'));
    }

    public function testPrepareSpecificInformationNonSecureModeIncludesRiskFactor(): void
    {
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturn(null);
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturnCallback(function ($key) {
                if ($key === 'afsReply.afsFactorCode') {
                    return 'B';
                }

                return null;
            });

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $this->helperMock->method('translateRiskFactor')
            ->with('B')
            ->willReturn('Billing address mismatch');

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        $this->assertSame('Billing address mismatch', $result->getData('Risk Factor'));
    }

    public function testPrepareSpecificInformationNonSecureModeWithMultipleRiskFactors(): void
    {
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturn(null);
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturnCallback(function ($key) {
                if ($key === 'afsReply.afsFactorCode') {
                    return 'A^B^C';
                }

                return null;
            });

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        // The last risk factor should be shown (code overwrites on each iteration)
        $this->helperMock->method('translateRiskFactor')
            ->willReturnCallback(function ($code) {
                $factors = [
                    'A' => 'Excessive address change',
                    'B' => 'Billing address mismatch',
                    'C' => 'Credit card BIN mismatch',
                ];

                return $factors[$code] ?? $code;
            });

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        // Due to the loop overwriting, the last factor 'C' should be the value
        $this->assertSame('Credit card BIN mismatch', $result->getData('Risk Factor'));
    }

    public function testPrepareSpecificInformationUsesAdditionalInfoForAvs(): void
    {
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturn(null);
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturnCallback(function ($key) {
                if ($key === 'ccAuthReply.avsCode') {
                    return 'X';
                }

                return null;
            });

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $this->helperMock->method('translateAvs')
            ->with('X')
            ->willReturn('All digits match');

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        $this->assertSame('All digits match', $result->getData('AVS Response'));
    }

    public function testPrepareSpecificInformationUsesAdditionalInfoForCvn(): void
    {
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturn(null);
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturnCallback(function ($key) {
                if ($key === 'ccAuthReply.cvCode') {
                    return 'M';
                }

                return null;
            });

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $this->helperMock->method('translateCvn')
            ->with('M')
            ->willReturn('CVN Match');

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        $this->assertSame('CVN Match', $result->getData('CVN Response'));
    }

    public function testPrepareSpecificInformationNoDataWhenValuesEmpty(): void
    {
        $this->block->setData('is_secure_mode', 0);

        $this->paymentInfoMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentInfoMock->method('getCcLast4')
            ->willReturn('1234');
        $this->paymentInfoMock->method('getCcType')
            ->willReturn('VI');
        $this->paymentInfoMock->method('getData')
            ->willReturn(null);
        $this->paymentInfoMock->method('getAdditionalInformation')
            ->willReturn(null);

        $this->paymentConfigMock->method('getCcTypes')
            ->willReturn(['VI' => 'Visa']);

        $result = $this->invokeProtectedMethod('_prepareSpecificInformation', [null]);

        $this->assertArrayNotHasKey('AVS Response', $result->getData());
        $this->assertArrayNotHasKey('CVN Response', $result->getData());
        $this->assertArrayNotHasKey('Fraud Risk Score', $result->getData());
        $this->assertArrayNotHasKey('Risk Factor', $result->getData());
    }

    /**
     * Invoke a protected method on the block instance
     *
     * @param string $methodName
     * @param array $params
     * @return mixed
     */
    private function invokeProtectedMethod(string $methodName, array $params): mixed
    {
        $method = new \ReflectionMethod(Cc::class, $methodName);
        $method->setAccessible(true);

        return $method->invoke($this->block, ...$params);
    }
}
