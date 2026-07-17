<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction\Repository;
use ParadoxLabs\CyberSource\Model\Method;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\AbstractGateway;
use ParadoxLabs\TokenBase\Model\AbstractMethod;
use ParadoxLabs\TokenBase\Model\Gateway\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Method
 */
class MethodTest extends TestCase
{
    private const AMOUNT = 24.00;

    private CardBuilder|MockObject $cardBuilderMock;
    private Method $method;

    protected function setUp(): void
    {
        $helperMock = $this->createMock(Data::class);
        $helperMock->method('getCurrentStoreId')->willReturn(1);

        $this->cardBuilderMock = $this->createMock(CardBuilder::class);

        $this->method = new Method(
            $this->createMock(Repository::class),
            $helperMock,
            $this->createMock(AbstractGateway::class),
            $this->createMock(CardInterfaceFactory::class),
            $this->createMock(CardRepositoryInterface::class),
            $this->createMock(Address::class),
            $this->createMock(ConfigInterface::class),
            $this->createMock(Registry::class),
            $this->cardBuilderMock,
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
