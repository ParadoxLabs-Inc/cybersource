<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Exception;
use Magento\Framework\Exception\RuntimeException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\FollowOn;
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

    public function testDeleteCardIssuesTmsDeleteOnPaymentInstrumentAndCustomer(): void
    {
        $this->restMock->method('delete')->willReturnCallback(function (string $path): string {
            $this->deletedPaths[] = $path;

            return '';
        });

        $response = $this->service->deleteCard('PI_123', 'CUST_456');

        $this->assertSame('/tms/v2/payment-instruments/PI_123', $this->deletedPaths[0]);
        $this->assertSame('/tms/v2/customers/CUST_456', $this->deletedPaths[1]);
        $this->assertTrue((bool)$response->getData('is_approved'));
    }

    public function testDeleteCardWithoutCustomerOnlyDeletesInstrument(): void
    {
        $this->restMock->method('delete')->willReturnCallback(function (string $path): string {
            $this->deletedPaths[] = $path;

            return '';
        });

        $this->service->deleteCard('PI_123');

        $this->assertCount(1, $this->deletedPaths);
        $this->assertSame('/tms/v2/payment-instruments/PI_123', $this->deletedPaths[0]);
    }

    public function testDeleteCardToleratesCustomerDeleteFailure(): void
    {
        $this->restMock->method('delete')->willReturnCallback(function (string $path): string {
            $this->deletedPaths[] = $path;
            if (str_contains($path, 'customers')) {
                throw new Exception('customer delete failed', 500);
            }

            return '';
        });

        // Instrument delete succeeds; customer delete failure is swallowed, no exception escapes.
        $response = $this->service->deleteCard('PI_123', 'CUST_456');

        $this->assertTrue((bool)$response->getData('is_approved'));
        $this->assertCount(2, $this->deletedPaths);
    }
}
