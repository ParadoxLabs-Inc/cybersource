<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout\Request;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\PaymentRequest
 */
class PaymentRequestTest extends TestCase
{
    public function testToArrayBuildsTheFullPaymentBody(): void
    {
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('the.transient.jwt')
            ->setClientReferenceCode('100000123')
            ->setActionList(['TOKEN_CREATE'])
            ->setActionTokenTypes(['customer', 'paymentInstrument', 'instrumentIdentifier'])
            ->setCapture(false)
            ->setTotalAmount('24.00')
            ->setCurrency('USD')
            ->setBillTo([
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'country' => 'US',
            ]);

        $result = $request->toArray();

        $this->assertSame('the.transient.jwt', $result['tokenInformation']['transientTokenJwt']);
        $this->assertSame('100000123', $result['clientReferenceInformation']['code']);
        $this->assertSame(['TOKEN_CREATE'], $result['processingInformation']['actionList']);
        $this->assertSame(
            ['customer', 'paymentInstrument', 'instrumentIdentifier'],
            $result['processingInformation']['actionTokenTypes']
        );
        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
    }

    public function testToArrayPreservesCaptureFalse(): void
    {
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')->setCapture(false);

        $result = $request->toArray();

        $this->assertArrayHasKey('capture', $result['processingInformation']);
        $this->assertFalse($result['processingInformation']['capture']);
    }

    public function testToArrayPreservesCaptureTrue(): void
    {
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')->setCapture(true);

        $result = $request->toArray();

        $this->assertTrue($result['processingInformation']['capture']);
    }

    public function testToArrayOmitsEmptyBranches(): void
    {
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt');

        $result = $request->toArray();

        $this->assertArrayHasKey('tokenInformation', $result);
        $this->assertArrayNotHasKey('orderInformation', $result);
        $this->assertArrayNotHasKey('clientReferenceInformation', $result);
        // No actionList/actionTokenTypes/capture set -> processingInformation omitted entirely.
        $this->assertArrayNotHasKey('processingInformation', $result);
    }

    public function testSettersFilterEmptyListEntries(): void
    {
        $request = new PaymentRequest();
        $request->setActionList(['TOKEN_CREATE', '', null])
            ->setActionTokenTypes(['customer', '']);

        $this->assertSame(['TOKEN_CREATE'], $request->getActionList());
        $this->assertSame(['customer'], $request->getActionTokenTypes());
    }

    public function testToArrayPreservesEnableDecisionManagerFalse(): void
    {
        // DM-suppression rides on processingInformation.enableDecisionManager=false; the boolean false must
        // survive empty-filtering (otherwise the suppression silently drops and DM runs).
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')->setEnableDecisionManager(false);

        $result = $request->toArray();

        $this->assertArrayHasKey('enableDecisionManager', $result['processingInformation']);
        $this->assertFalse($result['processingInformation']['enableDecisionManager']);
    }

    public function testToArrayOmitsEnableDecisionManagerWhenNull(): void
    {
        // Default (null) = leave DM at the account default -> the key must be absent, not sent as false.
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')->setActionList(['TOKEN_CREATE']);

        $result = $request->toArray();

        $this->assertArrayNotHasKey('enableDecisionManager', $result['processingInformation']);
    }

    public function testToArrayIncludesPartnerAttributionWhenAllSet(): void
    {
        // T4: partner solutionId, applicationName, and applicationVersion must appear in
        // clientReferenceInformation when set (shape confirmed by UC-API-REFERENCE §3).
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')
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
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')
            ->setSolutionId('')
            ->setApplicationName('ParadoxLabs_CyberSource')
            ->setApplicationVersion('3.0.0');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('partner', $result['clientReferenceInformation']);
    }

    public function testToArrayFiltersEmptyApplicationName(): void
    {
        // An empty applicationName must not emit the key.
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')
            ->setClientReferenceCode('100000123')
            ->setSolutionId('DEQXVEEG')
            ->setApplicationName('')
            ->setApplicationVersion('3.0.0');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('applicationName', $result['clientReferenceInformation']);
        $this->assertArrayHasKey('applicationVersion', $result['clientReferenceInformation']);
    }

