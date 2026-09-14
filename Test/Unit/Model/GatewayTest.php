<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\HTTP\ClientInterfaceFactory;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Card;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Gateway;
use ParadoxLabs\CyberSource\Model\Gateway\Context;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UnifiedCheckoutResponse;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use ParadoxLabs\TokenBase\Model\Gateway\Xml;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Gateway
 */
class GatewayTest extends TestCase
{
    private Gateway $gateway;
    private UnifiedCheckoutResponse|MockObject $ucResponse;
    private FollowOn|MockObject $followOn;
    private ResponseFactory|MockObject $responseFactory;

    protected function setUp(): void
    {
        $this->ucResponse = $this->createMock(UnifiedCheckoutResponse::class);
        $this->followOn   = $this->createMock(FollowOn::class);

        $context = new Context(
            $this->createMock(Config::class),
            $this->createMock(Rest::class),
            $this->ucResponse,
            $this->followOn,
        );

        $this->responseFactory = $this->createMock(ResponseFactory::class);
        $this->responseFactory->method('create')->willReturnCallback(
            static fn(array $args = []) => (new GatewayResponse())->setData($args['data'] ?? [])
        );

        $this->gateway = new Gateway(
            $this->createMock(Data::class),
            $this->createMock(Xml::class),
            $this->responseFactory,
            $this->createMock(ClientInterfaceFactory::class),
            $context,
        );
    }

    private function buildPayment(?string $transientToken = null): Payment&MockObject
    {
        $order = $this->createMock(Order::class);
        $order->method('getTotalDue')->willReturn(24.0);
        $order->method('getTotalPaid')->willReturn(24.0);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);
        $payment->method('getAdditionalInformation')
            ->willReturnCallback(
                static fn(?string $key = null) => $key === 'transient_token' ? $transientToken : null
            );

