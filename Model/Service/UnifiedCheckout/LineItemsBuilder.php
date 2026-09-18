<?php declare(strict_types=1);
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderItemInterface;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;

/**
 * Maps Magento sales line items to the REST orderInformation.lineItems[] field tree.
 *
 * The REST sibling of the SOAP-era ObjectBuilder::getOrderItems(): feeds Decision Manager
 * product/velocity signals and Level II/III interchange qualification. Accepts the item sets the
 * TokenBase send_line_items wiring already supplies — order items (auth/sale), invoice items
 * (capture), creditmemo items (refund) — and emits the shared five-field parity set per row:
 * productName, productSku, quantity, unitPrice, taxAmount (row total tax, as SOAP sent).
 *
 * Amounts are the BASE-currency figures, matching the amountDetails currency every request builder
 * sends (order baseCurrencyCode); the SOAP path mixed store-currency item prices into a
 * base-currency request, which this deliberately does not reproduce.
 */
class LineItemsBuilder
{
    /**
     * Maximum length for productName / productSku (REST field maximum).
     */
    public const FIELD_MAX_LENGTH = 255;

    /**
     * LineItemsBuilder constructor.
     *
     * @param Sanitizer $sanitizer
     */
    public function __construct(
        protected readonly Sanitizer $sanitizer
    ) {
    }

    /**
     * Map sales items (order/invoice/creditmemo rows) to the REST lineItems entries.
     *
     * Child rows (configurable/bundle children) are skipped — the visible parent row carries the
     * price, and emitting both would double-count the product. Zero-quantity rows are skipped, as are
     * rows with no positive unit price: the API rejects a 0.00 unitPrice (INVALID_DATA on
     * orderInformation.lineItems[n].unitPrice), and a free row carries no interchange or fraud signal.
     *
     * @param array<int|string, mixed> $items Order, invoice, or creditmemo items.
     * @return array<int, array<string, string|int>>
     */
    public function build(array $items): array
    {
        $lineItems = [];

        foreach ($items as $item) {
            if (!$item instanceof DataObject || $this->isChildRow($item)) {
                continue;
            }

            // Invoice/creditmemo rows carry qty; order rows carry qty_ordered (getQty() is unset there).
            $quantity = (float)($item->getData('qty') ?: $item->getData('qty_ordered'));
            if ($quantity <= 0) {
                continue;
            }

            $unitPrice = $this->formatAmount($item->getData('base_price'));
            if ($unitPrice === null || (float)$unitPrice <= 0) {
                continue;
            }

            $lineItems[] = array_filter([
                'productName' => $this->sanitizer->alphanumericPunc($item->getData('name'), self::FIELD_MAX_LENGTH),
                'productSku' => $this->sanitizer->alphanumericPunc($item->getData('sku'), self::FIELD_MAX_LENGTH),
                'quantity' => max(1, (int)round($quantity)),
                'unitPrice' => $unitPrice,
                'taxAmount' => $this->formatAmount($item->getData('base_tax_amount')),
            ], static fn($value): bool => $value !== null && $value !== '');
        }

        return $lineItems;
    }

    /**
     * Whether this row is a child of another row (configurable/bundle child) and must be skipped.
     *
     * Order rows carry parent_item_id once saved — but authorization runs on Order::place() BEFORE the
     * order is saved, when the child link exists only as the parent_item object set by the quote
     * conversion (standard and multishipping checkout alike). Both forms are checked. Invoice/creditmemo
     * rows resolve the link through their order item, when one is reachable.
     *
     * @param DataObject $item
     * @return bool
     */
    protected function isChildRow(DataObject $item): bool
    {
        if ($this->hasParent($item)) {
            return true;
        }

        $orderItem = method_exists($item, 'getOrderItem') ? $item->getOrderItem() : null;

        return $orderItem instanceof DataObject && $this->hasParent($orderItem);
    }

    /**
     * Whether an order row links to a parent row, by saved id or by unsaved parent object.
     *
     * @param DataObject $orderItem
     * @return bool
     */
    protected function hasParent(DataObject $orderItem): bool
    {
        if (!empty($orderItem->getData(OrderItemInterface::PARENT_ITEM_ID))) {
            return true;
        }

        return $orderItem->getData(OrderItemInterface::PARENT_ITEM) instanceof DataObject;
    }

    /**
     * Format a base-currency amount as the fixed 2-decimal string the API expects, or null when absent.
     *
     * @param mixed $amount
     * @return string|null
     */
    protected function formatAmount(mixed $amount): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        return number_format((float)$this->sanitizer->amount($amount), 2, '.', '');
    }
}
