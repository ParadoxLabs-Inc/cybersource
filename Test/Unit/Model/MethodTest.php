<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction\Repository;
use ParadoxLabs\CyberSource\Model\Method;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UnifiedCheckoutResponse;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\AbstractGateway;
use ParadoxLabs\TokenBase\Model\AbstractMethod;
use ParadoxLabs\TokenBase\Model\Card;
use ParadoxLabs\TokenBase\Model\Gateway\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Method
 */
class MethodTest extends TestCase
{
    private const AMOUNT = 24.00;

    private CardBuilder|MockObject $cardBuilderMock;
    private CardRepositoryInterface|MockObject $cardRepositoryMock;
    private UnifiedCheckoutResponse|MockObject $ucResponseMock;
    private Method $method;

    protected function setUp(): void
    {
        $helperMock = $this->createMock(Data::class);
        $helperMock->method('getCurrentStoreId')->willReturn(1);

        $this->cardBuilderMock = $this->createMock(CardBuilder::class);
        $this->cardRepositoryMock = $this->createMock(CardRepositoryInterface::class);
        $this->ucResponseMock = $this->createMock(UnifiedCheckoutResponse::class);

        $this->method = new Method(
            $this->createMock(Repository::class),
            $helperMock,
            $this->createMock(AbstractGateway::class),
            $this->createMock(CardInterfaceFactory::class),
            $this->cardRepositoryMock,
            $this->createMock(Address::class),
            $this->createMock(ConfigInterface::class),
            $this->createMock(Registry::class),
            $this->cardBuilderMock,
            $this->ucResponseMock,
            'paradoxlabs_cybersource'
        );
    }

    /**
     * afterCapture must map the UC token onto the card: authorize_capture routes sale through
     * capture()/afterCapture(), so without this the sale token_information is never persisted.
     *
     * @return void
     */
    public function testAfterCaptureAppliesCardBuilderMappingWhenTokenPresent(): void
    {
        $card     = $this->createMock(CardInterface::class);
        // Flat id strings: the shape Response::interpretResponse() actually emits (see ResponseTest).
        $response = new Response(['token_information' => ['instrumentIdentifier' => 'INSTR-1']]);
        $this->setCard($card);

        $this->cardBuilderMock->expects($this->once())
            ->method('applyTokenToCard')
            ->with($card, $response)
            ->willReturn($card);

        $payment = $this->buildPayment();
        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->invokeHook('afterCapture', $payment, $response);
    }

    /**
     * afterAuthorize retains the original UC mapping behavior for the authorize-only action.
     *
     * @return void
     */
    public function testAfterAuthorizeAppliesCardBuilderMappingWhenTokenPresent(): void
    {
        $card     = $this->createMock(CardInterface::class);
        // Flat id strings: the shape Response::interpretResponse() actually emits (see ResponseTest).
        $response = new Response(['token_information' => ['instrumentIdentifier' => 'INSTR-2']]);
        $this->setCard($card);

        $this->cardBuilderMock->expects($this->once())
            ->method('applyTokenToCard')
            ->with($card, $response)
            ->willReturn($card);

        $payment = $this->buildPayment();
        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->invokeHook('afterAuthorize', $payment, $response);
    }

    /**
     * The single-use transient_token must be cleared on any UC response (here: token minting failed,
     * uc_token_missing set) from both hooks, so recapture/re-auth fall back to the vaulted card.
     *
     * @dataProvider hookProvider
     * @param string $hook
     * @return void
     */
    #[DataProvider('hookProvider')]
    public function testTransientTokenIsClearedOnUnifiedCheckoutResponse(string $hook): void
    {
        $card     = $this->createMock(CardInterface::class);
        $response = new Response(['uc_token_missing' => true]);
        $this->setCard($card);

        $this->cardBuilderMock->expects($this->once())
            ->method('applyTokenToCard')
            ->with($card, $response)
            ->willReturn($card);

        $payment = $this->buildPayment();
        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->invokeHook($hook, $payment, $response);
    }