        return $payment;
    }

    public function testAuthorizeDelegatesTransientPathToA1(): void
    {
        $payment  = $this->buildPayment('the.jwt.token');
        $expected = (new GatewayResponse())->setData(['transaction_id' => 'TXN1']);

        $this->ucResponse->expects($this->once())
            ->method('place')
            ->with($payment, 24.0)
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->authorize($payment, 24.0));
    }

    public function testAuthorizePassesLineItemsThrough(): void
    {
        // Issue #14: the items TokenBase attached (send_line_items gate) must reach the request
        // builder; a gateway with none set passes an empty list.
        $payment  = $this->buildPayment('the.jwt.token');
        $items    = [new DataObject(['sku' => 'WID-1'])];
        $expected = (new GatewayResponse())->setData(['transaction_id' => 'TXN1']);

        $this->gateway->setLineItems($items);

        $this->ucResponse->expects($this->once())
            ->method('place')
            ->with($payment, 24.0, false, $items)
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->authorize($payment, 24.0));
    }

    public function testAuthorizeStoredCardSeamThrowsUntilA4(): void
    {
        // No transient token -> stored-card path -> A4 seam, which fails loudly (not faked).
        $this->expectException(RuntimeException::class);

        $this->gateway->authorize($this->buildPayment(), 24.0);
    }

    public function testCaptureLinkedDelegatesToFollowOnWithStoredId(): void
    {
        $payment  = $this->buildPayment();
        $expected = (new GatewayResponse())->setData(['transaction_id' => 'CAP1']);

        $this->gateway->setHaveAuthorized(true)->setTransactionId('AUTHID9');

        $this->followOn->expects($this->once())
            ->method('capture')
            ->with($payment, 24.0, 'AUTHID9')
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->capture($payment, 24.0));
    }

    public function testCaptureLinkedPassesLineItemsThrough(): void
    {
        // Issue #14: invoice items attached by TokenBase ride the linked capture for L2/L3 data.
        $payment  = $this->buildPayment();
        $items    = [new DataObject(['sku' => 'WID-1'])];
        $expected = (new GatewayResponse())->setData(['transaction_id' => 'CAP1']);

        $this->gateway->setHaveAuthorized(true)->setTransactionId('AUTHID9')->setLineItems($items);

        $this->followOn->expects($this->once())
            ->method('capture')
            ->with($payment, 24.0, 'AUTHID9', $items)
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->capture($payment, 24.0));
    }

    public function testCaptureBundledDelegatesToA1WhenNoPriorAuth(): void
    {
        $payment  = $this->buildPayment('the.jwt.token');
        $expected = (new GatewayResponse())->setData(['transaction_id' => 'SALE1']);

        $this->gateway->setHaveAuthorized(false);

        $this->ucResponse->expects($this->once())
            ->method('place')
            ->with($payment, 24.0)
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->capture($payment, 24.0));
    }

    public function testCaptureRecaptureFiresOnSoapCode242(): void
    {
        $payment = $this->buildPayment('the.jwt.token');
        $card    = $this->createMock(Card::class);
        $this->gateway->setData('card', $card);

        $this->gateway->setHaveAuthorized(true)->setTransactionId('AUTHID9');

        // Linked capture throws the SOAP-equivalent not-followable code -> gateway must recapture bundled.
        $this->followOn->expects($this->once())
            ->method('capture')
            ->willThrowException(new CommandException(__('gone'), null, 242));

        $expected = (new GatewayResponse())->setData(['transaction_id' => 'SALE_RETRY']);
        $this->ucResponse->expects($this->once())
            ->method('place')
            ->with($payment, 24.0)
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->capture($payment, 24.0));
    }

    public function testRefundLinkedDelegatesToFollowOn(): void
    {
        $payment  = $this->buildPayment();
        $expected = (new GatewayResponse())->setData(['transaction_id' => 'REF1']);

        $this->gateway->setTransactionId('CAPID7');

        $this->followOn->expects($this->once())
            ->method('refund')
            ->with($payment, 5.25, 'CAPID7')
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->refund($payment, 5.25));
    }

    public function testRefundUnlinkedFallbackFiresOnSoapCode241(): void
    {
        $payment = $this->buildPayment();
        // Parent txn is the CAPTURE id (PAYID3-capture); the unlinked credit must target the original
        // PAYMENT id (PAYID3, suffix stripped), NOT the capture id the linked refund failed against.
        $payment->method('getParentTransactionId')->willReturn('PAYID3-capture');
        $card    = $this->createMock(Card::class);
        $this->gateway->setData('card', $card);
        $this->gateway->setTransactionId('CAPID7');

        $this->followOn->expects($this->once())
            ->method('refund')
            ->willThrowException(new CommandException(__('expired'), null, 241));

        $expected = (new GatewayResponse())->setData(['transaction_id' => 'CREDIT1']);
        $this->followOn->expects($this->once())
            ->method('refundUnlinked')
            ->with($payment, 5.0, 'PAYID3')
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->refund($payment, 5.0));
    }

    public function testVoidDelegatesReversalToFollowOn(): void
    {
        // Uncaptured auth (totalDue > 0) -> auth reversal on the auth id.
        $payment  = $this->buildPayment();
        $expected = (new GatewayResponse())->setData(['transaction_id' => 'REV1']);

        $this->gateway->setTransactionId('AUTHID9');

        $this->followOn->expects($this->once())
            ->method('void')
            ->with($payment, 24.0, 'AUTHID9')
            ->willReturn($expected);
        $this->followOn->expects($this->never())->method('voidCapture');

        $this->assertSame($expected, $this->gateway->void($payment));
    }

    public function testVoidSettledCaptureRoutesToCaptureVoid(): void
    {
        // Settled order (totalDue == 0) -> capture void on the capture id, NOT an auth reversal.
        $order = $this->createMock(Order::class);
        $order->method('getTotalDue')->willReturn(0.0);
        $order->method('getTotalPaid')->willReturn(24.0);

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);

        $expected = (new GatewayResponse())->setData(['transaction_id' => 'VC1']);

        $this->gateway->setTransactionId('CAPID7');

        $this->followOn->expects($this->never())->method('void');
        $this->followOn->expects($this->once())
            ->method('voidCapture')
            ->with($payment, 'CAPID7')
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->void($payment));
    }

    public function testDeleteCardIssuesTmsDeleteOnStoredPaymentId(): void
    {
        // profileId stays stubbed to prove deleteCard never reads it (standalone TMS payment instrument).
        $card = $this->createMock(Card::class);
        $card->method('getPaymentId')->willReturn('PI_123');
        $card->method('getProfileId')->willReturn('CUST_456');
        $this->gateway->setCard($card);

        $expected = (new GatewayResponse())->setData(['is_approved' => true]);
        $this->followOn->expects($this->once())
            ->method('deleteCard')
            ->with('PI_123')
            ->willReturn($expected);

        $this->assertSame($expected, $this->gateway->deleteCard());
    }

    public function testDeleteCardWithEmptyPaymentIdSkipsRemoteDeleteAndApproves(): void
    {
        // Untokenized card (uc_token_missing) has no paymentId: an empty-id DELETE would 404 and block the
        // local card delete. The gateway must skip the remote delete entirely and approve so the card is
        // removed locally.
        $card = $this->createMock(Card::class);
        $card->method('getPaymentId')->willReturn('');
        $card->method('getProfileId')->willReturn('CUST_456');
        $this->gateway->setCard($card);

        $this->followOn->expects($this->never())->method('deleteCard');

        $response = $this->gateway->deleteCard();

        $this->assertTrue((bool)$response->getData('is_approved'));
    }
}
