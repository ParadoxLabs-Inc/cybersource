<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout\Request;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FollowOnRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FollowOnRequest
 */
class FollowOnRequestTest extends TestCase
{
    public function testToArrayBuildsOrderBody(): void
    {
        $request = new FollowOnRequest();
        $request->setClientReferenceCode('100000123')
            ->setTotalAmount('24.00')
            ->setCurrency('USD');

        $result = $request->toArray();

        $this->assertSame('100000123', $result['clientReferenceInformation']['code']);
        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertArrayNotHasKey('reversalInformation', $result);
    }

    public function testToArrayBuildsReversalBody(): void
    {
        $request = new FollowOnRequest();
        $request->setClientReferenceCode('100000123')
            ->setTotalAmount('24.00')
            ->setCurrency('USD')
            ->setReversal(true);

        $result = $request->toArray();

        $this->assertSame('24.00', $result['reversalInformation']['amountDetails']['totalAmount']);
        $this->assertArrayNotHasKey('orderInformation', $result);
    }

    public function testToArrayOmitsEmptyBranches(): void
    {
        $request = new FollowOnRequest();

        $this->assertSame([], $request->toArray());
    }

    public function testToArrayIncludesPartnerAttributionWhenAllSet(): void
    {
        // T4: partner solutionId, applicationName, and applicationVersion must appear in
        // clientReferenceInformation when set.
        $request = new FollowOnRequest();
        $request->setClientReferenceCode('100000123')
            ->setSolutionId('DEQXVEEG')
            ->setApplicationName('ParadoxLabs_CyberSource')
            ->setApplicationVersion('3.0.0');

        $result = $request->toArray();

        $this->assertSame('DEQXVEEG', $result['clientReferenceInformation']['partner']['solutionId']);
        $this->assertSame('ParadoxLabs_CyberSource', $result['clientReferenceInformation']['applicationName']);
        $this->assertSame('3.0.0', $result['clientReferenceInformation']['applicationVersion']);
    }

    public function testToArrayFiltersEmptyPartnerBlock(): void
    {
        // An empty solutionId must not emit an empty partner block.
        $request = new FollowOnRequest();
        $request->setClientReferenceCode('100000123')
            ->setSolutionId('')
            ->setApplicationName('ParadoxLabs_CyberSource');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('partner', $result['clientReferenceInformation']);
    }

    public function testToArrayFiltersEmptyApplicationFields(): void
    {
        // Empty applicationName/applicationVersion must not emit those keys.
        $request = new FollowOnRequest();
        $request->setClientReferenceCode('100000123')
            ->setSolutionId('DEQXVEEG')
            ->setApplicationName('')
            ->setApplicationVersion('');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('applicationName', $result['clientReferenceInformation']);
        $this->assertArrayNotHasKey('applicationVersion', $result['clientReferenceInformation']);
    }

    public function testToArrayAttributionAppearsAlongsideCode(): void
    {
        // All clientReferenceInformation fields coexist in the same block.
        $request = new FollowOnRequest();
        $request->setClientReferenceCode('100000123')
            ->setSolutionId('DEQXVEEG')
            ->setApplicationName('ParadoxLabs_CyberSource')
            ->setApplicationVersion('3.0.0')
            ->setTotalAmount('12.00')
            ->setCurrency('USD');

        $result = $request->toArray();

        $cri = $result['clientReferenceInformation'];
        $this->assertSame('100000123', $cri['code']);
        $this->assertSame('DEQXVEEG', $cri['partner']['solutionId']);
        $this->assertSame('ParadoxLabs_CyberSource', $cri['applicationName']);
        $this->assertSame('3.0.0', $cri['applicationVersion']);
    }
}
