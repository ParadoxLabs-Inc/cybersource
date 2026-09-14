<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout\Request;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\StoredCardRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\StoredCardRequest
 */
class StoredCardRequestTest extends TestCase
{
    public function testToArrayBuildsTheFullMitBody(): void
    {
        $request = new StoredCardRequest();
        $request->setClientReferenceCode('100000123')
            ->setCapture(false)
            ->setCommerceIndicator('recurring')
            ->setInitiatorType('merchant')
            ->setStoredCredentialUsed(true)
            ->setPreviousTransactionId('PRIORTXN1')
            ->setPaymentInstrumentId('PI1')
            ->setTotalAmount('24.00')
            ->setCurrency('USD')
            ->setBillTo([
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'country' => 'US',
            ]);

        $result = $request->toArray();

        $this->assertSame('100000123', $result['clientReferenceInformation']['code']);

        $initiator = $result['processingInformation']['authorizationOptions']['initiator'];
        $this->assertSame('merchant', $initiator['type']);
        $this->assertTrue($initiator['storedCredentialUsed']);
        $this->assertSame('PRIORTXN1', $initiator['merchantInitiatedTransaction']['previousTransactionId']);
        $this->assertSame('recurring', $result['processingInformation']['commerceIndicator']);

        // No transient token / TOKEN_CREATE artifacts on a stored-card body.
        $this->assertArrayNotHasKey('tokenInformation', $result);
        $this->assertArrayNotHasKey('actionList', $result['processingInformation']);

        // Standalone TMS payment instrument: no paymentInformation.customer block, ever. And PI-only:
        // no instrumentIdentifier alongside it (400 INVALID_REQUEST/INVALID_DATA — CAS sandbox spike).
        $this->assertArrayNotHasKey('customer', $result['paymentInformation']);
        $this->assertSame('PI1', $result['paymentInformation']['paymentInstrument']['id']);
        $this->assertArrayNotHasKey('instrumentIdentifier', $result['paymentInformation']);

        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);

