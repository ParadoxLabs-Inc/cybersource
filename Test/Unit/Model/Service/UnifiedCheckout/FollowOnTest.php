<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\RuntimeException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\LineItemsBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FollowOnRequest;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FollowOnRequestFactory;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use ParadoxLabs\TokenBase\Model\Gateway\ResponseFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn
 */
class FollowOnTest extends TestCase
{
    private FollowOn $service;
    private Rest|MockObject $restMock;

    /**
     * @var string Captured REST path of the last post()/delete() call.
     */
    private string $lastPath = '';

    /**
     * @var array<string, mixed> Captured request body of the last post().
     */
    private array $lastBody = [];

    /**
     * @var string[] All DELETE paths issued, in order.
     */
    private array $deletedPaths = [];

    protected function setUp(): void
    {
        $this->restMock = $this->createMock(Rest::class);

        $requestFactory = $this->createMock(FollowOnRequestFactory::class);
        $requestFactory->method('create')->willReturnCallback(fn() => new FollowOnRequest());

        $responseFactory = $this->createMock(ResponseFactory::class);
        $responseFactory->method('create')->willReturnCallback(
            static fn(array $args = []) => (new GatewayResponse())->setData($args['data'] ?? [])
        );

        $this->service = new FollowOn(
            $this->restMock,
            $this->createMock(Config::class),
            new Sanitizer(),
            $this->createMock(Data::class),
            $responseFactory,
            $requestFactory,
            new LineItemsBuilder(new Sanitizer()),
        );
    }

    /**
     * @param array<string, mixed> $cannedResponse
     */
    private function primePost(array $cannedResponse): void
    {
        $this->restMock->method('post')
            ->willReturnCallback(function (string $path, array $body) use ($cannedResponse): array {
                $this->lastPath = $path;
                $this->lastBody = $body;

                return $cannedResponse;
            });
    }

    private function primePostThrows(Exception $exception): void
    {
        $this->restMock->method('post')
            ->willReturnCallback(function (string $path) use ($exception): array {
                $this->lastPath = $path;

                throw $exception;
            });
    }

    private function buildPayment(): Payment&MockObject
    {
        $order = $this->createMock(Order::class);
        $order->method('getIncrementId')->willReturn('100000123');
        $order->method('getStoreId')->willReturn(1);
        $order->method('getBaseCurrencyCode')->willReturn('USD');

        $payment = $this->createMock(Payment::class);
        $payment->method('getOrder')->willReturn($order);

        return $payment;
    }

    public function testCaptureBuildsCorrectPathAndBody(): void
    {
        $this->primePost(['id' => 'CAP1', 'status' => 'AUTHORIZED', 'processorInformation' => ['responseCode' => '100']]);

        $response = $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');

        $this->assertSame('/pts/v2/payments/AUTHID9/captures', $this->lastPath);
        $this->assertSame('24.00', $this->lastBody['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $this->lastBody['orderInformation']['amountDetails']['currency']);
        $this->assertSame('100000123', $this->lastBody['clientReferenceInformation']['code']);
        $this->assertFalse($response->getIsError());
        $this->assertSame('CAP1', $response->getData('transaction_id'));
    }

    public function testCaptureSendsLineItems(): void
    {
        // Issue #14: a linked capture carries the invoice line items for Level II/III settlement data.
        $this->primePost(
            ['id' => 'CAP1', 'status' => 'AUTHORIZED', 'processorInformation' => ['responseCode' => '100']]
        );

        $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9', [
            new DataObject([
                'name' => 'Widget',
                'sku' => 'WID-1',
                'qty' => '2',
                'base_price' => '12.0000',
                'base_tax_amount' => '1.9800',
            ]),
        ]);

        $this->assertSame(
            [
                [
                    'productName' => 'Widget',
                    'productSku' => 'WID-1',
                    'quantity' => 2,
                    'unitPrice' => '12.00',
                    'taxAmount' => '1.98',
                ],
            ],
            $this->lastBody['orderInformation']['lineItems']
        );
    }

    public function testCaptureOmitsLineItemsWhenNoneGiven(): void
    {
        // send_line_items off -> no items reach the service -> body identical to the pre-#14 capture.
        $this->primePost(
            ['id' => 'CAP1', 'status' => 'AUTHORIZED', 'processorInformation' => ['responseCode' => '100']]
        );

        $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');

        $this->assertArrayNotHasKey('lineItems', $this->lastBody['orderInformation']);
    }

    public function testRefundBuildsLinkedPathAndPreservesPartialAmount(): void
    {
        $this->primePost(['id' => 'REF1', 'status' => 'PENDING', 'processorInformation' => ['responseCode' => '100']]);

        $this->service->refund($this->buildPayment(), 5.25, 'CAPID7');

        $this->assertSame('/pts/v2/captures/CAPID7/refunds', $this->lastPath);
        $this->assertSame('5.25', $this->lastBody['orderInformation']['amountDetails']['totalAmount']);
    }

