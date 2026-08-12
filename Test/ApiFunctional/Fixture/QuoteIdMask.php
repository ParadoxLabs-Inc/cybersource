<?php declare(strict_types=1);
/**
 * Copyright © 2015-present ParadoxLabs, Inc.
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

namespace ParadoxLabs\CyberSource\Test\ApiFunctional\Fixture;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResourceModel;
use Magento\TestFramework\Fixture\DataFixtureInterface;

/**
 * Persist a quote id mask. Local copy of Magento\Quote\Test\Fixture\QuoteIdMask,
 * which only exists as of Magento 2.4.7 — this keeps the suite running on 2.4.6.
 */
class QuoteIdMask implements DataFixtureInterface
{
    private const FIELD_CART_ID = 'cart_id';

    public function __construct(
        private readonly QuoteIdMaskFactory $quoteIdMaskFactory,
        private readonly QuoteIdMaskResourceModel $quoteIdMaskResourceModel,
    ) {
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidArgumentException
     */
    public function apply(array $data = []): ?DataObject
    {
        if (empty($data[self::FIELD_CART_ID])) {
            throw new InvalidArgumentException(__('"%field" is required', ['field' => self::FIELD_CART_ID]));
        }

        $quoteIdMask = $this->quoteIdMaskFactory->create();
        $quoteIdMask->setQuoteId((int)$data[self::FIELD_CART_ID]);
        $this->quoteIdMaskResourceModel->save($quoteIdMask);

        return $quoteIdMask;
    }
}
