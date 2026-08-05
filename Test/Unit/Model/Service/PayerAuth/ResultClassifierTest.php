<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\AuthenticationResult;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\AuthenticationResultFactory;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ResultClassifier;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Fixture-driven verdict rules, pinned against the live-sandbox outcome matrix.
 *
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\ResultClassifier
 */
class ResultClassifierTest extends TestCase
{
    use FixtureLoaderTrait;

    /**
     * @var Data&MockObject
     */
    private $helper;

    /**
     * @var ResultClassifier
     */
    private ResultClassifier $classifier;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $factory = $this->createMock(AuthenticationResultFactory::class);
        $factory->method('create')->willReturnCallback(
            static fn(array $data): AuthenticationResult => new AuthenticationResult(
                $data['verdict'],
                $data['consumerAuthenticationInformation'] ?? []
            )
        );

        $this->helper = $this->createMock(Data::class);

        $this->classifier = new ResultClassifier($factory, $this->helper);
    }

    /**
     * @return array<string, array{0: string, 1: Verdict}>
     */
    public static function fixtureVerdictProvider(): array
    {
        return [
            '2.1 frictionless success' => ['case-2-1-success', Verdict::AUTHENTICATED],
            '2.2 frictionless fail' => ['case-2-2-frictionless-fail', Verdict::FAILED],
            '2.3 attempts' => ['case-2-3-attempts', Verdict::ATTEMPTED],
            '2.4 unavailable (status SUCCESSFUL trap)' => ['case-2-4-unavailable', Verdict::UNAVAILABLE],
            '2.5 rejected' => ['case-2-5-rejected', Verdict::FAILED],
            '2.6 lookup n/a' => ['case-2-6-lookup-na', Verdict::UNAVAILABLE],
            '2.7 enrollment error' => ['case-2-7-enroll-error', Verdict::UNAVAILABLE],
            '2.8 timeout' => ['case-2-8-timeout', Verdict::UNAVAILABLE],
            '2.9 bypassed' => ['case-2-9-bypassed', Verdict::UNAVAILABLE],
            '2.10a challenge' => ['case-2-10a-challenge', Verdict::CHALLENGE],
            'degraded thin deviceInformation' => [
                'degraded-thin-device-information',
                Verdict::UNAVAILABLE,
            ],
        ];
    }

    /**
     * @param string $fixture
     * @param Verdict $expected
     * @return void
     * @dataProvider fixtureVerdictProvider
     */
    #[DataProvider('fixtureVerdictProvider')]
    public function testEveryPinnedShapeClassifies(string $fixture, Verdict $expected): void
    {
        $result = $this->classifier->classify($this->loadFixture($fixture));

        $this->assertSame($expected, $result->getVerdict());
    }

    public function testSuccessCarriesTheLiabilityShiftFields(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-1-success'));
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::AUTHENTICATED, $result->getVerdict());
        $this->assertTrue($result->getVerdict()->hasLiabilityShift());
        $this->assertSame('Y', $ca['paresStatus']);
        $this->assertSame('05', $ca['eci']);
        $this->assertSame('05', $ca['eciRaw']);
        $this->assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $ca['cavv']);
        $this->assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $ca['xid']);
        $this->assertSame('vbv', $ca['ecommerceIndicator']);
        $this->assertSame('2.2.0', $ca['specificationVersion']);
        $this->assertSame('6544862121286012304009', $result->authenticationTransactionId());
    }

    public function testAttemptsIsAttemptedWithEci06AndCavv(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-3-attempts'));
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::ATTEMPTED, $result->getVerdict());
        $this->assertTrue($result->getVerdict()->hasLiabilityShift());
        $this->assertSame('A', $ca['paresStatus']);
        $this->assertSame('06', $ca['eci']);
        $this->assertNotEmpty($ca['cavv']);
        $this->assertNotEmpty($ca['xid']);
        $this->assertSame('vbv_attempted', $ca['ecommerceIndicator']);
    }

    /**
     * THE MISCLASSIFICATION TRAP: 2.4 replies AUTHENTICATION_SUCCESSFUL with paresStatus U and no
     * CAVV. Keying on the top-level status would attach a liability shift that does not exist.
     *
     * @return void
     */
    public function testUnavailableDespiteSuccessfulStatusIsTheMisclassificationTrap(): void
    {
        $reply = $this->loadFixture('case-2-4-unavailable');

        $this->assertSame('AUTHENTICATION_SUCCESSFUL', $reply['status']);

        $result = $this->classifier->classify($reply);
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::UNAVAILABLE, $result->getVerdict());
        $this->assertFalse($result->getVerdict()->hasLiabilityShift());
        $this->assertSame('U', $ca['paresStatus']);
        $this->assertArrayNotHasKey('cavv', $ca);
        $this->assertSame('07', $ca['eci']);
        $this->assertSame('vbv_failure', $ca['ecommerceIndicator']);
    }

    public function testRejectedIsFailedWithParesStatusR(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-5-rejected'));
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::FAILED, $result->getVerdict());
        $this->assertSame('R', $ca['paresStatus']);
        $this->assertArrayNotHasKey('cavv', $ca);
        $this->assertArrayNotHasKey('eci', $ca);
        $this->assertSame('07', $ca['eciRaw']);
    }

    public function testFrictionlessFailIsFailedWithParesStatusN(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-2-frictionless-fail'));

        $this->assertSame(Verdict::FAILED, $result->getVerdict());
        $this->assertSame('N', $result->getConsumerAuthenticationInformation()['paresStatus']);
    }

    public function testBypassedIsUnavailableWithVeresEnrolledB(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-9-bypassed'));
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::UNAVAILABLE, $result->getVerdict());
        $this->assertSame('B', $ca['veresEnrolled']);
        $this->assertArrayNotHasKey('paresStatus', $ca);
        $this->assertArrayNotHasKey('cavv', $ca);
        $this->assertSame('internet', $ca['ecommerceIndicator']);
    }

    public function testChallengeExposesAcsUrlAndPareq(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-10a-challenge'));
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::CHALLENGE, $result->getVerdict());
        $this->assertFalse($result->getVerdict()->hasLiabilityShift());
        $this->assertSame(
            'https://1merchantacsstag.cardinalcommerce.com/MerchantACSWeb/creq.jsp',
            $result->acsUrl()
        );
        $this->assertStringStartsWith('eyJtZXNzYWdlVHlwZSI6IkNSZXEi', (string)$result->pareq());
        $this->assertSame('6544863011992807913018', $result->authenticationTransactionId());
        // challengeRequired is "N" on a real challenge — never key on it.
        $this->assertSame('N', $ca['challengeRequired']);
        $this->assertSame('C', $ca['paresStatus']);
    }

    public function testLookupUnavailableSurfacesDirectoryServerError(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-6-lookup-na'));
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::UNAVAILABLE, $result->getVerdict());
        $this->assertSame('U', $ca['veresEnrolled']);
        $this->assertSame('101', $ca['directoryServerErrorCode']);
    }

    public function testTimeoutKeepsOutageExemptionIndicator(): void
    {
        $result = $this->classifier->classify($this->loadFixture('case-2-8-timeout'));
        $ca     = $result->getConsumerAuthenticationInformation();

        $this->assertSame(Verdict::UNAVAILABLE, $result->getVerdict());
        $this->assertSame('402', $ca['directoryServerErrorCode']);
        $this->assertSame('1', $ca['strongAuthentication']['OutageExemptionIndicator']);
    }

    public function testUnavailableLogsTheDirectoryServerDiagnosticsWithoutSecrets(): void
    {
        $logged = null;

        $this->helper->expects($this->once())
            ->method('log')
            ->willReturnCallback(
                function ($code, $message) use (&$logged) {
                    $logged = (string)$message;

                    return $this->helper;
                }
            );

        $this->classifier->classify($this->loadFixture('case-2-7-enroll-error'));

        $this->assertStringContainsString('directoryServerErrorCode=101', (string)$logged);
        $this->assertStringContainsString(
            'directoryServerErrorDescription=Error Processing Message Request 1001',
            (string)$logged
        );
        $this->assertStringNotContainsString('AxizbwSTdSyq', (string)$logged);
    }

    public function testAuthenticatedOutcomeIsNotLogged(): void
    {
        $this->helper->expects($this->never())->method('log');

        $this->classifier->classify($this->loadFixture('case-2-1-success'));
    }

    /**
     * @return array<string, array{0: array<string, mixed>}>
     */
    public static function noCavvProvider(): array
    {
        return [
            'paresStatus Y without cavv' => [
                [
                    'status' => 'AUTHENTICATION_SUCCESSFUL',
                    'consumerAuthenticationInformation' => [
                        'paresStatus' => 'Y',
                        'eci' => '05',
                    ],
                ],
            ],
            'paresStatus A without cavv' => [
                [
                    'status' => 'AUTHENTICATION_SUCCESSFUL',
                    'consumerAuthenticationInformation' => [
                        'paresStatus' => 'A',
                        'eci' => '06',
                    ],
                ],
            ],
            'paresStatus Y with empty cavv' => [
                [
                    'status' => 'AUTHENTICATION_SUCCESSFUL',
                    'consumerAuthenticationInformation' => [
                        'paresStatus' => 'Y',
                        'cavv' => '   ',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $reply
     * @return void
     * @dataProvider noCavvProvider
     */
    #[DataProvider('noCavvProvider')]
    public function testLiabilityShiftRequiresACavv(array $reply): void
    {
        $this->assertSame(Verdict::UNAVAILABLE, $this->classifier->classify($reply)->getVerdict());
    }

    public function testEmptyReplyIsUnavailable(): void
    {
        $result = $this->classifier->classify([]);

        $this->assertSame(Verdict::UNAVAILABLE, $result->getVerdict());
        $this->assertSame([], $result->getConsumerAuthenticationInformation());
        $this->assertNull($result->authenticationTransactionId());
        $this->assertNull($result->acsUrl());
        $this->assertNull($result->pareq());
    }

    public function testParesStatusFailureWithoutFailedStatusStillBlocks(): void
    {
        $reply = [
            'status' => 'AUTHENTICATION_SUCCESSFUL',
            'consumerAuthenticationInformation' => [
                'paresStatus' => 'N',
                'cavv' => 'AJkBBkhgQQAAAE4gSEJydQAAAAA=',
            ],
        ];

        $this->assertSame(Verdict::FAILED, $this->classifier->classify($reply)->getVerdict());
    }

    public function testChallengeWinsOverEveryOtherRule(): void
    {
        $reply = [
            'status' => 'PENDING_AUTHENTICATION',
            'consumerAuthenticationInformation' => [
                'paresStatus' => 'C',
                'acsUrl' => 'https://acs.example.com/creq',
            ],
        ];

        $this->assertSame(Verdict::CHALLENGE, $this->classifier->classify($reply)->getVerdict());
    }

    public function testNonArrayConsumerAuthenticationBlockIsNormalizedAway(): void
    {
        $result = $this->classifier->classify([
            'status' => 'AUTHENTICATION_SUCCESSFUL',
            'consumerAuthenticationInformation' => 'unexpected-scalar',
        ]);

        $this->assertSame(Verdict::UNAVAILABLE, $result->getVerdict());
        $this->assertSame([], $result->getConsumerAuthenticationInformation());
    }

    public function testNumericLeavesAreNormalizedToStrings(): void
    {
        $result = $this->classifier->classify([
            'status' => 'AUTHENTICATION_SUCCESSFUL',
            'consumerAuthenticationInformation' => [
                'paresStatus' => 'Y',
                'cavv' => 'AJkBBkhgQQAAAE4gSEJydQAAAAA=',
                'eci' => 5,
            ],
        ]);

        $this->assertSame(Verdict::AUTHENTICATED, $result->getVerdict());
        $this->assertSame('5', $result->getConsumerAuthenticationInformation()['eci']);
    }
}
