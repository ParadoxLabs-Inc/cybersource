<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\BindingValidator;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\BindingValidator
 */
class BindingValidatorTest extends TestCase
{
    use FixtureLoaderTrait;

    /**
     * @var Persistor&MockObject
     */
    private $persistor;

    /**
     * @var Data&MockObject
     */
    private $helper;

    /**
     * @var InfoInterface&MockObject
     */
    private $payment;

    /**
     * @var BindingValidator
     */
    private BindingValidator $validator;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->persistor = $this->createMock(Persistor::class);
        $this->helper = $this->createMock(Data::class);
        $this->payment = $this->createMock(InfoInterface::class);

        // The validator is a PURE GATE: no scenario may write or clear. Asserted globally so a new
        // test cannot reintroduce the discard-on-block bypass without failing here.
        $this->persistor->expects($this->never())->method('clear');
        $this->persistor->expects($this->never())->method('saveResult');
        $this->persistor->expects($this->never())->method('saveReferenceId');

        $this->validator = new BindingValidator($this->persistor, $this->helper);
    }

    public function testMissingRecordResolvesToNullSilently(): void
    {
        $this->persistor->method('load')->willReturn(null);
        $this->helper->expects($this->never())->method('log');

        $this->assertNull($this->validator->resolve($this->payment, '24.00', 'USD', 'jti-1'));
    }

    /**
     * @return array<string, array{0: Verdict}>
     */
    public static function usableVerdictProvider(): array
    {
        return [
            'authenticated' => [Verdict::AUTHENTICATED],
            'attempted' => [Verdict::ATTEMPTED],
            'unavailable' => [Verdict::UNAVAILABLE],
        ];
    }

    /**
     * @dataProvider usableVerdictProvider
     */
    #[DataProvider('usableVerdictProvider')]
    public function testUsableRecordReturnsVerdictAndCaWithoutClearing(Verdict $verdict): void
    {
        $ca = $this->loadFixture('case-2-1-success')['consumerAuthenticationInformation'];

        $this->persistor->method('load')->willReturn($this->record(['verdict' => $verdict->value, 'ca' => $ca]));

        $this->assertSame(
            ['verdict' => $verdict, 'ca' => $ca],
            $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc')
        );
    }

    public function testStoredCardBindingMatchesOnTheCardPrefix(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['binding' => 'card:9']));

        $result = $this->validator->resolve($this->payment, '24.00', 'USD', 'card:9');

        $this->assertSame(Verdict::AUTHENTICATED, $result['verdict']);
    }

    public function testFailedRecordBlocksPlacementAndSurvivesForTheRetry(): void
    {
        $record = $this->record(['verdict' => 'failed', 'obligation' => Persistor::OBLIGATION_FAILED]);

        $this->persistor->method('load')->willReturn($record);

        // First place attempt: blocked.
        try {
            $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
            $this->fail('Expected the first place attempt to be blocked.');
        } catch (CommandException $exception) {
            $this->assertStringContainsString('Your payment could not be verified.', (string)$exception->getMessage());
        }

        // Re-submitting Place Order must be blocked identically — the record was NOT discarded.
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Your payment could not be verified.');

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    public function testFailedRecordBlocksEvenWhenAlsoStale(): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record(['verdict' => 'failed', 'amount' => '99.99', 'created_at' => time() - 5000])
        );

        $this->expectException(CommandException::class);

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    public function testAbandonedChallengeRecordBlocksPlacement(): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record(['verdict' => 'challenge', 'ca' => [], 'obligation' => Persistor::OBLIGATION_CHALLENGE])
        );

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Your payment verification is no longer valid.');

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function obligationProvider(): array
    {
        return [
            'failed' => [Persistor::OBLIGATION_FAILED],
            'challenge' => [Persistor::OBLIGATION_CHALLENGE],
        ];
    }

    /**
     * A wiped-then-re-seeded record (setup run again after a refusal) keeps blocking.
     *
     * @dataProvider obligationProvider
     */
    #[DataProvider('obligationProvider')]
    public function testOutstandingObligationOnAReSeededRecordStillBlocks(string $obligation): void
    {
        $this->persistor->method('load')->willReturn(
            [
                'reference_id' => 'ref-2',
                'auth_transaction_id' => null,
                'verdict' => null,
                'obligation' => $obligation,
                'ca' => [],
                'amount' => null,
                'currency' => null,
                'binding' => 'jti-abc',
                'created_at' => time(),
            ]
        );

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Your payment verification is no longer valid.');

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    /**
     * Issue #12 regression: a failure on card A must not block card C on the same cart.
     *
     * This is the record Persistor::saveReferenceId() now writes when setup runs for a DIFFERENT
     * instrument — the obligation was discharged with the binding change, so nothing is owed and the
     * new card places on its own merits.
     *
     * @return void
     */
    public function testSeedForADifferentInstrumentAfterAFailureDoesNotBlock(): void
    {
        $this->persistor->method('load')->willReturn(
            [
                'reference_id' => 'ref-2',
                'auth_transaction_id' => null,
                'verdict' => null,
                'obligation' => null,
                'obligation_binding' => null,
                'ca' => [],
                'amount' => null,
                'currency' => null,
                'binding' => 'card:9',
                'created_at' => time(),
            ]
        );

        $this->assertNull($this->validator->resolve($this->payment, '24.00', 'USD', 'card:9'));
    }

    /**
     * ...while the SAME instrument stays blocked: the anti-bypass property is what #12 preserves.
     *
     * @dataProvider obligationProvider
     * @param string $obligation
     * @return void
     */
    #[DataProvider('obligationProvider')]
    public function testSeedForTheSameInstrumentAfterAFailureStillBlocks(string $obligation): void
    {
        $this->persistor->method('load')->willReturn(
            [
                'reference_id' => 'ref-2',
                'auth_transaction_id' => null,
                'verdict' => null,
                'obligation' => $obligation,
                'obligation_binding' => 'card:9',
                'ca' => [],
                'amount' => null,
                'currency' => null,
                'binding' => 'card:9',
                'created_at' => time(),
            ]
        );

        $this->expectException(CommandException::class);

        $this->validator->resolve($this->payment, '24.00', 'USD', 'card:9');
    }

    /**
     * An UNAVAILABLE result does not discharge an obligation, so the block stands.
     *
     * @dataProvider obligationProvider
     */
    #[DataProvider('obligationProvider')]
    public function testUnavailableResultDoesNotDischargeAnObligation(string $obligation): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record(['verdict' => Verdict::UNAVAILABLE->value, 'obligation' => $obligation])
        );

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Your payment verification is no longer valid.');

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    public function testObligationIsDischargedByASuccessfulResult(): void
    {
        // The shape the Persistor writes on AUTHENTICATED after a prior refusal: obligation nulled.
        $this->persistor->method('load')->willReturn(
            $this->record(['verdict' => Verdict::AUTHENTICATED->value, 'obligation' => null])
        );

        $result = $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');

        $this->assertSame(Verdict::AUTHENTICATED, $result['verdict']);
    }

    /**
     * An obligation is only overridden by a shift that ACTUALLY covers this charge.
     *
     * The Persistor never writes this shape (a successful result discharges the obligation), but the
     * rule is stated in terms of the record alone, so pin both directions.
     *
     * @dataProvider obligationProvider
     */
    #[DataProvider('obligationProvider')]
    public function testAnObligatedRecordWithADriftedShiftIsStillBlocked(string $obligation): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record([
                'verdict' => Verdict::AUTHENTICATED->value,
                'obligation' => $obligation,
                'amount' => '1.00',
            ])
        );

        $this->expectException(CommandException::class);

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    /**
     * @dataProvider obligationProvider
     */
    #[DataProvider('obligationProvider')]
    public function testAnObligatedRecordCarryingACoveringShiftResolves(string $obligation): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record(['verdict' => Verdict::AUTHENTICATED->value, 'obligation' => $obligation])
        );

        $result = $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');

        $this->assertSame(Verdict::AUTHENTICATED, $result['verdict']);
    }

    public function testSetupOnlyRecordWithNoObligationResolvesToNull(): void
    {
        $this->persistor->method('load')->willReturn(
            [
                'reference_id' => 'ref-1',
                'auth_transaction_id' => null,
                'verdict' => null,
                'obligation' => null,
                'ca' => [],
                'amount' => null,
                'currency' => null,
                'binding' => 'jti-abc',
                'created_at' => time(),
            ]
        );

        $this->assertNull($this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc'));
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string, 2: string, 3: string}>
     */
    public static function driftProvider(): array
    {
        return [
            'amount up' => [[], '24.01', 'USD', 'jti-abc'],
            'amount far up' => [[], '500.00', 'USD', 'jti-abc'],
            'amount unformatted' => [[], '24', 'USD', 'jti-abc'],
            'currency swap' => [[], '24.00', 'EUR', 'jti-abc'],
            'binding jti to other jti' => [[], '24.00', 'USD', 'jti-other'],
            'binding jti to stored card' => [[], '24.00', 'USD', 'card:9'],
            'binding stored card swap' => [['binding' => 'card:1'], '24.00', 'USD', 'card:2'],
            'expired by one second' => [['created_at' => time() - 901], '24.00', 'USD', 'jti-abc'],
            'ancient' => [['created_at' => time() - 86400], '24.00', 'USD', 'jti-abc'],
            'no timestamp' => [['created_at' => 0], '24.00', 'USD', 'jti-abc'],
        ];
    }

    /**
     * @param array<string, mixed> $overrides
     * @dataProvider driftProvider
     */
    #[DataProvider('driftProvider')]
    public function testDriftOnAShiftedRecordDemandsReverificationAndKeepsTheRecord(
        array $overrides,
        string $amount,
        string $currency,
        string $binding
    ): void {
        $this->persistor->method('load')->willReturn($this->record($overrides));

        // First attempt blocks...
        try {
            $this->validator->resolve($this->payment, $amount, $currency, $binding);
            $this->fail('Expected the drifted record to block placement.');
        } catch (CommandException $exception) {
            $this->assertStringContainsString(
                'Your payment verification is no longer valid.',
                (string)$exception->getMessage()
            );
        }

        // ...and so does the retry, because nothing was discarded.
        $this->expectException(CommandException::class);

        $this->validator->resolve($this->payment, $amount, $currency, $binding);
    }

    /**
     * @param array<string, mixed> $overrides
     * @dataProvider driftProvider
     */
    #[DataProvider('driftProvider')]
    public function testDriftOnAnUnavailableRecordResolvesToNullSilently(
        array $overrides,
        string $amount,
        string $currency,
        string $binding
    ): void {
        $this->persistor->method('load')->willReturn(
            $this->record($overrides + ['verdict' => Verdict::UNAVAILABLE->value])
        );

        $this->assertNull($this->validator->resolve($this->payment, $amount, $currency, $binding));
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function amountDirectionProvider(): array
    {
        return [
            // Authenticated at 30.00.
            'one cent over blocks' => ['30.01', false],
            'far over blocks' => ['500.00', false],
            'exact match passes' => ['30.00', true],
            'one cent under passes' => ['29.99', true],
            'store credit reduction passes' => ['5.00', true],
            'zero passes' => ['0.00', true],
        ];
    }

    /**
     * The amount rule is directional: charge <= authenticated.
     *
     * Charging MORE than was authenticated is the attack (authenticate $1, place $500). Charging
     * LESS is legal — store credit / gift cards / partial-payment modules reduce the gateway charge
     * below the quote grand total that was authenticated.
     *
     * @dataProvider amountDirectionProvider
     */
    #[DataProvider('amountDirectionProvider')]
    public function testChargeMayNotExceedTheAuthenticatedAmount(string $charge, bool $allowed): void
    {
        $this->persistor->method('load')->willReturn($this->record(['amount' => '30.00']));

        if ($allowed === false) {
            $this->expectException(CommandException::class);
            $this->expectExceptionMessage('Your payment verification is no longer valid.');

            $this->validator->resolve($this->payment, $charge, 'USD', 'jti-abc');

            return;
        }

        $result = $this->validator->resolve($this->payment, $charge, 'USD', 'jti-abc');

        $this->assertSame(Verdict::AUTHENTICATED, $result['verdict']);
    }

    public function testRecordIsStillUsableOneSecondInsideTheTtl(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['created_at' => time() - 899]));

        $result = $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');

        $this->assertSame(Verdict::AUTHENTICATED, $result['verdict']);
    }

    public function testTtlBoundaryIsFifteenMinutes(): void
    {
        $this->assertSame(900, BindingValidator::MAX_AGE_SECONDS);

        $this->persistor->method('load')->willReturn($this->record(['created_at' => time() - 900]));

        $this->assertNotNull($this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc'));
    }

    public function testCurrencyComparisonIsCaseInsensitive(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['currency' => 'usd']));

        $this->assertNotNull($this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc'));
    }

    public function testRefusalLoggingCarriesNoRecordValues(): void
    {
        $ca = $this->loadFixture('case-2-1-success')['consumerAuthenticationInformation'];
        $messages = [];

        $this->persistor->method('load')->willReturn($this->record(['ca' => $ca, 'amount' => '9.99']));
        $this->helper->method('log')->willReturnCallback(
            function ($code, $message) use (&$messages) {
                $messages[] = (string)$message;

                return $this->helper;
            }
        );

        try {
            $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
            $this->fail('Expected a CommandException for the drifted amount.');
        } catch (CommandException $exception) {
            $this->assertNotEmpty($messages);
        }

        foreach ($messages as $message) {
            $this->assertStringNotContainsString((string)$ca['cavv'], $message);
            $this->assertStringNotContainsString('jti-abc', $message);
            $this->assertStringNotContainsString('9.99', $message);
        }

        $this->assertStringContainsString('reason=amount_mismatch', $messages[0]);
    }

    /**
     * The re-verify refusal is a CONTRACT with the checkout client, not just copy.
     *
     * isReverifyFailure() in view/frontend/web/js/view/payment/method-renderer/paradoxlabs_cybersource.js
     * substring-matches this sentence to decide whether to re-run the ceremony automatically; JS cannot
     * import a PHP constant, so this assertion is the coupling control. If it fails, either restore the
     * wording or change the JS matcher in the same commit — otherwise the auto-retry silently dies and
     * customers hit a dead end on every stale-record refusal.
     */
    public function testReverifyRefusalWordingIsPinnedToTheCheckoutClientMatcher(): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record(['verdict' => 'challenge', 'ca' => [], 'obligation' => Persistor::OBLIGATION_CHALLENGE])
        );

        try {
            $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
            self::fail('An abandoned challenge must be refused.');
        } catch (CommandException $exception) {
            self::assertSame(
                'Your payment verification is no longer valid. Please verify your payment again.',
                $exception->getMessage()
            );
            self::assertStringContainsString(BindingValidator::REVERIFY_MARKER, $exception->getMessage());
        }
    }

    /**
     * Build a usable AUTHENTICATED record for $24.00 USD bound to jti-abc.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function record(array $overrides = []): array
    {
        return $overrides + [
            'reference_id' => 'ref-1',
            'auth_transaction_id' => 'txn-1',
            'verdict' => Verdict::AUTHENTICATED->value,
            'obligation' => null,
            'ca' => ['paresStatus' => 'Y', 'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA='],
            'amount' => '24.00',
            'currency' => 'USD',
            'binding' => 'jti-abc',
            'created_at' => time(),
        ];
    }
}
