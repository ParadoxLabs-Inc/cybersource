<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\AuthenticationResult;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ResultClassifier;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Results;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Results
 */
class ResultsTest extends TestCase
{
    use FixtureLoaderTrait;

    public function testExecutePostsTheResultsRequestAndClassifiesTheReply(): void
    {
        $reply  = $this->loadFixture('case-2-1-success');
        $result = new AuthenticationResult(Verdict::AUTHENTICATED, ['paresStatus' => 'Y']);

        $config = $this->createMock(Config::class);
        $config->expects($this->once())->method('setStoreId')->with(2);

        $rest = $this->createMock(Rest::class);
        $rest->expects($this->once())->method('setStoreId')->with(2);
        $rest->expects($this->once())
            ->method('post')
            ->with(
                '/risk/v1/authentication-results',
                [
                    'clientReferenceInformation' => ['code' => 'quote-1234'],
                    'consumerAuthenticationInformation' => [
                        'authenticationTransactionId' => '6544863011992807913018',
                    ],
                ]
            )
            ->willReturn($reply);

        $classifier = $this->createMock(ResultClassifier::class);
        $classifier->expects($this->once())->method('classify')->with($reply)->willReturn($result);

        $request = new ResultsRequest();
        $request->setClientReferenceCode('quote-1234')
            ->setAuthenticationTransactionId('6544863011992807913018');

        $this->assertSame($result, (new Results($config, $rest, $classifier))->execute($request, 2));
    }

    /**
     * The ACS reports the outcome to CyberSource out of band, so the first read can land before it
     * does: AUTHENTICATION_SUCCESSFUL with no paresStatus and no CAVV. Classifying that shape would
     * cost a correctly answered challenge its liability shift (Verdict::UNAVAILABLE), so the
     * service re-reads until the outcome settles.
     */
    public function testExecuteRereadsAnOutcomeThatHasNotLandedYet(): void
    {
        $pending  = ['status' => 'AUTHENTICATION_SUCCESSFUL', 'consumerAuthenticationInformation' => []];
        $settled  = $this->loadFixture('case-2-1-success');
        $result   = new AuthenticationResult(Verdict::AUTHENTICATED, ['paresStatus' => 'Y']);

        $rest = $this->createMock(Rest::class);
        $rest->expects($this->exactly(3))
            ->method('post')
            ->willReturnOnConsecutiveCalls($pending, $pending, $settled);

        $classifier = $this->createMock(ResultClassifier::class);
        // Only the settled reply is classified: the pending ones never reach the verdict rules.
        $classifier->expects($this->once())->method('classify')->with($settled)->willReturn($result);

        $request = new ResultsRequest();
        $request->setAuthenticationTransactionId('6544863011992807913018');

        $service = new TestableResults($this->createMock(Config::class), $rest, $classifier);

        $this->assertSame($result, $service->execute($request));

        // Escalating backoff: the common case costs one short wait, the tail gets progressively
        // longer ones. Measured tail is 4.67s, so the schedule must stay generous.
        $this->assertSame([250000, 500000], $service->pauses);
    }

    /**
     * Any paresStatus is terminal — including U, which is a real "could not authenticate" answer
     * and not a not-landed-yet reply. Retrying those would stall every unavailable outcome.
     */
    public function testExecuteAcceptsAnUnavailableOutcomeWithoutRereading(): void
    {
        $reply  = [
            'status' => 'AUTHENTICATION_SUCCESSFUL',
            'consumerAuthenticationInformation' => ['paresStatus' => 'U', 'veresEnrolled' => 'U'],
        ];
        $result = new AuthenticationResult(Verdict::UNAVAILABLE, ['paresStatus' => 'U']);

        $rest = $this->createMock(Rest::class);
        $rest->expects($this->once())->method('post')->willReturn($reply);

        $classifier = $this->createMock(ResultClassifier::class);
        $classifier->expects($this->once())->method('classify')->with($reply)->willReturn($result);

        $request = new ResultsRequest();
        $request->setAuthenticationTransactionId('6544863011992807913018');

        $service = new TestableResults($this->createMock(Config::class), $rest, $classifier);

        $this->assertSame($result, $service->execute($request));
        $this->assertSame([], $service->pauses, 'A settled outcome must not cost the shopper any wait.');
    }

    /**
     * The re-read is bounded: a challenge that never gets reported must still resolve (as
     * UNAVAILABLE) rather than hold the checkout request open indefinitely.
     */
    public function testExecuteStopsRereadingAtTheAttemptCeiling(): void
    {
        $pending = ['status' => 'AUTHENTICATION_SUCCESSFUL', 'consumerAuthenticationInformation' => []];
        $result  = new AuthenticationResult(Verdict::UNAVAILABLE, []);

        $rest = $this->createMock(Rest::class);
        $rest->expects($this->exactly(7))->method('post')->willReturn($pending);

        $classifier = $this->createMock(ResultClassifier::class);
        $classifier->expects($this->once())->method('classify')->with($pending)->willReturn($result);

        $request = new ResultsRequest();
        $request->setAuthenticationTransactionId('6544863011992807913018');

        $service = new TestableResults($this->createMock(Config::class), $rest, $classifier);

        $this->assertSame($result, $service->execute($request));

        // The whole schedule, and no more: an outcome that never lands must not stall checkout.
        $this->assertSame([250000, 500000, 1000000, 1500000, 2000000, 2500000], $service->pauses);
        $this->assertLessThanOrEqual(
            8000000,
            array_sum($service->pauses),
            'The retry budget must stay within a few seconds; it runs inside a checkout request.'
        );
    }

    public function testEndpointConstantIsTheResultsPath(): void
    {
        $this->assertSame('/risk/v1/authentication-results', Results::RESULTS_PATH);
    }

    public function testMissingTransactionIdThrowsBeforeAnyTransport(): void
    {
        $config = $this->createMock(Config::class);
        $rest   = $this->createMock(Rest::class);
        $rest->expects($this->never())->method('post');

        $classifier = $this->createMock(ResultClassifier::class);
        $classifier->expects($this->never())->method('classify');

        $this->expectException(InputException::class);

        (new Results($config, $rest, $classifier))->execute(new ResultsRequest());
    }
}
