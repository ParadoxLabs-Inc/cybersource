<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Config;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\CcConfig;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\CheckoutProvider;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Method;
use ParadoxLabs\TokenBase\Model\Card;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Config\CheckoutProvider
 */
class CheckoutProviderTest extends TestCase
{
    private CheckoutProvider $provider;
    private CcConfig|MockObject $ccConfigMock;
    private PaymentHelper|MockObject $paymentHelperMock;
    private CheckoutSession|MockObject $checkoutSessionMock;
    private CustomerSession|MockObject $customerSessionMock;
    private Data|MockObject $dataHelperMock;
    private UrlInterface|MockObject $urlBuilderMock;
    private Config|MockObject $configMock;
    private Method|MockObject $methodMock;

    /**
     * @var array<string, mixed>
     */
    private array $methodConfig = [];

    protected function setUp(): void
    {
        $this->ccConfigMock = $this->createMock(CcConfig::class);
        $this->paymentHelperMock = $this->createMock(PaymentHelper::class);
        $this->checkoutSessionMock = $this->createMock(CheckoutSession::class);
        $this->customerSessionMock = $this->createMock(CustomerSession::class);
        $this->dataHelperMock = $this->createMock(Data::class);
        $this->urlBuilderMock = $this->createMock(UrlInterface::class);
        $this->configMock = $this->createMock(Config::class);

        $this->methodMock = $this->createMock(Method::class);
        $this->methodMock->method('isAvailable')->willReturn(true);
        $this->methodMock->method('getConfigData')
            ->willReturnCallback(fn($key) => $this->methodConfig[$key] ?? null);

        $this->paymentHelperMock->method('getMethodInstance')
            ->with(Config::CODE)
            ->willReturn($this->methodMock);

        // Parent CcGenericConfigProvider::getConfig() passthroughs.
        $this->ccConfigMock->method('getCcAvailableTypes')->willReturn(['VI' => 'Visa', 'MC' => 'MasterCard']);
        $this->ccConfigMock->method('getCcMonths')->willReturn([1 => '01 - January']);
        $this->ccConfigMock->method('getCcYears')->willReturn([2026 => 2026]);
        $this->ccConfigMock->method('hasVerification')->willReturn(true);
        $this->ccConfigMock->method('getCvvImageUrl')->willReturn('https://example.com/cvv.png');
        $this->ccConfigMock->method('getViewFileUrl')->willReturn('https://example.com/logo.webp');

        $this->urlBuilderMock->method('getUrl')
            ->willReturnCallback(static fn($path) => 'https://example.com/' . $path);

        $this->provider = new CheckoutProvider(
            $this->ccConfigMock,
            $this->paymentHelperMock,
            $this->checkoutSessionMock,
            $this->customerSessionMock,
            $this->dataHelperMock,
            $this->urlBuilderMock,
            $this->configMock,
            [],
        );
    }

    /**
     * Build a vaulted card stub.
     */
    private function buildCard(
        string $hash,
        string $label,
        string $type,
        string $bin,
        string $last4,
        int $id = 1,
    ): Card&MockObject {
        $card = $this->createMock(Card::class);
        $card->method('getId')->willReturn($id);
        $card->method('getHash')->willReturn($hash);
        $card->method('getLabel')->willReturn($label);
        $card->method('getType')->willReturn($type);
        $card->method('getAdditional')->willReturnCallback(
            static fn($key = null) => match ($key) {
                'cc_bin' => $bin,
                'cc_last4' => $last4,
                default => null,
            }
        );

        return $card;
    }

    /**
     * Shortcut to the method's slice of the checkout config.
     */
    private function getMethodConfig(): array
    {
        return $this->provider->getConfig()['payment'][Config::CODE];
    }

