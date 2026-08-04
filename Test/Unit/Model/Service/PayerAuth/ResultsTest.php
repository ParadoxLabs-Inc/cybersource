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
