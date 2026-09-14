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

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request;

/**
 * Shared orderInformation.lineItems storage/emission for the Unified Checkout request DTOs.
 *
 * Entries are already API-mapped (productName, productSku, quantity, unitPrice, taxAmount) by
 * LineItemsBuilder; the DTO only prunes empty leaves and empty rows at emission time.
 */
trait LineItemsTrait
{
    /**
     * Order line items (orderInformation.lineItems), each entry keyed by API field name.
     *
     * Empty (default) = line items disabled (send_line_items) or none available; omitted entirely.
     *
     * @var array<int, array<string, string|int|null>>
     */
    private array $lineItems = [];

    /**
     * Get the order line items.
     *
     * @return array<int, array<string, string|int|null>>
     */
    public function getLineItems(): array
    {
        return $this->lineItems;
    }

    /**
     * Set the order line items, each entry keyed by API field name.
     *
     * @param array<int, array<string, string|int|null>> $lineItems
     * @return $this
     */
    public function setLineItems(array $lineItems): self
    {
        $this->lineItems = $lineItems;

        return $this;
    }

    /**
     * Prune the line items for emission: drop empty leaves per row, then drop empty rows.
     *
     * @return array<int, array<string, string|int>>
     */
    protected function buildLineItems(): array
    {
        $lineItems = [];

        foreach ($this->lineItems as $lineItem) {
            $lineItem = $this->filterEmpty($lineItem);

            if (!empty($lineItem)) {
                $lineItems[] = $lineItem;
            }
        }

        return $lineItems;
    }
}
