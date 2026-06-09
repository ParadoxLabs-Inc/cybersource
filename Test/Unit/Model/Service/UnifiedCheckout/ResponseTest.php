<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Framework\Exception\RuntimeException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response
 */
class ResponseTest extends TestCase
{
    private Response $service;
    private Rest|MockObject $restMock;
    private Config|MockObject $configMock;
    private Data|MockObject $helperMock;

    /**
     * @var array<string, mixed> Captured request body POSTed to Rest.
     */
    private array $sentBody = [];

    protected function setUp(): void
    {
        $this->restMock   = $this->createMock(Rest::class);
        $this->configMock = $this->createMock(Config::class);
        $this->helperMock = $this->createMock(Data::class);

        $this->configMock->method('getUcCompleteMandateType')->willReturn('AUTH');

        $requestFactory = $this->createMock(PaymentRequestFactory::class);
        $requestFactory->method('create')->willReturnCallback(fn() => new PaymentRequest());

        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->method('create')->willReturnCallback(
            static fn(array $args = []) => (new GatewayResponse())->setData($args['data'] ?? [])
        );

        $this->service = new Response(
            $this->restMock,
            $this->configMock,
            new Sanitizer(),
            $this->helperMock,
            new CardType(),
            $responseFactory,
            $requestFactory,
        );
    }

    /**
     * @param array<string, mixed> $cannedResponse
     */
    private function primeRest(array $cannedResponse): void
    {
        $this->restMock->method('post')
            ->willReturnCallback(function (string $path, array $body) use ($cannedResponse): array {
                $this->sentBody = $body;
                $this->assertSame(Response::PAYMENTS_PATH, $path);

                return $cannedResponse;
            });
    }

    private function buildPayment(string $transientToken = 'header.payload.sig'): Payment&MockObject
    {
        $address = $this->createMock(OrderAddressInterface::class);
        $address->method('getFirstname')->willReturn('Jane');
        $address->method('getLastname')->willReturn('Doe');
        $address->method('getStreet')->willReturn(['123 Main St']);
        $address->method('getCity')->willReturn('Austin');
        $address->method('getRegionCode')->willReturn('TX');
        $address->method('getPostcode')->willReturn('78701');
        $address->method('getCountryId')->willReturn('US');
        $address->method('getEmail')->willReturn('jane@example.com');
        $address->method('getTelephone')->willReturn('5125551234');

        $order = $this->createMock(Order::class);
        $order->method('getIncrementId')->willReturn('100000123');
        $order->method('getStoreId')->willReturn(1);
        $order->method('getBaseCurrencyCode')->willReturn('USD');
        $order->method('getBillingAddress')->willReturn($address);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);
        $payment->method('getAdditionalInformation')
            ->willReturnCallback(
                static fn(?string $key = null) => $key === 'transient_token' ? $transientToken : null
            );