    /**
     * A non-UC response (neither key set, e.g. legacy SA/SOAP path) must not touch the card or the token.
     *
     * @dataProvider hookProvider
     * @param string $hook
     * @return void
     */
    #[DataProvider('hookProvider')]
    public function testNonUnifiedCheckoutResponseLeavesPaymentAndCardUntouched(string $hook): void
    {
        $card     = $this->createMock(CardInterface::class);
        $response = new Response(['transaction_id' => 'legacy-1']);
        $this->setCard($card);

        $this->cardBuilderMock->expects($this->never())
            ->method('applyTokenToCard');

        $payment = $this->buildPayment();
        $payment->expects($this->never())
            ->method('unsAdditionalInformation');

        $this->invokeHook($hook, $payment, $response);
    }

    // --- Payment cc_* re-sync from card_information (UC Task 1) ---

    /**
     * AbstractMethod copies cc fields from the card PRE-auth (while the card is still empty), so the
     * UC response's card_information must be re-synced onto the payment post-auth — empty fields only.
     *
     * @return void
     */
    public function testPaymentCcFieldsFilledFromCardInformationWhenEmpty(): void
    {
        $card     = $this->createMock(CardInterface::class);
        $response = new Response([
            'token_information' => ['instrumentIdentifier' => 'INSTR-3'],
            'card_information' => [
                'cc_type' => 'VI',
                'cc_last4' => '1111',
                'cc_bin' => '411111',
                'cc_exp_month' => '09',
                'cc_exp_year' => '2029',
            ],
        ]);
        $this->setCard($card);
        $this->cardBuilderMock->method('applyTokenToCard')->willReturn($card);

        $set     = [];
        $payment = $this->buildRecordingPayment([], $set);

        $this->invokeApplyToken($payment, $response);

        // cc_last4 maps to the payment's cc_last_4 column; cc_bin has no payment column and is skipped.
        $this->assertSame(
            [
                'cc_type' => 'VI',
                'cc_last_4' => '1111',
                'cc_exp_month' => '09',
                'cc_exp_year' => '2029',
            ],
            $set
        );
    }

    /**
     * Fields already set on the payment are NEVER overwritten; only the empty ones are filled.
     *
     * @return void
     */
    public function testPaymentCcFieldsNeverOverwriteExistingValues(): void
    {
        $card     = $this->createMock(CardInterface::class);
        $response = new Response([
            'token_information' => ['instrumentIdentifier' => 'INSTR-4'],
            'card_information' => [
                'cc_type' => 'MC',
                'cc_last4' => '4444',
                'cc_exp_month' => '01',
                'cc_exp_year' => '2031',
            ],
        ]);
        $this->setCard($card);
        $this->cardBuilderMock->method('applyTokenToCard')->willReturn($card);

        $set     = [];
        $payment = $this->buildRecordingPayment(
            [
                'cc_type' => 'VI',
                'cc_last_4' => '1111',
            ],
            $set
        );

        $this->invokeApplyToken($payment, $response);

        $this->assertSame(
            [
                'cc_exp_month' => '01',
                'cc_exp_year' => '2031',
            ],
            $set
        );
    }

    /**
     * Guest / unsaved-card orders have no vault card at all; the payment re-sync must still run so
     * sales_order_payment gets its card identity.
     *
     * @return void
     */
    public function testPaymentCcResyncAppliesWithoutVaultCard(): void
    {
        $response = new Response([
            'uc_token_missing' => true,
            'card_information' => [
                'cc_type' => 'VI',
                'cc_last4' => '1111',
            ],
        ]);
        // No card set: getCard() returns null, so CardBuilder must not run — but the re-sync must.
        $this->cardBuilderMock->expects($this->never())->method('applyTokenToCard');

        $set     = [];
        $payment = $this->buildRecordingPayment([], $set);

        $this->invokeApplyToken($payment, $response);

        $this->assertSame(
            [
                'cc_type' => 'VI',
                'cc_last_4' => '1111',
            ],
            $set
        );
    }

