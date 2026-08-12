<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\PriorTransactionIdTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * The host classes (Gateway, Response) only exercise the parent-transaction-id path with a
 * populated parent id. The last-trans-id fallback and the unreachable-id ('') contract are
 * uncovered branches, so they are pinned directly here.
 *
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\PriorTransactionIdTrait
 */
class PriorTransactionIdTraitTest extends TestCase
{
    private object $subject;

    protected function setUp(): void
    {
        $this->subject = new class {
            use PriorTransactionIdTrait;
        };
    }

    /**
     * Build a payment stub with the two transaction-id sources the trait consults.
     */
    private function buildPayment(?string $parentTransactionId, ?string $lastTransId): InfoInterface
    {
        $payment = $this->createMock(Payment::class);
        $payment->method('getParentTransactionId')->willReturn($parentTransactionId);
        $payment->method('getLastTransId')->willReturn($lastTransId);

        return $payment;
    }

    /**
     * Invoke the protected trait method on the anonymous host.
     */
    private function getPriorTransactionId(InfoInterface $payment): string
    {
        $method = new ReflectionMethod($this->subject, 'getPriorTransactionId');

        return $method->invoke($this->subject, $payment);
    }

    /**
     * @dataProvider priorTransactionIdDataProvider
     */
    #[DataProvider('priorTransactionIdDataProvider')]
    public function testGetPriorTransactionId(
        ?string $parentTransactionId,
        ?string $lastTransId,
        string $expected
    ): void {
        $this->assertSame(
            $expected,
            $this->getPriorTransactionId($this->buildPayment($parentTransactionId, $lastTransId))
        );
    }

    public static function priorTransactionIdDataProvider(): array
    {
        return [
            // Parent id wins when present.
            'parent id preferred over last trans id' => ['PAYID1', 'OTHERID', 'PAYID1'],
            'bare parent id passes through' => ['PAYID1', null, 'PAYID1'],

            // Suffix stripping: CyberSource follow-on ids are '<paymentId>-<operation>'; the API
            // wants the original PAYMENT id, not the capture/refund child.
            'capture suffix stripped' => ['PAYID1-capture', null, 'PAYID1'],
            'refund suffix stripped' => ['PAYID1-refund', null, 'PAYID1'],
            'void suffix stripped' => ['PAYID1-void', null, 'PAYID1'],
            'only first segment kept on multiple hyphens' => ['PAYID1-capture-2', null, 'PAYID1'],

            // Fallback to last trans id when there is no parent (first follow-on against an auth).
            'falls back to last trans id when parent null' => [null, 'LASTID1', 'LASTID1'],
            'falls back to last trans id when parent empty string' => ['', 'LASTID1', 'LASTID1'],
            'fallback also gets suffix stripped' => [null, 'LASTID1-capture', 'LASTID1'],

            // Unreachable prior id: must degrade to '' so callers omit the reference entirely
            // rather than sending a bogus/partial id upstream.
            'both null yields empty string' => [null, null, ''],
            'both empty yields empty string' => ['', '', ''],

            // A leading hyphen means there is no id segment at all.
            'leading hyphen yields empty string' => ['-capture', null, ''],
        ];
    }

    /**
     * Numeric transaction ids arrive from the ORM as ints; the trait's contract is a string return.
     */
    public function testNumericTransactionIdIsReturnedAsString(): void
    {
        $payment = $this->createMock(Payment::class);
        $payment->method('getParentTransactionId')->willReturn(1234567890);
        $payment->method('getLastTransId')->willReturn(null);

        $result = $this->getPriorTransactionId($payment);

        $this->assertSame('1234567890', $result);
    }
}
