<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth\Request;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequest
 */
class ResultsRequestTest extends TestCase
{
    public function testToArrayEmitsTheTransactionId(): void
    {
        $request = new ResultsRequest();
        $request->setClientReferenceCode('quote-1234')
            ->setAuthenticationTransactionId('6544863011992807913018');

        $result = $request->toArray();

        $this->assertSame('quote-1234', $result['clientReferenceInformation']['code']);
        $this->assertSame(
            '6544863011992807913018',
            $result['consumerAuthenticationInformation']['authenticationTransactionId']
        );
    }

    public function testClientReferenceInformationIsOmittedWhenEmpty(): void
    {
        $request = new ResultsRequest();
        $request->setAuthenticationTransactionId('6544863011992807913018');

        $this->assertArrayNotHasKey('clientReferenceInformation', $request->toArray());
    }

    public function testMissingTransactionIdThrows(): void
    {
        $request = new ResultsRequest();
        $request->setClientReferenceCode('quote-1234');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testEmptyTransactionIdThrows(): void
    {
        $request = new ResultsRequest();
        $request->setAuthenticationTransactionId('');

        $this->expectException(InputException::class);

        $request->toArray();
    }

    public function testGettersRoundTrip(): void
    {
        $request = new ResultsRequest();
        $request->setClientReferenceCode('quote-1234')
            ->setAuthenticationTransactionId('6544863011992807913018');

        $this->assertSame('quote-1234', $request->getClientReferenceCode());
        $this->assertSame('6544863011992807913018', $request->getAuthenticationTransactionId());
    }
}