    public function testRefundUnlinkedBuildsPaymentPath(): void
    {
        $this->primePost(['id' => 'REF2', 'status' => 'PENDING']);

        $this->service->refundUnlinked($this->buildPayment(), 9.0, 'PAYID3');

        $this->assertSame('/pts/v2/payments/PAYID3/refunds', $this->lastPath);
    }

    public function testVoidBuildsReversalPathAndReversalInformation(): void
    {
        $this->primePost(['id' => 'REV1', 'status' => 'REVERSED']);

        $this->service->void($this->buildPayment(), 24.0, 'AUTHID9');

        $this->assertSame('/pts/v2/payments/AUTHID9/reversals', $this->lastPath);
        // Reversal amount lives under reversalInformation, NOT orderInformation.
        $this->assertSame('24.00', $this->lastBody['reversalInformation']['amountDetails']['totalAmount']);
        $this->assertArrayNotHasKey('orderInformation', $this->lastBody);
    }

    public function testBackCatalogSoapEraIdRoutesThroughRestFollowOn(): void
    {
        // A pre-3.0.0 SOAP-era requestID is just a string id; REST follow-on uses it directly (D4).
        $this->primePost(['id' => '4912345678901234', 'status' => 'AUTHORIZED']);

        $this->service->capture($this->buildPayment(), 10.0, '4912345678901234');

        $this->assertSame('/pts/v2/payments/4912345678901234/captures', $this->lastPath);
    }

    public function testCapture404MapsToSoapCode242(): void
    {
        $this->primePostThrows(new Exception('Not Found', 404));

        try {
            $this->service->capture($this->buildPayment(), 24.0, 'GONE');
            $this->fail('Expected CommandException');
        } catch (CommandException $e) {
            $this->assertSame(FollowOn::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE, $e->getCode());
        }
    }

    public function testCapture404MapsToSoapCode242FromRestRuntimeExceptionShape(): void
    {
        // Rest::throwOnHttpError() now throws a Magento RuntimeException (generic message, raw detail on
        // the cause) with the HTTP status as its code. The 404 -> 242 retry mapping must still fire off
        // getCode() on that shape exactly as it did for the old plain \Exception.
        $this->primePostThrows(
            new RuntimeException(
                __('The transaction was declined. Please verify your payment details and try again.'),
                new Exception('Resource not found', 404),
                404
            )
        );

        try {
            $this->service->capture($this->buildPayment(), 24.0, 'GONE');
            $this->fail('Expected CommandException');
        } catch (CommandException $e) {
            $this->assertSame(FollowOn::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE, $e->getCode());
        }
    }

    public function testRefund404MapsToSoapCode241(): void
    {
        $this->primePostThrows(new Exception('Not Found', 404));

        try {
            $this->service->refund($this->buildPayment(), 24.0, 'GONE');
            $this->fail('Expected CommandException');
        } catch (CommandException $e) {
            $this->assertSame(FollowOn::SOAP_CODE_REFUND_NOT_FOLLOWABLE, $e->getCode());
        }
    }

    public function testCaptureNotFollowableReasonInBodyMapsToSoapCode(): void
    {
        // A 2xx body that is nonetheless not-followable (status + reason) must also map to the SOAP code.
        $this->primePost([
            'id' => 'X',
            'status' => 'INVALID_REQUEST',
            'errorInformation' => ['reason' => 'NOT_FOUND', 'message' => 'Invalid transaction'],
        ]);

        try {
            $this->service->capture($this->buildPayment(), 24.0, 'GONE');
            $this->fail('Expected CommandException');
        } catch (CommandException $e) {
            $this->assertSame(FollowOn::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE, $e->getCode());
        }
    }

    public function testGenuineProcessorDeclineIsNotMappedToNotFollowableCode(): void
    {
        // FAIL-SAFE: a real processor decline carrying errorInformation.reason=PROCESSOR_ERROR AND a
        // decline responseCode must be thrown as a plain decline (its own code), NEVER as the 242/241
        // retry code. Mapping it to 242/241 would re-charge (capture) / re-credit (refund) silently.
        // This test FAILS against the old overbroad PROCESSOR_ERROR substring mapping.
        $this->primePost([
            'id' => 'D9',
            'status' => 'DECLINED',
            'processorInformation' => ['responseCode' => '203'],
            'errorInformation' => ['reason' => 'PROCESSOR_ERROR', 'message' => 'Requested service is forbidden'],
        ]);

        try {
            $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');
            $this->fail('Expected CommandException');
        } catch (CommandException $e) {
            $this->assertSame(203, $e->getCode());
            $this->assertNotSame(FollowOn::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE, $e->getCode());
        }
    }

    public function testInvalidRequestReasonIsNotMappedToNotFollowableCode(): void
    {
        // FAIL-SAFE: INVALID_REQUEST is no longer a not-found reason; with no decline responseCode it is a
        // RuntimeException (surfaced), not a 242/241 retry. This FAILS against the old mapping.
        $this->primePost([
            'id' => 'X',
            'status' => 'INVALID_REQUEST',
            'errorInformation' => ['reason' => 'INVALID_REQUEST', 'message' => 'Invalid field'],
        ]);

        try {
            $this->service->refund($this->buildPayment(), 24.0, 'CAPID7');
            $this->fail('Expected RuntimeException');
        } catch (RuntimeException $e) {
            $this->assertNotSame(FollowOn::SOAP_CODE_REFUND_NOT_FOLLOWABLE, $e->getCode());
        }
    }

