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
     * setAdditional()/getAdditional() replicate the REAL \ParadoxLabs\TokenBase\Model\Card semantics
     * (Card.php:612-642), not mock-echo: a string key with a null value is a NO-OP; an array $key
     * REPLACES the whole additional array. This is what makes the stale-flag-clear test meaningful.
     *
     * @param array<int, mixed> $profileId
     * @param array<int, mixed> $paymentId
     * @param array<string, mixed> $additional
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
                // Mirror Card::setAdditional(): write a single key only when value is non-null;
                // an array key replaces the whole additional array; null scalar is a no-op.
                if ($value !== null) {
                    $additional[$key] = $value;
                } elseif (is_array($key)) {
                    $additional = $key;
                }

                return $card;
            }
        );
        $card->method('getAdditional')->willReturnCallback(
            function ($key = null) use (&$additional) {
                if ($key !== null) {
                    return $additional[$key] ?? null;
                }

                return $additional;
            }
        );

        return $card;
    }

    private function buildResponse(array $data): GatewayResponse
    {
        return (new GatewayResponse())->setData($data);
    }

    public function testMapsTmsIdsAndMetadataPerD5(): void
    {
        $profileId  = [];
        $paymentId  = [];
        $additional = [];

        $card = $this->buildCardSpy($profileId, $paymentId, $additional);

        // A stray 'customer' key (e.g. from an older gateway response) must be ignored, not mapped.
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

        // D5: paymentId <- paymentInstrument. No profileId — standalone TMS payment instrument,
        // the customer key is ignored even when present.
        $this->assertSame([], $profileId);
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

        // Token-missing flag absent on a successful tokenization (never set, nothing to clear).
        $this->assertArrayNotHasKey(CardBuilder::CARD_FLAG_TOKEN_MISSING, $additional);
    }

    public function testStaleTokenMissingFlagIsClearedOnSuccessfulTokenization(): void
    {
        $profileId  = [];
        $paymentId  = [];
        // Card was previously flagged token-less and is now being successfully tokenized.
        $additional = [CardBuilder::CARD_FLAG_TOKEN_MISSING => '1'];

        $card = $this->buildCardSpy($profileId, $paymentId, $additional);

        $response = $this->buildResponse([
            'token_information' => [
                'paymentInstrument' => 'PI-456',
                'instrumentIdentifier' => 'II-789',
            ],
            'uc_token_missing' => false,
        ]);

        $this->cardBuilder->applyTokenToCard($card, $response);

        // Real ids written...
        $this->assertSame([], $profileId);
        $this->assertSame(['PI-456'], $paymentId);

        // ...and the stale flag is GONE (this fails with a string-key null no-op clear).
        $this->assertArrayNotHasKey(CardBuilder::CARD_FLAG_TOKEN_MISSING, $additional);
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

        // Only paymentInstrument present. Write what we have, skip the rest.
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
        $this->assertArrayNotHasKey(CardBuilder::CARD_FLAG_TOKEN_MISSING, $additional);
    }

    public function testCardInformationKeysMatchResponseExtractorShape(): void
    {
        $profileId  = [];
        $paymentId  = [];
        $additional = [];

        $card = $this->buildCardSpy($profileId, $paymentId, $additional);

        // These keys are the contract produced by Response::extractCardMetadata() (A1) and consumed
        // here (A2). Pinning them so the card_information shape can't silently drift between producer
        // and consumer.
        $response = $this->buildResponse([
            'card_information' => [
                'cc_type' => 'VI',
                'cc_last4' => '1111',
                'cc_bin' => '411111',
                'cc_exp_month' => '12',
                'cc_exp_year' => '2030',
            ],
            'uc_token_missing' => true,
        ]);

        $this->cardBuilder->applyTokenToCard($card, $response);

        $this->assertSame('VI', $additional['cc_type']);
        $this->assertSame('1111', $additional['cc_last4']);
        $this->assertSame('411111', $additional['cc_bin']);
        $this->assertSame('12', $additional['cc_exp_month']);
        $this->assertSame('2030', $additional['cc_exp_year']);
    }
}
