<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth\Request;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequest
 */
class AuthenticationRequestTest extends TestCase
{
    /**
     * A complete browser profile, as the client + server derive it.
     *
     * @return array<string, string>
     */
    private function deviceInformation(): array
    {
        return [
            'httpAcceptBrowserValue' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'userAgentBrowserValue' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/127.0.0.0',
            'ipAddress' => '198.51.100.24',
            'httpBrowserLanguage' => 'en-US',
            'httpBrowserJavaEnabled' => 'N',
            'httpBrowserJavaScriptEnabled' => 'Y',
            'httpBrowserColorDepth' => '24',
            'httpBrowserScreenHeight' => '1080',
            'httpBrowserScreenWidth' => '1920',
            'httpBrowserTimeDifference' => '300',
        ];
    }

    /**
     * A request with every required field set, addressing a raw card.
     *
     * @return AuthenticationRequest
     */
    private function completeRequest(): AuthenticationRequest
    {
        $request = new AuthenticationRequest();

        return $request->setClientReferenceCode('quote-1234')
            ->setReferenceId('2611dbe9-b63b-4ac4-a172-a4278a32aecb')
            ->setReturnUrl('https://store.example.com/pdl_cybs/payerauth/callback')
            ->setTotalAmount('24.00')
            ->setCurrency('USD')
            ->setBillTo([
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'country' => 'US',
                'address2' => null,
            ])
            ->setCard([
                'number' => '4456530000001005',
                'expirationMonth' => '01',
                'expirationYear' => '2029',
                'type' => '001',
            ])
            ->setDeviceInformation($this->deviceInformation());
    }

    public function testToArrayPlacesEveryFieldInTheRightBranch(): void
    {
        $result = $this->completeRequest()->toArray();

        $this->assertSame('quote-1234', $result['clientReferenceInformation']['code']);
        $this->assertSame(
            '2611dbe9-b63b-4ac4-a172-a4278a32aecb',
            $result['consumerAuthenticationInformation']['referenceId']
        );
        $this->assertSame(
            'https://store.example.com/pdl_cybs/payerauth/callback',
            $result['consumerAuthenticationInformation']['returnUrl']
        );
        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
        $this->assertSame('4456530000001005', $result['paymentInformation']['card']['number']);
        $this->assertSame('01', $result['paymentInformation']['card']['expirationMonth']);
        $this->assertSame('2029', $result['paymentInformation']['card']['expirationYear']);
        $this->assertSame('001', $result['paymentInformation']['card']['type']);
        $this->assertSame('198.51.100.24', $result['deviceInformation']['ipAddress']);
        $this->assertSame('24', $result['deviceInformation']['httpBrowserColorDepth']);
        $this->assertArrayNotHasKey('paymentInstrument', $result['paymentInformation']);
    }

    public function testEmptyBillToLeavesAreFiltered(): void
    {
        $result = $this->completeRequest()->toArray();

        $this->assertArrayNotHasKey('address2', $result['orderInformation']['billTo']);
    }

    public function testEmptyBillToOmitsTheBranch(): void
    {
        $request = $this->completeRequest()->setBillTo([]);

        $this->assertArrayNotHasKey('billTo', $request->toArray()['orderInformation']);
    }

    public function testReferenceIdIsOptional(): void
    {
        $request = $this->completeRequest()->setReferenceId(null);

        $result = $request->toArray();

        $this->assertArrayNotHasKey('referenceId', $result['consumerAuthenticationInformation']);
        $this->assertArrayHasKey('returnUrl', $result['consumerAuthenticationInformation']);
    }

    public function testStoredCardShapeEmitsPaymentInstrumentId(): void
    {
        $request = $this->completeRequest()
            ->setCard([])
            ->setPaymentInstrumentId('F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4');

        $result = $request->toArray();

        $this->assertSame(
            'F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4',
            $result['paymentInformation']['paymentInstrument']['id']
        );
        $this->assertArrayNotHasKey('card', $result['paymentInformation']);
    }

    public function testSetCardKeepsOnlyKnownFields(): void
    {
        $request = $this->completeRequest()->setCard([
            'number' => '4456530000001005',
            'expirationMonth' => '01',
            'expirationYear' => '2029',
            'type' => '001',
            'securityCode' => '737',
            'bogus' => 'value',
        ]);

        $card = $request->toArray()['paymentInformation']['card'];

        $this->assertSame(
            ['number', 'expirationMonth', 'expirationYear', 'type'],
            array_keys($card)
        );
    }

