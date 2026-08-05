<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Framework\Exception\RuntimeException;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\SetupRequest;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Setup;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Setup
 */
class SetupTest extends TestCase
{
    use FixtureLoaderTrait;

    public function testExecutePostsTheSetupRequestAndReturnsTheDecodedReply(): void
    {
        $reply = $this->loadFixture('setups-201');

        $config = $this->createMock(Config::class);
        $config->expects($this->once())->method('setStoreId')->with(3);

        $rest = $this->createMock(Rest::class);
        $rest->expects($this->once())->method('setStoreId')->with(3);
        $rest->expects($this->once())
            ->method('post')
            ->with(
                '/risk/v1/authentication-setups',
                [
                    'clientReferenceInformation' => ['code' => 'quote-1234'],
                    'tokenInformation' => ['transientTokenJwt' => 'the.transient.token'],
                ]
            )
            ->willReturn($reply);

        $request = new SetupRequest();
        $request->setClientReferenceCode('quote-1234')
            ->setTransientToken('the.transient.token');

        $result = (new Setup($config, $rest))->execute($request, 3);

        $this->assertSame(
            'https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect',
            $result['consumerAuthenticationInformation']['deviceDataCollectionUrl']
        );
        $this->assertSame(
            '2611dbe9-b63b-4ac4-a172-a4278a32aecb',
            $result['consumerAuthenticationInformation']['referenceId']
        );
        $this->assertNotEmpty($result['consumerAuthenticationInformation']['accessToken']);
    }

    public function testExecutePostsTheStoredCardShape(): void
    {
        $config = $this->createMock(Config::class);
        $rest   = $this->createMock(Rest::class);
        $rest->expects($this->once())
            ->method('post')
            ->with(
                Setup::SETUP_PATH,
                [
                    'paymentInformation' => [
                        'paymentInstrument' => ['id' => 'F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4'],
                    ],
                ]
            )
            ->willReturn($this->loadFixture('setups-201'));

        $request = new SetupRequest();
        $request->setPaymentInstrumentId('F2C0A1B2C3D4E5F6G7H8I9J0K1L2M3N4');

        (new Setup($config, $rest))->execute($request);
    }

    public function testTransportErrorsPropagate(): void
    {
        $config = $this->createMock(Config::class);
        $rest   = $this->createMock(Rest::class);
        $rest->method('post')->willThrowException(
            new RuntimeException(__('Declined - The request is missing one or more fields'), null, 400)
        );

        $request = new SetupRequest();
        $request->setTransientToken('the.transient.token');

        $this->expectException(RuntimeException::class);

        (new Setup($config, $rest))->execute($request);
    }

    public function testNullStoreIdIsPassedThroughAsAssumedScope(): void
    {
        $config = $this->createMock(Config::class);
        $config->expects($this->once())->method('setStoreId')->with(null);

        $rest = $this->createMock(Rest::class);
        $rest->expects($this->once())->method('setStoreId')->with(null);
        $rest->method('post')->willReturn($this->loadFixture('setups-201'));

        $request = new SetupRequest();
        $request->setTransientToken('the.transient.token');

        (new Setup($config, $rest))->execute($request);
    }
}
