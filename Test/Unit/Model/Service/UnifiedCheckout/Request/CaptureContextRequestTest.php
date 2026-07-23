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
            ->setCompleteMandateType('AUTH')
            ->setDecisionManager(true)
            ->setConsumerAuthentication(true)
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

        $this->assertSame('AUTH', $result['completeMandate']['type']);
        $this->assertTrue($result['completeMandate']['decisionManager']);
        $this->assertTrue($result['completeMandate']['consumerAuthentication']);

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
            ->setCompleteMandateType('AUTH')
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
        $this->assertSame('AUTH', $result['completeMandate']['type']);
    }

    public function testToArrayPreservesBooleanFalseFlags(): void
    {
        $this->request
            ->setDecisionManager(false)
            ->setConsumerAuthentication(false)
            ->setRequestEmail(false)
            ->setRequestPhone(false)
            ->setRequestShipping(false);

        $result = $this->request->toArray();

        $this->assertFalse($result['completeMandate']['decisionManager']);
        $this->assertFalse($result['completeMandate']['consumerAuthentication']);
        $this->assertFalse($result['captureMandate']['requestEmail']);
        $this->assertFalse($result['captureMandate']['requestPhone']);
        $this->assertFalse($result['captureMandate']['requestShipping']);
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
