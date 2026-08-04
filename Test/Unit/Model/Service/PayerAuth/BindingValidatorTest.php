<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Model\InfoInterface;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\BindingValidator;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Persistor;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
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

        $this->validator = new BindingValidator($this->persistor, $this->helper);
    }

    public function testMissingRecordResolvesToNullSilently(): void
    {
        $this->persistor->method('load')->willReturn(null);
        $this->persistor->expects($this->never())->method('clear');
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
    public function testUsableRecordReturnsVerdictAndCaWithoutClearing(Verdict $verdict): void
    {
        $ca = $this->loadFixture('case-2-1-success')['consumerAuthenticationInformation'];

        $this->persistor->method('load')->willReturn($this->record(['verdict' => $verdict->value, 'ca' => $ca]));
        $this->persistor->expects($this->never())->method('clear');

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

    public function testFailedRecordIsDiscardedAndBlocksPlacement(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['verdict' => 'failed']));
        $this->persistor->expects($this->once())->method('clear')->with($this->payment);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Your payment could not be verified.');

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    public function testFailedRecordBlocksEvenWhenAlsoStale(): void
    {
        $this->persistor->method('load')->willReturn(
            $this->record(['verdict' => 'failed', 'amount' => '99.99', 'created_at' => time() - 5000])
        );
        $this->persistor->expects($this->once())->method('clear');

        $this->expectException(CommandException::class);

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    public function testAbandonedChallengeRecordIsDiscardedAndBlocksPlacement(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['verdict' => 'challenge', 'ca' => []]));
        $this->persistor->expects($this->once())->method('clear')->with($this->payment);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Your payment verification is no longer valid.');

        $this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc');
    }

    public function testSetupOnlyRecordIsDiscardedAndResolvesToNull(): void
    {
        $this->persistor->method('load')->willReturn(
            [
                'reference_id' => 'ref-1',
                'auth_transaction_id' => null,
                'verdict' => null,
                'ca' => [],
                'amount' => null,
                'currency' => null,
                'binding' => 'jti-abc',
                'created_at' => time(),
            ]
        );
        $this->persistor->expects($this->once())->method('clear')->with($this->payment);

        $this->assertNull($this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc'));
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string, 2: string, 3: string}>
     */
    public static function driftProvider(): array
    {
        return [
            'amount up' => [[], '24.01', 'USD', 'jti-abc'],
            'amount down' => [[], '23.99', 'USD', 'jti-abc'],
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
    public function testDriftOnAShiftedRecordDiscardsAndDemandsReverification(
        array $overrides,
        string $amount,
        string $currency,
        string $binding
    ): void {
        $this->persistor->method('load')->willReturn($this->record($overrides));
        $this->persistor->expects($this->once())->method('clear')->with($this->payment);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Your payment verification is no longer valid.');

        $this->validator->resolve($this->payment, $amount, $currency, $binding);
    }

    /**
     * @param array<string, mixed> $overrides
     * @dataProvider driftProvider
     */
    public function testDriftOnAnUnavailableRecordDiscardsSilently(
        array $overrides,
        string $amount,
        string $currency,
        string $binding
    ): void {
        $this->persistor->method('load')->willReturn(
            $this->record($overrides + ['verdict' => Verdict::UNAVAILABLE->value])
        );
        $this->persistor->expects($this->once())->method('clear')->with($this->payment);

        $this->assertNull($this->validator->resolve($this->payment, $amount, $currency, $binding));
    }

    public function testRecordIsStillUsableOneSecondInsideTheTtl(): void
    {
        $this->persistor->method('load')->willReturn($this->record(['created_at' => time() - 899]));
        $this->persistor->expects($this->never())->method('clear');

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
        $this->persistor->expects($this->never())->method('clear');

        $this->assertNotNull($this->validator->resolve($this->payment, '24.00', 'USD', 'jti-abc'));
    }

    public function testDiscardLoggingCarriesNoRecordValues(): void
    {
        $ca = $this->loadFixture('case-2-1-success')['consumerAuthenticationInformation'];
        $messages = [];

        $this->persistor->method('load')->willReturn($this->record(['ca' => $ca, 'amount' => '99.99']));
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
            $this->assertStringNotContainsString('99.99', $message);
        }

        $this->assertStringContainsString('reason=amount_mismatch', $messages[0]);
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
            'ca' => ['paresStatus' => 'Y', 'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA='],
            'amount' => '24.00',
            'currency' => 'USD',
            'binding' => 'jti-abc',
            'created_at' => time(),
        ];
    }
}