    /**
     * A newly entered card has no PAN server-side and no payment-instrument id yet, so the
     * transient token IS the card reference. Same shape the setups call takes.
     *
     * @return void
     */
    public function testTransientTokenShapeEmitsTokenInformation(): void
    {
        $request = $this->completeRequest()
            ->setCard([])
            ->setTransientToken('the.transient.token');

        $result = $request->toArray();

        $this->assertSame('the.transient.token', $request->getTransientToken());
        $this->assertSame(['transientTokenJwt' => 'the.transient.token'], $result['tokenInformation']);
        $this->assertArrayNotHasKey('paymentInformation', $result);
        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
    }

    public function testTransientTokenIsMutuallyExclusiveWithTheOtherCardShapes(): void
    {
        $request = $this->completeRequest()->setTransientToken('the.transient.token');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testTransientTokenIsMutuallyExclusiveWithThePaymentInstrumentId(): void
    {
        $request = $this->completeRequest()
            ->setCard([])
            ->setPaymentInstrumentId('F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4')
            ->setTransientToken('the.transient.token');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testBothCardShapesThrow(): void
    {
        $request = $this->completeRequest()->setPaymentInstrumentId('F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testNeitherCardShapeThrows(): void
    {
        $request = $this->completeRequest()->setCard([]);

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testMissingReturnUrlThrows(): void
    {
        $request = $this->completeRequest()->setReturnUrl(null);

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testMissingAmountThrows(): void
    {
        $request = $this->completeRequest()->setTotalAmount(null);

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testMissingCurrencyThrows(): void
    {
        $request = $this->completeRequest()->setCurrency('');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function requiredDeviceFieldProvider(): array
    {
        $cases = [];

        foreach (AuthenticationRequest::REQUIRED_DEVICE_FIELDS as $field) {
            $cases[$field] = [$field];
        }

        return $cases;
    }

    /**
     * Thin deviceInformation silently degrades an enrolled card to veresEnrolled U — a silent 3DS
     * bypass. Every required field must therefore fail loud.
     *
     * @param string $field
     * @return void
     * @dataProvider requiredDeviceFieldProvider
     */
    #[DataProvider('requiredDeviceFieldProvider')]
    public function testEachMissingBrowserFieldThrows(string $field): void
    {
        $device = $this->deviceInformation();
        unset($device[$field]);

        $request = $this->completeRequest()->setDeviceInformation($device);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage($field);

        $request->toArray();
    }

    /**
     * @param string $field
     * @return void
     * @dataProvider requiredDeviceFieldProvider
     */
    #[DataProvider('requiredDeviceFieldProvider')]
    public function testEachEmptyBrowserFieldThrows(string $field): void
    {
        $device          = $this->deviceInformation();
        $device[$field]  = '';

        $request = $this->completeRequest()->setDeviceInformation($device);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage($field);

        $request->toArray();
    }

    public function testEmptyDeviceInformationListsEveryMissingField(): void
    {
        $request = $this->completeRequest()->setDeviceInformation([]);

        try {
            $request->toArray();
            $this->fail('Expected InputException for empty deviceInformation.');
        } catch (InputException $exception) {
            foreach (AuthenticationRequest::REQUIRED_DEVICE_FIELDS as $field) {
                $this->assertStringContainsString($field, $exception->getMessage());
            }
        }
    }

    public function testExtraDeviceFieldsPassThrough(): void
    {
        $device = $this->deviceInformation() + ['fingerprintSessionId' => 'abc123'];

        $result = $this->completeRequest()->setDeviceInformation($device)->toArray();

        $this->assertSame('abc123', $result['deviceInformation']['fingerprintSessionId']);
    }

    public function testGettersRoundTrip(): void
    {
        $request = $this->completeRequest();

        $this->assertSame('quote-1234', $request->getClientReferenceCode());
        $this->assertSame('2611dbe9-b63b-4ac4-a172-a4278a32aecb', $request->getReferenceId());
        $this->assertSame('24.00', $request->getTotalAmount());
        $this->assertSame('USD', $request->getCurrency());
        $this->assertSame('US', $request->getBillTo()['country']);
        $this->assertSame('4456530000001005', $request->getCard()['number']);
        $this->assertNull($request->getPaymentInstrumentId());
        $this->assertSame('en-US', $request->getDeviceInformation()['httpBrowserLanguage']);
        $this->assertStringStartsWith('https://store.example.com/', (string)$request->getReturnUrl());
    }
}
