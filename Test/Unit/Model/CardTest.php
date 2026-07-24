<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context as ModelContext;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use ParadoxLabs\CyberSource\Model\Card;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Response as UcResponse;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Card\Context as CardContext;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use ParadoxLabs\TokenBase\Model\ResourceModel\Card as CardResource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Card
 */
class CardTest extends TestCase
{
    private const STORE_ID  = 3;
    private const CURRENCY  = 'USD';
    private const TOKEN      = 'transient-jwt';

    private Card $card;
    private UcResponse|MockObject $ucResponse;
    private CardBuilder|MockObject $cardBuilder;
    private StoreManagerInterface|MockObject $storeManager;

    protected function setUp(): void
    {
        // Initialize ObjectManager with mocks to prevent "ObjectManager isn't initialized" errors.
        $resource = $this->createMock(CardResource::class);
        $resource->method('getIdFieldName')->willReturn('id');

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('get')
            ->willReturnCallback(function ($className) use ($resource) {
                if ($className === CardResource::class) {
                    return $resource;
                }

                return $this->createMock($className);
            });
        ObjectManager::setInstance($objectManager);

        $context = $this->createMock(ModelContext::class);
        $context->method('getEventDispatcher')->willReturn($this->createMock(ManagerInterface::class));

        $this->ucResponse   = $this->createMock(UcResponse::class);
        $this->cardBuilder  = $this->createMock(CardBuilder::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);

        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(self::STORE_ID);
        $store->method('getBaseCurrencyCode')->willReturn(self::CURRENCY);
        $this->storeManager->method('getStore')->willReturn($store);

        // Card::exchangeTransientToken() logs the orphaned-instrument note on an edit-card replace via the
        // TokenBase helper the parent pulls from the Card Context; give the context a real helper mock.
        $cardContext = $this->createMock(CardContext::class);
        $cardContext->method('getHelper')->willReturn($this->createMock(Data::class));

        // exchangeTransientToken() passes the card's billing address (getAddressObject) into
        // tokenizeCard; the parent builds it via these two context factories, whose untyped getters
        // would otherwise return null from the context mock.
        $addressFactory = $this->getMockBuilder(\Magento\Customer\Api\Data\AddressInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $addressFactory->method('create')
            ->willReturnCallback(fn() => $this->createMock(\Magento\Customer\Api\Data\AddressInterface::class));
        $regionFactory = $this->getMockBuilder(\Magento\Customer\Api\Data\RegionInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $regionFactory->method('create')
            ->willReturnCallback(fn() => $this->createMock(\Magento\Customer\Api\Data\RegionInterface::class));
        $cardContext->method('getAddressFactory')->willReturn($addressFactory);
        $cardContext->method('getAddressRegionFactory')->willReturn($regionFactory);

        $this->card = new Card(
            $context,
            $this->createMock(Registry::class),
            $this->createMock(ExtensionAttributesFactory::class),
            $this->createMock(AttributeValueFactory::class),
            $cardContext,
            $this->ucResponse,
            $this->cardBuilder,
            $this->storeManager,
        );
    }

    protected function tearDown(): void
    {
        // Reset ObjectManager to avoid affecting other tests.
        $reflection = new ReflectionClass(ObjectManager::class);
        $property = $reflection->getProperty('_instance');
        $property->setValue(null, null);
    }

    /**
     * Happy path: a paymentinfo add-card (no payment_id) with a transient token exchanges it, maps the
     * result onto the card, and unsets the single-use token.
     *
     * @return void
     */
    public function testExchangeTokenizesMapsAndUnsetsTokenOnPaymentinfoPath(): void
    {
        $gatewayResponse = new GatewayResponse(['token_information' => ['paymentInstrument' => 'PI-1']]);

        $payment = $this->buildPayment('paymentinfo', self::TOKEN);
        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->card->setInfoInstance($payment);

        $this->ucResponse->expects($this->once())
            ->method('tokenizeCard')
            ->with($payment, self::CURRENCY, self::STORE_ID)
            ->willReturn($gatewayResponse);

        $this->cardBuilder->expects($this->once())
            ->method('applyTokenToCard')
            ->with($this->card, $gatewayResponse)
            ->willReturn($this->card);

        $this->invokeExchange();
    }

    /**
     * A token-less approval (uc_token_missing) on the add-card path throws so the Save controllers can
     * surface the error -- the deliberate difference from the checkout path.
     *
     * @return void
     */
    public function testExchangeThrowsLocalizedExceptionWhenTokenMissing(): void
    {
        $gatewayResponse = new GatewayResponse(['uc_token_missing' => true]);

        $payment = $this->buildPayment('paymentinfo', self::TOKEN);
        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->card->setInfoInstance($payment);

        $this->ucResponse->expects($this->once())
            ->method('tokenizeCard')
            ->willReturn($gatewayResponse);

        $this->cardBuilder->expects($this->once())
            ->method('applyTokenToCard')
            ->with($this->card, $gatewayResponse)
            ->willReturn($this->card);

        $this->expectException(LocalizedException::class);

        $this->invokeExchange();
    }

    /**
     * Checkout-source saves must NOT tokenize here: the same payment carries an already-consumed token,
     * and Method::afterAuthorize/afterCapture already handled it.
     *
     * @return void
     */
    public function testExchangeSkipsWhenSourceIsNotPaymentinfo(): void
    {
        $payment = $this->buildPayment('checkout', self::TOKEN);
        $payment->expects($this->never())->method('unsAdditionalInformation');

        $this->card->setInfoInstance($payment);

        $this->ucResponse->expects($this->never())->method('tokenizeCard');
        $this->cardBuilder->expects($this->never())->method('applyTokenToCard');

        $this->invokeExchange();
    }

    /**
     * Edit-card REPLACE: a card that already carries a payment_id (an established vault token) but receives
     * a fresh transient token on a paymentinfo save MUST re-exchange and overwrite the stored instrument,
     * then unset the single-use token. This is the money-path fix: previously the exchange short-circuited
     * on the existing payment_id, so the stored TMS token kept pointing at the OLD card.
     *
     * @return void
     */
    public function testExchangeReplacesInstrumentWhenPaymentIdAlreadySetOnEdit(): void
    {
        $gatewayResponse = new GatewayResponse(['token_information' => ['paymentInstrument' => 'PI-NEW']]);

        $payment = $this->buildPayment('paymentinfo', self::TOKEN);
        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->card->setInfoInstance($payment);
        $this->card->setPaymentId('PI-EXISTING');

        $this->ucResponse->expects($this->once())
            ->method('tokenizeCard')
            ->with($payment, self::CURRENCY, self::STORE_ID)
            ->willReturn($gatewayResponse);

        $this->cardBuilder->expects($this->once())
            ->method('applyTokenToCard')
            ->with($this->card, $gatewayResponse)
            ->willReturn($this->card);

        $this->invokeExchange();
    }

    /**
     * Edit-card REPLACE with a token-less approval: the exchange runs (existing payment_id no longer
     * blocks it) but a uc_token_missing reply must throw so the Save controllers abort the save and the
     * old card is left intact -- never a silent success on the stale instrument.
     *
     * @return void
     */
    public function testExchangeThrowsWhenTokenMissingOnEditReplace(): void
    {
        $gatewayResponse = new GatewayResponse(['uc_token_missing' => true]);

        $payment = $this->buildPayment('paymentinfo', self::TOKEN);
        $payment->expects($this->once())
            ->method('unsAdditionalInformation')
            ->with('transient_token');

        $this->card->setInfoInstance($payment);
        $this->card->setPaymentId('PI-EXISTING');

        $this->ucResponse->expects($this->once())
            ->method('tokenizeCard')
            ->willReturn($gatewayResponse);

        $this->cardBuilder->expects($this->once())
            ->method('applyTokenToCard')
            ->with($this->card, $gatewayResponse)
            ->willReturn($this->card);

        $this->expectException(LocalizedException::class);

        $this->invokeExchange();
    }

    /**
     * No transient token present (e.g. a metadata-only re-save) must not attempt an exchange.
     *
     * @return void
     */
    public function testExchangeSkipsWhenNoTransientToken(): void
    {
        $payment = $this->buildPayment('paymentinfo', '');
        $payment->expects($this->never())->method('unsAdditionalInformation');

        $this->card->setInfoInstance($payment);

        $this->ucResponse->expects($this->never())->method('tokenizeCard');
        $this->cardBuilder->expects($this->never())->method('applyTokenToCard');

        $this->invokeExchange();
    }

    /**
     * Build a payment mock returning the given tokenbase_source and transient_token.
     *
     * @param string $source
     * @param string $token
     * @return Payment&MockObject
     */
    private function buildPayment(string $source, string $token): Payment
    {
        $payment = $this->createMock(Payment::class);
        $payment->method('getData')
            ->willReturnCallback(static fn($key) => $key === 'tokenbase_source' ? $source : null);
        $payment->method('getAdditionalInformation')
            ->willReturnCallback(static fn($key = null) => $key === 'transient_token' ? $token : null);

        return $payment;
    }

    /**
     * Invoke the protected exchangeTransientToken() with the card's current info instance.
     *
     * @return void
     */
    private function invokeExchange(): void
    {
        $method = new ReflectionMethod(Card::class, 'exchangeTransientToken');
        $method->invoke($this->card, $this->card->getInfoInstance());
    }
}
