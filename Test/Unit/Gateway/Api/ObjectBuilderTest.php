<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Gateway\Api;

use Magento\Sales\Model\Order\Item as OrderItem;
use ParadoxLabs\CyberSource\Gateway\Api\BusinessRules;
use ParadoxLabs\CyberSource\Gateway\Api\DecisionManager;
use ParadoxLabs\CyberSource\Gateway\Api\MerchantDefinedData;
use ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Gateway\Api\ObjectBuilder
 */
class ObjectBuilderTest extends TestCase
{
    private ObjectBuilder $objectBuilder;

    protected function setUp(): void
    {
        $this->objectBuilder = new ObjectBuilder();
    }

    public function testGetOrderItemsEmpty(): void
    {
        $result = $this->objectBuilder->getOrderItems([]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetOrderItemsFiltersZeroQty(): void
    {
        $item1 = $this->createOrderItemMock(0);
        $item2 = $this->createOrderItemMock(2);

        $result = $this->objectBuilder->getOrderItems([$item1, $item2]);

        $this->assertCount(1, $result);
    }

    public function testGetOrderItemsSequentialLineNumbers(): void
    {
        $item1 = $this->createOrderItemMock(1);
        $item2 = $this->createOrderItemMock(1);
        $item3 = $this->createOrderItemMock(1);

        $result = $this->objectBuilder->getOrderItems([$item1, $item2, $item3]);

        $this->assertCount(3, $result);
        // Line numbers should be 0, 1, 2
        $this->assertSame(0, $result[0]->getId());
        $this->assertSame(1, $result[1]->getId());
        $this->assertSame(2, $result[2]->getId());
    }

    public function testGetOrderItemsSkipsNegativeQty(): void
    {
        $item1 = $this->createOrderItemMock(-1);
        $item2 = $this->createOrderItemMock(1);

        $result = $this->objectBuilder->getOrderItems([$item1, $item2]);

        $this->assertCount(1, $result);
    }

    public function testGetOrderItemsLineNumbersAccountForSkipped(): void
    {
        // Items with qty 0 should be skipped, but shouldn't create gaps in line numbers
        $item1 = $this->createOrderItemMock(1);  // included, line 0
        $item2 = $this->createOrderItemMock(0);  // skipped
        $item3 = $this->createOrderItemMock(1);  // included, line 1
        $item4 = $this->createOrderItemMock(0);  // skipped
        $item5 = $this->createOrderItemMock(1);  // included, line 2

        $result = $this->objectBuilder->getOrderItems([$item1, $item2, $item3, $item4, $item5]);

        $this->assertCount(3, $result);
        $this->assertSame(0, $result[0]->getId());
        $this->assertSame(1, $result[1]->getId());
        $this->assertSame(2, $result[2]->getId());
    }

    public function testGetMerchantDefinedDataSingleField(): void
    {
        $fields = [1 => 'value1'];

        $result = $this->objectBuilder->getMerchantDefinedData($fields);

        $this->assertInstanceOf(MerchantDefinedData::class, $result);
        $this->assertSame('value1', $result->getField1());
    }

    public function testGetMerchantDefinedDataMultipleFields(): void
    {
        $fields = [
            1 => 'value1',
            5 => 'value5',
            10 => 'value10',
            20 => 'value20',
        ];

        $result = $this->objectBuilder->getMerchantDefinedData($fields);

        $this->assertSame('value1', $result->getField1());
        $this->assertSame('value5', $result->getField5());
        $this->assertSame('value10', $result->getField10());
        $this->assertSame('value20', $result->getField20());
    }

    public function testGetMerchantDefinedDataEmptyArray(): void
    {
        $result = $this->objectBuilder->getMerchantDefinedData([]);

        $this->assertInstanceOf(MerchantDefinedData::class, $result);
    }

    public function testGetMerchantDefinedDataKeysAsIntStrings(): void
    {
        $fields = ['3' => 'value3'];

        $result = $this->objectBuilder->getMerchantDefinedData($fields);

        $this->assertSame('value3', $result->getField3());
    }

    public function testGetBusinessRulesBothFalse(): void
    {
        $result = $this->objectBuilder->getBusinessRules(false, false);

        $this->assertInstanceOf(BusinessRules::class, $result);
        $this->assertSame('false', $result->getIgnoreCVResult());
        $this->assertSame('false', $result->getIgnoreAVSResult());
    }

    public function testGetBusinessRulesBothTrue(): void
    {
        $result = $this->objectBuilder->getBusinessRules(true, true);

        $this->assertSame('true', $result->getIgnoreCVResult());
        $this->assertSame('true', $result->getIgnoreAVSResult());
    }

    public function testGetBusinessRulesMixed(): void
    {
        $result = $this->objectBuilder->getBusinessRules(true, false);

        $this->assertSame('true', $result->getIgnoreCVResult());
        $this->assertSame('false', $result->getIgnoreAVSResult());
    }

    public function testGetBusinessRulesDefaults(): void
    {
        $result = $this->objectBuilder->getBusinessRules();

        $this->assertSame('false', $result->getIgnoreCVResult());
        $this->assertSame('false', $result->getIgnoreAVSResult());
    }

    public function testEnableDecisionManagerEnabled(): void
    {
        $result = $this->objectBuilder->enableDecisionManager(true);

        $this->assertInstanceOf(DecisionManager::class, $result);
        $this->assertSame('true', $result->getEnabled());
    }

    public function testEnableDecisionManagerDisabled(): void
    {
        $result = $this->objectBuilder->enableDecisionManager(false);

        $this->assertSame('false', $result->getEnabled());
    }

    public function testEnableDecisionManagerDefault(): void
    {
        $result = $this->objectBuilder->enableDecisionManager();

        $this->assertSame('true', $result->getEnabled());
    }

    public function testSoapDefaultsConstant(): void
    {
        $defaults = ObjectBuilder::SOAP_DEFAULTS;

        $this->assertArrayHasKey('connection_timeout', $defaults);
        $this->assertArrayHasKey('encoding', $defaults);
        $this->assertArrayHasKey('exceptions', $defaults);
        $this->assertArrayHasKey('trace', $defaults);
        $this->assertSame(3, $defaults['connection_timeout']);
        $this->assertSame('utf-8', $defaults['encoding']);
        $this->assertTrue($defaults['exceptions']);
        $this->assertTrue($defaults['trace']);
    }

    /**
     * Create mock OrderItem with specified qty to invoice
     */
    private function createOrderItemMock(float $qtyToInvoice): OrderItem
    {
        $item = $this->createMock(OrderItem::class);
        $item->method('getQtyToInvoice')->willReturn($qtyToInvoice);
        $item->method('getPrice')->willReturn(10.00);
        $item->method('getSku')->willReturn('SKU123');
        $item->method('getName')->willReturn('Test Product');
        $item->method('getTaxAmount')->willReturn(0.80);

        return $item;
    }
}
