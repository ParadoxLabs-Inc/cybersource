<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use ParadoxLabs\CyberSource\Model\Service\PayerAuth\PassThroughMapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\PassThroughMapper
 */
class PassThroughMapperTest extends TestCase
{
    use FixtureLoaderTrait;

    private PassThroughMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new PassThroughMapper();
    }

    /**
     * Pull the consumerAuthenticationInformation block out of a pinned live-sandbox reply fixture.
     *
     * @param string $name
     * @return array<string, mixed>
     */
    private function loadCa(string $name): array
    {
        $reply = $this->loadFixture($name);

        $this->assertIsArray($reply['consumerAuthenticationInformation'] ?? null);

        return $reply['consumerAuthenticationInformation'];
    }

    public function testVisaAuthenticatedSendsCavvAndNoUcaf(): void
    {
        // Case 2.1 (live sandbox): paresStatus Y, ECI 05, CAVV + dsTransId minted.
        $mapped = $this->mapper->map($this->loadCa('case-2-1-success'), 'VI');
        $block  = $mapped['consumerAuthenticationInformation'];

        $this->assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $block['cavv']);
        $this->assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $block['xid']);
        $this->assertSame('05', $block['eciRaw']);
        $this->assertSame('Y', $block['paresStatus']);
        // specificationVersion is renamed to the payments-API field name.
        $this->assertSame('2.2.0', $block['paSpecificationVersion']);
        $this->assertArrayNotHasKey('specificationVersion', $block);
        $this->assertSame(
            '0f4e0e6d-9b5c-4b3e-9a2f-2b0f1f0a1c11',
            $block['directoryServerTransactionId']
        );

        // CAVV networks never carry the Mastercard UCAF pair.
        $this->assertArrayNotHasKey('ucafAuthenticationData', $block);
        $this->assertArrayNotHasKey('ucafCollectionIndicator', $block);

        // The reply's TEXT indicator is passed through verbatim, never derived.
        $this->assertSame('vbv', $mapped['commerceIndicator']);
    }

    public function testVisaAttemptedSendsCavvAndAttemptedIndicator(): void
    {
        // Case 2.3 (live sandbox): paresStatus A, ECI 06, CAVV + xid present.
        $mapped = $this->mapper->map($this->loadCa('case-2-3-attempts'), 'VI');
        $block  = $mapped['consumerAuthenticationInformation'];

        $this->assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $block['cavv']);
        $this->assertSame('06', $block['eciRaw']);
        $this->assertSame('A', $block['paresStatus']);
        $this->assertArrayNotHasKey('ucafAuthenticationData', $block);
        $this->assertSame('vbv_attempted', $mapped['commerceIndicator']);
    }

    public function testMastercardAuthenticatedRidesUcafAndOmitsCavv(): void
    {
        // Same authentication value, different rail: Mastercard carries the AAV as UCAF, so `cavv`
        // must NOT be sent and the collection indicator says "fully authenticated" (paresStatus Y).
        $ca = $this->loadCa('case-2-1-success');
        $ca['eciRaw'] = '02';
        $ca['eci'] = '02';
        $ca['ecommerceIndicator'] = 'spa';

        $block = $this->mapper->map($ca, 'MC')['consumerAuthenticationInformation'];

        $this->assertArrayNotHasKey('cavv', $block);
        $this->assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $block['ucafAuthenticationData']);
        $this->assertSame('2', $block['ucafCollectionIndicator']);
        // Mastercard REQUIRES the directory-server transaction id alongside UCAF.
        $this->assertSame(
            '0f4e0e6d-9b5c-4b3e-9a2f-2b0f1f0a1c11',
            $block['directoryServerTransactionId']
        );
        $this->assertSame('02', $block['eciRaw']);
        $this->assertSame('Y', $block['paresStatus']);
    }

    public function testMastercardAttemptedUsesCollectionIndicatorOne(): void
    {
        $ca = $this->loadCa('case-2-3-attempts');
        $ca['eciRaw'] = '01';

        $block = $this->mapper->map($ca, 'MC')['consumerAuthenticationInformation'];

        $this->assertArrayNotHasKey('cavv', $block);
        $this->assertSame('1', $block['ucafCollectionIndicator']);
        $this->assertSame('AJkBBkhgQQAAAE4gSEJydQAAAAA=', $block['ucafAuthenticationData']);
    }

    public function testMaestroAlsoRidesUcaf(): void
    {
        // G3 pins UCAF to "Mastercard/Maestro"; Maestro shares the Mastercard rails.
        $block = $this->mapper->map($this->loadCa('case-2-1-success'), 'MI')['consumerAuthenticationInformation'];

        $this->assertArrayNotHasKey('cavv', $block);
        $this->assertArrayHasKey('ucafAuthenticationData', $block);
    }

    public function testUcafIndicatorFallsBackToMastercardEciWhenParesStatusAbsent(): void
    {
        $ca = $this->loadCa('case-2-1-success');
        unset($ca['paresStatus']);
        $ca['eciRaw'] = '01';

        $block = $this->mapper->map($ca, 'MC')['consumerAuthenticationInformation'];

        $this->assertSame('1', $block['ucafCollectionIndicator']);
        $this->assertArrayNotHasKey('paresStatus', $block);
    }

    public function testUcafIndicatorOmittedWhenNeitherSignalIsUsable(): void
    {
        $block = $this->mapper->map(
            ['cavv' => 'AAA=', 'paresStatus' => '', 'eciRaw' => '07'],
            'MC'
        )['consumerAuthenticationInformation'];

        $this->assertSame('AAA=', $block['ucafAuthenticationData']);
        $this->assertArrayNotHasKey('ucafCollectionIndicator', $block);
    }

    public function testEmptyEcommerceIndicatorIsOmittedNeverInvented(): void
    {
        $ca = $this->loadCa('case-2-1-success');
        $ca['ecommerceIndicator'] = '';

        $this->assertNull($this->mapper->map($ca, 'VI')['commerceIndicator']);

        unset($ca['ecommerceIndicator']);

        $this->assertNull($this->mapper->map($ca, 'VI')['commerceIndicator']);
    }

    public function testEmptyAndNonScalarFieldsAreFiltered(): void
    {
        $mapped = $this->mapper->map(
            [
                'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'xid' => '',
                'eciRaw' => null,
                'paresStatus' => 'Y',
                'specificationVersion' => ['nested' => 'shape'],
                'directoryServerTransactionId' => '  ds-txn-1  ',
                'ecommerceIndicator' => 'vbv',
            ],
            'VI'
        );

        $this->assertSame(
            [
                'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'paresStatus' => 'Y',
                // Trimmed, and the non-scalar / empty / null values are gone entirely.
                'directoryServerTransactionId' => 'ds-txn-1',
            ],
            $mapped['consumerAuthenticationInformation']
        );
        $this->assertSame('vbv', $mapped['commerceIndicator']);
    }

    public function testEmptyResultProducesNothingToAttach(): void
    {
        $mapped = $this->mapper->map([], 'VI');

        $this->assertSame([], $mapped['consumerAuthenticationInformation']);
        $this->assertNull($mapped['commerceIndicator']);
    }
}