    public function testVoidCaptureBuildsCaptureVoidPathWithoutAmount(): void
    {
        $this->primePost(['id' => 'VC1', 'status' => 'VOIDED']);

        $response = $this->service->voidCapture($this->buildPayment(), 'CAPID7');

        $this->assertSame('/pts/v2/captures/CAPID7/voids', $this->lastPath);
        // A capture void is full; no amount is sent (clientReferenceInformation only).
        $this->assertArrayNotHasKey('orderInformation', $this->lastBody);
        $this->assertArrayNotHasKey('reversalInformation', $this->lastBody);
        $this->assertSame('100000123', $this->lastBody['clientReferenceInformation']['code']);
        $this->assertFalse($response->getIsError());
    }

    public function testDeclineThrowsCommandExceptionWithResponseCode(): void
    {
        $this->primePost([
            'id' => 'D1',
            'status' => 'DECLINED',
            'processorInformation' => ['responseCode' => '202'],
            'errorInformation' => ['message' => 'Insufficient funds'],
        ]);

        try {
            $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');
            $this->fail('Expected CommandException');
        } catch (CommandException $e) {
            $this->assertSame(202, $e->getCode());
        }
    }

    public function testCaptureApprovedStatusWithProcessorSpecificResponseCodeIsApproved(): void
    {
        // 4.0.0 regression: the raw processor code is processor-specific; status decides.
        $this->primePost([
            'id' => 'CAP00',
            'status' => 'PENDING',
            'processorInformation' => ['responseCode' => '00'],
        ]);

        $response = $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');

        $this->assertFalse($response->getIsError());
        $this->assertSame('CAP00', $response->getTransactionId());
    }

    public function testDeclinedStatusWithResponseCode100IsStillADecline(): void
    {
        // The inverse: a raw '100' on a DECLINED status is not an approval.
        $this->primePost([
            'id' => 'D100',
            'status' => 'DECLINED',
            'processorInformation' => ['responseCode' => '100'],
            'errorInformation' => ['message' => 'Declined'],
        ]);

        $this->expectException(CommandException::class);

        $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');
    }

    public function testProcessorCodeCollidingWithReservedSoapCodeIsNeutralised(): void
    {
        // A raw code equal to a reserved retry code must not surface as the exception code, or
        // Gateway::capture() would recapture and a failed void would report as benign.
        $this->primePost([
            'id' => 'D242',
            'status' => 'DECLINED',
            'processorInformation' => ['responseCode' => '242'],
            'errorInformation' => ['message' => 'Declined'],
        ]);

        try {
            $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');
            $this->fail('Expected CommandException');
        } catch (CommandException $e) {
            $this->assertSame(0, $e->getCode());
            $this->assertNotSame(FollowOn::SOAP_CODE_CAPTURE_NOT_FOLLOWABLE, $e->getCode());
        }
    }

    public function testGenericErrorThrowsRuntimeException(): void
    {
        $this->primePost([
            'id' => 'E1',
            'status' => 'SERVER_ERROR',
            'errorInformation' => ['message' => 'System error'],
        ]);

        $this->expectException(RuntimeException::class);

        $this->service->capture($this->buildPayment(), 24.0, 'AUTHID9');
    }

    public function testDeleteCardIssuesTmsDeleteOnPaymentInstrument(): void
    {
        $this->restMock->method('delete')->willReturnCallback(function (string $path): string {
            $this->deletedPaths[] = $path;

            return '';
        });

        $response = $this->service->deleteCard('PI_123');

        // Cards are standalone TMS payment instruments; only the instrument delete is issued.
        $this->assertCount(1, $this->deletedPaths);
        $this->assertSame('/tms/v2/payment-instruments/PI_123', $this->deletedPaths[0]);
        $this->assertTrue((bool)$response->getData('is_approved'));
    }

    public function testDeleteCardToleratesInstrument404(): void
    {
        // A 404 on the payment-instrument delete means the token is already gone from TMS. Treat it as
        // success; the whole operation still approves.
        $this->restMock->method('delete')->willReturnCallback(function (string $path): string {
            $this->deletedPaths[] = $path;
            throw new Exception('Not Found', 404);
        });

        $response = $this->service->deleteCard('PI_GONE');

        $this->assertTrue((bool)$response->getData('is_approved'));
        $this->assertSame('/tms/v2/payment-instruments/PI_GONE', $this->deletedPaths[0]);
    }

    public function testDeleteCardPropagatesNonNotFoundInstrumentFailure(): void
    {
        // Any non-404 instrument failure (e.g. a 500) is a real error and must propagate.
        $this->restMock->method('delete')->willReturnCallback(function (string $path): string {
            $this->deletedPaths[] = $path;
            throw new Exception('Server error', 500);
        });

        $this->expectException(Exception::class);
        $this->expectExceptionCode(500);

        $this->service->deleteCard('PI_123');
    }
}