        // capture=false must survive empty-filtering.
        $this->assertArrayHasKey('capture', $result['processingInformation']);
        $this->assertFalse($result['processingInformation']['capture']);
    }

    public function testToArrayPreservesCaptureTrue(): void
    {
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')->setCapture(true);

        $result = $request->toArray();

        $this->assertTrue($result['processingInformation']['capture']);
    }

    public function testToArrayBuildsCitBodyWithoutMitArtifacts(): void
    {
        $request = new StoredCardRequest();
        $request->setClientReferenceCode('100000123')
            ->setCapture(true)
            ->setInitiatorType('customer')
            ->setStoredCredentialUsed(true)
            ->setPaymentInstrumentId('PI1')
            ->setTotalAmount('24.00')
            ->setCurrency('USD');

        $result = $request->toArray();

        $initiator = $result['processingInformation']['authorizationOptions']['initiator'];
        $this->assertSame('customer', $initiator['type']);
        $this->assertTrue($initiator['storedCredentialUsed']);

        // CIT: no MIT sub-object, no recurring commerceIndicator.
        $this->assertArrayNotHasKey('merchantInitiatedTransaction', $initiator);
        $this->assertArrayNotHasKey('commerceIndicator', $result['processingInformation']);
    }

    public function testToArrayOmitsMerchantInitiatedTransactionWhenNoPreviousTransactionId(): void
    {
        $request = new StoredCardRequest();
        $request->setInitiatorType('merchant')
            ->setStoredCredentialUsed(true)
            ->setCommerceIndicator('recurring')
            ->setPaymentInstrumentId('PI1');

        $result = $request->toArray();

        $initiator = $result['processingInformation']['authorizationOptions']['initiator'];
        $this->assertSame('merchant', $initiator['type']);
        $this->assertArrayNotHasKey('merchantInitiatedTransaction', $initiator);
        // The recurring indicator still rides along on an MIT with no reachable prior id.
        $this->assertSame('recurring', $result['processingInformation']['commerceIndicator']);
    }

    public function testToArrayEmitsSecurityCodeUnderCardBlock(): void
    {
        // require_ccv re-entry: the re-collected code rides under paymentInformation.card.securityCode —
        // the ONLY key in the card block (PAN/expiration live on the vaulted payment instrument).
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')
            ->setInitiatorType('customer')
            ->setStoredCredentialUsed(true)
            ->setSecurityCode('123');

        $result = $request->toArray();

        $this->assertSame(['securityCode' => '123'], $result['paymentInformation']['card']);
    }

    public function testToArrayOmitsCardBlockWhenNoSecurityCode(): void
    {
        // No re-collected code (default, e.g. an MIT rebill) -> no paymentInformation.card block at all;
        // only the REQUIRED paymentInstrument id is present.
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')
            ->setInitiatorType('customer')
            ->setStoredCredentialUsed(true);

        $result = $request->toArray();

        $this->assertSame('PI1', $result['paymentInformation']['paymentInstrument']['id']);
        $this->assertArrayNotHasKey('card', $result['paymentInformation']);
        $this->assertArrayNotHasKey('instrumentIdentifier', $result['paymentInformation']);
    }

    public function testToArrayOmitsCardBlockWhenSecurityCodeEmpty(): void
    {
        // An empty-string code must not emit an empty card block.
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')->setSecurityCode('');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('card', $result['paymentInformation']);
    }

    public function testToArrayOmitsInitiatorBlockWhenNoInitiatorType(): void
    {
        $request = new StoredCardRequest();
        $request->setStoredCredentialUsed(true)->setPaymentInstrumentId('PI1');

        $result = $request->toArray();

        // No initiatorType -> the whole authorizationOptions.initiator block is omitted.
        $this->assertArrayNotHasKey('authorizationOptions', $result['processingInformation'] ?? []);
    }

    public function testToArrayEmptyDtoIsEmpty(): void
    {
        $request = new StoredCardRequest();

        $this->assertSame([], $request->toArray());
    }

    public function testToArrayPreservesEnableDecisionManagerFalse(): void
    {
        // MIT DM-suppression: enableDecisionManager=false must survive empty-filtering on the stored-card
        // body too (alongside the recurring/MIT initiator block).
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI-1')->setEnableDecisionManager(false);

        $result = $request->toArray();

        $this->assertArrayHasKey('enableDecisionManager', $result['processingInformation']);
        $this->assertFalse($result['processingInformation']['enableDecisionManager']);
    }

    public function testToArrayOmitsEnableDecisionManagerWhenNull(): void
    {
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI-1')->setCapture(false);

        $result = $request->toArray();

        $this->assertArrayNotHasKey('enableDecisionManager', $result['processingInformation']);
    }

    public function testToArrayIncludesPartnerAttributionWhenAllSet(): void
    {
        // T4: partner solutionId, applicationName, and applicationVersion must appear in
        // clientReferenceInformation when set.
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')
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
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')
            ->setSolutionId('')
            ->setApplicationName('ParadoxLabs_CyberSource');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('partner', $result['clientReferenceInformation']);
    }

    public function testToArrayEmitsDeviceInformationWhenFingerprintSessionIdSet(): void
    {
        // Decision Manager device signal (legacy deviceFingerprintID parity): a set fingerprint session id
        // must surface under deviceInformation.fingerprintSessionId (CIT stored-card charges only).
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')->setFingerprintSessionId('FP-SESSION-1');

        $result = $request->toArray();

        $this->assertSame('FP-SESSION-1', $result['deviceInformation']['fingerprintSessionId']);
    }

    public function testToArrayOmitsDeviceInformationWhenFingerprintSessionIdNotSet(): void
    {
        // No fingerprint session id (default, e.g. an MIT rebill) -> deviceInformation must be absent.
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('deviceInformation', $result);
    }

    public function testToArrayFiltersEmptyApplicationFields(): void
    {
        // Empty applicationName/applicationVersion must not emit those keys.
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')
            ->setClientReferenceCode('100000123')
            ->setSolutionId('DEQXVEEG')
            ->setApplicationName('')
            ->setApplicationVersion('');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('applicationName', $result['clientReferenceInformation']);
        $this->assertArrayNotHasKey('applicationVersion', $result['clientReferenceInformation']);
    }

    // --- Payer Authentication pass-through ---

    /**
     * A fully-populated stored-card request using ONLY the pre-Payer-Auth setters.
     */
    private function buildPrePayerAuthRequest(): StoredCardRequest
    {
        $request = new StoredCardRequest();

        return $request->setClientReferenceCode('100000123')
            ->setCapture(false)
            ->setEnableDecisionManager(false)
            ->setCommerceIndicator('recurring')
            ->setInitiatorType('merchant')
            ->setStoredCredentialUsed(true)
            ->setPreviousTransactionId('PRIORTXN1')
            ->setPaymentInstrumentId('PI1')
            ->setSecurityCode('123')
            ->setTotalAmount('24.00')
            ->setCurrency('USD')
            ->setBillTo(['firstName' => 'Jane', 'lastName' => 'Doe', 'country' => 'US'])
            ->setSolutionId('DEQXVEEG')
            ->setApplicationName('ParadoxLabs_CyberSource')
            ->setApplicationVersion('4.0.0')
            ->setFingerprintSessionId('merchant12345');
    }

    /**
     * THE money-path regression test: with no payer-auth result attached, the emitted body must be
     * byte-identical to what this DTO emitted before Payer Authentication existed. The expected JSON
     * below is transcribed from the pre-change toArray() (field order included) and is deliberately
     * NOT generated from the class under test.
     */
    public function testUnsetPayerAuthEmitsByteIdenticalBody(): void
    {
        $expected = '{"clientReferenceInformation":{"code":"100000123",'
            . '"applicationName":"ParadoxLabs_CyberSource","applicationVersion":"4.0.0",'
            . '"partner":{"solutionId":"DEQXVEEG"}},'
            . '"processingInformation":{"capture":false,"commerceIndicator":"recurring",'
            . '"enableDecisionManager":false,'
            . '"authorizationOptions":{"initiator":{"type":"merchant","storedCredentialUsed":true,'
            . '"merchantInitiatedTransaction":{"previousTransactionId":"PRIORTXN1"}}}},'
            . '"paymentInformation":{"paymentInstrument":{"id":"PI1"},"card":{"securityCode":"123"}},'
            . '"orderInformation":{"amountDetails":{"totalAmount":"24.00","currency":"USD"},'
            . '"billTo":{"firstName":"Jane","lastName":"Doe","country":"US"}},'
            . '"deviceInformation":{"fingerprintSessionId":"merchant12345"}}';

        $this->assertSame($expected, json_encode($this->buildPrePayerAuthRequest()->toArray()));
    }

    public function testEmptyPayerAuthAttachmentStillEmitsNothing(): void
    {
        $request = $this->buildPrePayerAuthRequest()->setConsumerAuthenticationInformation([]);

        $this->assertArrayNotHasKey('consumerAuthenticationInformation', $request->toArray());
    }

    public function testConsumerAuthenticationInformationIsEmittedVerbatimAtTopLevel(): void
    {
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')
            ->setInitiatorType('customer')
            ->setConsumerAuthenticationInformation([
                'ucafAuthenticationData' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'ucafCollectionIndicator' => '2',
                'eciRaw' => '02',
                'paresStatus' => 'Y',
                'emptyValue' => '',
            ])
            ->setCommerceIndicator('spa');

        $result = $request->toArray();

        $this->assertSame(
            [
                'ucafAuthenticationData' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'ucafCollectionIndicator' => '2',
                'eciRaw' => '02',
                'paresStatus' => 'Y',
            ],
            $result['consumerAuthenticationInformation']
        );
        $this->assertSame('spa', $result['processingInformation']['commerceIndicator']);
    }

    public function testToArrayIncludesShipToAndLineItems(): void
    {
        // Issue #14: stored-card auths carried shipTo + items in 3.x too (same authorize path).
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI123')
            ->setShipTo(['firstName' => 'Jane', 'country' => 'US'])
            ->setLineItems([
                ['productName' => 'Widget', 'productSku' => 'WID-1', 'quantity' => 1, 'unitPrice' => '12.00'],
            ]);

        $result = $request->toArray();

        $this->assertSame('Jane', $result['orderInformation']['shipTo']['firstName']);
        $this->assertSame('WID-1', $result['orderInformation']['lineItems'][0]['productSku']);
    }

    public function testToArrayOmitsShipToAndLineItemsWhenEmpty(): void
    {
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI123')->setTotalAmount('5.00');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('shipTo', $result['orderInformation']);
        $this->assertArrayNotHasKey('lineItems', $result['orderInformation']);
    }
}
