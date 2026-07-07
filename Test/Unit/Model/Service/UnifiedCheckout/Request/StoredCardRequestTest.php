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
            ->setCustomerId('CUST1')
            ->setPaymentInstrumentId('PI1')
            ->setInstrumentIdentifierId('II1')
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

        $this->assertSame('CUST1', $result['paymentInformation']['customer']['id']);
        $this->assertSame('PI1', $result['paymentInformation']['paymentInstrument']['id']);
        $this->assertSame('II1', $result['paymentInformation']['instrumentIdentifier']['id']);

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
            ->setCustomerId('CUST1')
            ->setPaymentInstrumentId('PI1')
            ->setInstrumentIdentifierId('II1')
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

    public function testToArrayOmitsEmptyCustomerAndInstrumentIdentifierIds(): void
    {
        $request = new StoredCardRequest();
        $request->setPaymentInstrumentId('PI1')
            ->setInitiatorType('customer')
            ->setStoredCredentialUsed(true);

        $result = $request->toArray();

        // Only the REQUIRED paymentInstrument id is present; customer/instrumentIdentifier omitted.
        $this->assertSame('PI1', $result['paymentInformation']['paymentInstrument']['id']);
        $this->assertArrayNotHasKey('customer', $result['paymentInformation']);
        $this->assertArrayNotHasKey('instrumentIdentifier', $result['paymentInformation']);
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
}
