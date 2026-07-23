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
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\StoredCardRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\StoredCardRequestFactory;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\TransientTokenReader;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
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

        $storedCardRequestFactory = $this->createMock(StoredCardRequestFactory::class);
        $storedCardRequestFactory->method('create')->willReturnCallback(fn() => new StoredCardRequest());

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
            $storedCardRequestFactory,
            new TransientTokenReader(new CardType()),
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

    private function buildPayment(
        string $transientToken = 'header.payload.sig',
        float $amountPaid = 0.0,
        bool $isSubscriptionGenerated = false,
        ?int $quoteId = null
    ): Payment&MockObject {
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
        $order->method('getQuoteId')->willReturn($quoteId);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);
        $payment->method('getAmountPaid')->willReturn($amountPaid);
        $payment->method('getAdditionalInformation')
            ->willReturnCallback(
                static fn(?string $key = null) => match ($key) {
                    'transient_token' => $transientToken,
                    'is_subscription_generated' => $isSubscriptionGenerated,
                    default => null,
                }
            );

        return $payment;
    }

    public function testBuildsCorrectPaymentBodyForAuthorize(): void
    {
        $this->primeRest(['id' => 'TXN1', 'status' => 'AUTHORIZED']);

        $this->service->place($this->buildPayment('the.jwt.token'), 24.0);

        $this->assertSame('the.jwt.token', $this->sentBody['tokenInformation']['transientTokenJwt']);
        $this->assertSame(['TOKEN_CREATE'], $this->sentBody['processingInformation']['actionList']);
        // No 'customer': cards are standalone TMS payment instruments (customer-token creation is a
        // separately provisioned vault permission not enabled on all accounts).
        $this->assertSame(
            ['paymentInstrument', 'instrumentIdentifier'],
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

        $storedCardRequestFactory = $this->createMock(StoredCardRequestFactory::class);
        $storedCardRequestFactory->method('create')->willReturnCallback(fn() => new StoredCardRequest());

        $service = new Response(
            $this->restMock,
            $config,
            new Sanitizer(),
            $this->helperMock,
            new CardType(),
            $this->createMock(ResponseFactory::class),
            $requestFactory,
            $storedCardRequestFactory,
            new TransientTokenReader(new CardType()),
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
        // TMS ids extracted into the structured token_information tree (no customer key).
        $tokens = $response->getData('token_information');
        $this->assertArrayNotHasKey('customer', $tokens);
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
            ['paymentInstrument', 'instrumentIdentifier'],
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
        $this->assertArrayNotHasKey('customer', $tokens);
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

    /**
     * Build a stored-card payment (no transient token) with the given subscription/MIT flag and txn ids.
     */
    private function buildStoredPayment(
        bool $isSubscriptionGenerated = false,
        ?string $parentTransactionId = null,
        ?string $lastTransId = null,
        float $amountPaid = 0.0,
        ?int $quoteId = null,
        ?string $ccCid = null
    ): Payment&MockObject {
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
        $order->method('getQuoteId')->willReturn($quoteId);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);
        $payment->method('getParentTransactionId')->willReturn($parentTransactionId);
        $payment->method('getLastTransId')->willReturn($lastTransId);
        $payment->method('getAmountPaid')->willReturn($amountPaid);
        // TokenBase's assign-data observer stores the require_ccv re-entered code as payment data cc_cid.
        $payment->method('getData')
            ->willReturnCallback(
                static fn(?string $key = null) => $key === 'cc_cid' ? $ccCid : null
            );
        $payment->method('getAdditionalInformation')
            ->willReturnCallback(
                static fn(?string $key = null) => $key === 'is_subscription_generated'
                    ? $isSubscriptionGenerated
                    : null
            );

        return $payment;
    }

    /**
     * Build a vaulted card stub carrying the TMS ids. profileId remains stubbed to prove the request
     * builder never reads it (cards are standalone TMS payment instruments).
     */
    private function buildCard(
        ?string $paymentId = 'PI-CARD',
        ?string $profileId = 'CUST-CARD',
        ?string $instrumentIdentifier = 'II-CARD',
        bool $tokenMissing = false
    ): CardInterface&MockObject {
        $card = $this->createMock(CardInterface::class);
        $card->method('getPaymentId')->willReturn($paymentId);
        $card->method('getProfileId')->willReturn($profileId);
        $card->method('getAdditional')->willReturnCallback(
            static function ($key = null) use ($instrumentIdentifier, $tokenMissing) {
                return match ($key) {
                    'instrument_identifier' => $instrumentIdentifier,
                    'uc_token_missing' => $tokenMissing ? '1' : null,
                    default => null,
                };
            }
        );

        return $card;
    }

    public function testPlaceStoredDoesNotFlagTokenMissing(): void
    {
        // A stored-card reply carries no tokenInformation BY DESIGN: the card is already vaulted, so
        // StoredCardRequest sends no actionList/TOKEN_CREATE. "Missing" is only meaningful relative to
        // what was requested. Flagging here marked the card's own good token as missing, and
        // Method::applyUnifiedCheckoutToken() then stamped uc_token_missing='1' onto the vaulted card,
        // so Gateway::buildStoredCardAuth() refused the NEXT charge -- killing every subscription rebill
        // after the first. Both keys must stay unset so Method's guard leaves the card untouched.
        $this->primeRest([
            'id' => 'TXN-STORED-NOTOKEN',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100', 'approvalCode' => '654321'],
        ]);

        $response = $this->service->placeStored($this->buildStoredPayment(), $this->buildCard(), 24.0);

        $this->assertFalse($response->getIsError());
        $this->assertNull($response->getData('token_information'));
        $this->assertNull(
            $response->getData('uc_token_missing'),
            'A stored-card charge requests no token, so its token-less reply must not be flagged.'
        );
    }

    public function testPlaceStoredDoesNotFlagTokenMissingOnProcessorError(): void
    {
        // The token-forbidden carve-out (approved auth + PROCESSOR_ERROR + no token) must likewise only
        // apply when TOKEN_CREATE was actually requested; otherwise an unrelated processor error on a
        // stored-card charge would poison the card by the same route.
        $this->primeRest([
            'id' => 'TXN-STORED-PROCERR',
            'status' => 'DECLINED',
            'errorInformation' => ['reason' => 'PROCESSOR_ERROR', 'message' => 'Requested service is forbidden.'],
            'processorInformation' => ['responseCode' => '100', 'approvalCode' => '888888'],
        ]);

        $response = $this->service->placeStored($this->buildStoredPayment(), $this->buildCard(), 24.0);

        $this->assertNull(
            $response->getData('uc_token_missing'),
            'No token was requested, so a PROCESSOR_ERROR reply must not flag the stored card.'
        );
    }

    public function testPlaceStoredMitPostsExpectedBodyAndApproves(): void
    {
        $this->primeRest([
            'id' => 'TXN-STORED-MIT',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100', 'approvalCode' => '654321'],
        ]);

        $payment = $this->buildStoredPayment(true, 'PRIORTXN-capture');
        $response = $this->service->placeStored($payment, $this->buildCard(), 24.0);

        // Approved, no exception.
        $this->assertFalse($response->getIsError());
        $this->assertSame('TXN-STORED-MIT', $response->getTransactionId());

        // MIT initiator block.
        $initiator = $this->sentBody['processingInformation']['authorizationOptions']['initiator'];
        $this->assertSame('merchant', $initiator['type']);
        $this->assertTrue($initiator['storedCredentialUsed']);
        $this->assertSame(
            'PRIORTXN',
            $initiator['merchantInitiatedTransaction']['previousTransactionId']
        );
        $this->assertSame('recurring', $this->sentBody['processingInformation']['commerceIndicator']);

        // The paymentInstrument id from the card is the ONLY TMS id; NO transient-token / TOKEN_CREATE
        // artifacts, NO customer block (standalone TMS payment instrument — profileId is never read or
        // sent), and NO instrumentIdentifier (sending it alongside the paymentInstrument draws a
        // 400 INVALID_REQUEST/INVALID_DATA — CAS sandbox A/B spike).
        $this->assertArrayNotHasKey('customer', $this->sentBody['paymentInformation']);
        $this->assertSame('PI-CARD', $this->sentBody['paymentInformation']['paymentInstrument']['id']);
        $this->assertArrayNotHasKey('instrumentIdentifier', $this->sentBody['paymentInformation']);
        $this->assertArrayNotHasKey('tokenInformation', $this->sentBody);
        $this->assertArrayNotHasKey('actionList', $this->sentBody['processingInformation']);

        $this->assertSame('100000123', $this->sentBody['clientReferenceInformation']['code']);
        $this->assertSame('24.00', $this->sentBody['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('Jane', $this->sentBody['orderInformation']['billTo']['firstName']);
    }

    public function testPlaceStoredCitSetsCustomerInitiatorAndNoMit(): void
    {
        $this->primeRest([
            'id' => 'TXN-STORED-CIT',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $payment = $this->buildStoredPayment(false, 'PRIORTXN');
        $this->service->placeStored($payment, $this->buildCard(), 24.0);

        $initiator = $this->sentBody['processingInformation']['authorizationOptions']['initiator'];
        $this->assertSame('customer', $initiator['type']);
        $this->assertTrue($initiator['storedCredentialUsed']);
        // CIT: no MIT sub-object, no recurring indicator.
        $this->assertArrayNotHasKey('merchantInitiatedTransaction', $initiator);
        $this->assertArrayNotHasKey('commerceIndicator', $this->sentBody['processingInformation']);
    }

    public function testPlaceStoredNeverSendsInstrumentIdentifier(): void
    {
        // CAS sandbox A/B spike (400 root cause): sending paymentInformation.instrumentIdentifier.id
        // ALONGSIDE the paymentInstrument id is rejected with 400 INVALID_REQUEST/INVALID_DATA; the
        // paymentInstrument alone authorizes. So even when the card carries an instrument_identifier,
        // it must never be sent on the stored-card charge.
        $this->primeRest([
            'id' => 'TXN-STORED-MIN',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        // buildCard() default carries instrument_identifier II-CARD — it must still be omitted.
        $this->service->placeStored($this->buildStoredPayment(), $this->buildCard(), 24.0);

        $this->assertSame('PI-CARD', $this->sentBody['paymentInformation']['paymentInstrument']['id']);
        $this->assertArrayNotHasKey('customer', $this->sentBody['paymentInformation']);
        $this->assertArrayNotHasKey('instrumentIdentifier', $this->sentBody['paymentInformation']);
    }

    public function testPlaceStoredMissingPaymentInstrumentIdThrows(): void
    {
        $card = $this->buildCard(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stored-card payment requires a vaulted card token');

        $this->service->placeStored($this->buildStoredPayment(), $card, 24.0);
    }

    public function testPlaceStoredTokenMissingCardThrowsEvenWithStalePaymentId(): void
    {
        // Defense in depth (HIGH-1): a card flagged uc_token_missing must be rejected by the builder
        // itself, even when it still carries a STALE paymentId that would pass the empty-id guard.
        $card = $this->buildCard('STALE-PI', 'CUST-CARD', 'II-CARD', true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stored-card payment requires a vaulted card token');

        $this->service->placeStored($this->buildStoredPayment(), $card, 24.0);
    }

    public function testPlaceStoredDeclineThrowsCommandException(): void
    {
        $this->primeRest([
            'id' => 'TXN-STORED-DECLINE',
            'status' => 'DECLINED',
            'processorInformation' => ['responseCode' => '202'],
            'errorInformation' => ['reason' => 'EXPIRED_CARD', 'message' => 'Card expired'],
        ]);

        $this->expectException(CommandException::class);

        $this->service->placeStored($this->buildStoredPayment(), $this->buildCard(), 24.0);
    }

    // --- Iter 4: Decision Manager suppression on MIT / follow-on (legacy SOAP parity) ---

    public function testStoredCardMitSuppressesDecisionManager(): void
    {
        // Legacy parity (Gateway::authorize feature/php81): a subscription-generated (MIT) rebill must NOT
        // re-run Decision Manager. UC analog: processingInformation.enableDecisionManager = false.
        $this->primeRest([
            'id' => 'TXN-MIT-DM',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored($this->buildStoredPayment(true, 'PRIORTXN'), $this->buildCard(), 24.0);

        $this->assertArrayHasKey('enableDecisionManager', $this->sentBody['processingInformation']);
        $this->assertFalse($this->sentBody['processingInformation']['enableDecisionManager']);
    }

    public function testStoredCardCitDoesNotSuppressDecisionManager(): void
    {
        // A first-party (CIT) stored-card charge with nothing yet paid leaves DM at the account default
        // (no enableDecisionManager key — DM runs).
        $this->primeRest([
            'id' => 'TXN-CIT-DM',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored($this->buildStoredPayment(false), $this->buildCard(), 24.0);

        $this->assertArrayNotHasKey('enableDecisionManager', $this->sentBody['processingInformation']);
    }

    public function testStoredCardFollowOnAmountPaidSuppressesDecisionManager(): void
    {
        // The other legacy suppression leg: amountPaid > 0 (a follow-on auth on an already-paid order)
        // suppresses DM even when it is NOT a subscription rebill.
        $this->primeRest([
            'id' => 'TXN-FOLLOWON-DM',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored(
            $this->buildStoredPayment(false, null, null, 50.0),
            $this->buildCard(),
            24.0
        );

        $this->assertFalse($this->sentBody['processingInformation']['enableDecisionManager']);
    }

    public function testNewCardSubscriptionGeneratedSuppressesDecisionManager(): void
    {
        // The new-card path (place()) honors the same legacy suppression condition: a subscription-generated
        // transaction suppresses DM.
        $this->primeRest(['id' => 'TXN-NEWCARD-MIT', 'status' => 'AUTHORIZED']);

        $this->service->place($this->buildPayment('the.jwt.token', 0.0, true), 24.0);

        $this->assertFalse($this->sentBody['processingInformation']['enableDecisionManager']);
    }

    public function testNewCardFreshCustomerCheckoutDoesNotSuppressDecisionManager(): void
    {
        // A normal new-card storefront checkout (nothing paid, not a subscription) leaves DM running.
        $this->primeRest(['id' => 'TXN-NEWCARD', 'status' => 'AUTHORIZED']);

        $this->service->place($this->buildPayment('the.jwt.token'), 24.0);

        $this->assertArrayNotHasKey('enableDecisionManager', $this->sentBody['processingInformation']);
    }

    // --- PR #1 finding 4: Decision Manager device-fingerprint parity (legacy SOAP deviceFingerprintID) ---

    public function testNewCardEmitsDeviceFingerprintWhenConfigProvidesSessionId(): void
    {
        // Fingerprinting enabled + a reachable quote session -> deviceInformation.fingerprintSessionId is
        // sent, requested at DEFAULT scope (apiScope=false — the merchant-prefixed session id the frontend
        // tag profiled under; the bare quote id matches no profiled session on REST).
        $this->configMock->method('getFingerprintSessionId')
            ->willReturnCallback(
                function (string $sessionId, ?int $storeId = null, bool $apiScope = false): string {
                    $this->assertSame('42', $sessionId);
                    $this->assertFalse($apiScope, 'REST must use the tag-scope (merchant-prefixed) session id.');

                    return 'FP-SESSION-9';
                }
            );
        $this->primeRest(['id' => 'TXN-FP', 'status' => 'AUTHORIZED']);

        $this->service->place($this->buildPayment('the.jwt.token', 0.0, false, 42), 24.0);

        $this->assertSame(
            'FP-SESSION-9',
            $this->sentBody['deviceInformation']['fingerprintSessionId']
        );
    }

    public function testNewCardOmitsDeviceFingerprintWhenConfigReturnsNull(): void
    {
        // Config returns null (fingerprinting disabled) even with a reachable quote id -> no device signal.
        $this->configMock->method('getFingerprintSessionId')->willReturn(null);
        $this->primeRest(['id' => 'TXN-NOFP', 'status' => 'AUTHORIZED']);

        $this->service->place($this->buildPayment('the.jwt.token', 0.0, false, 42), 24.0);

        $this->assertArrayNotHasKey('deviceInformation', $this->sentBody);
    }

    public function testNewCardOmitsDeviceFingerprintWhenNoQuoteId(): void
    {
        // No quote id reachable -> Config is never consulted and no device signal is sent.
        $this->primeRest(['id' => 'TXN-NOQUOTE', 'status' => 'AUTHORIZED']);

        $this->service->place($this->buildPayment('the.jwt.token'), 24.0);

        $this->assertArrayNotHasKey('deviceInformation', $this->sentBody);
    }

    public function testStoredCardCitEmitsDeviceFingerprint(): void
    {
        // A CIT stored-card charge is cardholder-present, so the device signal is forwarded. The session
        // id must be requested at DEFAULT scope (apiScope=false, the merchant-prefixed value the frontend
        // online-metrix tag profiled under) — REST does not prepend the merchant id server-side the way
        // SOAP did, so the bare quote id (apiScope=true) matches no profiled session.
        $this->configMock->method('getFingerprintSessionId')
            ->willReturnCallback(
                function (string $sessionId, ?int $storeId = null, bool $apiScope = false): string {
                    $this->assertSame('42', $sessionId);
                    $this->assertFalse($apiScope, 'REST must use the tag-scope (merchant-prefixed) session id.');

                    return 'FP-CIT';
                }
            );
        $this->primeRest([
            'id' => 'TXN-CIT-FP',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored(
            $this->buildStoredPayment(false, null, null, 0.0, 42),
            $this->buildCard(),
            24.0
        );

        $this->assertSame('FP-CIT', $this->sentBody['deviceInformation']['fingerprintSessionId']);
    }

    public function testStoredCardMitOmitsDeviceFingerprint(): void
    {
        // An MIT (subscription-generated) rebill has no cardholder device present; the fingerprint must be
        // omitted even when Config would otherwise provide a session id.
        $this->configMock->method('getFingerprintSessionId')->willReturn('FP-MIT');
        $this->primeRest([
            'id' => 'TXN-MIT-NOFP',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored(
            $this->buildStoredPayment(true, 'PRIORTXN', null, 0.0, 42),
            $this->buildCard(),
            24.0
        );

        $this->assertArrayNotHasKey('deviceInformation', $this->sentBody);
    }

    // --- Stored-card CVV re-entry (require_ccv) ---

    public function testStoredCardCitForwardsReenteredSecurityCode(): void
    {
        // require_ccv: the checkout re-prompts for the security code on a stored card; TokenBase's
        // assign-data observer stores it as payment data cc_cid. It must be forwarded as
        // paymentInformation.card.securityCode on the CIT charge.
        $this->primeRest([
            'id' => 'TXN-CIT-CVV',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored(
            $this->buildStoredPayment(false, null, null, 0.0, null, '123'),
            $this->buildCard(),
            24.0
        );

        $this->assertSame('123', $this->sentBody['paymentInformation']['card']['securityCode']);
    }

    public function testStoredCardCitOmitsCardBlockWithoutSecurityCode(): void
    {
        // No re-entered code (require_ccv off, or nothing posted) -> no paymentInformation.card block at
        // all; the vaulted payment instrument is the only payment information.
        $this->primeRest([
            'id' => 'TXN-CIT-NOCVV',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored($this->buildStoredPayment(), $this->buildCard(), 24.0);

        $this->assertArrayNotHasKey('card', $this->sentBody['paymentInformation']);
    }

    public function testStoredCardMitNeverSendsSecurityCode(): void
    {
        // An MIT/subscription rebill has no cardholder present; even a stale cc_cid on the payment must
        // never ride onto the merchant-initiated charge.
        $this->primeRest([
            'id' => 'TXN-MIT-NOCVV',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $this->service->placeStored(
            $this->buildStoredPayment(true, 'PRIORTXN', null, 0.0, null, '123'),
            $this->buildCard(),
            24.0
        );

        $this->assertArrayNotHasKey('card', $this->sentBody['paymentInformation']);
    }

    // --- Iter 4: Decision Manager REJECT propagation ---

    public function testDecisionManagerRejectThrowsCommandExceptionDespiteApprovedResponseCode(): void
    {
        // Money-path guard: AUTHORIZED_RISK_DECLINED carries processorInformation.responseCode=100 (the
        // processor approved the auth) but Decision Manager REJECTED the order (DECISION_PROFILE_REJECT).
        // It must NOT be placed — it is a hard decline, mirroring the legacy REJECT path.
        $this->primeRest([
            'id' => 'TXN-RISK-DECLINED',
            'status' => 'AUTHORIZED_RISK_DECLINED',
            'processorInformation' => ['approvalCode' => '123456', 'responseCode' => '100'],
            'errorInformation' => [
                'reason' => 'DECISION_PROFILE_REJECT',
                'message' => 'Decision Manager rejected the order',
            ],
        ]);

        try {
            $this->service->place($this->buildPayment(), 24.0);

            $this->fail('Expected CommandException was not thrown');
        } catch (CommandException $exception) {
            $this->assertStringContainsString('Transaction Failed', (string)$exception->getMessage());
            // The DM-reject exception must never carry the approval code (100); it is forced to 0 so it
            // matches nothing in the retry code space.
            $this->assertSame(0, $exception->getCode());
        }
    }

    public function testDecisionProfileRejectReasonThrowsEvenWithoutRiskDeclinedStatus(): void
    {
        // Defense in depth: regardless of the status string, a DECISION_PROFILE_REJECT reason means DM
        // rejected the order and the transaction must fail as a decline.
        $this->primeRest([
            'id' => 'TXN-REJECTED',
            'status' => 'REJECTED',
            'processorInformation' => ['responseCode' => '100'],
            'errorInformation' => [
                'reason' => 'DECISION_PROFILE_REJECT',
                'message' => 'Rejected by Decision Manager',
            ],
        ]);

        $this->expectException(CommandException::class);

        $this->service->place($this->buildPayment(), 24.0);
    }

    public function testDecisionManagerReviewStillApproves(): void
    {
        // Contrast with REJECT: a REVIEW hold (AUTHORIZED_PENDING_REVIEW) still approves + flags fraud and
        // must NOT be treated as a DM reject.
        $this->primeRest([
            'id' => 'TXN-REVIEW2',
            'status' => 'AUTHORIZED_PENDING_REVIEW',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $response = $this->service->place($this->buildPayment(), 24.0);

        $this->assertFalse($response->getIsError());
        $this->assertTrue($response->getIsFraud());
    }

    // --- Iter 4: 3DS (Payer Auth) authentication-result surfacing ---

    public function testConsumerAuthenticationResultSurfacedOnApprovedResponse(): void
    {
        // 3DS via UC completeMandate folds the authenticated result into the transient token; the
        // /pts/v2/payments reply carries consumerAuthenticationInformation. Surface the liability-shift /
        // authentication-result fields so they persist on the transaction record.
        $this->primeRest([
            'id' => 'TXN-3DS',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
            'consumerAuthenticationInformation' => [
                'eci' => '05',
                'eciRaw' => '05',
                'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'paresStatus' => 'Y',
                'authenticationResult' => '0',
                'xid' => 'ABCDEF1234567890',
                'veresEnrolled' => 'Y',
                'specificationVersion' => '2.2.0',
                'directoryServerTransactionId' => 'f38e6948-5388-41a6-bca4-b49723c19437',
                'ucafAuthenticationData' => 'someUcafData',
                'unrelatedField' => 'ignored',
                // Allowlisted key but non-scalar value: must be skipped by the scalar guard.
                'token' => [
                    'unexpected' => 'structure',
                ],
            ],
        ]);

        $response = $this->service->place($this->buildPayment(), 24.0);

        $this->assertFalse($response->getIsError());

        $auth = $response->getData('consumer_authentication');
        $this->assertIsArray($auth);
        $this->assertSame('05', $auth['eci']);
        $this->assertSame('AAABCZIhcQAAAABZlyFxAAAAAAA=', $auth['cavv']);
        $this->assertSame('Y', $auth['paresStatus']);
        $this->assertSame('0', $auth['authenticationResult']);
        $this->assertSame('Y', $auth['veresEnrolled']);
        $this->assertSame('2.2.0', $auth['specificationVersion']);
        // Only the known authentication-result fields are surfaced; unknown keys are dropped.
        $this->assertArrayNotHasKey('unrelatedField', $auth);
        // Allowlisted but non-scalar values are skipped by the scalar guard.
        $this->assertArrayNotHasKey('token', $auth);
    }

    public function testNoConsumerAuthenticationKeyWhenAuthenticationAbsent(): void
    {
        // 3DS disabled / frictionless-without-data: no consumerAuthenticationInformation -> no surfaced key.
        $this->primeRest([
            'id' => 'TXN-NO3DS',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $response = $this->service->place($this->buildPayment(), 24.0);

        $this->assertNull($response->getData('consumer_authentication'));
    }

    // --- UC card-metadata seeding from the transient token (Task 1) ---

    /**
     * Build a transient-token JWT whose payload matches the real browser-minted gda-0.10.0 shape:
     * scalar card fields wrapped as {"value": ...}, number as {maskedValue, bin}, absent fields [].
     *
     * @param array<string, mixed> $card content.paymentInformation.card subtree.
     * @return string
     */
    private function buildTransientToken(array $card): string
    {
        $payload = [
            'iss' => 'Flex/07',
            'type' => 'gda-0.10.0',
            'metadata' => ['paymentType' => 'PANENTRY'],
            'content' => ['paymentInformation' => ['card' => $card]],
        ];
        $encode = static fn(array $data): string => rtrim(
            strtr(base64_encode((string)json_encode($data)), '+/', '-_'),
            '='
        );

        return $encode(['alg' => 'RS256']) . '.' . $encode($payload) . '.c2ln';
    }

    public function testPlaceSeedsCardInformationFromTransientTokenWhenReplyOmitsIt(): void
    {
        // The real /pts/v2/payments transient-token reply returns ONLY paymentInformation.card.type;
        // here it returns nothing at all — the decoded token must fill the whole card_information set.
        $this->primeRest([
            'id' => 'TXN-SEED',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
        ]);

        $token = $this->buildTransientToken([
            'number' => [
                'maskedValue' => 'XXXXXXXXXXXX1111',
                'bin' => '411111',
            ],
            'expirationMonth' => ['value' => '09'],
            'expirationYear' => ['value' => '2029'],
            'type' => ['value' => '001'],
            'securityCode' => [],
        ]);

        $response = $this->service->place($this->buildPayment($token), 24.0);

        $this->assertSame(
            [
                'cc_type' => 'VI',
                'cc_last4' => '1111',
                'cc_bin' => '411111',
                'cc_exp_month' => '09',
                'cc_exp_year' => '2029',
            ],
            $response->getData('card_information')
        );
    }

    public function testGatewayReplyCardFieldsOverrideTokenDecodedValues(): void
    {
        // The reply is authoritative wherever it DOES return data: its type/last4 must win over the
        // token's, while token-only fields (bin/expiry here) still fill in around it.
        $this->primeRest([
            'id' => 'TXN-MERGE',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
            'paymentInformation' => [
                'card' => [
                    'type' => '002',
                    'suffix' => '4444',
                ],
            ],
        ]);

        $token = $this->buildTransientToken([
            'number' => [
                'maskedValue' => 'XXXXXXXXXXXX1111',
                'bin' => '411111',
            ],
            'expirationMonth' => ['value' => '09'],
            'expirationYear' => ['value' => '2029'],
            'type' => ['value' => '001'],
        ]);

        $card = $this->service->place($this->buildPayment($token), 24.0)->getData('card_information');

        $this->assertSame('MC', $card['cc_type']);
        $this->assertSame('4444', $card['cc_last4']);
        $this->assertSame('411111', $card['cc_bin']);
        $this->assertSame('09', $card['cc_exp_month']);
        $this->assertSame('2029', $card['cc_exp_year']);
    }

    public function testMalformedTransientTokenLeavesReplyMetadataUntouched(): void
    {
        $this->primeRest([
            'id' => 'TXN-BADTOK',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
            'paymentInformation' => [
                'card' => ['type' => '001'],
            ],
        ]);

        $response = $this->service->place($this->buildPayment('not.a-real.jwt'), 24.0);

        $this->assertSame(['cc_type' => 'VI'], $response->getData('card_information'));
    }

    public function testZeroDollarTokenizeSeedsCardInformationFromTransientToken(): void
    {
        // The $0 add-card path saves a vault card off the same reply; it needs the seeded metadata too.
        $this->primeRest([
            'id' => 'TXN-ZERO-SEED',
            'status' => 'AUTHORIZED',
            'processorInformation' => ['responseCode' => '100'],
            'tokenInformation' => [
                'paymentInstrument' => ['id' => 'PI-1'],
            ],
        ]);

        $token = $this->buildTransientToken([
            'number' => [
                'maskedValue' => 'XXXXXXXXXXXX1111',
                'bin' => '411111',
            ],
            'expirationMonth' => ['value' => '09'],
            'expirationYear' => ['value' => '2029'],
            'type' => ['value' => '001'],
        ]);

        $response = $this->service->tokenizeCard($this->buildPayment($token), 'USD', 1);

        $this->assertSame('1111', $response->getData('card_information')['cc_last4']);
        $this->assertSame('VI', $response->getData('card_information')['cc_type']);
    }
}