    public function testToArrayEmitsDeviceInformationWhenFingerprintSessionIdSet(): void
    {
        // Decision Manager device signal (legacy deviceFingerprintID parity): a set fingerprint session id
        // must surface under deviceInformation.fingerprintSessionId.
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')->setFingerprintSessionId('FP-SESSION-1');

        $result = $request->toArray();

        $this->assertSame('FP-SESSION-1', $result['deviceInformation']['fingerprintSessionId']);
    }

    public function testToArrayOmitsDeviceInformationWhenFingerprintSessionIdNotSet(): void
    {
        // No fingerprint session id (default) -> deviceInformation must be absent, not an empty object.
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('deviceInformation', $result);
    }

    public function testToArrayFiltersAllPartnerFieldsWhenNoneSet(): void
    {
        // No attribution fields set -> clientReferenceInformation should not have partner, applicationName,
        // or applicationVersion keys.
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')->setClientReferenceCode('100000123');

        $result = $request->toArray();

        $this->assertArrayNotHasKey('partner', $result['clientReferenceInformation']);
        $this->assertArrayNotHasKey('applicationName', $result['clientReferenceInformation']);
        $this->assertArrayNotHasKey('applicationVersion', $result['clientReferenceInformation']);
    }

    // --- Payer Authentication pass-through (PA-1 T7) ---

    /**
     * A fully-populated request using ONLY the pre-Payer-Auth setters.
     */
    private function buildPrePayerAuthRequest(): PaymentRequest
    {
        $request = new PaymentRequest();

        return $request->setTransientTokenJwt('the.transient.jwt')
            ->setClientReferenceCode('100000123')
            ->setActionList(['TOKEN_CREATE'])
            ->setActionTokenTypes(['paymentInstrument', 'instrumentIdentifier'])
            ->setCapture(false)
            ->setEnableDecisionManager(false)
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
            . '"processingInformation":{"actionList":["TOKEN_CREATE"],'
            . '"actionTokenTypes":["paymentInstrument","instrumentIdentifier"],'
            . '"capture":false,"enableDecisionManager":false},'
            . '"orderInformation":{"amountDetails":{"totalAmount":"24.00","currency":"USD"},'
            . '"billTo":{"firstName":"Jane","lastName":"Doe","country":"US"}},'
            . '"tokenInformation":{"transientTokenJwt":"the.transient.jwt"},'
            . '"deviceInformation":{"fingerprintSessionId":"merchant12345"}}';

        $this->assertSame($expected, json_encode($this->buildPrePayerAuthRequest()->toArray()));
    }

    public function testEmptyPayerAuthAttachmentsStillEmitNothing(): void
    {
        // Explicitly setting the empty/null values must be indistinguishable from never setting them.
        $request = $this->buildPrePayerAuthRequest()
            ->setConsumerAuthenticationInformation([])
            ->setCommerceIndicator(null);

        $result = $request->toArray();

        $this->assertArrayNotHasKey('consumerAuthenticationInformation', $result);
        $this->assertArrayNotHasKey('commerceIndicator', $result['processingInformation']);
    }

    public function testConsumerAuthenticationInformationIsEmittedVerbatimAtTopLevel(): void
    {
        $request = new PaymentRequest();
        $request->setTransientTokenJwt('jwt')
            ->setConsumerAuthenticationInformation([
                'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'eciRaw' => '05',
                'paresStatus' => 'Y',
                'paSpecificationVersion' => '2.2.0',
                'directoryServerTransactionId' => 'ds-txn-1',
                'emptyValue' => '',
            ])
            ->setCommerceIndicator('vbv');

        $result = $request->toArray();

        $this->assertSame(
            [
                'cavv' => 'AAABCZIhcQAAAABZlyFxAAAAAAA=',
                'eciRaw' => '05',
                'paresStatus' => 'Y',
                'paSpecificationVersion' => '2.2.0',
                'directoryServerTransactionId' => 'ds-txn-1',
            ],
            $result['consumerAuthenticationInformation']
        );
        $this->assertSame('vbv', $result['processingInformation']['commerceIndicator']);
    }
}