    /**
     * A UC response without card_information (e.g. legacy replies) leaves the payment cc fields alone.
     *
     * @return void
     */
    public function testNoCardInformationWritesNoPaymentCcFields(): void
    {
        $card     = $this->createMock(CardInterface::class);
        $response = new Response(['token_information' => ['instrumentIdentifier' => 'INSTR-5']]);
        $this->setCard($card);
        $this->cardBuilderMock->method('applyTokenToCard')->willReturn($card);

        $set     = [];
        $payment = $this->buildRecordingPayment([], $set);

        $this->invokeApplyToken($payment, $response);

        $this->assertSame([], $set);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function hookProvider(): array
    {
        return [
            'afterAuthorize' => ['afterAuthorize'],
            'afterCapture'   => ['afterCapture'],
        ];
    }

    /**
     * A $0 order still has to mint a vault token: AbstractMethod::authorize()/capture() early-return
     * on `$amount <= 0` before ever reaching the gateway or the after-hooks, so without this the card
     * is vaulted with an empty payment_id and no flag, and the later rebill throws.
     *
     * @dataProvider zeroTotalEntryPointProvider
     * @param string $entryPoint
     * @return void
     */
    #[DataProvider('zeroTotalEntryPointProvider')]
    public function testZeroTotalOrderExchangesTransientTokenAndSavesCard(string $entryPoint): void
    {
        $card    = $this->buildCard();
        $payment = $this->buildZeroTotalPayment('jwt-abc');
        $response = new Response(['token_information' => ['instrumentIdentifier' => 'INSTR-1']]);

        $this->ucResponseMock->expects($this->once())
            ->method('tokenizeCard')
            ->with($payment, 'USD', 7)
            ->willReturn($response);

        $this->cardBuilderMock->expects($this->once())
            ->method('applyTokenToCard')
            ->with($card, $response)
            ->willReturn($card);

        // The parent's own card save lives after its `$amount <= 0` return, so the minted ids would
        // never be persisted without the save in tokenizeZeroTotalOrder().
        $this->cardRepositoryMock->expects($this->once())
            ->method('save')
            ->with($card)
            ->willReturn($card);

        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->method->setInfoInstance($payment);
        $this->setCard($card);

        $this->method->{$entryPoint}($payment, 0.0);
    }

    /**
     * No transient token (stored-card $0 order, or a token already consumed by a prior auth) means
     * there is nothing to exchange -- and re-posting a consumed single-use JWT would fail.
     *
     * @dataProvider zeroTotalEntryPointProvider
     * @param string $entryPoint
     * @return void
     */
    #[DataProvider('zeroTotalEntryPointProvider')]
    public function testZeroTotalOrderWithoutTransientTokenMakesNoGatewayCall(string $entryPoint): void
    {
        $card    = $this->buildCard();
        $payment = $this->buildZeroTotalPayment('');

        $this->ucResponseMock->expects($this->never())
            ->method('tokenizeCard');
        $this->cardRepositoryMock->expects($this->never())
            ->method('save');

        $this->method->setInfoInstance($payment);
        $this->setCard($card);

        $this->method->{$entryPoint}($payment, 0.0);
    }

    /**
     * A payable amount takes the normal gateway path, where the token is minted inline by the
     * auth/capture response; the $0 exchange must never fire there.
     *
     * @return void
     */
    public function testPositiveAmountDoesNotRunTheZeroTotalExchange(): void
    {
        $this->ucResponseMock->expects($this->never())
            ->method('tokenizeCard');

        $method = new \ReflectionMethod(Method::class, 'tokenizeZeroTotalOrder');
        $method->invoke($this->method, $this->buildZeroTotalPayment('jwt-abc'), self::AMOUNT);
    }

    /**
     * A token-less $0 exchange fails the order rather than vaulting a dead card silently -- but the
     * card is still saved first, carrying the uc_token_missing flag CardBuilder set on it.
     *
     * @return void
     */
    public function testZeroTotalOrderThrowsWhenNoVaultTokenWasMinted(): void
    {
        $card    = $this->buildCard();
        $payment = $this->buildZeroTotalPayment('jwt-abc');

        $this->ucResponseMock->method('tokenizeCard')
            ->willReturn(new Response(['uc_token_missing' => true]));

        $this->cardBuilderMock->expects($this->once())
            ->method('applyTokenToCard')
            ->willReturn($card);
        $this->cardRepositoryMock->expects($this->once())
            ->method('save')
            ->with($card)
            ->willReturn($card);

        $this->setCard($card);

        $this->expectException(LocalizedException::class);

        $method = new \ReflectionMethod(Method::class, 'tokenizeZeroTotalOrder');
        $method->invoke($this->method, $payment, 0.0);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function zeroTotalEntryPointProvider(): array
    {
        return [
            'authorize' => ['authorize'],
            'capture'   => ['capture'],
        ];
    }

    /**
     * Build a card the parent's setCard()/loadOrCreateCard() plumbing can accept.
     *
     * @return Card&MockObject
     */
    private function buildCard(): Card
    {
        $card = $this->createMock(Card::class);
        $card->method('getTypeInstance')->willReturnSelf();

        return $card;
    }

    /**
     * Build a $0-order payment carrying the given transient token.
     *
     * @param string $transientToken
     * @return Payment&MockObject
     */
    private function buildZeroTotalPayment(string $transientToken): Payment
    {
        $order = $this->createMock(Order::class);
        $order->method('getBaseCurrencyCode')->willReturn('USD');
        $order->method('getStoreId')->willReturn(7);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);
        $payment->method('setData')->willReturnSelf();
        $payment->method('getAdditionalInformation')->willReturnCallback(
            static fn(?string $key = null) => $key === 'transient_token' ? $transientToken : null
        );

        return $payment;
    }

    /**
     * Build a payment whose order has no outstanding balance, so parent::afterCapture() skips reauth.
     *
     * @return Payment&MockObject
     */
    private function buildPayment(): Payment
    {
        $order = $this->createMock(Order::class);
        $order->method('getBaseTotalDue')->willReturn(self::AMOUNT);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);
        $payment->method('setData')->willReturnSelf();

        return $payment;
    }

