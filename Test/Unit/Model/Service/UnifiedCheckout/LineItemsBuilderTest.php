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
        $parentOrderItem = $this->createPartialMock(OrderItem::class, []);
        $parentOrderItem->setData('parent_item_id', 55);

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

    public function testOmitsAbsentTaxLeaf(): void
    {
        $result = $this->builder->build([
            new DataObject(['name' => 'Widget', 'sku' => 'WID-1', 'qty_ordered' => '1', 'base_price' => '5.0000']),
        ]);

        $this->assertSame('5.00', $result[0]['unitPrice']);
        $this->assertArrayNotHasKey('taxAmount', $result[0]);
    }

    public function testSkipsUnsavedOrderChildRows(): void
    {
        // Authorization runs on Order::place() before the order is saved: child rows have no
        // parent_item_id yet, only the parent_item object set by the quote-to-order conversion
        // (standard and multishipping checkout). A configurable/bundle child (base_price 0) leaking
        // through here draws INVALID_DATA on lineItems[n].unitPrice from the API.
        $parent = new DataObject([
            'name' => 'Configurable',
            'sku' => 'CFG-1',
            'qty_ordered' => '1',
            'base_price' => '30.0000',
        ]);
        $child = new DataObject([
            'name' => 'Configurable Child',
            'sku' => 'CFG-1-M',
            'qty_ordered' => '1',
            'base_price' => '0.0000',
            'parent_item' => $parent,
        ]);

        $result = $this->builder->build([$parent, $child]);

        $this->assertCount(1, $result);
        $this->assertSame('CFG-1', $result[0]['productSku']);
    }

    public function testSkipsInvoiceChildRowsThroughUnsavedOrderItemParent(): void
    {
        $parentOrderItem = $this->createPartialMock(OrderItem::class, []);
        $parentOrderItem->setData('parent_item', new DataObject(['sku' => 'CFG-1']));

        $childRow = $this->createPartialMock(InvoiceItem::class, ['getOrderItem']);
        $childRow->method('getOrderItem')->willReturn($parentOrderItem);
        $childRow->setData(['name' => 'Child', 'sku' => 'CFG-1-M', 'qty' => '1', 'base_price' => '0.0000']);

        $this->assertSame([], $this->builder->build([$childRow]));
    }

    /**
     * @dataProvider nonPositivePriceProvider
     */
    public function testSkipsRowsWithoutPositiveUnitPrice(mixed $basePrice): void
    {
        // The API rejects a 0.00 unitPrice outright; a free/priceless row is dropped rather than
        // failing the whole authorization.
        $result = $this->builder->build([
            new DataObject(['name' => 'Free', 'sku' => 'FREE-1', 'qty_ordered' => '1', 'base_price' => $basePrice]),
            new DataObject(['name' => 'Paid', 'sku' => 'PAID-1', 'qty_ordered' => '1', 'base_price' => '9.9900']),
        ]);

        $this->assertCount(1, $result);
        $this->assertSame('PAID-1', $result[0]['productSku']);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function nonPositivePriceProvider(): array
    {
        return [
            'zero' => ['0.0000'],
            'null' => [null],
            'empty' => [''],
            'rounds to zero' => ['0.0040'],
        ];
    }
}
