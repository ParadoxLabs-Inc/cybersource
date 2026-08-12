<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\RuntimeException;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Authenticate;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\AuthenticationResult;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\AuthenticationRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ResultClassifier;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Verdict;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Authenticate
 */
class AuthenticateTest extends TestCase
{
    use FixtureLoaderTrait;

    /**
     * A request with every required field set, addressing a raw card.
     *
     * @return AuthenticationRequest
     */
    private function completeRequest(): AuthenticationRequest
    {
        $request = new AuthenticationRequest();

        return $request->setClientReferenceCode('quote-1234')
            ->setReferenceId('2611dbe9-b63b-4ac4-a172-a4278a32aecb')
            ->setReturnUrl('https://store.example.com/pdl_cybs/payerauth/callback')
            ->setTotalAmount('24.00')
            ->setCurrency('USD')
            ->setCard([
                'number' => '4456530000001005',
                'expirationMonth' => '01',
                'expirationYear' => '2029',
                'type' => '001',
            ])
            ->setDeviceInformation([
                'httpAcceptBrowserValue' => 'text/html,*/*;q=0.8',
                'httpAcceptContent' => 'text/html,*/*;q=0.8',
                'userAgentBrowserValue' => 'Mozilla/5.0 Chrome/127.0.0.0',
                'ipAddress' => '198.51.100.24',
                'httpBrowserLanguage' => 'en-US',
                'httpBrowserJavaEnabled' => 'N',
                'httpBrowserJavaScriptEnabled' => 'Y',
                'httpBrowserColorDepth' => '24',
                'httpBrowserScreenHeight' => '1080',
                'httpBrowserScreenWidth' => '1920',
                'httpBrowserTimeDifference' => '300',
            ]);
    }

    public function testExecutePostsTheAuthenticationAndClassifiesTheReply(): void
    {
        $request = $this->completeRequest();
        $reply   = $this->loadFixture('case-2-1-success');
        $result  = new AuthenticationResult(Verdict::AUTHENTICATED, ['paresStatus' => 'Y']);

        $config = $this->createMock(Config::class);
        $config->expects($this->once())->method('setStoreId')->with(1);

        $rest = $this->createMock(Rest::class);
        $rest->expects($this->once())->method('setStoreId')->with(1);
        $rest->expects($this->once())
            ->method('post')
            ->with('/risk/v1/authentications', $request->toArray())
            ->willReturn($reply);

        $classifier = $this->createMock(ResultClassifier::class);
        $classifier->expects($this->once())
            ->method('classify')
            ->with($reply)
            ->willReturn($result);

        $this->assertSame(
            $result,
            (new Authenticate($config, $rest, $classifier))->execute($request, 1)
        );
    }

    public function testEndpointConstantIsTheAuthenticationsPath(): void
    {
        $this->assertSame('/risk/v1/authentications', Authenticate::AUTHENTICATIONS_PATH);
    }

    public function testIncompleteBrowserDataThrowsBeforeAnyTransport(): void
    {
        $request = $this->completeRequest()->setDeviceInformation([]);

        $config = $this->createMock(Config::class);
        $rest   = $this->createMock(Rest::class);
        $rest->expects($this->never())->method('post');

        $classifier = $this->createMock(ResultClassifier::class);
        $classifier->expects($this->never())->method('classify');

        $this->expectException(InputException::class);

        (new Authenticate($config, $rest, $classifier))->execute($request);
    }

    public function testTransportErrorsPropagate(): void
    {
        $config = $this->createMock(Config::class);
        $rest   = $this->createMock(Rest::class);
        $rest->method('post')->willThrowException(
            new RuntimeException(__('The transaction was declined.'), null, 502)
        );

        $classifier = $this->createMock(ResultClassifier::class);
        $classifier->expects($this->never())->method('classify');

        $this->expectException(RuntimeException::class);

        (new Authenticate($config, $rest, $classifier))->execute($this->completeRequest());
    }
}