        return $payment;
    }

    public function testBuildsCorrectPaymentBodyForAuthorize(): void
    {
        $this->primeRest(['id' => 'TXN1', 'status' => 'AUTHORIZED']);

        $this->service->place($this->buildPayment('the.jwt.token'), 24.0);

        $this->assertSame('the.jwt.token', $this->sentBody['tokenInformation']['transientTokenJwt']);
        $this->assertSame(['TOKEN_CREATE'], $this->sentBody['processingInformation']['actionList']);
        $this->assertSame(
            ['customer', 'paymentInstrument', 'instrumentIdentifier'],
            $this->sentBody['processingInformation']['actionTokenTypes']
        );
        // payment_action=authorize (AUTH) -> capture=false, and the flag must survive empty-filtering.
        $this->assertArrayHasKey('capture', $this->sentBody['processingInformation']);
        $this->assertFalse($this->sentBody['processingInformation']['capture']);
        $this->assertSame('24.00', $this->sentBody['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $this->sentBody['orderInformation']['amountDetails']['currency']);
        $this->assertSame('100000123', $this->sentBody['clientReferenceInformation']['code']);
        $this->assertSame('Jane', $this->sentBody['orderInformation']['billTo']['firstName']);
    }

    public function testBuildsCaptureTrueForSale(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('getUcCompleteMandateType')->willReturn('CAPTURE');

        $requestFactory = $this->createMock(PaymentRequestFactory::class);
        $requestFactory->method('create')->willReturnCallback(fn() => new PaymentRequest());

        $service = new Response(
            $this->restMock,
            $config,
            new Sanitizer(),
            $this->helperMock,
            new CardType(),
            $this->createMock(ResponseFactory::class),
            $requestFactory,
        );

        $request = $service->buildRequest($this->buildPayment(), 30.5);

        $this->assertTrue($request->toArray()['processingInformation']['capture']);
        $this->assertSame('30.50', $request->toArray()['orderInformation']['amountDetails']['totalAmount']);
    }

    public function testApprovedResponseMapsFieldsAndTms(): void
    {
        $this->primeRest([
            'id' => 'TXN-APPROVED',
            'status' => 'AUTHORIZED',
            'processorInformation' => [
                'approvalCode' => '888888',
                'responseCode' => '100',
                'avs' => ['code' => 'Y'],
                'cardVerification' => ['resultCode' => 'M'],
            ],
            'paymentInformation' => [
                'card' => [
                    'type' => '001',
                    'suffix' => '1111',
                    'prefix' => '411111',
                    'expirationMonth' => '12',
                    'expirationYear' => '2030',
                ],
            ],
            'tokenInformation' => [
                'customer' => ['id' => 'CUST1'],
                'paymentInstrument' => ['id' => 'PI1'],
                'instrumentIdentifier' => ['id' => 'II1'],
            ],
        ]);

        $response = $this->service->place($this->buildPayment(), 24.0);

        $this->assertFalse($response->getIsError());
        $this->assertSame('TXN-APPROVED', $response->getTransactionId());
        $this->assertSame('888888', $response->getAuthCode());
        $this->assertSame('100', $response->getResponseCode());
        // AVS/CVV land on the ccAuthReply.* keys Method::storeTransactionStatuses() reads.
        $this->assertSame('Y', $response->getData('ccAuthReply.avsCode'));
        $this->assertSame('M', $response->getData('ccAuthReply.cvCode'));
        $this->assertSame('888888', $response->getData('ccAuthReply.authorizationCode'));
        // TMS ids extracted into the structured token_information tree.
        $tokens = $response->getData('token_information');
        $this->assertSame('CUST1', $tokens['customer']);
        $this->assertSame('PI1', $tokens['paymentInstrument']);
        $this->assertSame('II1', $tokens['instrumentIdentifier']);
        // Card metadata extracted + mapped through CardType (001 -> VI).
        $card = $response->getData('card_information');
        $this->assertSame('VI', $card['cc_type']);
        $this->assertSame('1111', $card['cc_last4']);
        $this->assertSame('411111', $card['cc_bin']);
        $this->assertFalse($response->getData('uc_token_missing'));
    }

    public function testAuthorizedPendingReviewSucceedsWithNoTokenAndDoesNotThrow(): void
    {
        // DM hold: status approved-pending, NO tokenInformation present at all.
        $this->primeRest([
            'id' => 'TXN-REVIEW',
            'status' => 'AUTHORIZED_PENDING_REVIEW',
            'processorInformation' => [
                'approvalCode' => '777777',
                'responseCode' => '100',
            ],
        ]);

        $response = $this->service->place($this->buildPayment(), 24.0);

        $this->assertFalse($response->getIsError());
        $this->assertTrue($response->getIsFraud());
        $this->assertSame('TXN-REVIEW', $response->getTransactionId());
        // No TMS ids; flagged token-less.
        $this->assertNull($response->getData('token_information'));
        $this->assertTrue($response->getData('uc_token_missing'));
    }

    public function testDeclinedResponseThrowsCommandException(): void
    {
        $this->primeRest([
            'id' => 'TXN-DECLINE',
            'status' => 'DECLINED',
            'processorInformation' => ['responseCode' => '202'],
            'errorInformation' => ['reason' => 'EXPIRED_CARD', 'message' => 'Card expired'],
        ]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Transaction Failed');

        $this->service->place($this->buildPayment(), 24.0);
    }

    public function testInvalidRequestThrowsRuntimeException(): void
    {
        $this->primeRest([
            'status' => 'INVALID_REQUEST',
            'errorInformation' => ['reason' => 'MISSING_FIELD', 'message' => 'Missing data'],
        ]);

        $this->expectException(RuntimeException::class);

        $this->service->place($this->buildPayment(), 24.0);
    }

    public function testTokenCreateForbiddenWhileAuthApprovedDoesNotFail(): void
    {
        // The spike isolation finding (UC-API-REFERENCE §4): when TOKEN_CREATE is not provisioned the
        // auth approves (processorInformation.responseCode=100) but CyberSource STILL returns top-level
        // status=DECLINED + errorInformation.reason=PROCESSOR_ERROR. Approval must key on responseCode,
        // not status, so this scenario must NOT throw.
        $this->primeRest([
            'id' => 'TXN-NOTMS',
            'status' => 'DECLINED',
            'processorInformation' => [
                'approvalCode' => '999999',
                'responseCode' => '100',
            ],
            'errorInformation' => [
                'reason' => 'PROCESSOR_ERROR',
                'message' => 'Requested service is forbidden',
            ],
        ]);

        $response = $this->service->place($this->buildPayment(), 24.0);

        // Auth stands; no exception; token-less; tx id + auth code exposed.
        $this->assertFalse($response->getIsError());
        $this->assertSame('TXN-NOTMS', $response->getTransactionId());
        $this->assertSame('999999', $response->getAuthCode());
        $this->assertSame('100', $response->getResponseCode());
        $this->assertTrue($response->getData('uc_token_missing'));
        $this->assertNull($response->getData('token_information'));
    }

    public function testGenuineDeclineWithoutApprovedResponseCodeThrows(): void
    {
        // Discriminator check: same status=DECLINED, but responseCode is a real decline (202, NOT 100),
        // so this is a genuine auth decline and MUST still throw CommandException.
        $this->primeRest([
            'id' => 'TXN-DECLINE-202',
            'status' => 'DECLINED',
            'processorInformation' => [
                'responseCode' => '202',
            ],
            'errorInformation' => [
                'reason' => 'PROCESSOR_DECLINED',
                'message' => 'Decline - General decline of the card',
            ],
        ]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Transaction Failed');

        $this->service->place($this->buildPayment(), 24.0);
    }

    public function testPartialAuthorizedIsSurfacedNotTreatedAsFullApproval(): void
    {
        $this->primeRest([
            'id' => 'TXN-PARTIAL',
            'status' => 'PARTIAL_AUTHORIZED',
            'processorInformation' => [
                'approvalCode' => '555555',
                'responseCode' => '100',
            ],
            'orderInformation' => [
                'amountDetails' => ['authorizedAmount' => '10.00'],
            ],
        ]);

        $response = $this->service->place($this->buildPayment(), 24.0);

        $this->assertFalse($response->getIsError());
        $this->assertTrue($response->getData('uc_partial_authorized'));
        $this->assertSame('10.00', $response->getData('uc_authorized_amount'));
    }

    public function testMissingTransientTokenThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Unified Checkout payment token');

        $this->service->place($this->buildPayment(''), 24.0);
    }

    public function testZeroDollarTokenizeBuildsTokenCreateRequestWithZeroAmountAndNoCapture(): void
    {
        $this->primeRest(['id' => 'TKN', 'status' => 'AUTHORIZED']);

        $this->service->tokenizeCard($this->buildPayment('add.card.jwt'), 'USD', 1);

        // Same transient-token + TOKEN_CREATE shape as a purchase...
        $this->assertSame('add.card.jwt', $this->sentBody['tokenInformation']['transientTokenJwt']);
        $this->assertSame(['TOKEN_CREATE'], $this->sentBody['processingInformation']['actionList']);
        $this->assertSame(
            ['customer', 'paymentInstrument', 'instrumentIdentifier'],
            $this->sentBody['processingInformation']['actionTokenTypes']
        );
        // ...but no charge: $0 amount and capture forced false (authorize-only), and no order code.
        $this->assertSame('0.00', $this->sentBody['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $this->sentBody['orderInformation']['amountDetails']['currency']);
        $this->assertArrayHasKey('capture', $this->sentBody['processingInformation']);
        $this->assertFalse($this->sentBody['processingInformation']['capture']);
        $this->assertArrayNotHasKey('clientReferenceInformation', $this->sentBody);
        // billTo IS sent for the $0 add-card auth when reachable from the payment (AVS support).
        $this->assertSame('Jane', $this->sentBody['orderInformation']['billTo']['firstName']);
        $this->assertSame('78701', $this->sentBody['orderInformation']['billTo']['postalCode']);
    }

    public function testZeroDollarTokenizeOmitsBillToWhenNoBillingAddress(): void
    {
        $this->primeRest(['id' => 'TKN', 'status' => 'AUTHORIZED']);

        // Payment whose order has no billing address: billTo must be omitted, not sent empty.
        $order = $this->createMock(Order::class);
        $order->method('getBillingAddress')->willReturn(null);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);
        $payment->method('getAdditionalInformation')
            ->willReturnCallback(
                static fn(?string $key = null) => $key === 'transient_token' ? 'add.card.jwt' : null
            );

        $this->service->tokenizeCard($payment, 'USD', 1);

        $this->assertArrayNotHasKey('billTo', $this->sentBody['orderInformation'] ?? []);
    }

    public function testZeroDollarTokenizeMapsReturnedTmsIds(): void
    {
        $this->primeRest([
            'id' => 'TKN-OK',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
            'tokenInformation' => [
                'customer' => ['id' => 'CUST-Z'],
                'paymentInstrument' => ['id' => 'PI-Z'],
                'instrumentIdentifier' => ['id' => 'II-Z'],
            ],
            'paymentInformation' => [
                'card' => ['type' => '001', 'suffix' => '4242'],
            ],
        ]);

        $response = $this->service->tokenizeCard($this->buildPayment('add.card.jwt'), 'USD', 1);

        $this->assertFalse($response->getIsError());
        $tokens = $response->getData('token_information');
        $this->assertSame('CUST-Z', $tokens['customer']);
        $this->assertSame('PI-Z', $tokens['paymentInstrument']);
        $this->assertSame('II-Z', $tokens['instrumentIdentifier']);
        $this->assertFalse($response->getData('uc_token_missing'));
    }

    public function testZeroDollarTokenizeMissingTransientTokenThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Unified Checkout payment token');

        $this->service->tokenizeCard($this->buildPayment(''), 'USD', 1);
    }
}
