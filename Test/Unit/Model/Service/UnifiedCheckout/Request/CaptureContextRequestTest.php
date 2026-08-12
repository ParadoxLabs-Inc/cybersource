<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout\Request;

use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequest
 */
class CaptureContextRequestTest extends TestCase
{
    private CaptureContextRequest $request;

    protected function setUp(): void
    {
        $this->request = new CaptureContextRequest();
    }

    public function testSettersReturnSelfForChaining(): void
    {
        $this->assertSame($this->request, $this->request->setClientVersion('0.34'));
        $this->assertSame($this->request, $this->request->setTargetOrigins(['https://example.com']));
        $this->assertSame($this->request, $this->request->setBillingType('FULL'));
        $this->assertSame($this->request, $this->request->setBillTo(['firstName' => 'Jane']));
        $this->assertSame($this->request, $this->request->setShowConfirmationStep(false));
        $this->assertSame($this->request, $this->request->setButtonType('PAY'));
        $this->assertSame($this->request, $this->request->setIncludeCardPrefix(true));
    }

    public function testEmptyRequestToArrayIsEmpty(): void
    {
        $this->assertSame([], $this->request->toArray());
    }

    public function testToArrayBuildsFullFieldTree(): void
    {
        $this->request
            ->setClientVersion('0.34')
            ->setTargetOrigins(['https://shop.example.com'])
            ->setAllowedCardNetworks(['VISA', 'MASTERCARD'])
            ->setAllowedPaymentTypes(['PANENTRY'])
            ->setCountry('US')
            ->setLocale('en_US')
            ->setBillingType('FULL')
            ->setRequestEmail(true)
            ->setRequestPhone(true)
            ->setRequestShipping(true)
            ->setShowConfirmationStep(true)
            ->setButtonType('CHECKOUT_AND_CONTINUE')
            ->setIncludeCardPrefix(true)
            ->setTotalAmount('24.00')
            ->setCurrency('USD')
            ->setBillTo([
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'address1' => '123 Main St',
                'buildingNumber' => '123',
                'country' => 'US',
            ]);

        $result = $this->request->toArray();

        $this->assertSame('0.34', $result['clientVersion']);
        $this->assertSame(['https://shop.example.com'], $result['targetOrigins']);
        $this->assertSame(['VISA', 'MASTERCARD'], $result['allowedCardNetworks']);
        $this->assertSame(['PANENTRY'], $result['allowedPaymentTypes']);
        $this->assertSame('US', $result['country']);
        $this->assertSame('en_US', $result['locale']);

        $this->assertSame('FULL', $result['captureMandate']['billingType']);
        // requestSaveCard is not a DTO field: the module payment[save] checkbox is the consent point.
        $this->assertArrayNotHasKey('requestSaveCard', $result['captureMandate']);
        $this->assertTrue($result['captureMandate']['requestEmail']);
        $this->assertTrue($result['captureMandate']['requestPhone']);
        $this->assertTrue($result['captureMandate']['requestShipping']);
        $this->assertTrue($result['captureMandate']['showConfirmationStep']);
        // buttonType is a top-level field, not part of captureMandate.
        $this->assertSame('CHECKOUT_AND_CONTINUE', $result['buttonType']);
        $this->assertArrayNotHasKey('buttonType', $result['captureMandate']);
        $this->assertTrue($result['transientTokenResponseOptions']['includeCardPrefix']);

        // completeMandate is never emitted: UC's client-side complete() is never called (C5, 2026-08-04).
        $this->assertArrayNotHasKey('completeMandate', $result);

        $this->assertSame('24.00', $result['orderInformation']['amountDetails']['totalAmount']);
        $this->assertSame('USD', $result['orderInformation']['amountDetails']['currency']);

        // billingType=FULL includes buildingNumber as a separate field.
        $this->assertSame('123', $result['orderInformation']['billTo']['buildingNumber']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
    }

    public function testToArrayOmitsNullAndEmptyLeaves(): void
    {
        $this->request
            ->setBillingType('FULL')
            ->setTotalAmount(null)
            ->setCurrency(null)
            ->setBillTo([]);

        $result = $this->request->toArray();

        // No amount/billTo -> no orderInformation node at all.
        $this->assertArrayNotHasKey('orderInformation', $result);
        // Null leaves dropped.
        $this->assertArrayNotHasKey('clientVersion', $result);
        $this->assertArrayNotHasKey('targetOrigins', $result);
        // Present scalars retained.
        $this->assertSame('FULL', $result['captureMandate']['billingType']);
    }

    public function testToArrayPreservesBooleanFalseFlags(): void
    {
        $this->request
            ->setRequestEmail(false)
            ->setRequestPhone(false)
            ->setRequestShipping(false)
            // false is the operative value here — it suppresses UC's review step — and must emit.
            ->setShowConfirmationStep(false)
            ->setIncludeCardPrefix(false);

        $result = $this->request->toArray();

        $this->assertFalse($result['captureMandate']['requestEmail']);
        $this->assertFalse($result['captureMandate']['requestPhone']);
        $this->assertFalse($result['captureMandate']['requestShipping']);
        $this->assertFalse($result['captureMandate']['showConfirmationStep']);
        $this->assertFalse($result['transientTokenResponseOptions']['includeCardPrefix']);
    }

    public function testToArrayOmitsPaneOptionNodesWhenUnset(): void
    {
        $this->request->setBillingType('NONE');

        $result = $this->request->toArray();

        $this->assertArrayNotHasKey('buttonType', $result);
        $this->assertArrayNotHasKey('showConfirmationStep', $result['captureMandate']);
        $this->assertArrayNotHasKey('transientTokenResponseOptions', $result);
    }

    public function testWalletPaymentTypesPassThrough(): void
    {
        $this->request->setAllowedPaymentTypes(['PANENTRY', 'APPLEPAY', 'GOOGLEPAY']);

        $result = $this->request->toArray();

        $this->assertSame(['PANENTRY', 'APPLEPAY', 'GOOGLEPAY'], $result['allowedPaymentTypes']);
    }

    public function testBillingOnlyContextOmitsAmountButKeepsBillTo(): void
    {
        $this->request
            ->setBillingType('FULL')
            ->setBillTo(['firstName' => 'Jane', 'country' => 'US']);

        $result = $this->request->toArray();

        $this->assertArrayNotHasKey('amountDetails', $result['orderInformation']);
        $this->assertSame('Jane', $result['orderInformation']['billTo']['firstName']);
    }
}