    /**
     * Invoke a protected after-hook via reflection.
     *
     * @param string $hook
     * @param Payment&MockObject $payment
     * @param Response $response
     * @return void
     */
    private function invokeHook(string $hook, Payment $payment, Response $response): void
    {
        $method = new \ReflectionMethod(Method::class, $hook);
        $method->invoke($this->method, $payment, self::AMOUNT, $response);
    }

    /**
     * Build a payment mock that reads getData from $existingData and records setData into $set.
     *
     * @param array<string, string> $existingData
     * @param array<string, string> $set Captured setData calls, by reference.
     * @return Payment&MockObject
     */
    private function buildRecordingPayment(array $existingData, array &$set): Payment
    {
        $payment = $this->createMock(Payment::class);
        $payment->method('getData')->willReturnCallback(
            static fn(string $key = '') => $existingData[$key] ?? null
        );
        $payment->method('setData')->willReturnCallback(
            function ($key, $value = null) use (&$set, $payment) {
                $set[$key] = $value;

                return $payment;
            }
        );

        return $payment;
    }

    /**
     * Invoke the protected applyUnifiedCheckoutToken directly (bypassing the parent after-hooks).
     *
     * @param Payment&MockObject $payment
     * @param Response $response
     * @return void
     */
    private function invokeApplyToken(Payment $payment, Response $response): void
    {
        $method = new \ReflectionMethod(Method::class, 'applyUnifiedCheckoutToken');
        $method->invoke($this->method, $payment, $response);
    }

    /**
     * Set the protected card on the method under test.
     *
     * @param CardInterface $card
     * @return void
     */
    private function setCard(CardInterface $card): void
    {
        $property = new \ReflectionProperty(AbstractMethod::class, 'card');
        $property->setValue($this->method, $card);
    }
}
