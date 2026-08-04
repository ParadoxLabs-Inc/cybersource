<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth\Request;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequest
 */
class SetupRequestTest extends TestCase
{
    public function testTransientTokenShapeEmitsTokenInformation(): void
    {
        $request = new SetupRequest();
        $request->setClientReferenceCode('quote-1234')
            ->setTransientToken('eyJraWQiOiIwOCIsImFsZyI6IlJTMjU2In0.transient.token');

        $result = $request->toArray();

        $this->assertSame('quote-1234', $result['clientReferenceInformation']['code']);
        $this->assertSame(
            'eyJraWQiOiIwOCIsImFsZyI6IlJTMjU2In0.transient.token',
            $result['tokenInformation']['transientToken']
        );
        $this->assertArrayNotHasKey('paymentInformation', $result);
    }

    public function testPaymentInstrumentShapeEmitsRawPaymentInstrumentId(): void
    {
        $request = new SetupRequest();
        $request->setClientReferenceCode('quote-1234')
            ->setPaymentInstrumentId('F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4');

        $result = $request->toArray();

        $this->assertSame(
            'F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4',
            $result['paymentInformation']['paymentInstrument']['id']
        );
        $this->assertArrayNotHasKey('tokenInformation', $result);
    }

    public function testClientReferenceInformationIsOmittedWhenEmpty(): void
    {
        $request = new SetupRequest();
        $request->setTransientToken('token');

        $this->assertArrayNotHasKey('clientReferenceInformation', $request->toArray());
    }

    public function testBothShapesThrow(): void
    {
        $request = new SetupRequest();
        $request->setTransientToken('token')
            ->setPaymentInstrumentId('F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testNeitherShapeThrows(): void
    {
        $request = new SetupRequest();
        $request->setClientReferenceCode('quote-1234');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testEmptyStringsCountAsUnset(): void
    {
        $request = new SetupRequest();
        $request->setTransientToken('')
            ->setPaymentInstrumentId('');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testGettersRoundTrip(): void
    {
        $request = new SetupRequest();
        $request->setClientReferenceCode('quote-1234')
            ->setTransientToken('token')
            ->setPaymentInstrumentId('pi');

        $this->assertSame('quote-1234', $request->getClientReferenceCode());
        $this->assertSame('token', $request->getTransientToken());
        $this->assertSame('pi', $request->getPaymentInstrumentId());
    }
}
