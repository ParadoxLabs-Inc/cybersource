<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\LineItemsBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\LineItemsBuilder
 */
class LineItemsBuilderTest extends TestCase
{
    private LineItemsBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new LineItemsBuilder(new Sanitizer());
    }

    public function testBuildsOrderItemRows(): void
    {
        // Order rows (authorize path) carry qty_ordered and base_price/base_tax_amount.
        $result = $this->builder->build([
            new DataObject([
                'name' => 'Widget',
                'sku' => 'WID-1',
                'qty_ordered' => '2.0000',
                'base_price' => '12.0000',
                'base_tax_amount' => '1.9800',
            ]),
        ]);

        $this->assertSame(
            [
                [
                    'productName' => 'Widget',
                    'productSku' => 'WID-1',
                    'quantity' => 2,
                    'unitPrice' => '12.00',
                    'taxAmount' => '1.98',
                ],
            ],
            $result
        );
    }

    public function testBuildsInvoiceItemRowsFromQty(): void
    {
        // Invoice/creditmemo rows carry qty (not qty_ordered) — the capture path item set.
        $result = $this->builder->build([
            new DataObject([
                'name' => 'Widget',
                'sku' => 'WID-1',
                'qty' => '1.0000',
                'base_price' => '12.0000',
                'base_tax_amount' => '0.9900',
            ]),
        ]);

        $this->assertCount(1, $result);
        $this->assertSame(1, $result[0]['quantity']);
        $this->assertSame('0.99', $result[0]['taxAmount']);
    }

    public function testSkipsZeroQuantityRows(): void
    {
        $result = $this->builder->build([
            new DataObject(['name' => 'Widget', 'sku' => 'WID-1', 'qty_ordered' => '0']),
        ]);

        $this->assertSame([], $result);
    }

    public function testSkipsOrderChildRows(): void
    {
        // A configurable/bundle child duplicates its visible parent's product; only the parent row
        // (which carries the price) may be emitted.
        $result = $this->builder->build([
            new DataObject([
                'name' => 'Widget',
                'sku' => 'WID-1',
                'qty_ordered' => '1',
                'base_price' => '12.0000',
            ]),
            new DataObject([
                'name' => 'Widget Child',
                'sku' => 'WID-1-S',
                'qty_ordered' => '1',
                'parent_item_id' => '55',
            ]),
        ]);

        $this->assertCount(1, $result);
        $this->assertSame('WID-1', $result[0]['productSku']);
    }

    public function testSkipsInvoiceChildRowsThroughTheirOrderItem(): void
    {
        // Invoice rows have no parent_item_id of their own; the child relationship lives on the
        // related order item.
        $parentOrderItem = $this->createMock(OrderItem::class);
        $parentOrderItem->method('getParentItemId')->willReturn(55);

        $childRow = $this->createPartialMock(InvoiceItem::class, ['getOrderItem']);
        $childRow->method('getOrderItem')->willReturn($parentOrderItem);
        $childRow->setData([
            'name' => 'Widget Child',
            'sku' => 'WID-1-S',
            'qty' => '1',
        ]);

        $result = $this->builder->build([$childRow]);

        $this->assertSame([], $result);
    }

    public function testSkipsNonObjectEntries(): void
    {
        $result = $this->builder->build(['not-an-item', null]);

        $this->assertSame([], $result);
    }

    public function testSanitizesNameAndSku(): void
    {
        $result = $this->builder->build([
            new DataObject([
                'name' => 'Widget <b>café</b>',
                'sku' => 'WID|1',
                'qty_ordered' => '1',
                'base_price' => '10.0000',
            ]),
        ]);

        $this->assertSame('Widget bcaf/b', $result[0]['productName']);
        $this->assertSame('WID1', $result[0]['productSku']);
    }

    public function testFractionalQuantityRoundsToAtLeastOne(): void
    {
        // The REST quantity field is an integer; a fractional-qty row (decimal qty products) must
        // not truncate to zero.
        $result = $this->builder->build([
            new DataObject(['name' => 'Bulk', 'sku' => 'BLK-1', 'qty_ordered' => '0.4', 'base_price' => '5.0000']),
        ]);

        $this->assertSame(1, $result[0]['quantity']);
    }

    public function testOmitsAbsentAmountLeaves(): void
    {
        $result = $this->builder->build([
            new DataObject(['name' => 'Widget', 'sku' => 'WID-1', 'qty_ordered' => '1']),
        ]);

        $this->assertArrayNotHasKey('unitPrice', $result[0]);
        $this->assertArrayNotHasKey('taxAmount', $result[0]);
    }
}
