<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\Gateway\Response as GatewayResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder
 */
class CardBuilderTest extends TestCase
{
    private CardBuilder $cardBuilder;
    private Data|MockObject $helperMock;

    protected function setUp(): void
    {
        $this->helperMock  = $this->createMock(Data::class);
        $this->cardBuilder = new CardBuilder($this->helperMock);
    }

    /**
     * A spy card that records the real setter calls so we assert the mapping, not mock echo.
     *
     * @return CardInterface&MockObject
     */
    private function buildCardSpy(array &$profileId, array &$paymentId, array &$additional): CardInterface
    {
        $card = $this->createMock(CardInterface::class);

        $card->method('setProfileId')->willReturnCallback(
            function ($value) use (&$profileId, $card) {
                $profileId[] = $value;

                return $card;
            }
        );
        $card->method('setPaymentId')->willReturnCallback(
            function ($value) use (&$paymentId, $card) {
                $paymentId[] = $value;

                return $card;
            }
        );
        $card->method('setAdditional')->willReturnCallback(
            function ($key, $value = null) use (&$additional, $card) {
                $additional[$key] = $value;

                return $card;
            }
        );

        return $card;
    }

    private function buildResponse(array $data): GatewayResponse
    {
        return (new GatewayResponse())->setData($data);
    }

    public function testMapsAllThreeTmsIdsAndMetadataPerD5(): void
    {
        $profileId  = [];
        $paymentId  = [];
        $additional = [];

        $card = $this->buildCardSpy($profileId, $paymentId, $additional);

        $response = $this->buildResponse([
            'token_information' => [
                'customer' => 'CUST-123',
                'paymentInstrument' => 'PI-456',
                'instrumentIdentifier' => 'II-789',
            ],
            'card_information' => [
                'cc_type' => 'VI',
                'cc_last4' => '1111',
                'cc_bin' => '411111',
                'cc_exp_month' => '12',
                'cc_exp_year' => '2030',
            ],
            'uc_token_missing' => false,
        ]);

        $result = $this->cardBuilder->applyTokenToCard($card, $response);

        $this->assertSame($card, $result);

        // D5: profileId <- customer; paymentId <- paymentInstrument.
        $this->assertSame(['CUST-123'], $profileId);
        $this->assertSame(['PI-456'], $paymentId);

        // D5: instrument_identifier <- instrumentIdentifier (and fingerprint mirror, like SA).
        $this->assertSame('II-789', $additional['instrument_identifier']);
        $this->assertSame('II-789', $additional['fingerprint']);

        // cc_* metadata mirrored from SA field writes.
        $this->assertSame('VI', $additional['cc_type']);
        $this->assertSame('1111', $additional['cc_last4']);
        $this->assertSame('411111', $additional['cc_bin']);
        $this->assertSame('12', $additional['cc_exp_month']);
        $this->assertSame('2030', $additional['cc_exp_year']);

        // Token-missing flag cleared on a successful tokenization.
        $this->assertNull($additional[CardBuilder::CARD_FLAG_TOKEN_MISSING]);
    }

    public function testTokenLessResultDoesNotWriteEmptyIdsAndFlagsCard(): void
    {
        $profileId  = [];
        $paymentId  = [];
        $additional = [];

        $card = $this->buildCardSpy($profileId, $paymentId, $additional);

        // Approved auth held for DM review: no token_information, uc_token_missing=true.
        $response = $this->buildResponse([
            'uc_token_missing' => true,
            'card_information' => [
                'cc_type' => 'MC',
                'cc_last4' => '4444',
            ],
        ]);

        $this->helperMock->expects($this->once())->method('log');

        $result = $this->cardBuilder->applyTokenToCard($card, $response);

        $this->assertSame($card, $result);

        // No ids written — the vault is NOT corrupted with empty/null ids.
        $this->assertSame([], $profileId);
        $this->assertSame([], $paymentId);
        $this->assertArrayNotHasKey('instrument_identifier', $additional);

        // Metadata still captured; card flagged for reconciliation.
        $this->assertSame('MC', $additional['cc_type']);
        $this->assertSame('4444', $additional['cc_last4']);
        $this->assertSame('1', $additional[CardBuilder::CARD_FLAG_TOKEN_MISSING]);
    }

    public function testMissingTokenInformationKeyTreatedAsTokenLess(): void
    {
        $profileId  = [];
        $paymentId  = [];
        $additional = [];

        $card = $this->buildCardSpy($profileId, $paymentId, $additional);

        // No uc_token_missing flag set and no token_information at all — still must not write empties.
        $response = $this->buildResponse([
            'card_information' => [],
        ]);

        $result = $this->cardBuilder->applyTokenToCard($card, $response);

        $this->assertSame($card, $result);
        $this->assertSame([], $profileId);
        $this->assertSame([], $paymentId);
        $this->assertSame('1', $additional[CardBuilder::CARD_FLAG_TOKEN_MISSING]);
    }

    public function testPartialTokenWritesOnlyPresentIds(): void
    {
        $profileId  = [];
        $paymentId  = [];
        $additional = [];

        $card = $this->buildCardSpy($profileId, $paymentId, $additional);

        // Only paymentInstrument present (e.g. customer reused). Write what we have, skip the rest.
        $response = $this->buildResponse([
            'token_information' => [
                'paymentInstrument' => 'PI-456',
            ],
            'uc_token_missing' => false,
        ]);

        $this->cardBuilder->applyTokenToCard($card, $response);

        $this->assertSame([], $profileId);
        $this->assertSame(['PI-456'], $paymentId);
        $this->assertArrayNotHasKey('instrument_identifier', $additional);
        $this->assertNull($additional[CardBuilder::CARD_FLAG_TOKEN_MISSING]);
    }
}