    public function testGetStoredCardsDelegatesToHelperScopedToTheMethod(): void
    {
        $cards = [$this->buildCard('hash1', 'Visa 1111', 'VI', '411111', '1111')];

        $this->dataHelperMock->expects($this->once())
            ->method('getActiveCustomerCardsByMethod')
            ->with(Config::CODE)
            ->willReturn($cards);

        $this->assertSame($cards, $this->provider->getStoredCards());
    }

    public function testCanSaveCardTrueWhenCustomerLoggedIn(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);

        $this->assertTrue($this->provider->canSaveCard());
    }

    public function testCanSaveCardFalseForGuest(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $this->assertFalse($this->provider->canSaveCard());
    }

    /**
     * An unavailable method must contribute nothing to the checkout config -- not even the ccform
     * scaffolding -- so the renderer never mounts.
     */
    public function testGetConfigReturnsEmptyArrayWhenMethodUnavailable(): void
    {
        $method = $this->createMock(Method::class);
        $method->method('isAvailable')->willReturn(false);

        $paymentHelper = $this->createMock(PaymentHelper::class);
        $paymentHelper->method('getMethodInstance')->willReturn($method);

        $provider = new CheckoutProvider(
            $this->ccConfigMock,
            $paymentHelper,
            $this->checkoutSessionMock,
            $this->customerSessionMock,
            $this->dataHelperMock,
            $this->urlBuilderMock,
            $this->configMock,
            [],
        );

        $this->assertSame([], $provider->getConfig());
    }

    /**
     * The renderer fetches the capture-context JWT from this endpoint; it must be the UC
     * capture-context controller route.
     */
    public function testGetConfigExposesCaptureContextUrl(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $this->urlBuilderMock->expects($this->atLeastOnce())
            ->method('getUrl')
            ->with('pdl_cybs/unifiedCheckout/captureContext');

        $this->assertSame(
            'https://example.com/pdl_cybs/unifiedCheckout/captureContext',
            $this->getMethodConfig()['captureContextUrl']
        );
    }

    /**
     * The client library URL/SRI and layout come from the decoded JWT, not the checkout config;
     * exposing them here would let the client contradict the server-pinned capture context.
     */
    public function testGetConfigDoesNotExposeClientVersionOrLayoutToTheClient(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $config = $this->getMethodConfig();

        $this->assertArrayNotHasKey('clientVersion', $config);
        $this->assertArrayNotHasKey('ucLayout', $config);
    }

    /**
     * Decision Manager fingerprinting must be keyed to the current quote.
     */
    public function testGetConfigExposesFingerprintUrlKeyedToCurrentQuote(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn(99);

        $this->configMock->expects($this->once())
            ->method('getFingerprintUrl')
            ->with(99)
            ->willReturn('https://h.online-metrix.net/fp/tags.js?org_id=a&session_id=b');

        $this->assertSame(
            'https://h.online-metrix.net/fp/tags.js?org_id=a&session_id=b',
            $this->getMethodConfig()['fingerprintUrl']
        );
    }

    public function testGetConfigFingerprintUrlIsNullWhenDecisionManagerDisabled(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);
        $this->checkoutSessionMock->method('getQuoteId')->willReturn(99);
        $this->configMock->method('getFingerprintUrl')->willReturn(null);

        $this->assertNull($this->getMethodConfig()['fingerprintUrl']);
    }

    /**
     * The vault is always on for this method.
     */
    public function testGetConfigAlwaysEnablesVault(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $this->assertTrue($this->getMethodConfig()['useVault']);
    }

    /**
     * A guest cannot vault a card, so no stored-card options may leak into the config and the
     * helper must not even be queried.
     */
    public function testGetConfigForGuestExposesNoStoredCards(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $this->dataHelperMock->expects($this->never())
            ->method('getActiveCustomerCardsByMethod');

        $config = $this->getMethodConfig();

        $this->assertFalse($config['canSaveCard']);
        $this->assertSame([], $config['storedCards']);
        $this->assertNull($config['selectedCard']);
    }

    /**
     * Stored cards must be projected to the exact shape the renderer consumes -- and must carry no
     * PAN/CVV, only the hash, bin and last4.
     */
    public function testGetConfigProjectsStoredCardsForLoggedInCustomer(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);
        $this->dataHelperMock->method('getActiveCustomerCardsByMethod')->willReturn([
            $this->buildCard('hash1', 'Visa ending 1111', 'VI', '411111', '1111'),
        ]);

        $config = $this->getMethodConfig();

        $this->assertTrue($config['canSaveCard']);
        $this->assertSame(
            [
                [
                    'id' => 'hash1',
                    'label' => 'Visa ending 1111',
                    'selected' => false,
                    'new' => false,
                    'type' => 'VI',
                    'cc_bin' => '411111',
                    'cc_last4' => '1111',
                ],
            ],
            $config['storedCards']
        );
    }

    /**
     * With a single stored card, that card must be preselected so the customer isn't forced to
     * re-enter a card they already have on file.
     */
    public function testGetConfigPreselectsTheOnlyStoredCard(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);
        $this->dataHelperMock->method('getActiveCustomerCardsByMethod')->willReturn([
            $this->buildCard('hash1', 'Visa 1111', 'VI', '411111', '1111'),
        ]);

        $this->assertSame('hash1', $this->getMethodConfig()['selectedCard']);
    }

    /**
     * With several stored cards, the NEWEST (highest id) must be preselected regardless of the
     * order the collection yields them in — the collection carries no explicit ordering, so the
     * provider may not rely on iteration order to find the most recent card.
     */
    public function testGetConfigPreselectsTheNewestStoredCard(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);
        $this->dataHelperMock->method('getActiveCustomerCardsByMethod')->willReturn([
            $this->buildCard('hash3', 'Visa 9999', 'VI', '411111', '9999', 3),
            $this->buildCard('hash1', 'Visa 1111', 'VI', '411111', '1111', 1),
            $this->buildCard('hash2', 'MC 4444', 'MC', '555555', '4444', 2),
        ]);

        $this->assertSame('hash3', $this->getMethodConfig()['selectedCard']);
    }

    /**
     * selectedCard must name a card that is actually in storedCards; otherwise the renderer
     * preselects an id it cannot resolve and the customer sees an empty selection.
     */
    public function testGetConfigSelectedCardIsAlwaysOneOfTheStoredCards(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);
        $this->dataHelperMock->method('getActiveCustomerCardsByMethod')->willReturn([
            $this->buildCard('hash1', 'Visa 1111', 'VI', '411111', '1111'),
            $this->buildCard('hash2', 'MC 4444', 'MC', '555555', '4444'),
            $this->buildCard('hash3', 'Visa 9999', 'VI', '411111', '9999'),
        ]);

        $config = $this->getMethodConfig();

        $this->assertContains(
            $config['selectedCard'],
            array_column($config['storedCards'], 'id')
        );
    }

    /**
     * The per-option `selected` flag is initial state only -- the renderer drives selection off the
     * top-level selectedCard -- so every option ships unflagged.
     */
    public function testGetConfigShipsStoredCardOptionsUnflagged(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);
        $this->dataHelperMock->method('getActiveCustomerCardsByMethod')->willReturn([
            $this->buildCard('hash1', 'Visa 1111', 'VI', '411111', '1111'),
            $this->buildCard('hash2', 'MC 4444', 'MC', '555555', '4444'),
        ]);

        $config = $this->getMethodConfig();

        $this->assertSame([false, false], array_column($config['storedCards'], 'selected'));
        $this->assertSame([false, false], array_column($config['storedCards'], 'new'));
    }

    public function testGetConfigWithNoStoredCardsSelectsNothing(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(true);
        $this->dataHelperMock->method('getActiveCustomerCardsByMethod')->willReturn([]);

        $config = $this->getMethodConfig();

        $this->assertSame([], $config['storedCards']);
        $this->assertNull($config['selectedCard']);
    }

    public function testGetLogoImageReturnsModuleLogoWhenBrandingEnabled(): void
    {
        $this->methodConfig['show_branding'] = '1';

        $this->ccConfigMock->expects($this->once())
            ->method('getViewFileUrl')
            ->with('ParadoxLabs_CyberSource::images/logo.webp')
            ->willReturn('https://example.com/logo.webp');

        $this->assertSame('https://example.com/logo.webp', $this->provider->getLogoImage());
    }

    public function testGetLogoImageReturnsFalseWhenBrandingDisabled(): void
    {
        $this->methodConfig['show_branding'] = '0';

        $this->ccConfigMock->expects($this->never())->method('getViewFileUrl');

        $this->assertFalse($this->provider->getLogoImage());
    }

    /**
     * @dataProvider booleanConfigDataProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('booleanConfigDataProvider')]
    public function testBooleanConfigAccessors(string $method, string $configKey, $configValue, bool $expected): void
    {
        $this->methodConfig[$configKey] = $configValue;

        $this->assertSame($expected, $this->provider->$method());
    }

    public static function booleanConfigDataProvider(): array
    {
        return [
            // requireCcv: opt-in.
            'requireCcv enabled' => ['requireCcv', 'require_ccv', '1', true],
            'requireCcv disabled' => ['requireCcv', 'require_ccv', '0', false],
            'requireCcv unset defaults off' => ['requireCcv', 'require_ccv', null, false],

            // forceSaveCard is the INVERSE of allow_unsaved: if unsaved cards are not allowed, the
            // save is forced. Default (unset) forces the save.
            'forceSaveCard off when unsaved allowed' => ['forceSaveCard', 'allow_unsaved', '1', false],
            'forceSaveCard on when unsaved disallowed' => ['forceSaveCard', 'allow_unsaved', '0', true],
            'forceSaveCard unset defaults on' => ['forceSaveCard', 'allow_unsaved', null, true],

            // defaultSaveCard follows savecard_opt_out.
            'defaultSaveCard on when opt-out' => ['defaultSaveCard', 'savecard_opt_out', '1', true],
            'defaultSaveCard off when opt-in' => ['defaultSaveCard', 'savecard_opt_out', '0', false],
            'defaultSaveCard unset defaults off' => ['defaultSaveCard', 'savecard_opt_out', null, false],
        ];
    }

    /**
     * The provider must not drop the parent's ccform scaffolding when merging its own keys.
     */
    public function testGetConfigPreservesParentCcFormConfig(): void
    {
        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $config = $this->provider->getConfig();

        $this->assertArrayHasKey('ccform', $config['payment']);
        $this->assertSame(
            ['VI' => 'Visa', 'MC' => 'MasterCard'],
            $config['payment']['ccform']['availableTypes'][Config::CODE]
        );
        $this->assertSame([1 => '01 - January'], $config['payment']['ccform']['months'][Config::CODE]);
    }

    /**
     * The constructor accepts a $methodCodes list but must register exactly the CyberSource method;
     * a caller-supplied list must not be able to attach this provider to another method's config.
     */
    public function testConstructorIgnoresCallerSuppliedMethodCodes(): void
    {
        $paymentHelper = $this->createMock(PaymentHelper::class);
        $paymentHelper->expects($this->once())
            ->method('getMethodInstance')
            ->with(Config::CODE)
            ->willReturn($this->methodMock);

        $this->customerSessionMock->method('isLoggedIn')->willReturn(false);

        $provider = new CheckoutProvider(
            $this->ccConfigMock,
            $paymentHelper,
            $this->checkoutSessionMock,
            $this->customerSessionMock,
            $this->dataHelperMock,
            $this->urlBuilderMock,
            $this->configMock,
            ['some_other_method'],
        );

        $this->assertArrayHasKey(Config::CODE, $provider->getConfig()['payment']);
        $this->assertArrayNotHasKey('some_other_method', $provider->getConfig()['payment']);
    }
}
