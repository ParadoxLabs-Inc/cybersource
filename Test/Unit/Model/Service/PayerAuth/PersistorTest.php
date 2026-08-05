<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Payment as QuotePayment;
use Magento\Quote\Model\ResourceModel\Quote\Payment as QuotePaymentResource;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OrderPayment;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\AuthenticationResult;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor
 */
class PersistorTest extends TestCase
{
    use FixtureLoaderTrait;

    /**
     * @var QuotePaymentResource&MockObject
     */
    private $paymentResource;

    /**
     * @var CartRepositoryInterface&MockObject
     */
    private $cartRepository;

    /**
     * @var Data&MockObject
     */
    private $helper;

    /**
     * @var Persistor
     */
    private Persistor $persistor;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->paymentResource = $this->createMock(QuotePaymentResource::class);
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        $this->helper = $this->createMock(Data::class);

        $this->persistor = new Persistor(
            $this->paymentResource,
            $this->cartRepository,
            new Json(),
            $this->helper
        );
    }

    public function testCardBindingPrefixesTheTokenbaseId(): void
    {
        $this->assertSame('card:17', $this->persistor->cardBinding(17));
        $this->assertSame('card:17', $this->persistor->cardBinding('17'));
    }

    public function testSaveReferenceIdWritesTheFullSeedRecordRollbackSafely(): void
    {
        $payment = $this->quotePayment();

        $this->paymentResource->expects($this->once())->method('save')->with($payment);

        $before = time();
        $this->persistor->saveReferenceId($payment, 'ref-123', 'jti-abcdef123456');
        $record = $this->persistor->load($payment);

        $this->assertIsArray($record);
        $this->assertSame('ref-123', $record['reference_id']);
        $this->assertNull($record['auth_transaction_id']);
        $this->assertNull($record['verdict']);
        $this->assertSame([], $record['ca']);
        $this->assertNull($record['amount']);
        $this->assertNull($record['currency']);
        $this->assertSame('jti-abcdef123456', $record['binding']);
        $this->assertGreaterThanOrEqual($before, $record['created_at']);
        $this->assertLessThanOrEqual(time(), $record['created_at']);
    }

    public function testRecordIsStoredAsAJsonStringUnderThePersistKey(): void
    {
        $payment = $this->quotePayment();

        $this->persistor->saveReferenceId($payment, 'ref-123', 'jti-abcdef123456');

        $raw = $payment->getAdditionalInformation(Persistor::PERSIST_KEY);

        $this->assertIsString($raw);
        $this->assertSame('payer_auth', Persistor::PERSIST_KEY);
        $this->assertSame('ref-123', json_decode($raw, true)['reference_id']);
    }

    public function testSaveResultRoundTripsEveryFieldAndPreservesTheReferenceId(): void
    {
        $payment = $this->quotePayment();
        $ca = $this->loadFixture('case-2-1-success')['consumerAuthenticationInformation'];

        $this->persistor->saveReferenceId($payment, 'ref-123', 'jti-abcdef123456');
        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult(Verdict::AUTHENTICATED, $ca),
            '24.00',
            'USD',
            'jti-abcdef123456'
        );

        $record = $this->persistor->load($payment);

        $this->assertSame('ref-123', $record['reference_id']);
        $this->assertSame($ca['authenticationTransactionId'], $record['auth_transaction_id']);
        $this->assertSame('authenticated', $record['verdict']);
        $this->assertSame($ca, $record['ca']);
        $this->assertSame('24.00', $record['amount']);
        $this->assertSame('USD', $record['currency']);
        $this->assertSame('jti-abcdef123456', $record['binding']);
        $this->assertIsInt($record['created_at']);
    }

    public function testSaveResultUnderANewBindingDropsThePriorReferenceId(): void
    {
        $payment = $this->quotePayment();

        $this->persistor->saveReferenceId($payment, 'ref-123', 'jti-abcdef123456');
        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult(Verdict::ATTEMPTED, ['paresStatus' => 'A']),
            '24.00',
            'USD',
            'card:9'
        );

        $record = $this->persistor->load($payment);

        $this->assertNull($record['reference_id']);
        $this->assertSame('card:9', $record['binding']);
        $this->assertSame('attempted', $record['verdict']);
    }

    public function testSaveReferenceIdReplacesAnEntirePriorResultRecord(): void
    {
        $payment = $this->quotePayment();

        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult(Verdict::AUTHENTICATED, ['cavv' => 'AAA', 'paresStatus' => 'Y']),
            '24.00',
            'USD',
            'jti-abcdef123456'
        );
        $this->persistor->saveReferenceId($payment, 'ref-999', 'jti-abcdef123456');

        $record = $this->persistor->load($payment);

        $this->assertSame('ref-999', $record['reference_id']);
        $this->assertNull($record['verdict']);
        $this->assertSame([], $record['ca']);
        $this->assertNull($record['amount']);
        $this->assertNull($record['currency']);
    }

    /**
     * @return array<string, array{0: Verdict, 1: string|null, 2: string|null}>
     */
    public static function obligationMatrixProvider(): array
    {
        return [
            'failed sets the obligation' => [Verdict::FAILED, null, Persistor::OBLIGATION_FAILED],
            'challenge sets the obligation' => [Verdict::CHALLENGE, null, Persistor::OBLIGATION_CHALLENGE],
            'authenticated discharges' => [Verdict::AUTHENTICATED, Persistor::OBLIGATION_FAILED, null],
            'attempted discharges' => [Verdict::ATTEMPTED, Persistor::OBLIGATION_CHALLENGE, null],
            'unavailable preserves a failure' => [
                Verdict::UNAVAILABLE,
                Persistor::OBLIGATION_FAILED,
                Persistor::OBLIGATION_FAILED,
            ],
            'unavailable preserves a challenge' => [
                Verdict::UNAVAILABLE,
                Persistor::OBLIGATION_CHALLENGE,
                Persistor::OBLIGATION_CHALLENGE,
            ],
            'unavailable adds nothing' => [Verdict::UNAVAILABLE, null, null],
        ];
    }

    /**
     * @dataProvider obligationMatrixProvider
     */
    #[DataProvider('obligationMatrixProvider')]
    public function testSaveResultMapsTheVerdictToAnObligation(
        Verdict $verdict,
        ?string $priorObligation,
        ?string $expected
    ): void {
        $payment = $this->quotePayment();

        $this->seedObligation($payment, $priorObligation);

        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult($verdict, ['paresStatus' => 'Y']),
            '24.00',
            'USD',
            'jti-abcdef123456'
        );

        $this->assertSame($expected, $this->persistor->load($payment)['obligation']);
    }

    /**
     * @return array<string, array{0: string|null}>
     */
    public static function obligationProvider(): array
    {
        return [
            'failed' => [Persistor::OBLIGATION_FAILED],
            'challenge' => [Persistor::OBLIGATION_CHALLENGE],
            'none' => [null],
        ];
    }

    /**
     * A fresh setup must NOT launder an outstanding refusal.
     *
     * @dataProvider obligationProvider
     */
    #[DataProvider('obligationProvider')]
    public function testSaveReferenceIdPreservesTheObligation(?string $obligation): void
    {
        $payment = $this->quotePayment();

        $this->seedObligation($payment, $obligation);

        $this->persistor->saveReferenceId($payment, 'ref-999', 'jti-abcdef123456');

        $record = $this->persistor->load($payment);

        $this->assertSame($obligation, $record['obligation']);
        $this->assertNull($record['verdict']);
        $this->assertSame('ref-999', $record['reference_id']);
    }

    public function testObligationRoundTripsThroughSerialization(): void
    {
        $payment = $this->quotePayment();

        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult(Verdict::FAILED, ['paresStatus' => 'N']),
            '24.00',
            'USD',
            'jti-abcdef123456'
        );

        $raw = $payment->getAdditionalInformation(Persistor::PERSIST_KEY);

        $this->assertIsString($raw);
        $this->assertSame(Persistor::OBLIGATION_FAILED, json_decode($raw, true)['obligation']);
        $this->assertSame(Persistor::OBLIGATION_FAILED, $this->persistor->load($payment)['obligation']);
    }

    public function testClearDropsTheObligationToo(): void
    {
        $payment = $this->quotePayment();

        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult(Verdict::FAILED, []),
            '24.00',
            'USD',
            'jti-abcdef123456'
        );
        $this->persistor->clear($payment);

        $this->assertNull($this->persistor->load($payment));
    }

    public function testClearRemovesTheKeyAndPersists(): void
    {
        $payment = $this->quotePayment();

        $this->persistor->saveReferenceId($payment, 'ref-123', 'jti-abcdef123456');

        $this->paymentResource->expects($this->once())->method('save')->with($payment);

        $this->persistor->clear($payment);

        $this->assertNull($payment->getAdditionalInformation(Persistor::PERSIST_KEY));
        $this->assertNull($this->persistor->load($payment));
    }

    public function testClearWithNoRecordDoesNotTouchTheDatabase(): void
    {
        $payment = $this->quotePayment();

        $this->paymentResource->expects($this->never())->method('save');

        $this->persistor->clear($payment);
    }

    public function testLoadReturnsNullForMissingAndUndecodableValues(): void
    {
        $payment = $this->quotePayment();

        $this->assertNull($this->persistor->load($payment));

        $payment->setAdditionalInformation(Persistor::PERSIST_KEY, 'not-json{');
        $this->assertNull($this->persistor->load($payment));

        $payment->setAdditionalInformation(Persistor::PERSIST_KEY, '"scalar"');
        $this->assertNull($this->persistor->load($payment));
    }

    public function testLoadToleratesALegacyArrayValue(): void
    {
        $payment = $this->quotePayment();
        $payment->setAdditionalInformation(Persistor::PERSIST_KEY, ['binding' => 'card:1']);

        $this->assertSame(['binding' => 'card:1'], $this->persistor->load($payment));
    }

    public function testQuotePaymentWithoutAnIdIsNotSaved(): void
    {
        $payment = $this->quotePayment(null);

        $this->paymentResource->expects($this->never())->method('save');

        $this->persistor->saveReferenceId($payment, 'ref-123', 'jti-abcdef123456');

        $this->assertNotNull($this->persistor->load($payment));
    }

    public function testOrderPaymentPersistsOntoTheResolvedQuotePayment(): void
    {
        $quotePayment = $this->quotePayment();
        $orderPayment = $this->orderPayment(77);

        $quote = $this->createMock(Quote::class);
        $quote->method('getPayment')->willReturn($quotePayment);

        $this->cartRepository->expects($this->once())->method('get')->with(77)->willReturn($quote);
        $this->paymentResource->expects($this->once())->method('save')->with($quotePayment);

        $this->persistor->saveReferenceId($orderPayment, 'ref-123', 'card:9');

        // Both the passed-in payment and the quote payment behind it carry the record.
        $this->assertSame('ref-123', $this->persistor->load($orderPayment)['reference_id']);
        $this->assertSame('ref-123', $this->persistor->load($quotePayment)['reference_id']);
    }

    public function testOrderPaymentWithoutAQuoteStaysInMemoryOnly(): void
    {
        $orderPayment = $this->orderPayment(0);

        $this->cartRepository->expects($this->never())->method('get');
        $this->paymentResource->expects($this->never())->method('save');

        $this->persistor->saveReferenceId($orderPayment, 'ref-123', 'card:9');

        $this->assertSame('ref-123', $this->persistor->load($orderPayment)['reference_id']);
    }

    public function testUnresolvableQuoteIsSwallowedAndLeavesTheRecordInMemory(): void
    {
        $orderPayment = $this->orderPayment(77);

        $this->cartRepository->expects($this->once())
            ->method('get')
            ->willThrowException(new \RuntimeException('gone'));
        $this->paymentResource->expects($this->never())->method('save');

        $this->persistor->saveReferenceId($orderPayment, 'ref-123', 'card:9');

        $this->assertSame('ref-123', $this->persistor->load($orderPayment)['reference_id']);
    }

    public function testGenericPaymentInfoIsNeverSaved(): void
    {
        $payment = $this->infoPayment(InfoInterface::class);

        $this->paymentResource->expects($this->never())->method('save');

        $this->persistor->saveReferenceId($payment, 'ref-123', 'card:9');

        $this->assertSame('ref-123', $this->persistor->load($payment)['reference_id']);
    }

    public function testLoggingNeverCarriesTheCaBlockOrARawToken(): void
    {
        $payment = $this->quotePayment();
        $ca = $this->loadFixture('case-2-1-success')['consumerAuthenticationInformation'];
        $messages = [];

        $this->helper->method('log')->willReturnCallback(
            function ($code, $message) use (&$messages) {
                $messages[] = (string)$message;

                return $this->helper;
            }
        );

        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult(Verdict::AUTHENTICATED, $ca),
            '24.00',
            'USD',
            'jti-abcdef123456'
        );
        $this->persistor->clear($payment);

        $this->assertNotEmpty($messages);

        foreach ($messages as $message) {
            $this->assertStringNotContainsString((string)$ca['cavv'], $message);
            $this->assertStringNotContainsString((string)$ca['xid'], $message);
            $this->assertStringNotContainsString('jti-abcdef123456', $message);
        }

        $this->assertStringContainsString('verdict=authenticated', $messages[0]);
        $this->assertStringContainsString('binding=jti-***3456', $messages[0]);
    }

    /**
     * Put a record carrying the given obligation onto the payment, via the public API.
     *
     * @param MockObject $payment
     * @param string|null $obligation
     * @return void
     */
    private function seedObligation($payment, ?string $obligation): void
    {
        if ($obligation === null) {
            return;
        }

        $this->persistor->saveResult(
            $payment,
            new AuthenticationResult(
                $obligation === Persistor::OBLIGATION_FAILED ? Verdict::FAILED : Verdict::CHALLENGE,
                []
            ),
            '24.00',
            'USD',
            'jti-abcdef123456'
        );
    }

    /**
     * Build a quote payment carrying working in-memory additional_information.
     *
     * @param int|null $id
     * @return QuotePayment&MockObject
     */
    private function quotePayment(?int $id = 5)
    {
        $payment = $this->infoPayment(QuotePayment::class);
        $payment->method('getId')->willReturn($id);

        return $payment;
    }

    /**
     * Build an order payment whose order points at the given quote id (0 = no quote).
     *
     * @param int $quoteId
     * @return OrderPayment&MockObject
     */
    private function orderPayment(int $quoteId)
    {
        $order = $this->createMock(Order::class);
        $order->method('getQuoteId')->willReturn($quoteId ?: null);

        $payment = $this->infoPayment(OrderPayment::class);
        $payment->method('getOrder')->willReturn($order);

        return $payment;
    }

    /**
     * Build a payment mock of the given class with functional additional_information storage.
     *
     * @param string $class
     * @return MockObject
     */
    private function infoPayment(string $class)
    {
        $payment = $this->createMock($class);
        $store = [];

        $payment->method('setAdditionalInformation')->willReturnCallback(
            function ($key = null, $value = null) use (&$store, $payment) {
                $store[$key] = $value;

                return $payment;
            }
        );
        $payment->method('getAdditionalInformation')->willReturnCallback(
            function ($key = null) use (&$store) {
                return $key === null ? $store : ($store[$key] ?? null);
            }
        );
        $payment->method('unsAdditionalInformation')->willReturnCallback(
            function ($key = null) use (&$store, $payment) {
                unset($store[$key]);

                return $payment;
            }
        );

        return $payment;
    }
}
